<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingReminder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class RemindAboutBooking implements ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    public function __construct(public Booking $booking) {}

    public function handle(): void
    {
        $booking = $this->booking->fresh();

        if ($booking && $booking->status === 'new') {
            Notification::send(
                User::query()->where('role', 'admin')->get(),
                new BookingReminder($booking->load(['user', 'drink'])),
            );
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Не удалось отправить напоминание о заказе после всех попыток.', [
            'booking_id' => $this->booking->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
