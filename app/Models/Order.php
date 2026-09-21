<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'client_name', 'client_phone', 'client_email',
        'status', 'total', 'payment_method', 'payment_reference',
    ];

    /**
     * Nom du client (compte existant ou client hors ligne).
     */
    public function getClientNameAttribute(): ?string
    {
        if ($this->user_id && $this->user) {
            return $this->user->name;
        }
        return $this->attributes['client_name'] ?? null;
    }

    /**
     * Téléphone du client (compte existant ou client hors ligne).
     */
    public function getClientPhoneAttribute(): ?string
    {
        if ($this->user_id && $this->user) {
            return $this->user->phone ?? null;
        }
        return $this->attributes['client_phone'] ?? null;
    }

    /**
     * Email du client (compte existant ou client hors ligne).
     */
    public function getClientEmailAttribute(): ?string
    {
        if ($this->user_id && $this->user) {
            return $this->user->email ?? null;
        }
        return $this->attributes['client_email'] ?? null;
    }

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }
}

