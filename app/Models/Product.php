<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'price',
        'variants',
        'stock',
        'initial_stock',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'variants' => 'array',
    ];


    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }


    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Ordre d’affichage catalogue (aligné sur la page Produits).
     */
    public function scopeOrderedForCatalog(Builder $query): Builder
    {
        return $query->orderByRaw("
            CASE
                WHEN name = 'Parasel-Bio Marinade' THEN 1
                WHEN name IN ('Xwladjê du Chef Paludier', 'Xladjê du Chef Paludier') THEN 2
                WHEN name = 'Arôme Parasel' THEN 3
                WHEN name = 'ParaStress' THEN 4
                ELSE 5
            END
        ");
    }

    /** Prix affiché (minimum des variantes si présentes). */
    public function displayMinPrice(): float
    {
        $variants = $this->variants;
        if (is_array($variants) && count($variants) > 0) {
            return (float) collect($variants)->min(fn ($v) => (float) ($v['price'] ?? 0));
        }

        return (float) $this->price;
    }

    /** Libellé court type « catégorie » pour les cartes vitrine. */
    public function displayCategoryLabel(): string
    {
        $n = $this->name;
        if (str_contains($n, 'Marinade')) {
            return 'Assaisonnement bio';
        }
        if (str_contains($n, 'Xladjê') || str_contains($n, 'Xwladjê') || str_contains($n, 'Chef Paludier')) {
            return 'Sel marin';
        }
        if (str_contains($n, 'Arôme')) {
            return 'Arôme naturel';
        }
        if (str_contains($n, 'ParaStress') || str_contains($n, 'Stress')) {
            return 'Bien-être';
        }

        return 'Épices bio';
    }
}

