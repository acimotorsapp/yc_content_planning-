<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\MasterData;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BulkUploadController extends Controller
{
    /**
     * Display the bulk upload index page.
     */
    public function index()
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $totalEvents = CalendarEvent::count();
        $productEventsCount = CalendarEvent::where('team_type', 'product_team')->count();
        $digitalEventsCount = CalendarEvent::where('team_type', 'digital_team')->count();
        $masterDataCount = MasterData::count();

        return view('admin.bulk_upload.index', compact(
            'totalEvents',
            'productEventsCount',
            'digitalEventsCount',
            'masterDataCount'
        ));
    }

    /**
     * Handle bulk events upload (Excel / CSV).
     */
    public function uploadEvents(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:20480',
            'team_type' => 'required|in:auto,product_team,digital_team,global_team',
            'target_year' => 'nullable|integer|min:2020|max:2035',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();
        $teamType = $request->input('team_type', 'auto');
        $targetYear = (int) $request->input('target_year', date('Y'));

        try {
            $spreadsheet = IOFactory::load($filePath);
            $importedCount = 0;
            $duplicatesCount = 0;
            $skippedCount = 0;
            $errors = [];

            if ($teamType === 'auto') {
                $sheetNames = $spreadsheet->getSheetNames();
                $processedAny = false;

                foreach ($sheetNames as $sheetName) {
                    $sheet = $spreadsheet->getSheetByName($sheetName);
                    $lowerName = strtolower(trim($sheetName));

                    if (str_contains($lowerName, 'product')) {
                        $res = $this->importProductSheet($sheet, $targetYear);
                        $importedCount += $res['imported'];
                        $duplicatesCount += $res['duplicates'] ?? 0;
                        $skippedCount += $res['skipped'];
                        $errors = array_merge($errors, $res['errors']);
                        $processedAny = true;
                    } elseif (str_contains($lowerName, 'digital')) {
                        $res = $this->importDigitalSheet($sheet, $targetYear);
                        $importedCount += $res['imported'];
                        $duplicatesCount += $res['duplicates'] ?? 0;
                        $skippedCount += $res['skipped'];
                        $errors = array_merge($errors, $res['errors']);
                        $processedAny = true;
                    }
                }

                // If no specific sheet matched, inspect the active sheet headers
                if (!$processedAny) {
                    $activeSheet = $spreadsheet->getActiveSheet();
                    $headers = $this->extractHeaders($activeSheet);
                    if ($this->isProductHeader($headers)) {
                        $res = $this->importProductSheet($activeSheet, $targetYear);
                        $importedCount += $res['imported'];
                        $duplicatesCount += $res['duplicates'] ?? 0;
                        $skippedCount += $res['skipped'];
                        $errors = array_merge($errors, $res['errors']);
                    } else {
                        $res = $this->importDigitalSheet($activeSheet, $targetYear);
                        $importedCount += $res['imported'];
                        $duplicatesCount += $res['duplicates'] ?? 0;
                        $skippedCount += $res['skipped'];
                        $errors = array_merge($errors, $res['errors']);
                    }
                }
            } elseif ($teamType === 'product_team') {
                $sheet = $spreadsheet->getActiveSheet();
                $res = $this->importProductSheet($sheet, $targetYear);
                $importedCount += $res['imported'];
                $duplicatesCount += $res['duplicates'] ?? 0;
                $skippedCount += $res['skipped'];
                $errors = array_merge($errors, $res['errors']);
            } elseif ($teamType === 'digital_team') {
                $sheet = $spreadsheet->getActiveSheet();
                $res = $this->importDigitalSheet($sheet, $targetYear);
                $importedCount += $res['imported'];
                $duplicatesCount += $res['duplicates'] ?? 0;
                $skippedCount += $res['skipped'];
                $errors = array_merge($errors, $res['errors']);
            } elseif ($teamType === 'global_team') {
                $sheet = $spreadsheet->getActiveSheet();
                $res = $this->importGlobalSheet($sheet, $targetYear);
                $importedCount += $res['imported'];
                $duplicatesCount += $res['duplicates'] ?? 0;
                $skippedCount += $res['skipped'];
                $errors = array_merge($errors, $res['errors']);
            }

            $message = "Successfully uploaded and imported {$importedCount} new events.";
            if ($duplicatesCount > 0) {
                $message .= " {$duplicatesCount} duplicate events were skipped (already exist in database).";
            }
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} rows were skipped (empty or unparseable).";
            }

            return redirect()->route('admin.bulk_upload.index')
                ->with('success', $message)
                ->with('upload_errors', array_slice($errors, 0, 10));

        } catch (\Exception $e) {
            return redirect()->route('admin.bulk_upload.index')
                ->with('error', 'Error reading or importing file: ' . $e->getMessage());
        }
    }

    /**
     * Handle bulk master data upload (Categories: platform, format, aipe_pillar, product).
     */
    public function uploadMasterData(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            if (empty($rows)) {
                return redirect()->route('admin.bulk_upload.index')->with('error', 'The uploaded file is empty.');
            }

            $headerRow = array_shift($rows);
            $categoryCol = null;
            $valueCol = null;

            foreach ($headerRow as $colKey => $colName) {
                $clean = strtolower(trim((string)$colName));
                if (in_array($clean, ['category', 'type', 'cat'])) {
                    $categoryCol = $colKey;
                } elseif (in_array($clean, ['value', 'name', 'title', 'item', 'entry'])) {
                    $valueCol = $colKey;
                }
            }

            // Fallback to first and second columns if headers not clearly named
            if (!$categoryCol || !$valueCol) {
                $keys = array_keys($headerRow);
                $categoryCol = $keys[0] ?? 'A';
                $valueCol = $keys[1] ?? 'B';
            }

            $imported = 0;
            $duplicates = 0;
            $skipped = 0;
            $validCategories = ['platform', 'format', 'aipe_pillar', 'product'];

            foreach ($rows as $rowIndex => $row) {
                $categoryRaw = trim((string)($row[$categoryCol] ?? ''));
                $valueRaw = trim((string)($row[$valueCol] ?? ''));

                if (empty($categoryRaw) || empty($valueRaw)) {
                    $skipped++;
                    continue;
                }

                $category = strtolower(str_replace([' ', '-'], '_', $categoryRaw));
                if ($category === 'pillar' || $category === 'aipe') {
                    $category = 'aipe_pillar';
                } elseif ($category === 'platforms') {
                    $category = 'platform';
                } elseif ($category === 'formats') {
                    $category = 'format';
                } elseif ($category === 'products') {
                    $category = 'product';
                }

                if (!in_array($category, $validCategories)) {
                    $skipped++;
                    continue;
                }

                $exists = MasterData::where('category', $category)->where('value', $valueRaw)->exists();
                if ($exists) {
                    $duplicates++;
                    continue;
                }

                MasterData::create([
                    'category' => $category,
                    'value' => $valueRaw,
                    'is_active' => true,
                ]);
                $imported++;
            }

            $message = "Successfully imported {$imported} new Master Data entries.";
            if ($duplicates > 0) {
                $message .= " {$duplicates} duplicate entries were skipped (already exist).";
            }
            if ($skipped > 0) {
                $message .= " {$skipped} invalid/empty rows skipped.";
            }

            return redirect()->route('admin.bulk_upload.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('admin.bulk_upload.index')
                ->with('error', 'Failed to import Master Data: ' . $e->getMessage());
        }
    }

    /**
     * Generate and download sample template files.
     */
    public function downloadSample(string $type, Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $format = $request->query('format', 'xlsx'); // xlsx or csv
        $spreadsheet = new Spreadsheet();

        if ($type === 'product-events') {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Product Team');

            $headers = ['Date', 'Day', 'Content', 'A.I.P.E Pillar', 'Content Objective', 'Shoot Date', 'Publish Date', 'Color Concern', 'Format', 'Budget', 'Platform', 'Product'];
            $sheet->fromArray([$headers], null, 'A1');

            $samples = [
                ['8/8/2026', 'Saturday', 'Life Style Review', 'Interest', 'To showcase an authentic customer experience with Yamaha FZS Version 2', '2026-07-26', '2026-08-08', 'Dark night', 'Product Review', '15000', 'Facebook', 'FZS V2'],
                ['8/25/2026', 'Tuesday', 'Customer Review', 'Interest+Experience', 'Showcase real customer experience, authenticity, and guidelines for future riders', '2026-08-01', '2026-08-25', 'R15 V3 Racing Blue', 'Product Review', '15000', 'Facebook', 'R15'],
                ['8/28/2026', 'Friday', 'FZ 25 Spider Man Content', 'Awareness+Interest', 'Establishing Offline Activation, UGC Content, grabbing regular people attention', '2026-08-08', '2026-08-28', 'Blue', 'Special Content', '125000', 'YRC Page', 'FZ 25'],
            ];
            $sheet->fromArray($samples, null, 'A2');
            $this->styleHeaderRow($sheet, 'A1:L1');

            $filename = 'product_team_events_sample.' . $format;
        } elseif ($type === 'digital-events') {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Digital team');

            $headers = ['Date', 'Day', 'Post No.', 'A.I.P.E Pillar', 'Product Focus', 'Content Objective', 'Format', 'Asset/Drive Link', 'Remarks', 'Boosting budget'];
            $sheet->fromArray([$headers], null, 'A1');

            $samples = [
                ['1 Sep 2026', 'Tue', '1', 'Offer Post', 'Multi-model: FZS V2 DD, FZS V4, FZS Hybrid', 'September offer announcement with confirmed prices and showroom enquiry CTA.', 'Static', 'https://drive.google.com/...', 'Monthly Offer Post', '5000'],
                ['3 Sep 2026', 'Thu', '2', 'Purchase Generation', 'FZS V4', 'Offer video presenting the priority range, key value cues and direct lead generation.', 'Reel', '', 'High priority', '10000'],
                ['5 Sep 2026', 'Sat', '3', 'Interest Generation', 'FZS V2 DD', 'Everyday usability content highlighting comfortable ergonomics and dependable braking.', 'Motion', '', '', '3000'],
            ];
            $sheet->fromArray($samples, null, 'A2');
            $this->styleHeaderRow($sheet, 'A1:J1');

            $filename = 'digital_team_events_sample.' . $format;
        } elseif ($type === 'master-data') {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Master Data');

            $headers = ['Category', 'Value'];
            $sheet->fromArray([$headers], null, 'A1');

            $samples = [
                ['platform', 'Facebook'],
                ['platform', 'Instagram'],
                ['platform', 'Youtube'],
                ['format', 'Product Review'],
                ['format', 'Reels'],
                ['format', 'Special Content'],
                ['aipe_pillar', 'Awareness'],
                ['aipe_pillar', 'Interest'],
                ['aipe_pillar', 'Experience'],
                ['product', 'FZS V2'],
                ['product', 'FZS V4'],
                ['product', 'R15'],
                ['product', 'MT 15'],
            ];
            $sheet->fromArray($samples, null, 'A2');
            $this->styleHeaderRow($sheet, 'A1:B1');

            $filename = 'master_data_sample.' . $format;
        } elseif ($type === 'global-events') {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Global Events');

            $headers = ['Date', 'Event Title', 'Objective / Description'];
            $sheet->fromArray([$headers], null, 'A1');

            $samples = [
                ['2026-08-30', 'Aragon GP', 'Global MotoGP race event'],
                ['2026-09-05', 'International Day of Charity', 'Global observance and CSR post'],
                ['2026-09-12', 'Saluto UBS Launching', 'Product launch event'],
            ];
            $sheet->fromArray($samples, null, 'A2');
            $this->styleHeaderRow($sheet, 'A1:C1');

            $filename = 'global_events_sample.' . $format;
        } else {
            // Full Content Plan Multi-sheet Workbook
            $sheet1 = $spreadsheet->getActiveSheet();
            $sheet1->setTitle('Product Team');
            $sheet1->fromArray([['Date', 'Day', 'Content', 'A.I.P.E Pillar', 'Content Objective', 'Shoot Date', 'Publish Date', 'Color Concern', 'Format', 'Budget', 'Platform', 'Product']], null, 'A1');
            $sheet1->fromArray([
                ['8/8/2026', 'Saturday', 'Life Style Review', 'Interest', 'Yamaha FZS V2 lifestyle review', '2026-07-26', '2026-08-08', 'Dark night', 'Product Review', '15000', 'Facebook', 'FZS V2'],
            ], null, 'A2');
            $this->styleHeaderRow($sheet1, 'A1:L1');

            $sheet2 = $spreadsheet->createSheet();
            $sheet2->setTitle('Digital team');
            $sheet2->fromArray([['Date', 'Day', 'Post No.', 'A.I.P.E Pillar', 'Product Focus', 'Content Objective', 'Format', 'Asset/Drive Link', 'Remarks', 'Boosting budget']], null, 'A1');
            $sheet2->fromArray([
                ['1 Sep 2026', 'Tue', '1', 'Offer Post', 'FZS V4', 'Offer announcement with confirmed pricing', 'Static', '', 'Monthly launch', '5000'],
            ], null, 'A2');
            $this->styleHeaderRow($sheet2, 'A1:J1');

            $filename = 'yamaha_content_plan_full_template.xlsx';
            $format = 'xlsx';
        }

        return new StreamedResponse(function () use ($spreadsheet, $format) {
            if ($format === 'csv') {
                $writer = new Csv($spreadsheet);
            } else {
                $writer = new Xlsx($spreadsheet);
            }
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => $format === 'csv' ? 'text/csv' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Apply nice visual styling to header row in exported templates.
     */
    private function styleHeaderRow($sheet, string $range)
    {
        $sheet->getStyle($range)->getFont()->setBold(true);
        $sheet->getStyle($range)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF1F5F9');
        
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Import Product Team sheet data into database.
     */
    private function importProductSheet($sheet, int $targetYear): array
    {
        $rows = $sheet->toArray(null, true, true, true);
        if (empty($rows)) {
            return ['imported' => 0, 'duplicates' => 0, 'skipped' => 0, 'errors' => []];
        }

        $headerRow = array_shift($rows);
        $colMap = $this->mapColumns($headerRow);

        $imported = 0;
        $duplicates = 0;
        $skipped = 0;
        $errors = [];

        // Find or assign to default product team user, fallback to auth user
        $user = User::where('role', 'product_team')->first() ?? auth()->user();

        foreach ($rows as $rowIndex => $row) {
            // Check if row has any non-empty content
            $hasAnyCell = false;
            foreach ($row as $cell) {
                if (!is_null($cell) && trim((string)$cell) !== '') {
                    $hasAnyCell = true;
                    break;
                }
            }

            if (!$hasAnyCell) {
                $skipped++;
                continue;
            }

            $contentTitle = trim((string)($row[$colMap['content'] ?? ''] ?? $row[$colMap['content_title'] ?? ''] ?? ''));
            $objective = trim((string)($row[$colMap['content_objective'] ?? ''] ?? ''));
            $product = trim((string)($row[$colMap['product'] ?? ''] ?? ''));
            
            // Try getting date from Publish Date or Date column
            $dateRaw = $row[$colMap['publish_date'] ?? ''] ?? $row[$colMap['date'] ?? ''] ?? null;
            $eventDate = $this->parseDateValue($dateRaw, $targetYear);

            // If event date could not be parsed
            if (!$eventDate) {
                $firstColVal = reset($row);
                $eventDate = $this->parseDateValue($firstColVal, $targetYear);
            }

            // If still no event date and neither contentTitle nor product exists (e.g. Month banner row)
            if (!$eventDate) {
                if (empty($contentTitle) && empty($objective) && empty($product)) {
                    $skipped++;
                    continue;
                }
                $skipped++;
                $errors[] = "Row {$rowIndex}: Missing or invalid event date.";
                continue;
            }

            if (empty($contentTitle)) {
                $contentTitle = $product ? "Product: {$product}" : 'Product Content (' . $eventDate->format('d M') . ')';
            }

            // Check if this event already exists in database (avoid duplication)
            $existsQuery = CalendarEvent::where('team_type', 'product_team')
                ->where('event_date', $eventDate->format('Y-m-d'))
                ->where(function ($q) use ($contentTitle, $product) {
                    $q->where('content_title', $contentTitle);
                    if (!empty($product)) {
                        $q->orWhere('product', $product);
                    }
                });

            if ($existsQuery->exists()) {
                $duplicates++;
                continue;
            }

            $shootDateRaw = $row[$colMap['shoot_date'] ?? ''] ?? null;
            $shootDate = $this->parseDateValue($shootDateRaw, $targetYear);

            CalendarEvent::create([
                'user_id' => $user->id,
                'team_type' => 'product_team',
                'event_date' => $eventDate->format('Y-m-d'),
                'content_title' => $contentTitle,
                'aipe_pillar' => trim((string)($row[$colMap['aipe_pillar'] ?? ''] ?? '')),
                'content_objective' => $objective,
                'shoot_date' => $shootDate ? $shootDate->format('Y-m-d') : null,
                'color_concern' => trim((string)($row[$colMap['color_concern'] ?? ''] ?? '')),
                'format' => trim((string)($row[$colMap['format'] ?? ''] ?? '')),
                'boosting_budget' => trim((string)($row[$colMap['budget'] ?? ''] ?? $row[$colMap['boosting_budget'] ?? ''] ?? '')),
                'platform' => trim((string)($row[$colMap['platform'] ?? ''] ?? '')),
                'product' => $product,
                'drive_link' => trim((string)($row[$colMap['drive_link'] ?? ''] ?? $row[$colMap['asset_link'] ?? ''] ?? '')),
                'remarks' => trim((string)($row[$colMap['remarks'] ?? ''] ?? '')),
            ]);

            $imported++;
        }

        return ['imported' => $imported, 'duplicates' => $duplicates, 'skipped' => $skipped, 'errors' => $errors];
    }

    /**
     * Import Digital Team sheet data into database.
     */
    private function importDigitalSheet($sheet, int $targetYear): array
    {
        $rows = $sheet->toArray(null, true, true, true);
        if (empty($rows)) {
            return ['imported' => 0, 'duplicates' => 0, 'skipped' => 0, 'errors' => []];
        }

        $headerRow = array_shift($rows);
        $colMap = $this->mapColumns($headerRow);

        $imported = 0;
        $duplicates = 0;
        $skipped = 0;
        $errors = [];

        // Find or assign to default digital team user, fallback to auth user
        $user = User::where('role', 'digital_team')->first() ?? auth()->user();

        foreach ($rows as $rowIndex => $row) {
            $hasAnyCell = false;
            foreach ($row as $cell) {
                if (!is_null($cell) && trim((string)$cell) !== '') {
                    $hasAnyCell = true;
                    break;
                }
            }

            if (!$hasAnyCell) {
                $skipped++;
                continue;
            }

            $firstVal = reset($row);
            $firstValLower = strtolower(trim((string)$firstVal));

            // Skip footer summary, total posts, or planning dependency sections
            if (str_contains($firstValLower, 'planning depend') || 
                str_contains($firstValLower, 'total post') || 
                str_contains($firstValLower, 'core objective') ||
                str_contains($firstValLower, 'summary') ||
                str_contains($firstValLower, 'note:')) {
                $skipped++;
                continue;
            }

            $dateRaw = $row[$colMap['date'] ?? ''] ?? $row[$colMap['event_date'] ?? ''] ?? null;
            $eventDate = $this->parseDateValue($dateRaw, $targetYear);

            if (!$eventDate) {
                $eventDate = $this->parseDateValue($firstVal, $targetYear);
            }

            $objective = trim((string)($row[$colMap['content_objective'] ?? ''] ?? $row[$colMap['objective'] ?? ''] ?? ''));
            $productFocus = trim((string)($row[$colMap['product_focus'] ?? ''] ?? $row[$colMap['product'] ?? ''] ?? ''));
            $postNo = trim((string)($row[$colMap['post_no'] ?? ''] ?? $row[$colMap['post'] ?? ''] ?? ''));

            if (!$eventDate) {
                if (empty($objective) && empty($productFocus) && empty($postNo)) {
                    $skipped++;
                    continue;
                }
                // If it looks like a note row or secondary dependency row
                if (empty($postNo) && (str_contains($firstValLower, 'offer') || str_contains($firstValLower, 'day'))) {
                    $skipped++;
                    continue;
                }
                $skipped++;
                $errors[] = "Row {$rowIndex}: Missing valid event date.";
                continue;
            }

            $contentTitle = !empty($productFocus) ? "Post #{$postNo}: {$productFocus}" : ($postNo ? "Post #{$postNo}" : "Digital Content");

            // Check if this digital event already exists in database
            $existsQuery = CalendarEvent::where('team_type', 'digital_team')
                ->where('event_date', $eventDate->format('Y-m-d'))
                ->where(function ($q) use ($postNo, $contentTitle, $productFocus) {
                    if (!empty($postNo)) {
                        $q->where('post_no', $postNo);
                    }
                    $q->orWhere('content_title', $contentTitle);
                    if (!empty($productFocus)) {
                        $q->orWhere('product_focus', $productFocus);
                    }
                });

            if ($existsQuery->exists()) {
                $duplicates++;
                continue;
            }

            CalendarEvent::create([
                'user_id' => $user->id,
                'team_type' => 'digital_team',
                'event_date' => $eventDate->format('Y-m-d'),
                'content_title' => $contentTitle,
                'post_no' => $postNo ?: null,
                'aipe_pillar' => trim((string)($row[$colMap['aipe_pillar'] ?? ''] ?? '')),
                'product_focus' => $productFocus ?: null,
                'content_objective' => $objective ?: null,
                'format' => trim((string)($row[$colMap['format'] ?? ''] ?? '')),
                'drive_link' => trim((string)($row[$colMap['drive_link'] ?? ''] ?? $row[$colMap['asset_link'] ?? ''] ?? '')),
                'remarks' => trim((string)($row[$colMap['remarks'] ?? ''] ?? '')),
                'boosting_budget' => trim((string)($row[$colMap['budget'] ?? ''] ?? $row[$colMap['boosting_budget'] ?? ''] ?? '')),
            ]);

            $imported++;
        }

        return ['imported' => $imported, 'duplicates' => $duplicates, 'skipped' => $skipped, 'errors' => $errors];
    }

    /**
     * Import Global sheet data into database.
     */
    private function importGlobalSheet($sheet, int $targetYear): array
    {
        $rows = $sheet->toArray(null, true, true, true);
        if (empty($rows)) {
            return ['imported' => 0, 'duplicates' => 0, 'skipped' => 0, 'errors' => []];
        }

        $headerRow = array_shift($rows);
        $colMap = $this->mapColumns($headerRow);

        $imported = 0;
        $duplicates = 0;
        $skipped = 0;
        $errors = [];
        $user = auth()->user();

        foreach ($rows as $rowIndex => $row) {
            if (empty(array_filter($row))) {
                $skipped++;
                continue;
            }

            $dateRaw = $row[$colMap['date'] ?? ''] ?? $row[$colMap['event_date'] ?? ''] ?? null;
            $eventDate = $this->parseDateValue($dateRaw, $targetYear);

            $title = trim((string)($row[$colMap['content_title'] ?? ''] ?? $row[$colMap['title'] ?? ''] ?? $row[$colMap['event_title'] ?? ''] ?? $row[$colMap['content'] ?? ''] ?? ''));
            $objective = trim((string)($row[$colMap['content_objective'] ?? ''] ?? $row[$colMap['description'] ?? ''] ?? $row[$colMap['objective'] ?? ''] ?? ''));

            if (!$eventDate || empty($title)) {
                $skipped++;
                continue;
            }

            $exists = CalendarEvent::where('team_type', 'global_team')
                ->where('event_date', $eventDate->format('Y-m-d'))
                ->where('content_title', $title)
                ->exists();

            if ($exists) {
                $duplicates++;
                continue;
            }

            CalendarEvent::create([
                'user_id' => $user->id,
                'team_type' => 'global_team',
                'event_date' => $eventDate->format('Y-m-d'),
                'content_title' => $title,
                'content_objective' => $objective ?: 'Global observance',
            ]);

            $imported++;
        }

        return ['imported' => $imported, 'duplicates' => $duplicates, 'skipped' => $skipped, 'errors' => $errors];
    }

    /**
     * Map spreadsheet header columns to standard keys.
     */
    private function mapColumns(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $colLetter => $headerName) {
            if (is_null($headerName)) continue;
            $normalized = strtolower(trim((string)$headerName));
            $normalized = str_replace(['.', '-', '/', ' '], '_', $normalized);
            $normalized = preg_replace('/_+/', '_', $normalized);
            $normalized = trim($normalized, '_');

            // Standardize aliases
            if (in_array($normalized, ['date', 'event_date', 'publish_date', 'pub_date'])) {
                $map['date'] = $colLetter;
            }
            if (in_array($normalized, ['publish_date', 'publishing_date'])) {
                $map['publish_date'] = $colLetter;
            }
            if (in_array($normalized, ['content', 'content_title', 'title', 'event_title', 'topic'])) {
                $map['content'] = $colLetter;
                $map['content_title'] = $colLetter;
            }
            if (in_array($normalized, ['aipe_pillar', 'a_i_p_e_pillar', 'pillar', 'aipe'])) {
                $map['aipe_pillar'] = $colLetter;
            }
            if (in_array($normalized, ['content_objective', 'objective', 'description'])) {
                $map['content_objective'] = $colLetter;
            }
            if (in_array($normalized, ['shoot_date', 'shooting_date'])) {
                $map['shoot_date'] = $colLetter;
            }
            if (in_array($normalized, ['color_concern', 'color'])) {
                $map['color_concern'] = $colLetter;
            }
            if (in_array($normalized, ['format', 'content_format', 'type'])) {
                $map['format'] = $colLetter;
            }
            if (in_array($normalized, ['budget', 'boosting_budget', 'boost_budget'])) {
                $map['budget'] = $colLetter;
                $map['boosting_budget'] = $colLetter;
            }
            if (in_array($normalized, ['platform', 'channel', 'media'])) {
                $map['platform'] = $colLetter;
            }
            if (in_array($normalized, ['product', 'model', 'bike'])) {
                $map['product'] = $colLetter;
            }
            if (in_array($normalized, ['post_no', 'post_number', 'post_#', 'post'])) {
                $map['post_no'] = $colLetter;
            }
            if (in_array($normalized, ['product_focus', 'focus_product', 'model_focus'])) {
                $map['product_focus'] = $colLetter;
            }
            if (in_array($normalized, ['asset_drive_link', 'drive_link', 'asset_link', 'link', 'drive'])) {
                $map['drive_link'] = $colLetter;
                $map['asset_link'] = $colLetter;
            }
            if (in_array($normalized, ['remarks', 'remark', 'note', 'notes', 'comment'])) {
                $map['remarks'] = $colLetter;
            }
        }

        return $map;
    }

    /**
     * Extract header values from a worksheet.
     */
    private function extractHeaders($sheet): array
    {
        $rows = $sheet->rangeToArray('A1:Z1', null, true, true, true);
        return $rows[1] ?? [];
    }

    /**
     * Check if extracted headers match Product Team format.
     */
    private function isProductHeader(array $headers): bool
    {
        $joined = strtolower(implode(' ', array_filter($headers)));
        return str_contains($joined, 'shoot') || str_contains($joined, 'color') || str_contains($joined, 'platform');
    }

    /**
     * Robustly parse date values from Excel serial numbers or text strings.
     */
    private function parseDateValue($raw, int $defaultYear = 2026): ?Carbon
    {
        if (is_null($raw) || $raw === '' || $raw === 'TBA' || $raw === 'tba') {
            return null;
        }

        // If it's an Excel numeric date
        if (is_numeric($raw) && (float)$raw > 20000 && (float)$raw < 60000) {
            try {
                $dateTime = ExcelDate::excelToDateTimeObject((float)$raw);
                return Carbon::instance($dateTime);
            } catch (\Exception $e) {
                // fallback
            }
        }

        // Clean string (take first line if multiline)
        $str = trim((string)$raw);
        if (str_contains($str, "\n")) {
            $parts = explode("\n", $str);
            $str = trim($parts[0]);
        }
        if (str_contains($str, "and")) {
            $parts = explode("and", $str);
            $str = trim($parts[0]);
        }

        if (empty($str)) {
            return null;
        }

        // Attempt direct Carbon parse
        try {
            // E.g. "1 Sep", "15 Sep", "3 August" -> append default year
            if (preg_match('/^\d{1,2}\s+[A-Za-z]+$/', $str)) {
                $str .= " " . $defaultYear;
            }
            return Carbon::parse($str);
        } catch (\Exception $e) {
            // Try standard formats
            $formats = [
                'Y-m-d',
                'm/d/Y',
                'd/m/Y',
                'm/d/y',
                'd/m/y',
                'd-m-Y',
                'd-m-y',
                'j M Y',
                'j F Y',
                'j M',
                'j F',
            ];

            foreach ($formats as $fmt) {
                try {
                    $d = Carbon::createFromFormat($fmt, $str);
                    if ($d !== false) {
                        if (!str_contains($fmt, 'Y') && !str_contains($fmt, 'y')) {
                            $d->year = $defaultYear;
                        }
                        return $d;
                    }
                } catch (\Exception $e2) {
                    continue;
                }
            }
        }

        return null;
    }
}
