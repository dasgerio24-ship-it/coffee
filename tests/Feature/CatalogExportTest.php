<?php

namespace Tests\Feature;

use App\Jobs\GenerateCatalogPdf;
use App\Models\Category;
use App\Models\Drink;
use App\Models\User;
use App\Notifications\ExportReady;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_admin_can_queue_a_catalog_export_but_guests_and_users_cannot(): void
    {
        Queue::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $this->post(route('admin.exports.catalog'))->assertRedirect(route('login'));
        $this->actingAs($user)->post(route('admin.exports.catalog'))->assertForbidden();

        $this->actingAs($admin)
            ->post(route('admin.exports.catalog'))
            ->assertRedirect()
            ->assertSessionHas('success');

        Queue::assertPushed(GenerateCatalogPdf::class, fn (GenerateCatalogPdf $job): bool => $job->user->is($admin));
    }

    public function test_job_generates_a_private_pdf_and_notifies_the_requesting_admin(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        Drink::factory()->for($category)->create(['title' => 'Каталоговый напиток']);
        (new GenerateCatalogPdf($admin))->handle();

        $notification = $admin->notifications()->firstOrFail();
        $filename = basename($notification->data['url']);
        Storage::disk('local')->assertExists("exports/{$filename}");
        $this->assertStringStartsWith('%PDF', Storage::disk('local')->get("exports/{$filename}"));
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'type' => ExportReady::class,
        ]);
        $this->assertSame(route('admin.exports.download', $filename), $notification->data['url']);
    }

    public function test_admin_can_download_a_private_pdf_and_unsafe_or_missing_files_return_404(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['role' => 'admin']);
        $filename = 'catalog-'.now()->format('Y-m-d-His').'.pdf';
        Storage::disk('local')->put("exports/{$filename}", '%PDF-test');

        $this->get(route('admin.exports.download', $filename))
            ->assertRedirect(route('login'));

        $this->actingAs($admin)
            ->get(route('admin.exports.download', $filename))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertDownload($filename)
            ->assertStreamedContent('%PDF-test');

        $this->get('/admin/exports/unsafe.pdf')
            ->assertNotFound();
        $this->get('/admin/exports/../.env')
            ->assertNotFound();
        $this->get(route('admin.exports.download', 'catalog-2026-10-23-121234.pdf'))
            ->assertNotFound();
    }

    public function test_export_form_and_notification_links_are_visible(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $filename = 'catalog-'.now()->format('Y-m-d-His').'.pdf';
        $admin->notify(new ExportReady($filename));

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.exports.catalog'))
            ->assertSee('Сформировать PDF в фоне');

        $this->get(route('admin.notifications'))
            ->assertOk()
            ->assertSee(route('admin.exports.download', $filename));
        $this->get(route('profile'))
            ->assertOk()
            ->assertSee(route('admin.exports.download', $filename));
    }
}
