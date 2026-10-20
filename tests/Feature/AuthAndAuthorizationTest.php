<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Drink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthAndAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed();
    }

    public function test_registration_creates_a_user_role_and_ignores_submitted_role(): void
    {
        $this->post(route('register'), [
            'name' => 'Новый гость',
            'email' => 'new-user@test.kz',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ])->assertRedirect(route('home'));

        $user = User::where('email', 'new-user@test.kz')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertSame('user', $user->role);
        $this->assertTrue(Hash::check('password123', $user->password));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Новый гость')
            ->assertSee('Добро пожаловать, Новый гость');
    }

    public function test_registration_validation_messages_are_russian_and_keep_safe_old_input(): void
    {
        $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'Проверка',
                'email' => 'not-an-email',
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors([
                'email' => 'Введите корректный адрес электронной почты.',
                'password' => 'Пароли не совпадают.',
            ])
            ->assertSessionHas('_old_input.name', 'Проверка')
            ->assertSessionHas('_old_input.email', 'not-an-email');
    }

    public function test_registration_rejects_empty_fields_and_duplicate_email(): void
    {
        $this->from(route('register'))
            ->post(route('register'), [])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['name', 'email', 'password']);

        $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'Дубликат',
                'email' => 'admin@test.kz',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['email']);
    }

    public function test_login_uses_same_error_for_unknown_email_and_wrong_password(): void
    {
        $this->from(route('login'))
            ->post(route('login'), ['email' => 'admin@test.kz', 'password' => 'wrong-password'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Неверный e-mail или пароль']);

        $this->from(route('login'))
            ->post(route('login'), ['email' => 'missing@test.kz', 'password' => 'wrong-password'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Неверный e-mail или пароль']);
    }

    public function test_login_logout_and_intended_redirect_work(): void
    {
        $this->get(route('admin.drinks.index'))
            ->assertRedirect(route('login'));

        $this->from(route('login'))
            ->post(route('login'), ['email' => 'admin@test.kz', 'password' => 'password'])
            ->assertRedirect(route('admin.drinks.index'));

        $this->assertAuthenticatedAs(User::where('email', 'admin@test.kz')->firstOrFail());

        $this->get('/logout')->assertStatus(405);

        $this->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
        $this->get(route('admin.drinks.index'))->assertRedirect(route('login'));
    }

    public function test_login_is_throttled_after_five_attempts(): void
    {
        RateLimiter::clear('127.0.0.1|admin@test.kz');

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->from(route('login'))
                ->post(route('login'), [
                    'email' => 'admin@test.kz',
                    'password' => 'wrong-password',
                ])->assertRedirect(route('login'));
        }

        $this->from(route('login'))
            ->post(route('login'), [
                'email' => 'admin@test.kz',
                'password' => 'wrong-password',
            ])->assertStatus(429);

        RateLimiter::clear('127.0.0.1|admin@test.kz');
    }

    public function test_admin_panel_gate_allows_editor_and_admin_but_denies_user(): void
    {
        $admin = User::where('email', 'admin@test.kz')->firstOrFail();
        $editor = User::where('email', 'editor@test.kz')->firstOrFail();
        $user = User::where('email', 'user@test.kz')->firstOrFail();

        $this->assertTrue(Gate::forUser($admin)->allows('admin-panel'));
        $this->assertTrue(Gate::forUser($editor)->allows('admin-panel'));
        $this->assertFalse(Gate::forUser($user)->allows('admin-panel'));

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden()
            ->assertSee('Недостаточно прав для доступа к админ-панели');

        $this->actingAs($editor)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_drink_policy_limits_editor_to_owned_records_and_admin_can_manage_all(): void
    {
        $editor = User::where('email', 'editor@test.kz')->firstOrFail();
        $admin = User::where('email', 'admin@test.kz')->firstOrFail();
        $category = Category::firstOrFail();
        Drink::query()->delete();
        $ownedDrink = Drink::factory()->for($category)->for($editor, 'user')->create();
        $unownedDrink = Drink::factory()->for($category)->create();

        $this->assertTrue(Gate::forUser($editor)->allows('create', Drink::class));
        $this->assertTrue(Gate::forUser($editor)->allows('update', $ownedDrink));
        $this->assertTrue(Gate::forUser($editor)->allows('delete', $ownedDrink));
        $this->assertFalse(Gate::forUser($editor)->allows('update', $unownedDrink));
        $this->assertFalse(Gate::forUser($editor)->allows('delete', $unownedDrink));
        $this->assertTrue(Gate::forUser($admin)->allows('create', Drink::class));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $unownedDrink));
        $this->assertTrue(Gate::forUser($admin)->allows('delete', $unownedDrink));

        $this->actingAs($editor)
            ->get(route('admin.drinks.edit', $unownedDrink))
            ->assertForbidden()
            ->assertSee('Можно изменять только свои записи.');

        $this->actingAs($editor)
            ->put(route('admin.drinks.update', $unownedDrink), [])
            ->assertForbidden();

        $this->actingAs($editor)
            ->delete(route('admin.drinks.destroy', $unownedDrink))
            ->assertForbidden();

        $this->actingAs($admin)
            ->delete(route('admin.drinks.destroy', $ownedDrink))
            ->assertRedirect()
            ->assertSessionHas('success', 'Напиток удалён');

        $this->assertDatabaseMissing('drinks', ['id' => $ownedDrink->id]);
    }

    public function test_admin_drink_actions_are_rendered_only_when_authorized(): void
    {
        $editor = User::where('email', 'editor@test.kz')->firstOrFail();
        $admin = User::where('email', 'admin@test.kz')->firstOrFail();
        $category = Category::firstOrFail();
        Drink::query()->delete();
        $ownedDrink = Drink::factory()->for($category)->for($editor, 'user')->create();
        $unownedDrink = Drink::factory()->for($category)->create();

        $editorPage = $this->actingAs($editor)->get(route('admin.drinks.index'))->assertOk();
        $editorPage->assertSee(route('admin.drinks.edit', $ownedDrink), false)
            ->assertDontSee(route('admin.drinks.edit', $unownedDrink), false)
            ->assertSee('Автор записи');

        $adminPage = $this->actingAs($admin)->get(route('admin.drinks.index'))->assertOk();
        $adminPage->assertSee(route('admin.drinks.edit', $ownedDrink), false)
            ->assertSee(route('admin.drinks.edit', $unownedDrink), false)
            ->assertSee('Администратор (admin)');
    }

    public function test_editor_created_drinks_are_assigned_to_the_editor(): void
    {
        $editor = User::where('email', 'editor@test.kz')->firstOrFail();
        $category = Category::factory()->create();

        $this->actingAs($editor)
            ->post(route('admin.drinks.store'), [
                'category_id' => $category->id,
                'title' => 'Напиток редактора',
                'price' => 1200,
                'volume' => 300,
                'description' => 'Тест записи с владельцем.',
                'calories' => 100,
                'in_stock' => 1,
            ])
            ->assertRedirect(route('admin.drinks.index'));

        $this->assertDatabaseHas('drinks', [
            'title' => 'Напиток редактора',
            'user_id' => $editor->id,
        ]);
    }
}
