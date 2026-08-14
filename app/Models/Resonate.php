<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resonate extends Model
{
    protected $fillable = [
        'memory_id',
        'ip_hash',
    ];

    protected $hidden = [
        'ip_hash',
    ];

    public function memory(): BelongsTo
    {
        return $this->belongsTo(Memory::class);
    }
}
