<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Drink;

class DrinkController extends Controller
{
    public function index()
    {
        $drinks = Drink::with('category')
            ->orderByDesc('id')
            ->get();

        return view('admin.drinks.index', compact('drinks'));
    }

    public function create()
    {
        return view('admin.drinks.create');
    }
}
