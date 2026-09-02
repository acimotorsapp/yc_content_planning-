<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\MasterData;
use App\Support\ContentPlanWorkbookImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
                // Collapse stray spacing so " facebook " cannot be stored next to "Facebook".
                $valueRaw = trim(preg_replace('/\s+/', ' ', (string)($row[$valueCol] ?? '')));

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

                // Compare case-insensitively in SQL: relying on the collation would let
                // "facebook" through on SQLite, where string comparison is case-sensitive.
                $exists = MasterData::where('category', $category)
                    ->whereRaw('LOWER(value) = ?', [Str::lower($valueRaw)])
                    ->exists();

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
        $sheet = $spreadsheet->getActiveSheet();

        switch ($type) {
            case 'content-calendar':
                $this->buildContentCalendarSheet($sheet);
                $filename = 'final_content_calendar_sample.' . $format;
                break;

            case 'product-events':
                $this->buildProductSheet($sheet);
                $filename = 'product_team_events_sample.' . $format;
                break;

            case 'digital-events':
                $this->buildDigitalSheet($sheet);
                $filename = 'digital_team_events_sample.' . $format;
                break;

            case 'plan-logic':
                $this->buildPlanLogicSheet($sheet);
                $filename = 'content_plan_logic_sample.' . $format;
                break;

            case 'staff':
                $this->buildStaffSheet($sheet);
                $filename = 'staff_id_designation_sample.' . $format;
                break;

            case 'master-data':
                $this->buildMasterDataSheet($sheet);
                $filename = 'master_data_sample.' . $format;
                break;

            case 'global-events':
                $this->buildGlobalSheet($sheet);
                $filename = 'global_events_sample.' . $format;
                break;

            default:
                // Full workbook — the same five sheets, in the same order, as
                // "YAMAHA Content Plan.xlsx", so a download can be filled in and
                // uploaded straight back using Auto-Detect.
                $this->buildContentCalendarSheet($sheet);
                $this->buildProductSheet($spreadsheet->createSheet());
                $this->buildDigitalSheet($spreadsheet->createSheet());
                $this->buildPlanLogicSheet($spreadsheet->createSheet());
                $this->buildStaffSheet($spreadsheet->createSheet());
                $spreadsheet->setActiveSheetIndex(0);

                $filename = 'yamaha_content_plan_full_template.xlsx';
                $format = 'xlsx';
                break;
        }

        return new StreamedResponse(function () use ($spreadsheet, $format) {
            $writer = $format === 'csv' ? new Csv($spreadsheet) : new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => $format === 'csv' ? 'text/csv' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * "Final Content Calender (<Month> <Year>)" — a title banner, a blank row, then
     * the header on row 3. Imported as digital team events.
     */
    private function buildContentCalendarSheet($sheet): void
    {
        $month = now()->format('F Y');
        $sheet->setTitle(Str::limit('Final Content Calender (' . $month, 31, ''));

        $sheet->fromArray([[$month . ' Content Calendar']], null, 'A1');
        $sheet->fromArray([[
            'Date', 'Day', 'Product / Focus', 'AIPE Pillar', 'Format', 'Content Type',
            'RTM / Campaign Objective', 'Content Gist & Creative Direction', 'Content Link',
            'Budget', 'Platform', 'Boosting Budget',
        ]], null, 'A3');

        $sheet->fromArray([
            ['01-Sep-2026', 'Tue', 'FZS V2 (DD), FZS V4, FZS Hybrid', 'Offer Post', 'Static + Motion', 'Static', 'Month Kick-off', 'Month-opening offer announcement. One clean carousel revealing September price and EMI schemes.', 'https://drive.google.com/file/d/...', '', 'Facebook', '5000'],
            ['03-Sep-2026', 'Thu', 'FZS Hybrid', 'Interest', 'Reel', 'Short-form Video', 'Cyan Metallic', 'FZS FI Hybrid Lifestyle Trend.', '', '', 'Facebook', '3000'],
            ['06-Sep-2026', 'Sun', 'FZS V2 (DD)', 'Purchase', 'Reel', 'Short-form Video', 'Multiple Color', 'Model-specific offer post with price, EMI and exchange scheme plus a showroom-enquiry CTA.', '', '', 'Facebook', '8000'],
        ], null, 'A4');

        $this->styleHeaderRow($sheet, 'A3:L3');
    }

    /**
     * "Product Team" — header on row 1.
     */
    private function buildProductSheet($sheet): void
    {
        $sheet->setTitle('Product Team');

        $sheet->fromArray([[
            'Date', 'Day', 'Content', 'A.I.P.E Pillar', 'Content Objective', 'Shoot Date',
            'Publish Date', 'Color Concern', 'Format', 'Budget', 'Platform', 'Product',
            'Drive Link', 'Remarks', 'Boosting Budget',
        ]], null, 'A1');

        $sheet->fromArray([
            ['August', 'Saturday', 'Life Style Review', 'Interest', 'To showcase an authentic customer experience with Yamaha FZS Version 2', '2026-07-26', '2026-08-08', 'Dark night', 'Product Review', '15000', 'Facebook', 'FZS V2', '', '', '15000'],
            ['August', 'Tuesday', 'Customer Review', 'Interest+Experience', 'Showcase real customer experience and authenticity for future riders', '2026-08-01', '2026-08-25', 'R15 V3 Racing Blue', 'Product Review', '15000', 'Facebook', 'R15', '', '', '15000'],
            ['September', 'Friday', 'FZ 25 Spider Man Content', 'Awareness+Interest', 'Offline activation and UGC content to grab attention', '2026-08-08', '2026-09-04', 'Blue', 'Special Content', '125000', 'YRC Page', 'FZ 25', '', 'Offline activation', '25000'],
        ], null, 'A2');

        $this->styleHeaderRow($sheet, 'A1:O1');
    }

    /**
     * "Digital team" — header on row 1.
     */
    private function buildDigitalSheet($sheet): void
    {
        $sheet->setTitle('Digital team');

        $sheet->fromArray([[
            'Date', 'Day', 'Post No.', 'A.I.P.E Pillar', 'Product Focus', 'Content Objective',
            'Format', 'Asset/Drive Link', 'Remarks', 'Boosting budget',
        ]], null, 'A1');

        $sheet->fromArray([
            ['01-Sep-2026', 'Tue', '1', 'Offer Post', 'Multi-model: FZS V2 DD, FZS V4, FZS Hybrid', 'September offer announcement with confirmed prices and a showroom enquiry CTA.', 'Static', 'https://drive.google.com/...', 'Monthly offer post', '5000'],
            ['03-Sep-2026', 'Thu', '2', 'Purchase Generation', 'FZS V4', 'Offer video presenting the priority range and direct lead generation.', 'Reel', '', 'High priority', '10000'],
            ['05-Sep-2026', 'Sat', '3', 'Interest Generation', 'FZS V2 DD', 'Everyday usability content highlighting ergonomics and dependable braking.', 'Motion', '', '', '3000'],
        ], null, 'A2');

        $this->styleHeaderRow($sheet, 'A1:J1');
    }

    /**
     * "<Month> Logic" — banner on row 1, header on row 2, then the methodology
     * notes as free text below the table.
     */
    private function buildPlanLogicSheet($sheet): void
    {
        $month = now()->format('F Y');
        $sheet->setTitle(Str::limit(now()->format('F') . ' Logic', 31, ''));

        $sheet->fromArray([[$month . ' — Content Logic & Data Backup']], null, 'A1');
        $sheet->fromArray([[
            'Product', 'Units', 'Share', '12-Mo Share Shift', 'Retail',
            'Forecast', 'Posts This Month', 'Pillar Split', 'Why This Allocation',
        ]], null, 'A2');

        $sheet->fromArray([
            ['FZS V2 (DD)', '3,950', '50.8%', '+4.8 pts YoY', '2739', '~4,210', '4', '1 Purchase / 3 Experience', 'Volume engine, still gaining share. Content is pure conversion plus proof.'],
            ['FZS V4', '1,968', '25.3%', '-4.2 pts YoY', '1311', '~2,210', '3', '1 Purchase / 2 Experience', 'Top-two by volume but losing share; keeps a light interest thread.'],
            ['FZS Hybrid', '761', '9.8%', '+9.8 pts YoY (new)', '683', '~800-950', '5', '2 Interest / 1 Purchase / 2 Experience', 'Fastest-growing model; heaviest interest weight to clear the technology barrier.'],
        ], null, 'A3');

        $sheet->fromArray([
            [],
            ['Methodology & data notes'],
            ['Free-text notes placed under the table are stored alongside the allocation rows.'],
            ['Source: Product-wise retail sell-out data, Yamaha Bangladesh Retail Sales Reports.'],
        ], null, 'A6');

        $this->styleHeaderRow($sheet, 'A2:I2');
    }

    /**
     * "Staff ID & Designation" — becomes app users.
     */
    private function buildStaffSheet($sheet): void
    {
        $sheet->setTitle('Staff ID & Designation');

        $sheet->fromArray([['Staff ID', 'Name', 'Designation', 'Email Address']], null, 'A1');
        $sheet->fromArray([
            ['11465', 'Hossain Mohammad Option', 'BM,Yamaha', 'option@aci-bd.com'],
            ['18415', 'Mirajul Alam', 'DGM', 'mirajul@aci-bd.com'],
            ['29842', 'Nabanita Islam', 'APM,Yamaha', 'nabanita@aci-bd.com'],
        ], null, 'A2');

        $this->styleHeaderRow($sheet, 'A1:D1');
    }

    private function buildMasterDataSheet($sheet): void
    {
        $sheet->setTitle('Master Data');

        $sheet->fromArray([['Category', 'Value']], null, 'A1');
        $sheet->fromArray([
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
        ], null, 'A2');

        $this->styleHeaderRow($sheet, 'A1:B1');
    }

    private function buildGlobalSheet($sheet): void
    {
        $sheet->setTitle('Global Events');

        $sheet->fromArray([['Date', 'Event Title', 'Objective / Description']], null, 'A1');
        $sheet->fromArray([
            ['2026-08-30', 'Aragon GP', 'Global MotoGP race event'],
            ['2026-09-05', 'International Day of Charity', 'Global observance and CSR post'],
            ['2026-09-12', 'Saluto UBS Launching', 'Product launch event'],
        ], null, 'A2');

        $this->styleHeaderRow($sheet, 'A1:C1');
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
