<?php

namespace App\Services;

use App\Models\Setting;
use App\Support\ContentPlanWorkbookImporter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class GoogleSheetsSyncService
{
    public function __construct(private ContentPlanWorkbookImporter $importer)
    {
    }

    /**
     * Pull every tab from the live Google Sheet and upsert matching dashboard rows.
     *
     * @return array{synced: bool, hash: string, imported: int, updated: int, duplicates: int, skipped: int, capped: int, errors: array<int, string>, sheets: array}
     */
    public function sync(bool $force = false): array
    {
        $spreadsheetId = (string) config('services.google_sheets.spreadsheet_id');
        $apiKey = (string) config('services.google_sheets.api_key');
        $year = (int) config('services.google_sheets.year', 2026);

        if ($spreadsheetId === '' || $apiKey === '') {
            return $this->emptyResult('Google Sheets ID or API key is not configured.');
        }

        $titles = $this->fetchSheetTitles($spreadsheetId, $apiKey);
        if ($titles === []) {
            return $this->emptyResult('Could not read spreadsheet tabs.');
        }

        $valueRanges = $this->fetchSheetValues($spreadsheetId, $apiKey, $titles);
        $hash = sha1(json_encode($valueRanges));

        if (!$force && Cache::get('sheets_content_hash') === $hash) {
            return [
                'synced' => false,
                'unchanged' => true,
                'hash' => $hash,
                'imported' => 0,
                'updated' => 0,
                'duplicates' => 0,
                'skipped' => 0,
                'capped' => 0,
                'errors' => [],
                'sheets' => [],
                'message' => 'Spreadsheet has not changed since the last sync.',
            ];
        }

        $spreadsheet = $this->toSpreadsheet($valueRanges);
        $summary = $this->importer->importWorkbook($spreadsheet, $year, true);

        Cache::forever('sheets_content_hash', $hash);
        Cache::forever('sheets_synced_at', now()->toDateTimeString());
        Setting::updateOrCreate(['key' => 'sheets_content_hash'], ['value' => $hash]);
        Setting::updateOrCreate(['key' => 'sheets_synced_at'], ['value' => now()->toDateTimeString()]);

        return [
            'synced' => true,
            'unchanged' => false,
            'hash' => $hash,
            'imported' => $summary['imported'],
            'updated' => $summary['updated'] ?? 0,
            'duplicates' => $summary['duplicates'],
            'skipped' => $summary['skipped'],
            'capped' => $summary['capped'],
            'errors' => $summary['errors'],
            'sheets' => $summary['sheets'],
            'message' => 'Spreadsheet synced successfully.',
        ];
    }

    public function contentHash(): ?string
    {
        return Cache::get('sheets_content_hash') ?: Setting::where('key', 'sheets_content_hash')->value('value');
    }

    public function lastSyncedAt(): ?string
    {
        return Cache::get('sheets_synced_at') ?: Setting::where('key', 'sheets_synced_at')->value('value');
    }

    /**
     * @return array<int, string>
     */
    private function fetchSheetTitles(string $spreadsheetId, string $apiKey): array
    {
        $response = Http::timeout(20)->get(
            "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}",
            ['key' => $apiKey, 'fields' => 'sheets.properties.title']
        );

        if (!$response->successful()) {
            Log::warning('Google Sheets metadata request failed', ['status' => $response->status(), 'body' => $response->body()]);

            return [];
        }

        return collect($response->json('sheets') ?? [])
            ->map(fn ($sheet) => $sheet['properties']['title'] ?? null)
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $titles
     * @return array<int, array{range: string, values: array}>
     */
    private function fetchSheetValues(string $spreadsheetId, string $apiKey, array $titles): array
    {
        $query = http_build_query(['key' => $apiKey, 'majorDimension' => 'ROWS']);
        foreach ($titles as $title) {
            $query .= '&ranges=' . rawurlencode($title);
        }

        $response = Http::timeout(40)->get(
            "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values:batchGet?{$query}"
        );

        if (!$response->successful()) {
            Log::warning('Google Sheets values request failed', ['status' => $response->status(), 'body' => $response->body()]);

            return [];
        }

        return $response->json('valueRanges') ?? [];
    }

    /**
     * @param  array<int, array{range?: string, values?: array}>  $valueRanges
     */
    private function toSpreadsheet(array $valueRanges): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $created = 0;

        foreach ($valueRanges as $range) {
            $title = $this->sheetTitleFromRange($range['range'] ?? ('Sheet' . ($created + 1)));
            $values = $range['values'] ?? [];

            $worksheet = $created === 0
                ? $spreadsheet->getActiveSheet()
                : $spreadsheet->createSheet();

            $worksheet->setTitle(mb_substr($title, 0, 31));

            foreach ($values as $rowIndex => $row) {
                foreach ($row as $colIndex => $value) {
                    $coord = Coordinate::stringFromColumnIndex($colIndex + 1) . ($rowIndex + 1);
                    $worksheet->setCellValue($coord, $value);
                }
            }

            $created++;
        }

        return $spreadsheet;
    }

    private function sheetTitleFromRange(string $range): string
    {
        if (preg_match("/^'([^']+)'/", $range, $matches)) {
            return $matches[1];
        }

        $parts = explode('!', $range);

        return $parts[0] !== '' ? $parts[0] : 'Sheet';
    }

    private function emptyResult(string $message): array
    {
        return [
            'synced' => false,
            'unchanged' => false,
            'hash' => '',
            'imported' => 0,
            'updated' => 0,
            'duplicates' => 0,
            'skipped' => 0,
            'capped' => 0,
            'errors' => [$message],
            'sheets' => [],
            'message' => $message,
        ];
    }
}
