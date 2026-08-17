<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Mixtape extends Model
{
    use HasUuids;

    public const MAX_TRACKS = 12;
    public const MIN_TRACKS = 2;

    protected $fillable = [
        'title',
        'team_name',
        'team_color',
        'ip_hash',
        'status',
    ];

    protected $hidden = [
        'ip_hash',
    ];

    public function memories(): BelongsToMany
    {
        return $this->belongsToMany(Memory::class, 'mixtape_memory')
            ->withPivot('position')
            ->orderBy('mixtape_memory.position');
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
