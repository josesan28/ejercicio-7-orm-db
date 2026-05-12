<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = ['user_id', 'song_id', 'body'];

    /** Un comentario pertenece a un usuario */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Un comentario pertenece a una canción */
    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }
}
