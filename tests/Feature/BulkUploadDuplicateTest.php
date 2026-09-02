<?php

namespace Tests\Feature;

use App\Models\CalendarEvent;
use App\Models\ContentPlanLogic;
use App\Models\MasterData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

/**
 * The bulk upload screen must never create the same record twice, however many
 * times a file is uploaded.
 */
class BulkUploadDuplicateTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'super_admin', 'email_verified_at' => now()]);
    }

    private function toUpload(Spreadsheet $spreadsheet, string $name = 'plan.xlsx'): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'up') . '.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        return new UploadedFile($path, $name, null, null, true);
    }

    private function uploadEvents(User $admin, Spreadsheet $spreadsheet, string $teamType = 'auto')
    {
        return $this->actingAs($admin)->post(route('admin.bulk_upload.events'), [
            'file' => $this->toUpload($spreadsheet),
            'team_type' => $teamType,
            'target_year' => 2026,
        ]);
    }

    private function planWorkbook(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        $product = $spreadsheet->getActiveSheet();
        $product->setTitle('Product Team');
        $product->fromArray([
            ['Date', 'Day', 'Content', 'A.I.P.E Pillar', 'Content Objective', 'Shoot Date', 'Publish Date', 'Color Concern', 'Format', 'Budget', 'Platform', 'Product'],
            ['August', 'Sat', 'Life Style Review', 'Interest', 'Authentic customer experience', '2026-08-10', '2026-08-20', 'Dark night', 'Product Review', '15000', 'Facebook', 'FZS V2'],
            ['August', 'Tue', 'Customer Review', 'Experience', 'Real customer story', '2026-08-12', '2026-08-22', 'Blue', 'Product Review', '15000', 'Facebook', 'R15'],
        ], null, 'A1');

        $digital = $spreadsheet->createSheet();
        $digital->setTitle('Digital team');
        $digital->fromArray([
            ['Date', 'Day', 'Post No.', 'A.I.P.E Pillar', 'Product Focus', 'Content Objective', 'Format', 'Asset/Drive Link', 'Remarks', 'Boosting budget'],
            ['2026-09-01', 'Tue', '1', 'Offer Post', 'FZS V4', 'September offer announcement', 'Static', '', '', '2000'],
        ], null, 'A1');

        $staff = $spreadsheet->createSheet();
        $staff->setTitle('Staff ID & Designation');
        $staff->fromArray([
            ['Staff ID', 'Name', 'Designation', 'Email Address'],
            ['11465', 'Hossain Mohammad Option', 'BM,Yamaha', 'option@aci-bd.com'],
        ], null, 'A1');

        $logic = $spreadsheet->createSheet();
        $logic->setTitle('September Logic');
        $logic->fromArray([
            ['September 2026 — Content Logic'],
            ['Product', 'Units', 'Share', '12-Mo Share Shift', 'Retail', 'Forecast', 'Posts This Month', 'Pillar Split', 'Why This Allocation'],
            ['FZS V2 (DD)', '3,950', '50.8%', '+4.8 pts YoY', '2739', '~4,210', '4', '1 Purchase / 3 Experience', 'Volume engine.'],
        ], null, 'A1');

        return $spreadsheet;
    }

    public function test_uploading_the_same_workbook_twice_creates_nothing_the_second_time(): void
    {
        $admin = $this->admin();

        $this->uploadEvents($admin, $this->planWorkbook())->assertRedirect();

        $events = CalendarEvent::count();
        $users = User::count();
        $logics = ContentPlanLogic::count();

        $this->assertGreaterThan(0, $events, 'The first upload should store events');

        $second = $this->uploadEvents($admin, $this->planWorkbook());
        $second->assertRedirect();
        $second->assertSessionHas('success');

        $this->assertSame($events, CalendarEvent::count(), 'A second upload must not add events');
        $this->assertSame($users, User::count(), 'A second upload must not add users');
        $this->assertSame($logics, ContentPlanLogic::count(), 'A second upload must not add plan logic rows');
    }

    public function test_uploading_five_times_still_leaves_one_copy(): void
    {
        $admin = $this->admin();

        for ($i = 0; $i < 5; $i++) {
            $this->uploadEvents($admin, $this->planWorkbook())->assertRedirect();
        }

        $this->assertSame(1, CalendarEvent::where('content_title', 'Life Style Review')->count());
        $this->assertSame(1, CalendarEvent::where('content_title', 'Customer Review')->count());
        $this->assertSame(1, CalendarEvent::where('content_title', 'Post #1: FZS V4')->count());
        $this->assertSame(1, User::where('email', 'option@aci-bd.com')->count());
        $this->assertSame(1, ContentPlanLogic::where('product', 'FZS V2 (DD)')->count());
    }

    public function test_a_row_repeated_inside_one_file_is_stored_once(): void
    {
        $admin = $this->admin();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Product Team');
        $sheet->fromArray([
            ['Date', 'Day', 'Content', 'A.I.P.E Pillar', 'Content Objective', 'Shoot Date', 'Publish Date', 'Color Concern', 'Format', 'Budget', 'Platform', 'Product'],
            ['August', 'Sat', 'Life Style Review', 'Interest', 'First copy', '', '2026-08-20', '', 'Product Review', '', 'Facebook', 'FZS V2'],
            ['August', 'Sat', 'Life Style Review', 'Interest', 'Second copy', '', '2026-08-20', '', 'Product Review', '', 'Facebook', 'FZS V2'],
        ], null, 'A1');

        $this->uploadEvents($admin, $spreadsheet)->assertRedirect();

        $this->assertSame(1, CalendarEvent::where('content_title', 'Life Style Review')->count());
    }

    public function test_titles_differing_only_by_case_or_spacing_are_treated_as_the_same_event(): void
    {
        $admin = $this->admin();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Product Team');
        $sheet->fromArray([
            ['Date', 'Day', 'Content', 'A.I.P.E Pillar', 'Content Objective', 'Shoot Date', 'Publish Date', 'Color Concern', 'Format', 'Budget', 'Platform', 'Product'],
            ['August', 'Sat', 'Life Style Review', 'Interest', 'Original', '', '2026-08-20', '', 'Product Review', '', 'Facebook', 'FZS V2'],
            ['August', 'Sat', 'life style review', 'Interest', 'Lower case', '', '2026-08-20', '', 'Product Review', '', 'Facebook', 'FZS V2'],
            ['August', 'Sat', '  Life  Style   Review  ', 'Interest', 'Extra spaces', '', '2026-08-20', '', 'Product Review', '', 'Facebook', 'FZS V2'],
        ], null, 'A1');

        $this->uploadEvents($admin, $spreadsheet)->assertRedirect();

        $this->assertSame(
            1,
            CalendarEvent::whereDate('event_date', '2026-08-20')->count(),
            'Case and spacing differences should not slip past the duplicate check'
        );
    }

    public function test_master_data_upload_does_not_duplicate(): void
    {
        $admin = $this->admin();

        $build = function () {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getActiveSheet()->fromArray([
                ['Category', 'Value'],
                ['platform', 'Facebook'],
                ['platform', 'Facebook'],
                ['platform', ' facebook '],
                ['format', 'Reels'],
            ], null, 'A1');

            return $spreadsheet;
        };

        $this->actingAs($admin)->post(route('admin.bulk_upload.master_data'), [
            'file' => $this->toUpload($build(), 'master.xlsx'),
        ])->assertRedirect();

        $this->assertSame(1, MasterData::where('category', 'platform')->count(), 'Repeats inside one file, including case/spacing variants, should collapse');

        $this->actingAs($admin)->post(route('admin.bulk_upload.master_data'), [
            'file' => $this->toUpload($build(), 'master.xlsx'),
        ])->assertRedirect();

        $this->assertSame(2, MasterData::count(), 'A second upload must not add anything');
    }

    public function test_the_upload_reports_how_many_duplicates_were_skipped(): void
    {
        $admin = $this->admin();

        $this->uploadEvents($admin, $this->planWorkbook());
        $second = $this->uploadEvents($admin, $this->planWorkbook());

        $message = session('success');
        $this->assertStringContainsString('0 new records', $message);
        $this->assertStringContainsString('duplicate', $message);
    }
}
