<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Desactivar foreign key checks para inserción masiva
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->command->info('Seeding genres...');
        $this->seedGenres();

        $this->command->info('Seeding users...');
        $this->seedUsers();          // 500 usuarios

        $this->command->info('Seeding artists...');
        $this->seedArtists();        // 200 artistas

        $this->command->info('Seeding albums...');
        $this->seedAlbums();         // ~600 álbumes (3 por artista)

        $this->command->info('Seeding songs...');
        $this->seedSongs();          // ~6,000 canciones (10 por álbum)

        $this->command->info('Seeding playlists...');
        $this->seedPlaylists();      // ~1,000 playlists (2 por usuario)

        $this->command->info('Seeding playlist_song...');
        $this->seedPlaylistSongs();  // ~10,000 entradas

        $this->command->info('Seeding follows...');
        $this->seedFollows();        // ~2,000 follows

        $this->command->info('Seeding likes...');
        $this->seedLikes();          // ~5,000 likes

        $this->command->info('Seeding comments...');
        $this->seedComments();       // ~3,000 comentarios

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✅ Seeding completado.');
    }

    // Géneros musicales
    private function seedGenres(): void
    {
        $genres = [
            ['name' => 'Rock',            'description' => 'Género musical de origen anglosajón'],
            ['name' => 'Pop',             'description' => 'Música popular contemporánea'],
            ['name' => 'Jazz',            'description' => 'Género originario de Nueva Orleans'],
            ['name' => 'Hip-Hop',         'description' => 'Cultura y música urbana'],
            ['name' => 'Electronic',      'description' => 'Música generada electrónicamente'],
            ['name' => 'R&B',             'description' => 'Rhythm and Blues'],
            ['name' => 'Classical',       'description' => 'Música clásica occidental'],
            ['name' => 'Reggaeton',       'description' => 'Género urbano latinoamericano'],
            ['name' => 'Metal',           'description' => 'Derivado del rock, más pesado'],
            ['name' => 'Country',         'description' => 'Música country norteamericana'],
            ['name' => 'Latin',           'description' => 'Géneros de América Latina'],
            ['name' => 'Blues',           'description' => 'Raíces afroamericanas'],
        ];

        DB::table('genres')->insert(array_map(function ($g) {
            return array_merge($g, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }, $genres));
    }

    // Usuarios
    private function seedUsers(): void
    {
        $firstNames = ['Alejandro','María','Carlos','Sofía','Luis','Valentina','Diego','Isabella','Andrés','Camila',
                        'Juan','Daniela','Sergio','Paula','Miguel','Natalia','Roberto','Laura','Fernando','Ana'];
        $lastNames  = ['García','López','Martínez','Rodríguez','Hernández','González','Pérez','Sánchez','Ramírez','Torres'];

        $batch = [];
        for ($i = 1; $i <= 500; $i++) {
            $first  = $firstNames[array_rand($firstNames)];
            $last   = $lastNames[array_rand($lastNames)];
            $batch[] = [
                'name'       => "$first $last",
                'username'   => strtolower($first) . $i,
                'email'      => strtolower($first) . $i . '@music.test',
                'password'   => Hash::make('password'),
                'bio'        => "Fan de la música. Usuario #$i",
                'is_active'  => rand(0, 9) > 0, // 90% activos
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now(),
            ];
        }
        // Insertar en chunks para no saturar la memoria
        foreach (array_chunk($batch, 100) as $chunk) {
            DB::table('users')->insert($chunk);
        }
    }

    // Artistas
    private function seedArtists(): void
    {
        $countries = ['USA', 'UK', 'Colombia', 'España', 'México', 'Brasil', 'Argentina', 'Francia', 'Alemania', 'Japón'];
        $adjectives = ['The', 'DJ', 'MC', 'Los', 'Las', 'El', 'La', ''];
        $nouns = ['Waves','Night','Stars','Echo','Pulse','Storm','Fire','Dream','Soul','Vibe',
                  'Kings','Queens','Wolves','Angels','Ghosts','Riders','Shadows','Lights','Voices','Thunder'];

        $batch = [];
        for ($i = 1; $i <= 200; $i++) {
            $adj  = $adjectives[array_rand($adjectives)];
            $noun = $nouns[array_rand($nouns)];
            $name = trim("$adj $noun") . " " . $i;

            $batch[] = [
                'name'        => $name,
                'country'     => $countries[array_rand($countries)],
                'bio'         => "Artista musical destacado. Número $i en nuestra plataforma.",
                'formed_year' => rand(1970, 2022),
                'is_active'   => rand(0, 9) > 1, // 80% activos
                'created_at'  => now()->subDays(rand(0, 500)),
                'updated_at'  => now(),
            ];
        }
        foreach (array_chunk($batch, 100) as $chunk) {
            DB::table('artists')->insert($chunk);
        }
    }

    // Álbumes: 3 por artista = 600
    private function seedAlbums(): void
    {
        $genreCount = DB::table('genres')->count();
        $albumTypes = ['album', 'ep', 'single'];
        $words = ['Midnight','Eternal','Electric','Golden','Dark','Bright','Lost','Found','Rise','Fall',
                  'Blue','Red','Black','White','Shadow','Light','Dream','Storm','Fire','Ice'];

        $batch = [];
        for ($artistId = 1; $artistId <= 200; $artistId++) {
            $numAlbums = rand(2, 5);
            for ($j = 0; $j < $numAlbums; $j++) {
                $title = $words[array_rand($words)] . ' ' . $words[array_rand($words)];
                $batch[] = [
                    'artist_id'    => $artistId,
                    'genre_id'     => rand(1, $genreCount),
                    'title'        => $title,
                    'release_date' => now()->subDays(rand(30, 3650))->toDateString(),
                    'type'         => $albumTypes[array_rand($albumTypes)],
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
        }
        foreach (array_chunk($batch, 100) as $chunk) {
            DB::table('albums')->insert($chunk);
        }
    }

    // Canciones: 10 por álbum = 6,000+
    private function seedSongs(): void
    {
        $albums     = DB::table('albums')->select('id', 'artist_id', 'genre_id')->get();
        $genreCount = DB::table('genres')->count();
        $songTitles = ['Never','Always','Forever','Tonight','Yesterday','Tomorrow','Alone','Together',
                       'Lost','Found','Running','Falling','Rising','Breaking','Burning','Fading',
                       'Dreaming','Waking','Crying','Laughing'];

        $batch = [];
        foreach ($albums as $album) {
            $numSongs = rand(6, 14);
            for ($t = 1; $t <= $numSongs; $t++) {
                $word  = $songTitles[array_rand($songTitles)];
                $batch[] = [
                    'artist_id'        => $album->artist_id,
                    'album_id'         => $album->id,
                    'genre_id'         => $album->genre_id ?? rand(1, $genreCount),
                    'title'            => "$word (Track $t)",
                    'duration_seconds' => rand(120, 360),
                    'play_count'       => rand(0, 500000),
                    'is_explicit'      => rand(0, 4) === 0,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
        }
        foreach (array_chunk($batch, 200) as $chunk) {
            DB::table('songs')->insert($chunk);
        }
    }

    // Playlists: 2 por usuario = 1,000
    private function seedPlaylists(): void
    {
        $names = ['Mis favoritas','Road Trip','Gym Mix','Chill Vibes','Party Time','Late Night',
                  'Morning Coffee','Study Focus','Workout','Throwbacks','Top Hits','New Discoveries'];

        $batch = [];
        for ($userId = 1; $userId <= 500; $userId++) {
            $num = rand(1, 3);
            for ($p = 0; $p < $num; $p++) {
                $batch[] = [
                    'user_id'     => $userId,
                    'name'        => $names[array_rand($names)] . ' ' . $userId,
                    'description' => 'Playlist creada por el usuario ' . $userId,
                    'is_public'   => rand(0, 1),
                    'created_at'  => now()->subDays(rand(0, 300)),
                    'updated_at'  => now(),
                ];
            }
        }
        foreach (array_chunk($batch, 100) as $chunk) {
            DB::table('playlists')->insert($chunk);
        }
    }

    // Playlist → Song (muchos a muchos) = 10,000+ registros
    private function seedPlaylistSongs(): void
    {
        $playlistIds = DB::table('playlists')->pluck('id')->toArray();
        $songIds     = DB::table('songs')->pluck('id')->toArray();

        $batch = [];
        $seen  = [];

        $target = 12000;
        $attempts = 0;

        while (count($batch) < $target && $attempts < $target * 3) {
            $attempts++;
            $playlistId = $playlistIds[array_rand($playlistIds)];
            $songId     = $songIds[array_rand($songIds)];
            $key        = "$playlistId-$songId";

            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $batch[] = [
                'playlist_id' => $playlistId,
                'song_id'     => $songId,
                'position'    => rand(1, 50),
                'added_at'    => now()->subDays(rand(0, 200)),
            ];
        }

        foreach (array_chunk($batch, 300) as $chunk) {
            DB::table('playlist_song')->insert($chunk);
        }
    }

    // Follows (usuarios siguen artistas) = 2,000
    private function seedFollows(): void
    {
        $userIds   = DB::table('users')->pluck('id')->toArray();
        $artistIds = DB::table('artists')->pluck('id')->toArray();

        $batch = [];
        $seen  = [];

        $target   = 3000;
        $attempts = 0;

        while (count($batch) < $target && $attempts < $target * 3) {
            $attempts++;
            $userId   = $userIds[array_rand($userIds)];
            $artistId = $artistIds[array_rand($artistIds)];
            $key      = "$userId-$artistId";

            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $batch[] = [
                'user_id'     => $userId,
                'artist_id'   => $artistId,
                'followed_at' => now()->subDays(rand(0, 365)),
            ];
        }

        foreach (array_chunk($batch, 200) as $chunk) {
            DB::table('follows')->insert($chunk);
        }
    }

    // Likes = 5,000
    private function seedLikes(): void
    {
        $userIds = DB::table('users')->pluck('id')->toArray();
        $songIds = DB::table('songs')->pluck('id')->toArray();

        $batch = [];
        $seen  = [];

        $target   = 5000;
        $attempts = 0;

        while (count($batch) < $target && $attempts < $target * 3) {
            $attempts++;
            $userId = $userIds[array_rand($userIds)];
            $songId = $songIds[array_rand($songIds)];
            $key    = "$userId-$songId";

            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $batch[] = [
                'user_id'  => $userId,
                'song_id'  => $songId,
                'liked_at' => now()->subDays(rand(0, 200)),
            ];
        }

        foreach (array_chunk($batch, 200) as $chunk) {
            DB::table('likes')->insert($chunk);
        }
    }

    // Comentarios = 3,000
    private function seedComments(): void
    {
        $userIds = DB::table('users')->pluck('id')->toArray();
        $songIds = DB::table('songs')->pluck('id')->toArray();

        $phrases = [
            '¡Me encanta esta canción!',
            'Una obra maestra.',
            'La escucho en repeat.',
            'El solo de guitarra es increíble.',
            'El beat está brutal.',
            'Me recuerda a mi infancia.',
            'Merece más reproducciones.',
            'No me canso de escucharla.',
            'El artista rompió con esta.',
            'Perfecta para el gym.',
            'La mejor del álbum sin duda.',
            'Me llega directo al alma.',
        ];

        $batch = [];
        for ($i = 0; $i < 3000; $i++) {
            $batch[] = [
                'user_id'    => $userIds[array_rand($userIds)],
                'song_id'    => $songIds[array_rand($songIds)],
                'body'       => $phrases[array_rand($phrases)],
                'created_at' => now()->subDays(rand(0, 300)),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($batch, 300) as $chunk) {
            DB::table('comments')->insert($chunk);
        }
    }
}
