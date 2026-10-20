<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Drink;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DrinkController extends Controller
{
    public function index(Request $request)
    {
        [$query, $sort, $term] = $this->catalogQuery($request);
        $drinks = $query->paginate(9)->withQueryString();
        $categories = Category::withCount('drinks')->orderBy('name')->get();

        return view('drinks.index', compact('drinks', 'categories', 'sort', 'term'));
    }

    public function pdf(Request $request)
    {
        [$query] = $this->catalogQuery($request);
        $drinks = $query->get();

        return Pdf::loadView('pdf.catalog', compact('drinks'))
            ->setPaper('a4')
            ->download('catalog-'.now()->format('Y-m-d').'.pdf');
    }

    public function show(Drink $drink)
    {
        $drink->load('category');
        $qrImage = (new PngWriter)->write(new QrCode(
            data: route('drinks.show', $drink),
            size: 200,
            margin: 10,
        ))->getDataUri();
        $relatedDrinks = $drink->category->drinks()
            ->where('id', '!=', $drink->id)
            ->take(3)
            ->get();

        return view('drinks.show', compact('drink', 'relatedDrinks', 'qrImage'));
    }

    public function attachment(Drink $drink)
    {
        abort_if(! $drink->attachment || ! Storage::disk('local')->exists($drink->attachment), 404);

        return Storage::disk('local')->download($drink->attachment, 'Техкарта-'.$drink->id.'.pdf');
    }

    /**
     * @return array{0: Builder<Drink>, 1: string, 2: string|null}
     */
    private function catalogQuery(Request $request): array
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

        return [$query, $sort, $term];
    }
}
