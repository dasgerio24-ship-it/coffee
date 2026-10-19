<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function home()
    {
        $drinks = DB::table('drinks')
            ->join('categories', 'drinks.category_id', '=', 'categories.id')
            ->select('drinks.*', 'categories.name as category')
            ->orderBy('title')
            ->limit(3)
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
