<?php

namespace App\Models;

use Database\Factories\DrinkFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Drink extends Model
{
    /** @use HasFactory<DrinkFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'price',
        'volume',
        'description',
        'in_stock',
        'calories',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'volume' => 'integer',
            'calories' => 'integer',
            'in_stock' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeInStock(Builder $query): void
    {
        $query->where('in_stock', true);
    }

    public function scopeSearch(Builder $query, ?string $term): void
    {
        if (filled($term)) {
            $query->where(function (Builder $query) use ($term): void {
                $query->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }
    }
}
