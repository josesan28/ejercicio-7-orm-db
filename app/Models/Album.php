<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    protected $fillable = [
        'artist_id', 'genre_id', 'title',
        'release_date', 'cover_image', 'type',
    ];

    protected $casts = [
        'release_date' => 'date',
    ];

    /** Un álbum pertenece a un artista */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    /** Un álbum pertenece a un género */
    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    /** Un álbum tiene muchas canciones */
    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }
}
