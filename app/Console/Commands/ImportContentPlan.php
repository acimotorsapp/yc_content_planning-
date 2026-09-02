<?php

namespace App\Console\Commands;

use App\Support\ContentPlanWorkbookImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportContentPlan extends Command
{
    protected $signature = 'content-plan:import
                            {file : Path to the content plan .xlsx/.xls/.csv workbook}
                            {--year=2026 : Year to assume for date cells that omit one}
                            {--dry-run : Read and report without writing anything}';

    protected $description = 'Import every sheet of the YAMAHA content plan workbook (events, plan logic and staff)';

    public function handle(ContentPlanWorkbookImporter $importer): int
    {
        $file = $this->argument('file');

        if (!is_file($file)) {
            $this->error("File not found: {$file}");

            return self::FAILURE;
        }

        $this->info("Reading {$file} …");

        $reader = IOFactory::createReaderForFile($file);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);

        $year = (int) $this->option('year');
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Dry run — rolling back everything at the end.');
            DB::beginTransaction();
        }

        $summary = $importer->importWorkbook($spreadsheet, $year);

        if ($dryRun) {
            DB::rollBack();
        }

        $this->newLine();
        $this->table(
            ['Sheet', 'Stored in', 'Imported', 'Duplicates', 'Date full', 'Unusable'],
            collect($summary['sheets'])->map(fn ($s) => [
                $s['name'],
                $s['target'] ?? '—',
                $s['imported'],
                $s['duplicates'],
                $s['capped'],
                $s['skipped'],
            ])->all()
        );

        $this->line(sprintf(
            '  Total: <info>%d imported</info>, %d duplicates, %d blocked by the 6-per-date cap, %d unusable rows.',
            $summary['imported'],
            $summary['duplicates'],
            $summary['capped'],
            $summary['skipped']
        ));

        if (!empty($summary['errors'])) {
            $this->newLine();
            $this->warn('Notes (' . count($summary['errors']) . '):');
            foreach (array_slice($summary['errors'], 0, 25) as $error) {
                $this->line('  • ' . $error);
            }
            if (count($summary['errors']) > 25) {
                $this->line('  … ' . (count($summary['errors']) - 25) . ' more');
            }
        }

        return self::SUCCESS;
    }
}
