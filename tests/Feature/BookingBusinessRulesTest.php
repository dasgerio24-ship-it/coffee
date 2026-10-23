<?php

namespace Tests\Feature;

use App\Contracts\SmsSender;
use App\Mail\BookingReceived;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Drink;
use App\Models\User;
use App\Notifications\BookingStatusChanged;
use App\Notifications\NewBooking;
use App\Services\BookingService;
use App\Services\Sms\LogSmsSender;
use App\Services\Sms\NullSmsSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BookingBusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_service_rejects_an_unavailable_drink(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $drink = Drink::factory()->create(['in_stock' => false]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Напиток сейчас недоступен для заказа');

        app(BookingService::class)->create($user, $drink, ['phone' => '+77011234567']);
    }

    public function test_service_rejects_a_fourth_active_booking(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();

        foreach (range(1, 3) as $number) {
            $drink = Drink::factory()->for($category)->create(['in_stock' => true]);
            Booking::create([
                'user_id' => $user->id,
                'drink_id' => $drink->id,
                'phone' => '+77011234567',
                'status' => 'new',
            ]);
        }

        $newDrink = Drink::factory()->for($category)->create(['in_stock' => true]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Нельзя оформить больше 3 активных заказов');

        app(BookingService::class)->create($user, $newDrink, ['phone' => '+77011234567']);
    }

    public function test_service_rejects_a_duplicate_active_drink(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $drink = Drink::factory()->create(['in_stock' => true]);

        Booking::create([
            'user_id' => $user->id,
            'drink_id' => $drink->id,
            'phone' => '+77011234567',
            'status' => 'new',
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Этот напиток уже есть в ваших активных заказах');

        app(BookingService::class)->create($user, $drink, ['phone' => '+77011234567']);
    }

    public function test_guest_is_redirected_to_login_when_submitting_a_booking(): void
    {
        $drink = Drink::factory()->create(['in_stock' => true]);

        $this->post(route('drinks.bookings.store', $drink), [
            'phone' => '+77011234567',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_authenticated_user_receives_a_phone_validation_error(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $drink = Drink::factory()->create(['in_stock' => true]);

        $this->actingAs($user)
            ->post(route('drinks.bookings.store', $drink), [
                'phone' => '87011234567',
            ])
            ->assertSessionHasErrors([
                'phone' => 'Телефон в формате +7XXXXXXXXXX',
            ]);

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_authenticated_user_can_create_a_booking_and_notifies_user_and_admin(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $drink = Drink::factory()->create(['in_stock' => true]);

        Mail::fake();
        Notification::fake();

        $this->actingAs($user)
            ->post(route('drinks.bookings.store', $drink), [
                'phone' => '+77011234567',
                'comment' => 'Без сахара',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'drink_id' => $drink->id,
            'phone' => '+77011234567',
            'comment' => 'Без сахара',
            'status' => 'new',
        ]);

        Mail::assertSent(BookingReceived::class, function (BookingReceived $mail) use ($user): bool {
            return $mail->hasTo($user->email)
                && str_contains($mail->render(), $user->name);
        });

        Notification::assertSentTo($admin, NewBooking::class);
    }

    public function test_admin_status_change_updates_booking_and_notifies_its_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $drink = Drink::factory()->create(['in_stock' => true]);
        $booking = Booking::create([
            'user_id' => $user->id,
            'drink_id' => $drink->id,
            'phone' => '+77011234567',
            'status' => 'new',
        ]);

        Notification::fake();

        $this->actingAs($admin)
            ->patch(route('admin.bookings.update', $booking), ['status' => 'processed'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'processed',
        ]);
        Notification::assertSentTo($user, BookingStatusChanged::class);
    }

    public function test_user_can_mark_their_notifications_as_read(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $drink = Drink::factory()->create(['in_stock' => true]);
        $booking = Booking::create([
            'user_id' => $user->id,
            'drink_id' => $drink->id,
            'phone' => '+77011234567',
            'status' => 'processed',
        ]);
        $user->notify(new BookingStatusChanged($booking));
        $notification = $user->unreadNotifications()->firstOrFail();

        $this->actingAs($user)
            ->post(route('profile.notifications.read'))
            ->assertSessionHas('success');

        $this->assertNotNull($notification->fresh()->read_at);
        $this->actingAs($user)
            ->get(route('profile'))
            ->assertSee('Новых уведомлений нет.');
    }

    public function test_sms_sender_binding_selects_the_configured_implementation(): void
    {
        $this->app->forgetInstance(SmsSender::class);
        config()->set('services.sms.driver', 'log');
        $this->assertInstanceOf(LogSmsSender::class, app(SmsSender::class));

        $this->app->forgetInstance(SmsSender::class);
        config()->set('services.sms.driver', 'null');
        $this->assertInstanceOf(NullSmsSender::class, app(SmsSender::class));
    }

    public function test_only_admin_can_access_booking_management(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $editor = User::factory()->create(['role' => 'editor']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)->get(route('admin.bookings.index'))->assertForbidden();
        $this->actingAs($editor)->get(route('admin.bookings.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.bookings.index'))->assertOk();
    }
}
