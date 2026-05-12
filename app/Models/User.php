<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Model
{
    protected $fillable = [
        'name', 'username', 'email', 'password',
        'avatar', 'bio', 'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'password'   => 'hashed',
    ];

    protected $hidden = ['password'];

    /** Un usuario tiene muchas playlists */
    public function playlists(): HasMany
    {
        return $this->hasMany(Playlist::class);
    }

    /** Un usuario tiene muchos comentarios */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /** Un usuario tiene muchos likes */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Los artistas que sigue el usuario.
     * Relación muchos a muchos a través de la tabla follows.
     */
    public function followedArtists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'follows', 'user_id', 'artist_id')
                    ->withPivot('followed_at')
                    ->withTimestamps();
    }

    /** Canciones que le han gustado al usuario */
    public function likedSongs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'likes', 'user_id', 'song_id')
                    ->withPivot('liked_at');
    }
}
