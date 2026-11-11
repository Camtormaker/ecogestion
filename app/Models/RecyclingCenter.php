<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class RecyclingCenter extends Model
{
    protected $fillable = ['name', 'address', 'contact_person', 'phone', 'email', 'status'];

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }
}
