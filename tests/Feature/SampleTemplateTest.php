<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\ContentPlanWorkbookImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class SampleTemplateTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'super_admin', 'email_verified_at' => now()]);
    }

    private function download(string $type, string $format = 'xlsx'): string
    {
        $response = $this->actingAs($this->admin())
            ->get(route('admin.bulk_upload.sample', ['type' => $type, 'format' => $format]));

        $response->assertStatus(200);

        $path = tempnam(sys_get_temp_dir(), 'tpl') . '.' . $format;
        file_put_contents($path, $response->streamedContent());

        return $path;
    }

    public function test_every_template_downloads(): void
    {
        foreach (['full-content-plan', 'content-calendar', 'product-events', 'digital-events', 'plan-logic', 'staff', 'master-data', 'global-events'] as $type) {
            $path = $this->download($type);
            $this->assertGreaterThan(0, filesize($path), "{$type} template should not be empty");
            @unlink($path);
        }
    }

    public function test_full_workbook_has_the_same_five_sheets_as_the_plan_file(): void
    {
        $path = $this->download('full-content-plan');
        $names = IOFactory::load($path)->getSheetNames();
        @unlink($path);

        $this->assertCount(5, $names);
        $this->assertStringContainsString('Calender', $names[0]);
        $this->assertSame('Product Team', $names[1]);
        $this->assertSame('Digital team', $names[2]);
        $this->assertStringContainsString('Logic', $names[3]);
        $this->assertSame('Staff ID & Designation', $names[4]);

        foreach ($names as $name) {
            $this->assertLessThanOrEqual(31, mb_strlen($name), "Excel caps sheet names at 31 characters: {$name}");
        }
    }

    public function test_the_downloaded_workbook_imports_back_into_every_table(): void
    {
        $this->admin();

        $path = $this->download('full-content-plan');
        $summary = app(ContentPlanWorkbookImporter::class)->importWorkbook(IOFactory::load($path), 2026);
        @unlink($path);

        $byTarget = collect($summary['sheets'])->keyBy('target');

        $this->assertGreaterThan(0, $byTarget['digital_team']['imported'], 'Calendar/digital sheets should import');
        $this->assertGreaterThan(0, $byTarget['product_team']['imported'], 'Product sheet should import');
        $this->assertGreaterThan(0, $byTarget['content_plan_logics']['imported'], 'Logic sheet should import');
        $this->assertGreaterThan(0, $byTarget['users']['imported'], 'Staff sheet should import');

        $this->assertSame(
            [],
            collect($summary['sheets'])->filter(fn ($s) => $s['target'] === '—')->pluck('name')->all(),
            'Every sheet in our own template must be recognised'
        );
    }

    public function test_single_sheet_templates_carry_the_workbook_headers(): void
    {
        $product = IOFactory::load($this->download('product-events'))->getActiveSheet()->rangeToArray('A1:O1')[0];
        $this->assertSame('Publish Date', $product[6]);
        $this->assertSame('Drive Link', $product[12]);
        $this->assertSame('Boosting Budget', $product[14]);

        $calendar = IOFactory::load($this->download('content-calendar'))->getActiveSheet();
        $this->assertStringContainsString('Content Calendar', (string) $calendar->getCell('A1')->getValue());
        $this->assertSame('Date', $calendar->getCell('A3')->getValue());
        $this->assertSame('Content Gist & Creative Direction', $calendar->getCell('H3')->getValue());

        $staff = IOFactory::load($this->download('staff'))->getActiveSheet()->rangeToArray('A1:D1')[0];
        $this->assertSame(['Staff ID', 'Name', 'Designation', 'Email Address'], $staff);
    }

    public function test_the_bulk_upload_page_lists_every_sheet_and_template(): void
    {
        $response = $this->actingAs($this->admin())->get("/admin/bulk-upload");
        $response->assertStatus(200);

        $html = $response->getContent();

        foreach (["content-calendar", "product-events", "digital-events", "plan-logic", "staff", "master-data", "full-content-plan"] as $type) {
            $this->assertTrue(
                str_contains($html, "sample/{$type}"),
                "The page should offer the {$type} template"
            );
        }

        foreach (["Final Content Calender", "Product Team", "Digital team", "Logic", "Staff ID &amp; Designation"] as $sheet) {
            $this->assertTrue(str_contains($html, $sheet), "The guide should document the {$sheet} sheet");
        }

        $this->assertTrue(str_contains($html, "Content Gist &amp; Creative Direction"), "Calendar headers should be listed");
        $this->assertTrue(str_contains($html, "Why This Allocation"), "Logic headers should be listed");
    }
}