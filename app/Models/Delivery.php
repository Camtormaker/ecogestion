<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delivery extends Model
{
    protected $fillable = ['recycling_center_id', 'user_id', 'delivery_date', 'receipt_url', 'notes'];

    protected $casts = [
        'delivery_date' => 'datetime',
    ];

    public function recyclingCenter(): BelongsTo
    {
        return $this->belongsTo(RecyclingCenter::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(DeliveryDetail::class);
    }
}
