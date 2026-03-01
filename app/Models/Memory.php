<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Memory extends Model
{
    use HasUuids;

    protected $fillable = [
        'body',
        'ip_hash',
        'status',
    ];

    protected $hidden = [
        'ip_hash',
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'memory_tag');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
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
