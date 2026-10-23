<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Drink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DrinkFilesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('public');
        Storage::fake('local');
    }

    public function test_editor_can_upload_an_image_and_the_database_stores_only_its_path(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        $category = Category::factory()->create();

        $this->actingAs($editor)
            ->post(route('admin.drinks.store'), $this->validDrinkData($category) + [
                'image' => UploadedFile::fake()->image('raf.jpg'),
            ])
            ->assertRedirect(route('admin.drinks.index'));

        $drink = Drink::where('title', 'Раф ванильный')->firstOrFail();
        $this->assertStringStartsWith('drinks/', $drink->image);
        $this->assertIsString($drink->image);
        Storage::disk('public')->assertExists($drink->image);
        Storage::disk('local')->assertMissing($drink->image);
    }

    public function test_editor_can_replace_or_remove_an_image_and_old_file_is_deleted(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        $category = Category::factory()->create();
        Storage::disk('public')->put('drinks/old.jpg', 'old image');
        $drink = Drink::factory()->for($category)->create([
            'user_id' => $editor->id,
            'image' => 'drinks/old.jpg',
        ]);

        $this->actingAs($editor)
            ->put(route('admin.drinks.update', $drink), $this->validDrinkData($category, [
                'title' => $drink->title,
                'image' => UploadedFile::fake()->image('new.jpg'),
            ]))
            ->assertRedirect(route('admin.drinks.index'));

        $drink->refresh();
        $this->assertNotSame('drinks/old.jpg', $drink->image);
        Storage::disk('public')->assertMissing('drinks/old.jpg');
        Storage::disk('public')->assertExists($drink->image);

        $currentImage = $drink->image;
        $this->actingAs($editor)
            ->put(route('admin.drinks.update', $drink), $this->validDrinkData($category, [
                'title' => $drink->title,
                'remove_image' => '1',
            ]))
            ->assertRedirect(route('admin.drinks.index'));

        $this->assertNull($drink->fresh()->image);
        Storage::disk('public')->assertMissing($currentImage);
    }

    public function test_image_validation_rejects_non_images_and_files_over_two_megabytes(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        $category = Category::factory()->create();

        $this->actingAs($editor)
            ->from(route('admin.drinks.create'))
            ->post(route('admin.drinks.store'), $this->validDrinkData($category) + [
                'image' => UploadedFile::fake()->create('notes.txt', 2, 'text/plain'),
            ])
            ->assertRedirect(route('admin.drinks.create'))
            ->assertSessionHasErrors('image');

        $this->actingAs($editor)
            ->from(route('admin.drinks.create'))
            ->post(route('admin.drinks.store'), $this->validDrinkData($category, [
                'title' => 'Большое фото',
                'image' => UploadedFile::fake()->image('large.jpg')->size(2049),
            ]))
            ->assertRedirect(route('admin.drinks.create'))
            ->assertSessionHasErrors('image');
    }

    public function test_image_url_uses_a_placeholder_when_a_drink_has_no_image(): void
    {
        $drink = Drink::factory()->create(['image' => null]);

        $this->assertSame(asset('images/no-drink.png'), $drink->image_url);
    }

    public function test_observer_logs_a_created_drink(): void
    {
        Log::spy();

        $drink = Drink::factory()->create(['title' => 'lr10-test created log']);

        Log::shouldHaveReceived('info')
            ->once()
            ->with("Создан напиток #{$drink->id}: {$drink->title}");
    }

    public function test_observer_logs_changes_to_an_updated_drink(): void
    {
        $drink = Drink::factory()->create(['title' => 'lr10-test before update']);
        Log::spy();

        $drink->update(['title' => 'lr10-test after update']);

        Log::shouldHaveReceived('info')
            ->withArgs(fn (string $message, array $changes): bool => $message === "Изменён напиток #{$drink->id}"
                && ($changes['title'] ?? null) === 'lr10-test after update')
            ->once();
    }

    public function test_deleting_a_drink_removes_its_public_image_and_private_attachment(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        $category = Category::factory()->create();
        Storage::disk('public')->put('drinks/to-delete.jpg', 'image');
        Storage::disk('local')->put('attachments/to-delete.pdf', '%PDF-test');
        $drink = Drink::factory()->for($category)->create([
            'user_id' => $editor->id,
            'image' => 'drinks/to-delete.jpg',
            'attachment' => 'attachments/to-delete.pdf',
        ]);
        Log::spy();

        $this->actingAs($editor)
            ->delete(route('admin.drinks.destroy', $drink))
            ->assertRedirect();

        $this->assertDatabaseMissing('drinks', ['id' => $drink->id]);
        Storage::disk('public')->assertMissing('drinks/to-delete.jpg');
        Storage::disk('local')->assertMissing('attachments/to-delete.pdf');
        Log::shouldHaveReceived('info')
            ->withArgs(fn (string $message, array $context): bool => $message === 'Напиток удалён'
                && $context === [
                    'drink_id' => $drink->id,
                    'title' => $drink->title,
                ])
            ->once();
    }

    public function test_bulk_delete_does_not_trigger_the_drink_observer(): void
    {
        $category = Category::factory()->create();
        Storage::disk('public')->put('drinks/lr10-test.jpg', 'image');
        Storage::disk('local')->put('attachments/lr10-test.pdf', '%PDF-test');
        $drink = Drink::factory()->for($category)->create([
            'image' => 'drinks/lr10-test.jpg',
            'attachment' => 'attachments/lr10-test.pdf',
        ]);

        Drink::query()->whereKey($drink->id)->delete();

        $this->assertDatabaseMissing('drinks', ['id' => $drink->id]);
        Storage::disk('public')->assertExists('drinks/lr10-test.jpg');
        Storage::disk('local')->assertExists('attachments/lr10-test.pdf');
    }

    public function test_replacing_a_private_attachment_deletes_the_previous_file(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        $category = Category::factory()->create();
        Storage::disk('local')->put('attachments/old.pdf', '%PDF-old');
        $drink = Drink::factory()->for($category)->create([
            'user_id' => $editor->id,
            'attachment' => 'attachments/old.pdf',
        ]);

        $this->actingAs($editor)
            ->put(route('admin.drinks.update', $drink), $this->validDrinkData($category, [
                'title' => $drink->title,
                'attachment' => UploadedFile::fake()->createWithContent(
                    'new.pdf',
                    "%PDF-1.4\nReplacement document",
                ),
            ]))
            ->assertRedirect(route('admin.drinks.index'));

        $drink->refresh();
        $this->assertNotSame('attachments/old.pdf', $drink->attachment);
        Storage::disk('local')->assertMissing('attachments/old.pdf');
        Storage::disk('local')->assertExists($drink->attachment);
        Storage::disk('public')->assertMissing($drink->attachment);
    }

    public function test_private_attachment_is_downloadable_only_after_authentication(): void
    {
        $category = Category::factory()->create();
        Storage::disk('local')->put('attachments/tech.pdf', "%PDF-1.4\nPrivate test document");
        $drink = Drink::factory()->for($category)->create([
            'attachment' => 'attachments/tech.pdf',
        ]);

        $this->get(route('drinks.attachment', $drink))
            ->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->get(route('drinks.attachment', $drink))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertDownload('Texkarta-'.$drink->id.'.pdf')
            ->assertStreamedContent("%PDF-1.4\nPrivate test document");

        $this->get('/storage/attachments/tech.pdf')->assertNotFound();
    }

    public function test_attachment_route_returns_404_when_no_document_is_attached(): void
    {
        $drink = Drink::factory()->create(['attachment' => null]);

        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->get(route('drinks.attachment', $drink))
            ->assertNotFound();
    }

    public function test_catalog_pdf_is_registered_before_dynamic_drink_route_and_respects_category_filter(): void
    {
        $coffee = Category::factory()->create(['name' => 'Кофе']);
        $tea = Category::factory()->create(['name' => 'Чай']);
        Drink::factory()->for($coffee)->create(['title' => 'Кофе фильтр']);
        Drink::factory()->for($coffee)->create(['title' => 'Кофе эспрессо']);
        Drink::factory()->for($tea)->create(['title' => 'Чай зелёный']);

        $allDrinksPdf = $this->get(route('drinks.pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $allDrinksPdf->getContent());

        $response = $this->get(route('drinks.pdf', ['category' => $coffee->id]));
        $response->assertOk()->assertHeader('content-type', 'application/pdf');

        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertSame(2, Drink::where('category_id', $coffee->id)->count());
    }

    public function test_catalog_and_drink_detail_render_images_with_alt_and_qr_code(): void
    {
        $category = Category::factory()->create();
        Storage::disk('public')->put('drinks/raf.jpg', 'fake image');
        $drink = Drink::factory()->for($category)->create([
            'title' => 'Раф тестовый',
            'image' => 'drinks/raf.jpg',
        ]);

        $this->get(route('drinks.index'))
            ->assertOk()
            ->assertSee('alt="Раф тестовый"', false)
            ->assertSee('/storage/drinks/raf.jpg');

        $this->get(route('drinks.show', $drink))
            ->assertOk()
            ->assertSee('alt="Раф тестовый"', false)
            ->assertSee('alt="QR-код напитка"', false)
            ->assertSee('data:image/png;base64,', false);
    }

    private function validDrinkData(Category $category, array $overrides = []): array
    {
        return array_replace([
            'category_id' => $category->id,
            'title' => 'Раф ванильный',
            'price' => 1500,
            'volume' => 300,
            'description' => 'Напиток для проверки загрузки.',
            'calories' => 200,
            'in_stock' => 1,
        ], $overrides);
    }
}
