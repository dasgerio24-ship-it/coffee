<?php

namespace App\Http\Controllers;

use App\Events\BookingCreated;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Drink;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;

class BookingController extends Controller
{
    public function store(
        StoreBookingRequest $request,
        Drink $drink,
        BookingService $service,
    ): RedirectResponse {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        $booking = $service->create($user, $drink, $request->validated());
        BookingCreated::dispatch($booking);

        return back()->with('success', "Заказ №{$booking->id} отправлен");
    }
}
