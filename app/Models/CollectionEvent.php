<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CollectionEvent extends Model
{
    protected $fillable = ['collection_point_id', 'user_id', 'event_date', 'notes'];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    public function collectionPoint(): BelongsTo
    {
        return $this->belongsTo(CollectionPoint::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(CollectionDetail::class);
    }
}
