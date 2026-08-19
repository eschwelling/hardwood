<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resonates', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('memory_id')->constrained()->cascadeOnDelete();
            $table->string('ip_hash');
            $table->timestamps();

            $table->unique(['memory_id', 'ip_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resonates');
    }
};
