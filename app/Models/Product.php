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
        'name', 'slug', 'description', 'image', 'price', 'variants', 'stock', 'is_featured', 'status',
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
}

