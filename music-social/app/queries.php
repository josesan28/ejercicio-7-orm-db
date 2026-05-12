<?php
// Consultas Eloquent

use App\Models\Artist;
use App\Models\Song;
use App\Models\User;
use App\Models\Playlist;
use App\Models\Genre;

// CONSULTA 1 — Top 10 canciones más escuchadas de un género
// Filtra canciones por género, ordena por reproducciones desc.
$topSongsRock = Song::whereHas('genre', fn($q) => $q->where('name', 'Rock'))
    ->orderByDesc('play_count')
    ->limit(10)
    ->get(['id', 'title', 'play_count']);

// CONSULTA 2 — Artistas con más de 5 álbumes, ordenados por nombre
// Usa withCount para contar álbumes sin N+1 y filtra con having.
$prolificArtists = Artist::withCount('albums')
    ->having('albums_count', '>=', 5)
    ->orderBy('name')
    ->get();

// CONSULTA 3 — Playlists públicas con más de 10 canciones
// withCount sobre la relación BelongsToMany playlist_song.
$bigPlaylists = Playlist::where('is_public', true)
    ->withCount('songs')
    ->having('songs_count', '>', 10)
    ->orderByDesc('songs_count')
    ->get();

// CONSULTA 4 — Usuarios que siguen al artista con id=1
// Accede a la relación inversa definida en Artist::followers().
$fanbase = Artist::findOrFail(1)
    ->followers()
    ->where('is_active', true)
    ->orderBy('name')
    ->get(['users.id', 'users.name', 'users.username']);

// CONSULTA 5 — Canciones con sus comentarios y el usuario de cada uno
// EAGER LOADING — evita problema N+1
// Sin with(): por cada canción se ejecutaría una query para
// obtener comments y otra para obtener el user de cada comment.
// Con 100 canciones serían 201+ queries.
// Con with('comments.user') se resuelve todo en 3 queries:
//   1. SELECT * FROM songs ...
//   2. SELECT * FROM comments WHERE song_id IN (...)
//   3. SELECT * FROM users WHERE id IN (...)
//
$songsWithComments = Song::with('comments.user')
    ->withCount('comments')
    ->having('comments_count', '>', 0)
    ->orderByDesc('comments_count')
    ->limit(20)
    ->get();

// CONSULTA 6 — Feed: canciones de artistas que sigue el usuario #1
// Obtiene ids de artistas seguidos y busca sus canciones recientes.
$user = User::find(1);

$followedArtistIds = $user->followedArtists()->pluck('artists.id');
// Eager loading: evita N+1 al acceder a artist y album de cada canción
$feed = Song::with(['artist', 'album'])  
    ->whereIn('artist_id', $followedArtistIds)
    ->orderByDesc('created_at')
    ->limit(50)
    ->get();

// CONSULTA 7 — Canciones más likeadas por género
$likedByGenre = Genre::withCount(['songs as total_likes' => function ($q) {
        $q->join('likes', 'likes.song_id', '=', 'songs.id');
    }])
    ->orderByDesc('total_likes')
    ->get();