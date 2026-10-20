<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Drink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CoffeeShopPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed();
    }

    public function test_drink_pages_and_not_found_responses(): void
    {
        $coffee = Category::where('name', 'Кофе')->firstOrFail();

        $this->get('/drinks')
            ->assertOk()
            ->assertSee('Кофе')
            ->assertSee('Кофе ('.$coffee->drinks()->count().')')
            ->assertSee('Премиум')
            ->assertSee('alt="Американо"', false)
            ->assertSee('name="q"', false)
            ->assertSee('name="category"', false)
            ->assertSee('name="sort"', false)
            ->assertDontSee('Запросов к БД:')
            ->assertSee('class="active">Каталог', false);

        $this->get('/drinks/1')
            ->assertOk()
            ->assertSee('Капучино')
            ->assertSee('href="'.route('categories.show', 1).'"', false)
            ->assertSee('Ещё из этой категории')
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
        $drink = Drink::factory()->create([
            'title' => '<b>Тест</b>',
            'description' => 'Проверка экранирования.',
        ]);

        $this->get('/drinks')
            ->assertOk()
            ->assertSee('&lt;b&gt;Тест&lt;/b&gt;', false)
            ->assertDontSee('<b>Тест</b>', false);

        $this->get('/drinks/'.$drink->id)
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
            ->assertSee('По запросу', false)
            ->assertSee('class="alert alert-warning"', false);
    }

    public function test_model_relations_casts_and_scopes_work(): void
    {
        $category = Category::firstOrFail();
        $drink = Drink::factory()->for($category)->create([
            'price' => 1234.5,
            'volume' => 300,
            'calories' => 120,
            'in_stock' => true,
        ]);

        $this->assertSame($category->id, $drink->category->id);
        $this->assertTrue($category->drinks()->whereKey($drink->id)->exists());
        $this->assertSame('1234.50', $drink->fresh()->price);
        $this->assertIsInt($drink->fresh()->volume);
        $this->assertIsInt($drink->fresh()->calories);
        $this->assertTrue($drink->fresh()->in_stock);
        $this->assertTrue(Drink::inStock()->whereKey($drink->id)->exists());
        $this->assertSame(
            Drink::where('in_stock', true)->count(),
            Drink::inStock()->count(),
        );
    }

    public function test_search_scope_groups_title_and_description_conditions(): void
    {
        $category = Category::firstOrFail();
        $otherCategory = Category::whereKeyNot($category->id)->firstOrFail();

        $expected = Drink::factory()->for($category)->create([
            'title' => 'Needle title',
            'description' => 'No match here.',
        ]);

        Drink::factory()->for($otherCategory)->create([
            'title' => 'Unrelated title',
            'description' => 'Needle in description.',
        ]);

        $matches = Drink::where('category_id', $category->id)
            ->search('Needle')
            ->get();

        $this->assertCount(1, $matches);
        $this->assertSame($expected->id, $matches->first()->id);
        $this->assertSame(Drink::count(), Drink::search('')->count());
    }

    public function test_category_filter_sort_whitelist_and_paginated_search(): void
    {
        $category = Category::firstOrFail();
        $otherCategory = Category::whereKeyNot($category->id)->firstOrFail();

        Drink::factory(20)->for($category)->create([
            'title' => 'PaginationToken beverage',
            'description' => 'PaginationToken description.',
            'price' => 1234,
        ]);
        Drink::factory()->for($otherCategory)->create([
            'title' => 'Unrelated category item',
            'price' => 100,
        ]);

        $response = $this->get('/drinks?q=PaginationToken&page=2')
            ->assertOk()
            ->assertSee('PaginationToken')
            ->assertSee('<li class="active" aria-current="page"><span>2</span></li>', false)
            ->assertSee('q=PaginationToken', false);

        $this->assertLessThanOrEqual(9, substr_count($response->getContent(), '<article class="card">'));

        $this->get('/drinks?category='.$category->id)
            ->assertOk()
            ->assertSee('PaginationToken')
            ->assertDontSee('Unrelated category item');

        $this->get('/drinks?category=abc')->assertOk();

        $priceResponse = $this->get('/drinks?sort=price')
            ->assertOk()
            ->assertViewHas('sort', 'price');
        $priceResponse->assertViewHas('drinks', function ($drinks): bool {
            $prices = $drinks->getCollection()->pluck('price')->all();
            $sortedPrices = $prices;
            sort($sortedPrices, SORT_NUMERIC);

            return $prices === $sortedPrices;
        });

        $this->get('/drinks?sort=password')
            ->assertOk()
            ->assertViewHas('sort', 'title');

        $this->get('/drinks?q[]=malformed')->assertOk();
    }

    public function test_category_page_and_missing_category(): void
    {
        $category = Category::where('name', 'Кофе')->firstOrFail();

        $this->get(route('categories.show', $category))
            ->assertOk()
            ->assertSee($category->name)
            ->assertSee('Капучино');

        $this->get('/categories/999')->assertNotFound();
    }

    public function test_empty_search_shows_query_in_warning_alert(): void
    {
        $this->get('/drinks?q=zzzzzznotfound')
            ->assertOk()
            ->assertSee('class="alert alert-warning"', false)
            ->assertSee('По запросу «zzzzzznotfound» ничего не найдено');
    }

    public function test_rules_booking_and_main_redirect(): void
    {
        $this->get('/rules')->assertOk();
        $this->get('/contacts')->assertOk()->assertSee('+7705876769');
        $this->get('/booking')->assertOk();
        $this->get('/main')->assertRedirect('/');
    }

    public function test_admin_area_redirects_guests_to_login(): void
    {
        $this->get('/admin')
            ->assertRedirect(route('login'));
    }
}
