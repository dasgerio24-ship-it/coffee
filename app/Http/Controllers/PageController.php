<?php

namespace App\Http\Controllers;

use App\Models\Drink;

class PageController extends Controller
{
    public function home()
    {
        $drinks = Drink::with('category')
            ->orderBy('title')
            ->take(3)
            ->get();

        return view('home', compact('drinks'));
    }

    public function about()
    {
        $name = 'Зонингрин Максим';
        $group = '3ПоВТ-24Б';

        return view('about', compact('name', 'group'));
    }

    public function contacts()
    {
        $address = 'ул. Кофейная, 10';
        $phone = '+7705876769';
        $email = 'hello@coffee.example';

        return view('contacts', compact('address', 'phone', 'email'));
    }

    public function rules()
    {
        return view('rules');
    }

    public function hello(string $name)
    {
        return view('hello', compact('name'));
    }

    public function booking()
    {
        return view('booking');
    }
}
