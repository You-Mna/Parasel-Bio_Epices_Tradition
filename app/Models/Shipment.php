<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    public const STATUS_EN_COURS = 'en_cours';
    public const STATUS_EXPEDIEE = 'expediee';
    public const STATUS_ANNULEE = 'annulee';

    public const STATUSES = [
        self::STATUS_EN_COURS => 'En cours',
        self::STATUS_EXPEDIEE => 'Expédiée',
        self::STATUS_ANNULEE => 'Annulée',
    ];

    protected $fillable = [
        'order_id',
        'point_de_vente_id',
        'status',
        'shipped_at',
        'estimated_delivery_at',
        'delivery_method',
        'tracking_reference',
        'notes',
    ];

    protected $casts = [
        'shipped_at' => 'date',
        'estimated_delivery_at' => 'date',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function pointDeVente(): BelongsTo
    {
        return $this->belongsTo(PointDeVente::class, 'point_de_vente_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class, 'shipment_id');
    }

    /**
     * Estimation du montant total de l'expédition (basé sur les prix produits / variantes).
     */
    public function getEstimatedTotalAttribute(): float
    {
        $this->loadMissing('items.product');

        $total = 0.0;

        foreach ($this->items as $item) {
            if (!$item->product) {
                continue;
            }

            $product = $item->product;
            $unitPrice = (float) $product->price;

            // Si Parasel-Bio Marinade avec variante, on tente de prendre le prix de la variante
            if ($product->name === 'Parasel-Bio Marinade' && $item->variant_size && is_array($product->variants)) {
                foreach ($product->variants as $v) {
                    if (($v['size'] ?? '') === $item->variant_size && isset($v['price'])) {
                        $unitPrice = (float) $v['price'];
                        break;
                    }
                }
            }

            $total += $unitPrice * (int) $item->quantity;
        }

        return $total;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function isForPointDeVente(): bool
    {
        return (bool) $this->point_de_vente_id;
    }
}
