<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class CollectionDetail extends Model
{
    protected $fillable = ['collection_event_id', 'material_id', 'weight_kg'];

    protected $casts = [
        'weight_kg' => 'decimal:2',
    ];

    public function collectionEvent(): BelongsTo
    {
        return $this->belongsTo(CollectionEvent::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
