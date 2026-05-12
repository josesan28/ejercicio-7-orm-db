<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Song extends Model
{
    protected $fillable = [
        'artist_id', 'album_id', 'genre_id',
        'title', 'duration_seconds', 'play_count', 'is_explicit',
    ];

    protected $casts = [
        'is_explicit'      => 'boolean',
        'duration_seconds' => 'integer',
        'play_count'       => 'integer',
    ];

    /** Una canción pertenece a un artista */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    /** Una canción pertenece a un álbum (puede ser null) */
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    /** Una canción pertenece a un género */
    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    /** Una canción pertenece a muchas playlists */
    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class, 'playlist_song')
                    ->using(PlaylistSong::class)
                    ->withPivot(['position', 'added_at']);
    }

    /** Una canción tiene muchos comentarios */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /** Una canción tiene muchos likes */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }
}
