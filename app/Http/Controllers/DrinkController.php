<?php

namespace App\Http\Controllers;

class DrinkController extends Controller
{
    public function index()
    {
        $items = config('catalog.items');

        return view('drinks.index', compact('items'));
    }

    public function show(int $id)
    {
        $drink = config("catalog.items.$id");

        abort_if(is_null($drink), 404);

        return view('drinks.show', compact('id', 'drink'));
    }
}
