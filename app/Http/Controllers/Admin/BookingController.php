<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBookingStatusRequest;
use App\Models\Booking;
use App\Notifications\BookingStatusChanged;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(): View
    {
        return view('admin.bookings.index', [
            'bookings' => Booking::query()
                ->with(['user', 'drink'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function update(UpdateBookingStatusRequest $request, Booking $booking): RedirectResponse
    {
        $booking->update(['status' => $request->validated('status')]);
        $booking->load('user');
        $booking->user->notify(new BookingStatusChanged($booking));

        return back()->with('success', "Статус заказа №{$booking->id} изменён");
    }
}
