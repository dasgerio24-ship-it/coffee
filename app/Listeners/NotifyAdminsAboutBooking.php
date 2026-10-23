<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Models\User;
use App\Notifications\NewBooking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAdminsAboutBooking implements ShouldQueue
{
    public function handle(BookingCreated $event): void
    {
        $booking = $event->booking->load(['user', 'drink']);
        $admins = User::query()->where('role', 'admin')->get();

        Notification::send($admins, new NewBooking($booking));
    }
}
