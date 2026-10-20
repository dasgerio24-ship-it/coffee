<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDrinkRequest;
use App\Http\Requests\UpdateDrinkRequest;
use App\Models\Category;
use App\Models\Drink;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DrinkController extends Controller
{
    public function index(): View
    {
        $drinks = Drink::with('category')
            ->latest()
            ->paginate(15);

        return view('admin.drinks.index', compact('drinks'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.drinks.create', compact('categories'));
    }

    public function store(StoreDrinkRequest $request): RedirectResponse
    {
        Drink::create($request->validated());

        return redirect()
            ->route('admin.drinks.index')
            ->with('success', 'Напиток добавлен');
    }

    public function edit(Drink $drink): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.drinks.edit', compact('drink', 'categories'));
    }

    public function update(UpdateDrinkRequest $request, Drink $drink): RedirectResponse
    {
        $drink->update($request->validated());

        return redirect()
            ->route('admin.drinks.index')
            ->with('success', 'Изменения сохранены');
    }

    public function destroy(Drink $drink): RedirectResponse
    {
        $drink->delete();

        return back()->with('success', 'Напиток удалён');
    }
}
