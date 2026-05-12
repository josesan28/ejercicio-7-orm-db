<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para la tabla pivot follows.
 * Extiende Pivot porque representa la relación muchos-a-muchos
 * entre User y Artist.
 *
 * IMPORTANTE: la tabla tiene solo followed_at, NO created_at/updated_at.
 * Por eso $timestamps = false y NO se usa withTimestamps() en las relaciones.
 */
class Follow extends Pivot
{
    protected $table = 'follows';

    public $timestamps = false;

    protected $fillable = ['user_id', 'artist_id', 'followed_at'];

    protected $casts = [
        'followed_at' => 'datetime',
    ];

    /** El follow pertenece a un usuario */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** El follow pertenece a un artista */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }
}
