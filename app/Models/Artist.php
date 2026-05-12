<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Artist extends Model
{
    protected $fillable = [
        'name', 'country', 'bio', 'image',
        'formed_year', 'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'formed_year' => 'integer',
    ];

    /** Un artista tiene muchos álbumes */
    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    /** Un artista tiene muchas canciones */
    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }

    /**
     * Usuarios que siguen al artista.
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'artist_id', 'user_id')
                    ->withPivot('followed_at');
    }
}
