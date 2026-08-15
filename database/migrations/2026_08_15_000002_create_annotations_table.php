<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annotations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('memory_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('start_offset');
            $table->unsignedInteger('end_offset');
            $table->text('body');
            $table->string('ip_hash')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            $table->index(['memory_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annotations');
    }
};
