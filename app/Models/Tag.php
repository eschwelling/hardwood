<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'type',
        'name',
        'slug',
    ];

    public function memories(): BelongsToMany
    {
        return $this->belongsToMany(Memory::class, 'memory_tag');
    }
}
