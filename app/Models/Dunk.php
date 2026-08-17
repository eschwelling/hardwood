<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Dunk extends Model
{
    use HasUuids;

    protected $fillable = ['body', 'ip_hash', 'status'];
    protected $hidden = ['ip_hash'];

    public function mixtapes(): MorphToMany
    {
        return $this->morphToMany(Mixtape::class, 'trackable', 'mixtape_trackables')
            ->withPivot('position');
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
