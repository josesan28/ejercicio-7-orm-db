<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Genre extends Model
{
    protected $fillable = ['name', 'description'];

    /** Un género tiene muchos álbumes */
    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    /** Un género tiene muchas canciones */
    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }
}
