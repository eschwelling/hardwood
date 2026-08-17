<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('memory_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('box_score_summary')->nullable();
            $table->string('box_score_url')->nullable();
            $table->string('video_title')->nullable();
            $table->string('video_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_media');
    }
};
