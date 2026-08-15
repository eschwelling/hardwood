<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Annotation extends Model
{
    use HasUuids;

    protected $fillable = [
        'memory_id',
        'start_offset',
        'end_offset',
        'body',
        'ip_hash',
        'status',
    ];

    protected $hidden = [
        'ip_hash',
    ];

    public function memory(): BelongsTo
    {
        return $this->belongsTo(Memory::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
