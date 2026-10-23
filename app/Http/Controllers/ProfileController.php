<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        return view('profile', [
            'bookings' => Booking::query()
                ->with('drink')
                ->where('user_id', $user->id)
                ->latest()
                ->get(),
            'notifications' => $user->notifications()->unread()->latest()->get(),
        ]);
    }

    public function markNotificationsRead(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        $user->unreadNotifications->markAsRead();

        return back()->with('success', 'Все уведомления отмечены прочитанными.');
    }
}
