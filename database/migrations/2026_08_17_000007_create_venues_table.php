<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('latitude', 9, 6);
            $table->decimal('longitude', 9, 6);
            $table->timestamps();
        });

        Schema::create('memory_venue', function (Blueprint $table) {
            $table->foreignUuid('memory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->primary(['memory_id', 'venue_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memory_venue');
        Schema::dropIfExists('venues');
    }
};
