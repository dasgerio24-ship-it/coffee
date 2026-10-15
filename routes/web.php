<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/about', function () {
    return view('about', [
        'name' => 'Зонингрин Максим',
        'group' => '3ПоВТ-24Б',
    ]);
})->name('about');

Route::get('/contacts', function () {
    return view('contacts', [
        'address' => 'ул. Кофейная, 10',
        'phone' => '+7 (900) 123-45-67',
        'email' => 'hello@coffee.example',
    ]);
})->name('contacts');

Route::get('/hello/{name}', function (string $name) {
    return view('hello', ['name' => $name]);
})->name('hello');

Route::get('/drinks', function () {
    return view('drinks.index', ['items' => config('catalog.items')]);
})->name('drinks.index');

Route::get('/drinks/{id}', function (string $id) {
    $drink = config("catalog.items.$id");

    abort_if($drink === null, 404);

    return view('drinks.show', ['id' => $id, 'drink' => $drink]);
})->whereNumber('id')->name('drinks.show');

Route::view('/rules', 'rules')->name('rules');
Route::redirect('/main', '/')->name('main');

Route::get('/booking', function () {
    return view('booking');
})->middleware('hours:9,18')->name('booking');

Route::prefix('admin')->name('admin.')->middleware('admin.key')->group(function (): void {
    Route::view('/', 'admin.dashboard')->name('dashboard');

    Route::get('/drinks', function () {
        return view('admin.drinks.index', ['items' => config('catalog.items')]);
    })->name('drinks');

    Route::view('/drinks/create', 'admin.drinks.create')->name('drinks.create');

    Route::get('/logout', function (Request $request) {
        $request->session()->forget('is_admin');

        return redirect()->route('home');
    })->withoutMiddleware('admin.key')->name('logout');
});
