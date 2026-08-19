<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameMedia extends Model
{
    use HasUuids;

    protected $table = 'game_media';

    protected $fillable = [
        'memory_id',
        'box_score_summary',
        'box_score_url',
        'video_title',
        'video_url',
        'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function memory(): BelongsTo
    {
        return $this->belongsTo(Memory::class);
    }
}
