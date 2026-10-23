<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDrinkRequest;
use App\Http\Requests\UpdateDrinkRequest;
use App\Models\Category;
use App\Models\Drink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class DrinkController extends Controller
{
    public function index(): View
    {
        $drinks = Drink::with(['category', 'user'])
            ->latest()
            ->paginate(15);

        return view('admin.drinks.index', compact('drinks'));
    }

    public function create(): View
    {
        Gate::authorize('create', Drink::class);

        $categories = Category::orderBy('name')->get();

        return view('admin.drinks.create', compact('categories'));
    }

    public function store(StoreDrinkRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'attachment', 'remove_image']);

        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('drinks', 'public');
            if (! is_string($image)) {
                throw new RuntimeException('Не удалось сохранить изображение напитка.');
            }
            $data['image'] = $image;
        }

        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('attachments');
            if (! is_string($attachment)) {
                throw new RuntimeException('Не удалось сохранить документ напитка.');
            }
            $data['attachment'] = $attachment;
        }

        Drink::create($data + ['user_id' => $request->user()->id]);

        return redirect()
            ->route('admin.drinks.index')
            ->with('success', 'Напиток добавлен');
    }

    public function edit(Drink $drink): View
    {
        Gate::authorize('update', $drink);

        $categories = Category::orderBy('name')->get();

        return view('admin.drinks.edit', compact('drink', 'categories'));
    }

    public function update(UpdateDrinkRequest $request, Drink $drink): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'attachment', 'remove_image']);
        $oldImage = $drink->image;
        $oldAttachment = $drink->attachment;

        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('drinks', 'public');
            if (! is_string($image)) {
                throw new RuntimeException('Не удалось сохранить изображение напитка.');
            }
            $data['image'] = $image;
        } elseif ($request->boolean('remove_image')) {
            $data['image'] = null;
        }

        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('attachments');
            if (! is_string($attachment)) {
                throw new RuntimeException('Не удалось сохранить документ напитка.');
            }
            $data['attachment'] = $attachment;
        }

        $drink->update($data);

        if ($oldImage && $oldImage !== $drink->image) {
            Storage::disk('public')->delete($oldImage);
        }
        if ($oldAttachment && $oldAttachment !== $drink->attachment) {
            Storage::disk('local')->delete($oldAttachment);
        }

        return redirect()
            ->route('admin.drinks.index')
            ->with('success', 'Изменения сохранены');
    }

    public function destroy(Drink $drink): RedirectResponse
    {
        Gate::authorize('delete', $drink);

        $drink->delete();

        return back()->with('success', 'Напиток удалён');
    }
}
