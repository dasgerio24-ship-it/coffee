<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Mail\BookingReceived;
use App\Models\Drink;
use App\Models\User;
use App\Notifications\NewBooking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

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
        $booking->load(['user', 'drink']);
        Mail::to($user)->send(new BookingReceived($booking));
        Notification::send(
            User::query()->where('role', 'admin')->get(),
            new NewBooking($booking),
        );

        return back()->with('success', "Заказ №{$booking->id} отправлен");
    }
}
