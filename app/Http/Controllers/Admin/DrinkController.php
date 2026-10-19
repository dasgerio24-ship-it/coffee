<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        return view('admin.drinks.index', compact('drinks'));
    }

    public function create()
    {
        return view('admin.drinks.create');
    }
}
