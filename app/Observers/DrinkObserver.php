<?php

namespace App\Observers;

use App\Models\Drink;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DrinkObserver
{
    public function created(Drink $drink): void
    {
        Log::info("Создан напиток #{$drink->id}: {$drink->title}");
    }

    public function updated(Drink $drink): void
    {
        Log::info("Изменён напиток #{$drink->id}", $drink->getChanges());
    }

    public function deleting(Drink $drink): void
    {
        if ($drink->image) {
            Storage::disk('public')->delete($drink->image);
        }

        if ($drink->attachment) {
            Storage::disk('local')->delete($drink->attachment);
        }

        Log::info('Напиток удалён', [
            'drink_id' => $drink->id,
            'title' => $drink->title,
        ]);
    }
}
