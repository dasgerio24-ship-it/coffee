<?php

namespace App\Models;

use Database\Factories\DrinkFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Drink extends Model
{
    /** @use HasFactory<DrinkFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'price',
        'volume',
        'description',
        'in_stock',
        'calories',
        'image',
        'attachment',
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

    public function getImageUrlAttribute(): string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::disk('public')->url($this->image);
        }

        return asset('images/no-drink.png');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
