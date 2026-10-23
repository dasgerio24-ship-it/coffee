<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DrinkController as AdminDrinkController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DrinkController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/contacts', [PageController::class, 'contacts'])->name('contacts');

Route::get('/hello/{name}', [PageController::class, 'hello'])->name('hello');

Route::get('/drinks', [DrinkController::class, 'index'])->name('drinks.index');
Route::get('/drinks/pdf', [DrinkController::class, 'pdf'])->name('drinks.pdf');
Route::get('/drinks/{drink}/attachment', [DrinkController::class, 'attachment'])
    ->whereNumber('drink')
    ->middleware('auth')
    ->name('drinks.attachment');
Route::get('/drinks/{drink}', [DrinkController::class, 'show'])->whereNumber('drink')->name('drinks.show');
Route::post('/drinks/{drink}/bookings', [BookingController::class, 'store'])
    ->whereNumber('drink')
    ->middleware('auth')
    ->name('drinks.bookings.store');
Route::get('/categories/{category}', [CategoryController::class, 'show'])
    ->whereNumber('category')
    ->name('categories.show');

Route::get('/rules', [PageController::class, 'rules'])->name('rules');
Route::redirect('/main', '/')->name('main');

Route::get('/booking', [PageController::class, 'booking'])->middleware('hours:9,18')->name('booking');

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get('/profile', ProfileController::class)->middleware('auth')->name('profile');
Route::post('/profile/notifications/read', [ProfileController::class, 'markNotificationsRead'])
    ->middleware('auth')
    ->name('profile.notifications.read');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:admin-panel'])->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/read', [AdminNotificationController::class, 'markAllRead'])->name('notifications.read');
    Route::get('/bookings', [AdminBookingController::class, 'index'])
        ->middleware('can:manage-bookings')
        ->name('bookings.index');
    Route::patch('/bookings/{booking}', [AdminBookingController::class, 'update'])
        ->middleware('can:manage-bookings')
        ->name('bookings.update');

    Route::resource('drinks', AdminDrinkController::class)->except('show');
    Route::resource('categories', AdminCategoryController::class)->except('show');
});
