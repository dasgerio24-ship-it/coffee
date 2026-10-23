<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ExportReady extends Notification
{
    public function __construct(public string $file) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'text' => 'PDF-каталог готов: '.$this->file,
            'url' => route('admin.exports.download', $this->file),
        ];
    }
}
