<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Venue extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function memories(): BelongsToMany
    {
        return $this->belongsToMany(Memory::class, 'memory_venue');
    }
}
