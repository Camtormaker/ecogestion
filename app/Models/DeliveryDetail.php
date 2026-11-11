<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class DeliveryDetail extends Model
{
    protected $fillable = ['delivery_id', 'material_id', 'weight_kg'];

    protected $casts = [
        'weight_kg' => 'decimal:2',
    ];

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
