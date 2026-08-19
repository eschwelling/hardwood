<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

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

    public function memories(): MorphToMany
    {
        return $this->morphedByMany(Memory::class, 'trackable', 'mixtape_trackables')
            ->withPivot('position')
            ->orderByPivot('position');
    }

    public function dunks(): MorphToMany
    {
        return $this->morphedByMany(Dunk::class, 'trackable', 'mixtape_trackables')
            ->withPivot('position')
            ->orderByPivot('position');
    }

    /**
     * The tape's tracklist in play order, memories and dunks interleaved.
     */
    public function tracks(): Collection
    {
        return $this->memories
            ->concat($this->dunks)
            ->sortBy(fn ($track) => $track->pivot->position)
            ->values();
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
