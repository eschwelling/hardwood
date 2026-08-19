<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_media', function (Blueprint $table) {
            $table->timestamp('checked_at')->nullable()->after('video_url');
        });
    }

    public function down(): void
    {
        Schema::table('game_media', function (Blueprint $table) {
            $table->dropColumn('checked_at');
        });
    }
};
