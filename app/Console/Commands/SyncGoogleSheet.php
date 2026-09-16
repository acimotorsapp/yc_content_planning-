<?php

namespace App\Console\Commands;

use App\Services\GoogleSheetsSyncService;
use Illuminate\Console\Command;

class SyncGoogleSheet extends Command
{
    protected $signature = 'sheets:sync {--force : Sync even when the spreadsheet hash has not changed}';

    protected $description = 'Pull every tab from the live YAMAHA Google Sheet and update dashboard content';

    public function handle(GoogleSheetsSyncService $service): int
    {
        $summary = $service->sync((bool) $this->option('force'));

        $this->info($summary['message'] ?? 'Sync finished.');
        $this->line(sprintf(
            'Imported %d, updated %d, duplicates %d, capped %d, skipped %d.',
            $summary['imported'] ?? 0,
            $summary['updated'] ?? 0,
            $summary['duplicates'] ?? 0,
            $summary['capped'] ?? 0,
            $summary['skipped'] ?? 0
        ));

        if (!empty($summary['errors'])) {
            foreach (array_slice($summary['errors'], 0, 15) as $error) {
                $this->warn('  • '.$error);
            }
        }

        return self::SUCCESS;
    }
}
