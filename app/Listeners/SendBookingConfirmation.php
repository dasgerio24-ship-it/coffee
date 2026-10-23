<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Mail\BookingReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmation implements ShouldQueue
{
    public function handle(BookingCreated $event): void
    {
        $booking = $event->booking->load(['user', 'drink']);

        Mail::to($booking->user)->send(new BookingReceived($booking));
    }
}
