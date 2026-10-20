<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DrinkController as AdminDrinkController;
use App\Http\Controllers\Admin\LogoutController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DrinkController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/contacts', [PageController::class, 'contacts'])->name('contacts');

Route::get('/hello/{name}', [PageController::class, 'hello'])->name('hello');

Route::get('/drinks', [DrinkController::class, 'index'])->name('drinks.index');
Route::get('/drinks/{drink}', [DrinkController::class, 'show'])->whereNumber('drink')->name('drinks.show');
Route::get('/categories/{category}', [CategoryController::class, 'show'])
    ->whereNumber('category')
    ->name('categories.show');

Route::get('/rules', [PageController::class, 'rules'])->name('rules');
Route::redirect('/main', '/')->name('main');

Route::get('/booking', [PageController::class, 'booking'])->middleware('hours:9,18')->name('booking');

Route::prefix('admin')->name('admin.')->middleware('admin.key')->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/drinks', [AdminDrinkController::class, 'index'])->name('drinks');

    Route::get('/drinks/create', [AdminDrinkController::class, 'create'])->name('drinks.create');

    Route::get('/logout', LogoutController::class)->withoutMiddleware('admin.key')->name('logout');
});
