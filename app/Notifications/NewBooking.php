<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBooking extends Notification
{
    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Новый заказ №{$this->booking->id}")
            ->line("Кто: {$this->booking->user->name}")
            ->line("Напиток: {$this->booking->drink->title}")
            ->line("Телефон: {$this->booking->phone}")
            ->action('Открыть заказы', route('admin.bookings.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'text' => "Новый заказ №{$this->booking->id}: {$this->booking->user->name} — {$this->booking->drink->title}",
        ];
    }
}
