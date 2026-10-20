<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    public function show(Category $category): View
    {
        $drinks = $category->drinks()
            ->orderBy('title')
            ->get();

        return view('categories.show', compact('category', 'drinks'));
    }
}
