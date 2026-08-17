<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Memory extends Model
{
    use HasUuids;

    protected $fillable = [
        'body',
        'game_date',
        'game_date_precision',
        'ip_hash',
        'status',
    ];

    protected $casts = [
        'game_date' => 'date',
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

    public function resonates(): HasMany
    {
        return $this->hasMany(Resonate::class);
    }

    public function annotations(): HasMany
    {
        return $this->hasMany(Annotation::class);
    }

    public function gameMedia(): HasOne
    {
        return $this->hasOne(GameMedia::class);
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
