<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Playlist extends Model
{
    protected $fillable = [
        'user_id', 'name', 'description',
        'is_public', 'cover_image',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /** Una playlist pertenece a un usuario */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Una playlist tiene muchas canciones.
     * Relación muchos a muchos con tabla pivot playlist_song.
     */
    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'playlist_song')
                    ->using(PlaylistSong::class)
                    ->withPivot(['position', 'added_at'])
                    ->orderByPivot('position');
    }
}
