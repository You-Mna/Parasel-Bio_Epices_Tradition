<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PointDeVente extends Model
{
    use HasFactory;

    protected $table = 'points_de_vente';

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'city',
    ];

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'point_de_vente_id');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->code ? "{$this->name} ({$this->code})" : $this->name;
    }
}
