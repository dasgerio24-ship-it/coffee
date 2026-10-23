<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class BookingStatusChanged extends Notification
{
    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $status = [
            'processed' => 'обработан',
            'rejected' => 'отклонён',
        ][$this->booking->status] ?? $this->booking->status;

        return [
            'booking_id' => $this->booking->id,
            'text' => "Статус вашего заказа №{$this->booking->id} изменён: {$status}",
        ];
    }
}
