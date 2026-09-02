<?php

namespace App\Support;

use App\Models\CalendarEvent;
use App\Models\ContentPlanLogic;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Imports the YAMAHA content plan workbook.
 *
 * Every sheet has a home:
 *   "Product Team"            -> calendar_events (product_team)
 *   "Digital team"            -> calendar_events (digital_team)
 *   "Final Content Calender"  -> calendar_events (digital_team)
 *   "<Month> Logic"           -> content_plan_logics
 *   "Staff ID & Designation"  -> users
 *
 * Imports are additive: rows that already exist are counted as duplicates and skipped,
 * so re-running against the same workbook is safe.
 */
class ContentPlanWorkbookImporter
{
    /** Max events the app allows on a single date. */
    private const MAX_EVENTS_PER_DATE = 6;

    /**
     * Password given to accounts created from the staff sheet, so the team can sign
     * in straight after an import. Change it with `php artisan users:default-password`.
     */
    public const DEFAULT_STAFF_PASSWORD = '123456';

    /**
     * Import every sheet the workbook contains.
     *
     * @return array{sheets: array<int, array>, imported: int, duplicates: int, skipped: int, errors: array<int, string>}
     */
    public function importWorkbook(Spreadsheet $spreadsheet, int $targetYear): array
    {
        $summary = $this->blankSummary();

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $name = $sheet->getTitle();
            $lower = strtolower(trim($name));

            $result = match (true) {
                str_contains($lower, 'staff') => $this->tag($this->importStaffSheet($sheet), 'users'),
                str_contains($lower, 'logic') => $this->tag($this->importLogicSheet($sheet, $name), 'content_plan_logics'),
                str_contains($lower, 'product') => $this->tag($this->importProductSheet($sheet, $targetYear), 'product_team'),
                str_contains($lower, 'digital') => $this->tag($this->importDigitalSheet($sheet, $targetYear), 'digital_team'),
                str_contains($lower, 'calend') => $this->tag($this->importFinalCalendarSheet($sheet, $targetYear), 'digital_team'),
                default => null,
            };

            if ($result === null) {
                $summary['sheets'][] = array_merge($this->tag($this->blankResult(), '—'), [
                    'name' => $name,
                    'errors' => ['Sheet not recognised — nothing imported.'],
                ]);
                continue;
            }

            $result['name'] = $name;
            $summary['sheets'][] = $result;
            $summary['imported'] += $result['imported'];
            $summary['duplicates'] += $result['duplicates'];
            $summary['capped'] += $result['capped'];
            $summary['skipped'] += $result['skipped'];
            $summary['errors'] = array_merge($summary['errors'], array_map(fn ($e) => "[{$name}] {$e}", $result['errors']));
        }

        return $summary;
    }

    /**
     * Import a single sheet as a known team type — used by the bulk upload screen
     * when the operator picks the team explicitly instead of auto-detecting.
     */
    public function importSheetAs($sheet, string $teamType, int $targetYear): array
    {
        return match ($teamType) {
            'product_team' => $this->importProductSheet($sheet, $targetYear),
            'digital_team' => $this->importDigitalSheet($sheet, $targetYear),
            'global_team' => $this->importGlobalSheet($sheet, $targetYear),
            default => $this->blankResult(),
        };
    }

    // ---------------------------------------------------------------- sheets

    /**
     * "Final Content Calender (<Month> <Year>)" — the consolidated monthly calendar.
     *
     * Its header sits a couple of rows below the title banner and it carries two
     * columns the events table has no field for (Content Type, RTM / Campaign
     * Objective), which are preserved in colour concern and remarks.
     */
    public function importFinalCalendarSheet($sheet, int $targetYear): array
    {
        $rows = $sheet->toArray(null, true, true, true);
        $result = $this->blankResult();

        $headerRowIndex = $this->findHeaderRow($rows, ['date']);
        if ($headerRowIndex === null) {
            $result['errors'][] = 'Could not locate a header row.';
            return $result;
        }

        $colMap = $this->mapColumns($rows[$headerRowIndex]);
        $contentTypeCol = $this->findColumn($rows[$headerRowIndex], ['content_type']);
        $rtmCol = $this->findColumn($rows[$headerRowIndex], ['rtm_campaign_objective', 'rtm', 'campaign_objective']);
        $gistCol = $this->findColumn($rows[$headerRowIndex], ['content_gist_creative_direction', 'content_gist', 'gist']);
        $linkCol = $this->findColumn($rows[$headerRowIndex], ['content_link']);
        $productCol = $this->findColumn($rows[$headerRowIndex], ['product_focus', 'product']);

        $user = $this->fallbackUser('digital_team');
        $postNo = 0;

        foreach ($rows as $rowIndex => $row) {
            // Blank rows below the data are just sheet padding — not worth reporting.
            if ($rowIndex <= $headerRowIndex || !$this->rowHasContent($row)) {
                continue;
            }

            $eventDate = $this->parseDateValue($row[$colMap['date'] ?? ''] ?? reset($row), $targetYear);
            if (!$eventDate) {
                // The pillar/post-count summary block under the calendar lands here.
                $result['skipped']++;
                continue;
            }

            $gist = $this->cell($row, $gistCol);
            $rtm = $this->cell($row, $rtmCol);
            $title = $this->cleanTitle($this->headline($gist) ?: ($rtm ?: 'Content Calendar (' . $eventDate->format('d M') . ')'));
            $postNo++;

            if ($this->dateIsFull($eventDate)) {
                $result['capped']++;
                $result['errors'][] = $eventDate->format('d M Y') . ' already holds ' . self::MAX_EVENTS_PER_DATE . ' events — row not stored.';
                continue;
            }

            if ($this->eventExists($eventDate, 'digital_team', $title)) {
                $result['duplicates']++;
                continue;
            }

            $contentType = $this->cell($row, $contentTypeCol);

            CalendarEvent::create([
                'user_id' => $user?->id,
                'team_type' => 'digital_team',
                'event_date' => $eventDate->format('Y-m-d'),
                'content_title' => $title,
                'post_no' => (string) $postNo,
                'aipe_pillar' => $this->cell($row, $colMap['aipe_pillar'] ?? null),
                'product_focus' => $this->cell($row, $productCol),
                'content_objective' => $gist,
                'format' => $this->cell($row, $colMap['format'] ?? null),
                'color_concern' => $rtm,
                'platform' => $this->cell($row, $colMap['platform'] ?? null),
                'drive_link' => $this->cell($row, $linkCol),
                'boosting_budget' => $this->cell($row, $colMap['boosting_budget'] ?? null) ?: $this->cell($row, $colMap['budget'] ?? null),
                'remarks' => $contentType ? "Content Type: {$contentType}" : null,
            ]);

            $result['imported']++;
        }

        return $result;
    }

    /**
     * "<Month> Logic" — per-product post allocation plus the methodology notes.
     */
    public function importLogicSheet($sheet, string $sheetName): array
    {
        $rows = $sheet->toArray(null, true, true, true);
        $result = $this->blankResult();

        $period = $this->periodFromTitle($rows, $sheetName);
        $headerRowIndex = $this->findHeaderRow($rows, ['product']);

        if ($headerRowIndex === null) {
            $result['errors'][] = 'Could not locate a header row.';
            return $result;
        }

        $header = $rows[$headerRowIndex];
        $cols = [
            'product' => $this->findColumn($header, ['product']),
            'units' => $this->findColumn($header, ['units']),
            'share' => $this->findColumn($header, ['share']),
            'share_shift' => $this->findColumn($header, ['12_mo_share_shift', 'share_shift']),
            'previous_retail' => $this->findColumn($header, ['retail']),
            'forecast' => $this->findColumn($header, ['forecast']),
            'posts_planned' => $this->findColumn($header, ['posts_this_month', 'posts']),
            'pillar_split' => $this->findColumn($header, ['pillar_split']),
            'rationale' => $this->findColumn($header, ['why_this_allocation', 'why']),
        ];

        $order = 0;
        $noteOrder = 0;

        foreach ($rows as $rowIndex => $row) {
            // Blank rows below the data are just sheet padding — not worth reporting.
            if ($rowIndex <= $headerRowIndex || !$this->rowHasContent($row)) {
                continue;
            }

            $product = $this->cell($row, $cols['product']);
            $forecast = $this->cell($row, $cols['forecast']);

            // Below the table the sheet turns into prose: a heading, bullet notes and a
            // source line, all of which live in the first column only.
            $isAllocation = $product !== '' && $forecast !== '';

            if ($isAllocation) {
                $attributes = [
                    'period' => $period,
                    'row_type' => 'allocation',
                    'sort_order' => $order++,
                ];

                $created = ContentPlanLogic::updateOrCreate($attributes, [
                    'product' => $product,
                    'units' => $this->cell($row, $cols['units']),
                    'share' => $this->cell($row, $cols['share']),
                    'share_shift' => $this->cell($row, $cols['share_shift']),
                    'previous_retail' => $this->cell($row, $cols['previous_retail']),
                    'forecast' => $forecast,
                    'posts_planned' => (int) $this->cell($row, $cols['posts_planned']) ?: null,
                    'pillar_split' => $this->cell($row, $cols['pillar_split']),
                    'rationale' => $this->cell($row, $cols['rationale']),
                ]);

                $created->wasRecentlyCreated ? $result['imported']++ : $result['duplicates']++;
                continue;
            }

            $body = $this->firstNonEmptyCell($row);
            if ($body === '') {
                $result['skipped']++;
                continue;
            }

            $rowType = Str::startsWith(strtolower($body), 'source') ? 'source' : 'note';

            $created = ContentPlanLogic::updateOrCreate(
                ['period' => $period, 'row_type' => $rowType, 'sort_order' => $noteOrder++],
                ['rationale' => $body]
            );

            $created->wasRecentlyCreated ? $result['imported']++ : $result['duplicates']++;
        }

        return $result;
    }

    /**
     * "Staff ID & Designation" — becomes app users so the team can sign in.
     *
     * Accounts are created with an unusable random password: an admin sets one, or
     * the person uses the password-reset flow. The sheet's header labels the first
     * two columns the wrong way round, so they are detected by shape instead.
     */
    public function importStaffSheet($sheet): array
    {
        $rows = $sheet->toArray(null, true, true, true);
        $result = $this->blankResult();

        $headerRowIndex = $this->findHeaderRow($rows, ['staff_id', 'name']);
        if ($headerRowIndex === null) {
            $result['errors'][] = 'Could not locate a header row.';
            return $result;
        }

        $header = $rows[$headerRowIndex];
        $nameCol = $this->findColumn($header, ['name']);
        $staffIdCol = $this->findColumn($header, ['staff_id', 'id']);
        $designationCol = $this->findColumn($header, ['designation', 'title']);
        $emailCol = $this->findColumn($header, ['email_address', 'email']);

        foreach ($rows as $rowIndex => $row) {
            // Blank rows below the data are just sheet padding — not worth reporting.
            if ($rowIndex <= $headerRowIndex || !$this->rowHasContent($row)) {
                continue;
            }

            $name = $this->cell($row, $nameCol);
            $staffId = $this->cell($row, $staffIdCol);

            // The sheet labels column A "Name" but stores the staff number there.
            if (is_numeric(str_replace(',', '', $name)) && !is_numeric(str_replace(',', '', $staffId))) {
                [$name, $staffId] = [$staffId, $name];
            }

            $email = strtolower($this->cell($row, $emailCol));

            if ($email === '' || $name === '') {
                $result['skipped']++;
                $result['errors'][] = "Row {$rowIndex}: missing name or email address.";
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result['errors'][] = "Row {$rowIndex}: '{$email}' is not a valid email address — imported as written, password reset will not reach it.";
            }

            if (User::where('email', $email)->exists()) {
                $result['duplicates']++;
                continue;
            }

            $user = new User([
                'name' => $name,
                'email' => $email,
                'staff_id' => $staffId ?: null,
                'designation' => $this->cell($row, $designationCol) ?: null,
                'role' => 'product_team',
                'password' => Hash::make(self::DEFAULT_STAFF_PASSWORD),
            ]);

            // Not mass assignable, and without it the "verified" middleware
            // would block these accounts from ever reaching the dashboard.
            $user->email_verified_at = now();
            $user->save();

            $result['imported']++;
        }

        return $result;
    }

    /**
     * "Product Team" sheet.
     */
    public function importProductSheet($sheet, int $targetYear): array
    {
        $rows = $sheet->toArray(null, true, true, true);
        $result = $this->blankResult();

        if (empty($rows)) {
            return $result;
        }

        $headerRow = array_shift($rows);
        $colMap = $this->mapColumns($headerRow);
        $user = $this->fallbackUser('product_team');

        foreach ($rows as $rowIndex => $row) {
            // Blank rows are just sheet padding — not worth reporting.
            if (!$this->rowHasContent($row)) {
                continue;
            }

            $contentTitle = $this->cleanTitle($this->cell($row, $colMap['content'] ?? $colMap['content_title'] ?? null));
            $objective = $this->cell($row, $colMap['content_objective'] ?? null);
            $product = $this->cell($row, $colMap['product'] ?? null);

            $dateRaw = $row[$colMap['publish_date'] ?? ''] ?? $row[$colMap['date'] ?? ''] ?? null;
            $eventDate = $this->parseDateValue($dateRaw, $targetYear) ?: $this->parseDateValue(reset($row), $targetYear);

            if (!$eventDate) {
                $result['skipped']++;
                if ($contentTitle !== '' || $objective !== '' || $product !== '') {
                    $result['errors'][] = "Row {$rowIndex}: Missing or invalid event date.";
                }
                continue;
            }

            if ($contentTitle === '') {
                $contentTitle = $this->cleanTitle($product ? "Product: {$product}" : 'Product Content (' . $eventDate->format('d M') . ')');
            }

            if ($this->dateIsFull($eventDate)) {
                $result['capped']++;
                $result['errors'][] = $eventDate->format('d M Y') . ' already holds ' . self::MAX_EVENTS_PER_DATE . ' events — row not stored.';
                continue;
            }

            if ($this->eventExists($eventDate, 'product_team', $contentTitle)) {
                $result['duplicates']++;
                continue;
            }

            $shootDate = $this->parseDateValue($row[$colMap['shoot_date'] ?? ''] ?? null, $targetYear);

            CalendarEvent::create([
                'user_id' => $user?->id,
                'team_type' => 'product_team',
                'event_date' => $eventDate->format('Y-m-d'),
                'content_title' => $contentTitle,
                'aipe_pillar' => $this->cell($row, $colMap['aipe_pillar'] ?? null),
                'content_objective' => $objective,
                'shoot_date' => $shootDate?->format('Y-m-d'),
                'color_concern' => $this->cell($row, $colMap['color_concern'] ?? null),
                'format' => $this->cell($row, $colMap['format'] ?? null),
                'boosting_budget' => $this->cell($row, $colMap['budget'] ?? $colMap['boosting_budget'] ?? null),
                'platform' => $this->cell($row, $colMap['platform'] ?? null),
                'product' => $product,
                'drive_link' => $this->cell($row, $colMap['drive_link'] ?? $colMap['asset_link'] ?? null),
                'remarks' => $this->cell($row, $colMap['remarks'] ?? null),
            ]);

            $result['imported']++;
        }

        return $result;
    }

    /**
     * "Digital team" sheet.
     */
    public function importDigitalSheet($sheet, int $targetYear): array
    {
        $rows = $sheet->toArray(null, true, true, true);
        $result = $this->blankResult();

        if (empty($rows)) {
            return $result;
        }

        $headerRow = array_shift($rows);
        $colMap = $this->mapColumns($headerRow);
        $user = $this->fallbackUser('digital_team');

        foreach ($rows as $rowIndex => $row) {
            // Blank rows are just sheet padding — not worth reporting.
            if (!$this->rowHasContent($row)) {
                continue;
            }

            $firstVal = reset($row);
            $firstValLower = strtolower(trim((string) $firstVal));

            // Footer summary / planning-dependency blocks are prose, not rows.
            foreach (['planning depend', 'total post', 'core objective', 'summary', 'note:'] as $marker) {
                if (str_contains($firstValLower, $marker)) {
                    $result['skipped']++;
                    continue 2;
                }
            }

            $eventDate = $this->parseDateValue($row[$colMap['date'] ?? ''] ?? null, $targetYear)
                ?: $this->parseDateValue($firstVal, $targetYear);

            $objective = $this->cell($row, $colMap['content_objective'] ?? null);
            $productFocus = $this->cell($row, $colMap['product_focus'] ?? $colMap['product'] ?? null);
            $postNo = $this->cell($row, $colMap['post_no'] ?? null);

            if (!$eventDate) {
                $result['skipped']++;
                if ($objective !== '' || $productFocus !== '' || $postNo !== '') {
                    $result['errors'][] = "Row {$rowIndex}: Missing valid event date.";
                }
                continue;
            }

            $contentTitle = $this->cleanTitle($productFocus !== ''
                ? "Post #{$postNo}: {$productFocus}"
                : ($postNo !== '' ? "Post #{$postNo}" : 'Digital Content'));

            if ($this->dateIsFull($eventDate)) {
                $result['capped']++;
                $result['errors'][] = $eventDate->format('d M Y') . ' already holds ' . self::MAX_EVENTS_PER_DATE . ' events — row not stored.';
                continue;
            }

            if ($this->eventExists($eventDate, 'digital_team', $contentTitle)) {
                $result['duplicates']++;
                continue;
            }

            CalendarEvent::create([
                'user_id' => $user?->id,
                'team_type' => 'digital_team',
                'event_date' => $eventDate->format('Y-m-d'),
                'content_title' => $contentTitle,
                'post_no' => $postNo ?: null,
                'aipe_pillar' => $this->cell($row, $colMap['aipe_pillar'] ?? null),
                'product_focus' => $productFocus ?: null,
                'content_objective' => $objective ?: null,
                'format' => $this->cell($row, $colMap['format'] ?? null),
                'drive_link' => $this->cell($row, $colMap['drive_link'] ?? $colMap['asset_link'] ?? null),
                'remarks' => $this->cell($row, $colMap['remarks'] ?? null),
                'boosting_budget' => $this->cell($row, $colMap['budget'] ?? $colMap['boosting_budget'] ?? null),
            ]);

            $result['imported']++;
        }

        return $result;
    }

    /**
     * Global observances sheet.
     */
    public function importGlobalSheet($sheet, int $targetYear): array
    {
        $rows = $sheet->toArray(null, true, true, true);
        $result = $this->blankResult();

        if (empty($rows)) {
            return $result;
        }

        $headerRow = array_shift($rows);
        $colMap = $this->mapColumns($headerRow);
        $user = $this->fallbackUser('super_admin');

        foreach ($rows as $row) {
            // Blank rows are just sheet padding — not worth reporting.
            if (!$this->rowHasContent($row)) {
                continue;
            }

            $eventDate = $this->parseDateValue($row[$colMap['date'] ?? ''] ?? null, $targetYear);
            $title = $this->cleanTitle($this->cell($row, $colMap['content_title'] ?? $colMap['content'] ?? null));
            $objective = $this->cell($row, $colMap['content_objective'] ?? null);

            if (!$eventDate || $title === '') {
                $result['skipped']++;
                continue;
            }

            if ($this->dateIsFull($eventDate)) {
                $result['capped']++;
                $result['errors'][] = $eventDate->format('d M Y') . ' already holds ' . self::MAX_EVENTS_PER_DATE . ' events — row not stored.';
                continue;
            }

            if ($this->eventExists($eventDate, 'global_team', $title)) {
                $result['duplicates']++;
                continue;
            }

            CalendarEvent::create([
                'user_id' => $user?->id,
                'team_type' => 'global_team',
                'event_date' => $eventDate->format('Y-m-d'),
                'content_title' => $title,
                'content_objective' => $objective ?: 'Global observance',
            ]);

            $result['imported']++;
        }

        return $result;
    }

    // ---------------------------------------------------------------- helpers

    private function blankResult(): array
    {
        return ['imported' => 0, 'duplicates' => 0, 'capped' => 0, 'skipped' => 0, 'errors' => []];
    }

    private function blankSummary(): array
    {
        return ['sheets' => [], 'imported' => 0, 'duplicates' => 0, 'capped' => 0, 'skipped' => 0, 'errors' => []];
    }

    private function tag(array $result, string $target): array
    {
        $result['target'] = $target;
        return $result;
    }

    /**
     * event_date is a datetime column, so compare on the date part only — a plain
     * equality check against "Y-m-d" silently matches nothing on SQLite.
     */
    private function dateIsFull(Carbon $date): bool
    {
        return CalendarEvent::whereDate('event_date', $date->format('Y-m-d'))->count() >= self::MAX_EVENTS_PER_DATE;
    }

    /**
     * Compare titles case-insensitively so "Life Style Review" and "life style
     * review" cannot both be stored. SQLite compares strings case-sensitively, so
     * the lowering has to happen in SQL rather than relying on the collation.
     */
    private function eventExists(Carbon $date, string $teamType, string $title): bool
    {
        return CalendarEvent::whereDate('event_date', $date->format('Y-m-d'))
            ->where('team_type', $teamType)
            ->whereRaw('LOWER(content_title) = ?', [Str::lower($this->cleanTitle($title))])
            ->exists();
    }

    /**
     * Trim and collapse runs of whitespace, so stray spacing in the sheet neither
     * reaches the database nor hides a duplicate.
     */
    private function cleanTitle(string $title): string
    {
        return trim(preg_replace('/\s+/', ' ', $title));
    }

    private function fallbackUser(string $preferredRole): ?User
    {
        return User::where('role', $preferredRole)->first()
            ?? (auth()->user() ?: User::where('role', 'super_admin')->first())
            ?? User::first();
    }

    private function rowHasContent(?array $row): bool
    {
        foreach ($row ?? [] as $cell) {
            if (!is_null($cell) && trim((string) $cell) !== '') {
                return true;
            }
        }

        return false;
    }

    private function cell(array $row, ?string $column): string
    {
        if ($column === null || !array_key_exists($column, $row)) {
            return '';
        }

        return trim((string) ($row[$column] ?? ''));
    }

    private function firstNonEmptyCell(array $row): string
    {
        foreach ($row as $cell) {
            $value = trim((string) ($cell ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    /**
     * Use the gist's opening sentence as the headline when it is short enough to read
     * as one, otherwise a trimmed version of it.
     */
    private function headline(string $gist): string
    {
        $gist = trim(preg_replace('/\s+/', ' ', $gist));
        if ($gist === '') {
            return '';
        }

        $firstSentence = trim(strtok($gist, '.'));
        if ($firstSentence !== '' && mb_strlen($firstSentence) <= 100) {
            return $firstSentence;
        }

        return Str::limit($gist, 100);
    }

    /**
     * Find the row that carries the column headers — sheets in this workbook start
     * with a title banner, so the header is not always the first row.
     */
    private function findHeaderRow(array $rows, array $expected): ?int
    {
        foreach ($rows as $index => $row) {
            foreach ($row as $cell) {
                if (in_array($this->normalize((string) ($cell ?? '')), $expected, true)) {
                    return $index;
                }
            }
        }

        return null;
    }

    /**
     * Locate a column letter by matching its header against a list of aliases,
     * accepting a header that merely starts with one (e.g. "Jul'26 Units" -> "units").
     */
    private function findColumn(array $headerRow, array $aliases): ?string
    {
        foreach ($headerRow as $letter => $value) {
            $normalized = $this->normalize((string) ($value ?? ''));
            if ($normalized === '') {
                continue;
            }

            foreach ($aliases as $alias) {
                if ($normalized === $alias || str_contains($normalized, $alias)) {
                    return $letter;
                }
            }
        }

        return null;
    }

    private function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace(['.', '-', '/', ' ', "'", '&', '(', ')'], '_', $value);
        $value = preg_replace('/_+/', '_', $value);

        return trim($value, '_');
    }

    /**
     * Read "September 2026" out of the sheet's banner row, falling back to its name.
     */
    private function periodFromTitle(array $rows, string $sheetName): string
    {
        $banner = $this->firstNonEmptyCell($rows[1] ?? []);
        if (preg_match('/([A-Z][a-z]+)\s+(\d{4})/', $banner, $m)) {
            return "{$m[1]} {$m[2]}";
        }

        if (preg_match('/([A-Z][a-z]+)\s+(\d{4})/', $sheetName, $m)) {
            return "{$m[1]} {$m[2]}";
        }

        return trim(str_ireplace('logic', '', $sheetName)) ?: $sheetName;
    }

    /**
     * Map spreadsheet header columns to standard keys.
     */
    public function mapColumns(array $headerRow): array
    {
        $map = [];

        foreach ($headerRow as $colLetter => $headerName) {
            if (is_null($headerName)) {
                continue;
            }

            $normalized = $this->normalize((string) $headerName);

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
            if (in_array($normalized, ['budget'])) {
                $map['budget'] = $colLetter;
            }
            if (in_array($normalized, ['boosting_budget', 'boost_budget'])) {
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

    public function extractHeaders($sheet): array
    {
        $rows = $sheet->rangeToArray('A1:Z1', null, true, true, true);

        return $rows[1] ?? [];
    }

    public function isProductHeader(array $headers): bool
    {
        $joined = strtolower(implode('|', array_map(fn ($h) => (string) $h, $headers)));

        return str_contains($joined, 'shoot date') || str_contains($joined, 'color concern');
    }

    /**
     * Parse a cell that may hold an Excel serial, a formatted date string, or a
     * multi-date note like "8/1/26 8/4/26".
     */
    public function parseDateValue($raw, int $defaultYear = 2026): ?Carbon
    {
        if (is_null($raw) || $raw === '' || strtolower(trim((string) $raw)) === 'tba') {
            return null;
        }

        if (is_numeric($raw) && (float) $raw > 20000 && (float) $raw < 60000) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $raw));
            } catch (\Exception $e) {
                // fall through to string parsing
            }
        }

        $str = trim((string) $raw);

        if (str_contains($str, "\n")) {
            $str = trim(explode("\n", $str)[0]);
        }
        if (str_contains($str, 'and')) {
            $str = trim(explode('and', $str)[0]);
        }

        if ($str === '') {
            return null;
        }

        try {
            if (preg_match('/^\d{1,2}\s+[A-Za-z]+$/', $str)) {
                $str .= ' ' . $defaultYear;
            }

            return Carbon::parse($str);
        } catch (\Exception $e) {
            foreach (['Y-m-d', 'm/d/Y', 'd/m/Y', 'm/d/y', 'd/m/y', 'd-m-Y', 'd-m-y', 'j M Y', 'j F Y', 'j M', 'j F'] as $fmt) {
                try {
                    $date = Carbon::createFromFormat($fmt, $str);
                    if ($date !== false) {
                        if (!str_contains($fmt, 'Y') && !str_contains($fmt, 'y')) {
                            $date->year = $defaultYear;
                        }

                        return $date;
                    }
                } catch (\Exception $inner) {
                    continue;
                }
            }
        }

        return null;
    }
}
