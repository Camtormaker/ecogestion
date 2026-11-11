<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CollectionPoint extends Model
{
    protected $fillable = ['name', 'type', 'address', 'contact_person', 'phone', 'status'];

    public function collectionEvents(): HasMany
    {
        return $this->hasMany(CollectionEvent::class);
    }
}
