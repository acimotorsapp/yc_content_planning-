<?php

namespace Tests\Feature;

use App\Models\CalendarEvent;
use App\Models\ContentPlanLogic;
use App\Models\User;
use App\Support\ContentPlanWorkbookImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Tests\TestCase;

class ContentPlanImportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A miniature stand-in for the YAMAHA workbook: one sheet of each kind, with the
     * same quirks as the real file (a title banner above the header, Excel serial
     * dates, and a staff sheet whose first two columns are labelled the wrong way).
     */
    private function workbook(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        $calendar = $spreadsheet->getActiveSheet();
        // Excel truncates sheet names at 31 characters, exactly as the real workbook does.
        $calendar->setTitle('Final Content Calender (Septemb');
        $calendar->fromArray([
            ['September 2026 Content Calendar'],
            [],
            ['Date', 'Day', 'Product / Focus', 'AIPE Pillar', 'Format', 'Content Type', 'RTM / Campaign Objective', 'Content Gist & Creative Direction', 'Content Link', 'Budget', 'Platform', 'Boosting Budget'],
            ['01-Sep-2026', 'Tue', 'FZS V2', 'Offer Post', 'Static', 'Static', 'Month Kick-off', 'Month-opening offer announcement. Carousel revealing September pricing.', 'https://drive.example/a', '', 'Facebook', '5000'],
            [46270, 'Sat', 'FZ25', 'Experience', 'Reel', 'Short-form Video', 'Racing Blue', 'FZ 25 Lifestyle OVC.', '', '', 'Facebook', ''],
            [],
            ['', '', 'Pillar', 'Post Count'],
            ['', '', 'Interest', 10],
        ], null, 'A1');

        $product = $spreadsheet->createSheet();
        $product->setTitle('Product Team');
        $product->fromArray([
            ['Date', 'Day', 'Content', 'A.I.P.E Pillar', 'Content Objective', 'Shoot Date', 'Publish Date', 'Color Concern', 'Format', 'Budget', 'Platform', 'Product', 'Drive Link', 'Remarks'],
            ['August', '', 'Life Style Review', 'Interest', 'Showcase an authentic experience', '2026-08-10', '2026-08-20', 'Dark night', 'Product Review', '15000', 'Facebook', 'FZS V2', '', ''],
        ], null, 'A1');

        $digital = $spreadsheet->createSheet();
        $digital->setTitle('Digital team');
        $digital->fromArray([
            ['Date', 'Day', 'Post No.', 'A.I.P.E Pillar', 'Product Focus', 'Content Objective', 'Format', 'Asset/Drive Link', 'Remarks', 'Boosting budget'],
            [46266, 'Tue', '1', 'Offer Post', 'FZS V4', 'September offer announcement', 'Static', '', '', '2000'],
            ['Planning Dependencies', '', '', 'Core Objective', '', '', '', '', '', ''],
        ], null, 'A1');

        $logic = $spreadsheet->createSheet();
        $logic->setTitle('September Logic');
        $logic->fromArray([
            ['September 2026 — Content Logic & Data Backup'],
            ['Product', "Jul'26 Units", "Jul'26 Share", '12-Mo Share Shift', "Aug'26 Retail", "Sep'26 Forecast", 'Posts This Month', 'Pillar Split', 'Why This Allocation'],
            ['FZS V2 (DD)', '3,950', '50.8%', '+4.8 pts YoY', '2739', '~4,210', '4', '1 Purchase / 3 Experience', 'Volume engine, still gaining share.'],
            [],
            ['Methodology & data notes'],
            ['• September is the seasonal purchase peak for nearly every model.'],
            ['Source: Yamaha Bangladesh Retail Sales Reports.'],
        ], null, 'A1');

        $staff = $spreadsheet->createSheet();
        $staff->setTitle('Staff ID & Designation');
        $staff->fromArray([
            ['Name', 'Staff ID', 'Designation', 'Email Address'],
            ['11465', 'Hossain Mohammad Option', 'BM,Yamaha', 'option@aci-bd.com'],
            ['24784', 'Sudipta Adhikary', 'APM,Yamaha', 'adhikary@aci-bdcom'],
        ], null, 'A1');

        return $spreadsheet;
    }

    private function import(): array
    {
        return app(ContentPlanWorkbookImporter::class)->importWorkbook($this->workbook(), 2026);
    }

    public function test_every_sheet_lands_in_its_own_table(): void
    {
        User::factory()->create(['role' => 'super_admin', 'email_verified_at' => now()]);

        $summary = $this->import();

        // Final content calendar -> digital events keyed on the gist's opening sentence
        $kickoff = CalendarEvent::where('content_title', 'Month-opening offer announcement')->first();
        $this->assertNotNull($kickoff, 'Calendar row should be stored');
        $this->assertSame('digital_team', $kickoff->team_type);
        $this->assertSame('2026-09-01', $kickoff->event_date->format('Y-m-d'));
        $this->assertSame('FZS V2', $kickoff->product_focus);
        $this->assertSame('Month Kick-off', $kickoff->color_concern);
        $this->assertSame('Content Type: Static', $kickoff->remarks);
        $this->assertSame('https://drive.example/a', $kickoff->drive_link);

        // Excel serial dates resolve
        $this->assertNotNull(CalendarEvent::whereDate('event_date', '2026-09-05')->first(), 'Serial date should parse');

        // Product sheet uses the Publish Date column, not the month banner in column A
        $review = CalendarEvent::where('team_type', 'product_team')->where('content_title', 'Life Style Review')->first();
        $this->assertNotNull($review);
        $this->assertSame('2026-08-20', $review->event_date->format('Y-m-d'));
        $this->assertSame('2026-08-10', $review->shoot_date->format('Y-m-d'));
        $this->assertSame('FZS V2', $review->product);

        // Digital sheet, and its trailing prose block is not turned into an event
        $post = CalendarEvent::where('team_type', 'digital_team')->where('content_title', 'Post #1: FZS V4')->first();
        $this->assertNotNull($post);
        $this->assertSame('2026-09-01', $post->event_date->format('Y-m-d'));
        $this->assertSame(0, CalendarEvent::where('content_title', 'like', '%Planning Dependencies%')->count());

        // Logic sheet: one allocation row plus the notes underneath it
        $allocation = ContentPlanLogic::allocations()->first();
        $this->assertSame('September 2026', $allocation->period);
        $this->assertSame('FZS V2 (DD)', $allocation->product);
        $this->assertSame('3,950', $allocation->units);
        $this->assertSame('~4,210', $allocation->forecast);
        $this->assertSame(4, $allocation->posts_planned);
        $this->assertSame('1 Purchase / 3 Experience', $allocation->pillar_split);
        $this->assertSame(3, ContentPlanLogic::notes()->count());
        $this->assertSame(1, ContentPlanLogic::where('row_type', 'source')->count());

        // Staff sheet: columns A and B are swapped in the file, and get corrected
        $staff = User::where('email', 'option@aci-bd.com')->first();
        $this->assertNotNull($staff);
        $this->assertSame('Hossain Mohammad Option', $staff->name);
        $this->assertSame('11465', $staff->staff_id);
        $this->assertSame('BM,Yamaha', $staff->designation);
        $this->assertNotNull($staff->email_verified_at, 'Imported staff should be able to sign in');

        $this->assertSame(2, User::whereNotNull('staff_id')->count());
        $this->assertGreaterThan(0, $summary['imported']);
    }

    public function test_malformed_email_is_reported_but_still_imported(): void
    {
        User::factory()->create(['role' => 'super_admin', 'email_verified_at' => now()]);

        $summary = $this->import();

        $this->assertNotNull(User::where('email', 'adhikary@aci-bdcom')->first());
        $this->assertTrue(
            collect($summary['errors'])->contains(fn ($e) => str_contains($e, 'adhikary@aci-bdcom')),
            'An invalid address should be flagged in the report'
        );
    }

    public function test_reimporting_the_same_workbook_adds_nothing(): void
    {
        User::factory()->create(['role' => 'super_admin', 'email_verified_at' => now()]);

        $this->import();
        $eventCount = CalendarEvent::count();
        $userCount = User::count();
        $logicCount = ContentPlanLogic::count();

        $second = $this->import();

        $this->assertSame(0, $second['imported'], 'A second run should import nothing');
        $this->assertSame($eventCount, CalendarEvent::count());
        $this->assertSame($userCount, User::count());
        $this->assertSame($logicCount, ContentPlanLogic::count());
    }

    public function test_staff_accounts_get_the_shared_default_password(): void
    {
        User::factory()->create(['role' => 'super_admin', 'email_verified_at' => now()]);

        $this->import();

        $staff = User::where('email', 'option@aci-bd.com')->first();
        $this->assertTrue(
            \Illuminate\Support\Facades\Hash::check(ContentPlanWorkbookImporter::DEFAULT_STAFF_PASSWORD, $staff->password),
            'Imported staff should be able to sign in with the shared default'
        );
        $this->assertNotSame(ContentPlanWorkbookImporter::DEFAULT_STAFF_PASSWORD, $staff->password, 'Password must be stored hashed');
    }

    public function test_the_six_events_per_date_cap_is_reported_separately(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'email_verified_at' => now()]);

        for ($i = 1; $i <= 6; $i++) {
            CalendarEvent::create([
                'user_id' => $admin->id,
                'team_type' => 'product_team',
                'event_date' => '2026-09-01',
                'content_title' => "Filler {$i}",
            ]);
        }

        $summary = $this->import();

        $this->assertGreaterThan(0, $summary['capped'], 'Rows blocked by the cap should be counted');
        $this->assertSame(6, CalendarEvent::whereDate('event_date', '2026-09-01')->count());
        $this->assertTrue(
            collect($summary['errors'])->contains(fn ($e) => str_contains($e, 'already holds 6 events')),
            'The cap should be explained in the report'
        );
    }
}
