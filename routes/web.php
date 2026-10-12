<?php

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
});

Route::get('/drinks/{id}', function (string $id) {
    return view('drink', ['id' => $id]);
})->whereNumber('id')->name('drinks.show');
