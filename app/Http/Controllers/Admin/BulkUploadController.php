<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\MasterData;
use App\Support\ContentPlanWorkbookImporter;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
    public function uploadEvents(Request $request, ContentPlanWorkbookImporter $importer)
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

            if ($teamType === 'auto') {
                // Walks every sheet: Product / Digital / Final Content Calendar events,
                // plus the plan-logic and staff sheets when the workbook carries them.
                $summary = $importer->importWorkbook($spreadsheet, $targetYear);
            } else {
                $summary = $importer->importSheetAs($spreadsheet->getActiveSheet(), $teamType, $targetYear);
            }

            $importedCount = $summary['imported'];
            $duplicatesCount = $summary['duplicates'];
            $skippedCount = $summary['skipped'];
            $errors = $summary['errors'];

            $message = "Successfully uploaded and imported {$importedCount} new records.";
            if ($duplicatesCount > 0) {
                $message .= " {$duplicatesCount} duplicate records were skipped (already exist in database).";
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
}
