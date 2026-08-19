<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mixtape_trackables', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('mixtape_id')->constrained()->cascadeOnDelete();
            $table->string('trackable_type');
            $table->uuid('trackable_id');
            $table->unsignedInteger('position');
            $table->timestamps();
            $table->index(['trackable_type', 'trackable_id']);
            $table->unique(['mixtape_id', 'trackable_type', 'trackable_id']);
        });

        if (Schema::hasTable('mixtape_memory')) {
            $now = now();
            $rows = DB::table('mixtape_memory')->get()->map(fn ($row) => [
                'mixtape_id' => $row->mixtape_id,
                'trackable_type' => \App\Models\Memory::class,
                'trackable_id' => $row->memory_id,
                'position' => $row->position,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            if ($rows) {
                DB::table('mixtape_trackables')->insert($rows);
            }

            Schema::drop('mixtape_memory');
        }
    }

    public function down(): void
    {
        Schema::create('mixtape_memory', function (Blueprint $table) {
            $table->foreignUuid('mixtape_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('memory_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->primary(['mixtape_id', 'memory_id']);
        });

        Schema::dropIfExists('mixtape_trackables');
    }
};
