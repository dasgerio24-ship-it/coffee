<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CoffeeShopPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_drink_pages_and_not_found_responses(): void
    {
        $this->get('/drinks')
            ->assertOk()
            ->assertSee('Капучино')
            ->assertSee('Кофе')
            ->assertSee('1. ', false)
            ->assertSee('Премиум')
            ->assertSee('class="active">Каталог', false);

        $this->get('/drinks/1')
            ->assertOk()
            ->assertSee('Капучино')
            ->assertSee('Кофе')
            ->assertSee('Заказать с собой')
            ->assertSee('1 400 ₸')
            ->assertSee('class="active">Каталог', false);

        $this->get('/drinks/999')
            ->assertNotFound()
            ->assertSee('Такой страницы нет')
            ->assertSee('class="alert alert-error"', false)
            ->assertSee('class="menu"', false)
            ->assertSee('site-footer');

        $this->get('/drinks/abc')->assertNotFound();
    }

    public function test_drink_titles_escape_html(): void
    {
        $categoryId = DB::table('categories')->where('name', 'Кофе')->value('id');

        DB::table('drinks')->insert([
            'id' => 99,
            'category_id' => $categoryId,
            'title' => '<b>Тест</b>',
            'price' => 1000.00,
            'volume' => 50,
            'calories' => null,
            'description' => 'Проверка экранирования.',
            'in_stock' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->get('/drinks')
            ->assertOk()
            ->assertSee('&lt;b&gt;Тест&lt;/b&gt;', false)
            ->assertDontSee('<b>Тест</b>', false);

        $this->get('/drinks/99')
            ->assertOk()
            ->assertSee('&lt;b&gt;Тест&lt;/b&gt;', false)
            ->assertDontSee('<b>Тест</b>', false);
    }

    public function test_home_page_uses_the_shared_layout_and_active_navigation(): void
    {
        $response = $this->get('/')
            ->assertOk()
            ->assertSee('<title>Главная — КофеШО</title>', false)
            ->assertSee('class="active">Главная', false)
            ->assertSee('site-footer');

        $this->assertSame(3, substr_count($response->getContent(), '<article class="card card-wide">'));
    }

    public function test_empty_drink_list_uses_the_blade_empty_branch(): void
    {
        DB::table('drinks')->delete();

        $this->get('/drinks')
            ->assertOk()
            ->assertSee('Каталог пока пуст')
            ->assertSee('class="alert alert-info"', false);
    }

    public function test_rules_booking_and_main_redirect(): void
    {
        $this->get('/rules')->assertOk();
        $this->get('/contacts')->assertOk()->assertSee('+7705876769');
        $this->get('/booking')->assertOk();
        $this->get('/main')->assertRedirect('/');
    }

    public function test_admin_access_requires_a_key_or_authorized_session(): void
    {
        $this->get('/admin')
            ->assertForbidden()
            ->assertSee('class="menu"', false)
            ->assertSee('site-footer');

        config(['app.admin_key' => 'test-admin-key']);

        $this->get('/admin?key=test-admin-key')
            ->assertOk()
            ->assertSee('Администрирование')
            ->assertSee('Панель')
            ->assertSee('Напитки')
            ->assertSee('Выход');

        $this->get('/admin/drinks')
            ->assertOk()
            ->assertSee('Флэт уайт')
            ->assertSee('новая')
            ->assertSee('class="active">Напитки', false);

        $this->get('/admin/drinks/create')
            ->assertOk()
            ->assertSee('Добавить напиток');

        $this->get('/admin/logout')
            ->assertRedirect('/');

        $this->get('/admin')->assertForbidden();
    }

    public function test_admin_key_must_be_configured(): void
    {
        config(['app.admin_key' => null]);

        $this->get('/admin?key=anything')->assertForbidden();
    }
}
