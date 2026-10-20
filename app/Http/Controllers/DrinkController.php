<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Drink;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DrinkController extends Controller
{
    public function index(Request $request)
    {
        $requestedSort = $request->query('sort');
        $sort = is_string($requestedSort) && in_array($requestedSort, ['title', 'price', 'volume', 'newest'], true)
            ? $requestedSort
            : 'title';

        $requestedTerm = $request->query('q');
        $term = is_string($requestedTerm) ? $requestedTerm : null;
        $requestedCategory = $request->query('category');
        $categoryId = is_string($requestedCategory) ? $requestedCategory : null;

        $query = Drink::with('category')
            ->search($term)
            ->when(filled($categoryId), function (Builder $query) use ($categoryId): void {
                $query->where('category_id', $categoryId);
            });

        if ($sort === 'newest') {
            $query->latest();
        } else {
            $query->orderBy($sort);
        }

        $drinks = $query->paginate(9)->withQueryString();
        $categories = Category::withCount('drinks')->orderBy('name')->get();

        return view('drinks.index', compact('drinks', 'categories', 'sort', 'term'));
    }

    public function show(Drink $drink)
    {
        $drink->load('category');
        $relatedDrinks = $drink->category->drinks()
            ->where('id', '!=', $drink->id)
            ->take(3)
            ->get();

        return view('drinks.show', compact('drink', 'relatedDrinks'));
    }
}
