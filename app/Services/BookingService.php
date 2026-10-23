<?php

namespace App\Services;

use App\Jobs\RemindAboutBooking;
use App\Models\Booking;
use App\Models\Drink;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    /**
     * @param  array{phone: string, comment?: string|null}  $data
     */
    public function create(User $user, Drink $drink, array $data): Booking
    {
        $booking = DB::transaction(function () use ($user, $drink, $data): Booking {
            User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $lockedDrink = Drink::query()->whereKey($drink->id)->lockForUpdate()->firstOrFail();

            if (! $lockedDrink->in_stock) {
                throw ValidationException::withMessages([
                    'drink' => 'Напиток сейчас недоступен для заказа',
                ]);
            }

            $activeBookings = Booking::query()
                ->where('user_id', $user->id)
                ->where('status', 'new');

            if ($activeBookings->count() >= 3) {
                throw ValidationException::withMessages([
                    'drink' => 'Нельзя оформить больше 3 активных заказов',
                ]);
            }

            if ($activeBookings->where('drink_id', $lockedDrink->id)->exists()) {
                throw ValidationException::withMessages([
                    'drink' => 'Этот напиток уже есть в ваших активных заказах',
                ]);
            }

            return Booking::create([
                'user_id' => $user->id,
                'drink_id' => $lockedDrink->id,
                'phone' => $data['phone'],
                'comment' => $data['comment'] ?? null,
                'status' => 'new',
            ]);
        });

        RemindAboutBooking::dispatch($booking)->delay(now()->addMinutes(2));

        return $booking;
    }
}
