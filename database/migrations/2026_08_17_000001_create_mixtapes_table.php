<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mixtapes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('team_name')->nullable();
            $table->string('team_color')->nullable();
            $table->string('ip_hash')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });

        Schema::create('mixtape_memory', function (Blueprint $table) {
            $table->foreignUuid('mixtape_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('memory_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->primary(['mixtape_id', 'memory_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mixtape_memory');
        Schema::dropIfExists('mixtapes');
    }
};
