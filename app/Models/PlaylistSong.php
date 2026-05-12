<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para la tabla pivot playlist_song.
 * Extiende Pivot (no Model) porque es una tabla intermedia.
 * Esto permite definir $fillable, $casts y relaciones sobre el pivot.
 */
class PlaylistSong extends Pivot
{
    protected $table = 'playlist_song';

    // La tabla no tiene columnas created_at/updated_at, solo added_at
    public $timestamps = false;

    protected $fillable = ['playlist_id', 'song_id', 'position', 'added_at'];

    protected $casts = [
        'position' => 'integer',
        'added_at' => 'datetime',
    ];

    /** El pivot pertenece a una playlist */
    public function playlist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class);
    }

    /** El pivot pertenece a una canción */
    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }
}
