<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DrinkController extends Controller
{
    public function index()
    {
        $drinks = DB::table('drinks')
            ->join('categories', 'drinks.category_id', '=', 'categories.id')
            ->select('drinks.*', 'categories.name as category')
            ->orderBy('title')
            ->get();

        return view('drinks.index', compact('drinks'));
    }

    public function show(int $id)
    {
        $drink = DB::table('drinks')
            ->join('categories', 'drinks.category_id', '=', 'categories.id')
            ->select('drinks.*', 'categories.name as category')
            ->where('drinks.id', $id)
            ->first();

        abort_if(! $drink, 404);

        return view('drinks.show', compact('drink'));
    }
}
