<?php

namespace App\Jobs;

use App\Models\Drink;
use App\Models\User;
use App\Notifications\ExportReady;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class GenerateCatalogPdf implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public User $user) {}

    public function handle(): void
    {
        $drinks = Drink::query()->with('category')->orderBy('title')->get();
        $pdf = Pdf::loadView('pdf.catalog', compact('drinks'))
            ->setPaper('a4');
        $file = 'catalog-'.now()->format('Y-m-d-His').'.pdf';
        $path = "exports/{$file}";

        if (! Storage::disk('local')->put($path, $pdf->output())) {
            throw new RuntimeException("Не удалось сохранить PDF-каталог: {$path}");
        }

        $this->user->notify(new ExportReady($file));
    }

    public function failed(Throwable $exception): void
    {
        Log::error('GenerateCatalogPdf: '.$exception->getMessage());
    }
}
