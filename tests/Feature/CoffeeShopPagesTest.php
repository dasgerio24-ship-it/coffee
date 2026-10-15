<?php

namespace Tests\Feature;

use Tests\TestCase;

class CoffeeShopPagesTest extends TestCase
{
    public function test_catalog_pages_and_not_found_responses(): void
    {
        $this->get('/drinks')
            ->assertOk()
            ->assertSee('Капучино');

        $this->get('/drinks/1')
            ->assertOk()
            ->assertSee('Заказать с собой')
            ->assertSee('1400 ₸');

        $this->get('/drinks/999')
            ->assertNotFound()
            ->assertSee('Такой страницы нет');

        $this->get('/drinks/abc')->assertNotFound();
    }

    public function test_rules_booking_and_main_redirect(): void
    {
        $this->get('/rules')->assertOk();
        $this->get('/booking')->assertOk();
        $this->get('/main')->assertRedirect('/');
    }

    public function test_admin_access_requires_a_key_or_authorized_session(): void
    {
        $this->get('/admin')->assertForbidden();

        $this->withSession(['is_admin' => true])
            ->get('/admin/drinks')
            ->assertOk()
            ->assertSee('Флэт уайт');

        $this->withSession(['is_admin' => true])
            ->get('/admin/logout')
            ->assertRedirect('/');
    }

    public function test_admin_key_must_be_configured(): void
    {
        config(['app.admin_key' => null]);

        $this->get('/admin?key=anything')->assertForbidden();
    }
}
