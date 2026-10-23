<?php

namespace App\Listeners;

use App\Contracts\SmsSender;
use App\Events\BookingCreated;

class SendBookingSms
{
    public function __construct(private readonly SmsSender $smsSender) {}

    public function handle(BookingCreated $event): void
    {
        $booking = $event->booking->loadMissing('drink');

        $this->smsSender->send(
            $booking->phone,
            "Заказ №{$booking->id} принят: {$booking->drink->title}",
        );
    }
}
