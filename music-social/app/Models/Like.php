<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para la tabla pivot likes.
 * Extiende Pivot porque representa la relación muchos-a-muchos
 * entre User y Song.
 * La tabla solo tiene liked_at, no created_at/updated_at.
 */
class Like extends Pivot
{
    protected $table = 'likes';

    public $timestamps = false;

    protected $fillable = ['user_id', 'song_id', 'liked_at'];

    protected $casts = [
        'liked_at' => 'datetime',
    ];

    /** Un like pertenece a un usuario */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Un like pertenece a una canción */
    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }
}
