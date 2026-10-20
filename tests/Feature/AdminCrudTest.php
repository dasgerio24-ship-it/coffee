<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Drink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Router;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_resource_route_names_and_methods_are_registered(): void
    {
        $routes = app(Router::class)->getRoutes();

        $this->assertContains('GET', $routes->getByName('admin.drinks.index')->methods());
        $this->assertContains('GET', $routes->getByName('admin.drinks.create')->methods());
        $this->assertContains('POST', $routes->getByName('admin.drinks.store')->methods());
        $this->assertContains('GET', $routes->getByName('admin.drinks.edit')->methods());
        $this->assertContains('PUT', $routes->getByName('admin.drinks.update')->methods());
        $this->assertContains('DELETE', $routes->getByName('admin.drinks.destroy')->methods());
        $this->assertContains('POST', $routes->getByName('admin.categories.store')->methods());
        $this->assertNull($routes->getByName('admin.drinks.show'));
    }

    public function test_drink_list_and_create_form_are_available_to_admin(): void
    {
        $category = Category::factory()->create();
        Drink::factory()->for($category)->create();

        $this->get(route('admin.drinks.index'))
            ->assertOk()
            ->assertSee($category->name);

        $this->get(route('admin.drinks.create'))
            ->assertOk()
            ->assertSee('name="title"', false);
    }

    public function test_admin_can_create_a_drink(): void
    {
        $category = Category::factory()->create();

        $this->post(route('admin.drinks.store'), $this->validDrinkData($category))
            ->assertRedirect(route('admin.drinks.index'))
            ->assertSessionHas('success', 'Напиток добавлен');

        $this->assertDatabaseHas('drinks', [
            'title' => 'Тестовый латте',
            'category_id' => $category->id,
        ]);
    }

    public function test_empty_drink_form_has_russian_validation_errors(): void
    {
        $this->from(route('admin.drinks.create'))
            ->post(route('admin.drinks.store'), [])
            ->assertRedirect(route('admin.drinks.create'))
            ->assertSessionHasErrors(['category_id', 'title', 'price', 'volume']);

        $messages = session('errors')->all();
        $this->assertStringContainsString('обязательно', implode(' ', $messages));
    }

    public function test_form_displays_three_errors_and_preserves_other_values(): void
    {
        $response = $this->followingRedirects()
            ->from(route('admin.drinks.create'))
            ->post(route('admin.drinks.store'), [
                'category_id' => 999,
                'title' => '',
                'price' => 'abc',
                'volume' => 300,
                'description' => 'Введённое описание',
                'calories' => 180,
                'in_stock' => '0',
            ])
            ->assertSee('Исправьте ошибки в форме (3)')
            ->assertSee('class="error"', false)
            ->assertSee('value="abc"', false)
            ->assertSee('Введённое описание')
            ->assertSee('id="in_stock" name="in_stock" type="checkbox" value="1"', false)
            ->assertDontSee('type="checkbox" value="1" checked', false)
            ->assertSee('class="invalid"', false);

        $this->assertSame(3, substr_count($response->getContent(), 'class="error"'));
    }

    public function test_drink_title_must_be_unique_and_category_must_exist(): void
    {
        $category = Category::factory()->create();
        Drink::factory()->for($category)->create(['title' => 'Тестовый латте']);

        $this->from(route('admin.drinks.create'))
            ->post(route('admin.drinks.store'), $this->validDrinkData($category))
            ->assertRedirect(route('admin.drinks.create'))
            ->assertSessionHasErrors(['title']);

        $invalidCategory = $this->validDrinkData($category);
        $invalidCategory['title'] = 'Другой напиток';
        $invalidCategory['category_id'] = 999;

        $this->from(route('admin.drinks.create'))
            ->post(route('admin.drinks.store'), $invalidCategory)
            ->assertRedirect(route('admin.drinks.create'))
            ->assertSessionHasErrors(['category_id']);
    }

    public function test_drink_update_ignores_own_title_and_unchecked_box_is_false(): void
    {
        $category = Category::factory()->create();
        $drink = Drink::factory()->for($category)->create([
            'title' => 'Тестовый латте',
            'in_stock' => true,
        ]);
        $data = $this->validDrinkData($category);
        unset($data['in_stock']);

        $this->put(route('admin.drinks.update', $drink), $data)
            ->assertRedirect(route('admin.drinks.index'))
            ->assertSessionHas('success', 'Изменения сохранены');

        $this->assertDatabaseHas('drinks', [
            'id' => $drink->id,
            'title' => 'Тестовый латте',
            'in_stock' => false,
        ]);
    }

    public function test_admin_can_delete_drink_and_get_update_url_is_not_allowed(): void
    {
        $drink = Drink::factory()->create();

        $this->get('/admin/drinks/'.$drink->id)->assertStatus(405);

        $this->delete(route('admin.drinks.destroy', $drink))
            ->assertRedirect()
            ->assertSessionHas('success', 'Напиток удалён');

        $this->assertDatabaseMissing('drinks', ['id' => $drink->id]);
    }

    public function test_admin_can_create_update_and_delete_categories(): void
    {
        $this->post(route('admin.categories.store'), ['name' => 'Сезонные', 'description' => 'Сезонные напитки'])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success', 'Категория добавлена');

        $category = Category::where('name', 'Сезонные')->firstOrFail();

        $this->put(route('admin.categories.update', $category), [
            'name' => 'Сезонные',
            'description' => 'Прежнее название',
        ])->assertRedirect(route('admin.categories.index'));

        $this->put(route('admin.categories.update', $category), [
            'name' => 'Специальные',
            'description' => 'Специальные напитки',
        ])->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Специальные']);

        $this->delete(route('admin.categories.destroy', $category))
            ->assertRedirect()
            ->assertSessionHas('success', 'Категория удалена');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_cannot_be_deleted_while_it_has_drinks(): void
    {
        $category = Category::factory()->has(Drink::factory())->create();

        $this->delete(route('admin.categories.destroy', $category))
            ->assertRedirect()
            ->assertSessionHas('error', 'Нельзя удалить: есть связанные записи');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_category_validation_is_russian_and_unique(): void
    {
        Category::factory()->create(['name' => 'Кофейные напитки']);

        $this->from(route('admin.categories.create'))
            ->post(route('admin.categories.store'), ['name' => ''])
            ->assertRedirect(route('admin.categories.create'))
            ->assertSessionHasErrors(['name']);

        $this->from(route('admin.categories.create'))
            ->post(route('admin.categories.store'), ['name' => 'Кофейные напитки'])
            ->assertRedirect(route('admin.categories.create'))
            ->assertSessionHasErrors(['name']);
    }

    /**
     * @return array{category_id: int, title: string, price: string, volume: int, description: string, calories: int, in_stock: int}
     */
    private function validDrinkData(Category $category): array
    {
        return [
            'category_id' => $category->id,
            'title' => 'Тестовый латте',
            'price' => '1250.00',
            'volume' => 300,
            'description' => 'Напиток для проверки CRUD.',
            'calories' => 180,
            'in_stock' => 1,
        ];
    }
}
