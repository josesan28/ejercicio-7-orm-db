<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{
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
