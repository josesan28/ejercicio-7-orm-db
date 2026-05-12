<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('song_id')->constrained()->cascadeOnDelete();
            $table->timestamp('liked_at')->useCurrent();

            $table->unique(['user_id', 'song_id']); // un usuario solo puede dar like una vez por canción
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
