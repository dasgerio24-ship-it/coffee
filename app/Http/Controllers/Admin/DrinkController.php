<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DrinkController extends Controller
{
    public function index()
    {
        $items = config('catalog.items');

        return view('admin.drinks.index', compact('items'));
    }

    public function create()
    {
        return view('admin.drinks.create');
    }
}
