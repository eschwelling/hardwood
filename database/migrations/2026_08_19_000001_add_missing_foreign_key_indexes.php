<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Indexes three foreign keys that had no covering index.
 *
 * Both pivot tables are keyed on a composite primary key — (memory_id,
 * tag_id) and (memory_id, venue_id) — which only covers lookups leading
 * with memory_id. Filtering the feed by tag or venue searches the other
 * way round, from the tag/venue to its memories, and had nothing to use.
 * reports.memory_id had no index at all.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memory_tag', function (Blueprint $table) {
            $table->index('tag_id');
        });

        Schema::table('memory_venue', function (Blueprint $table) {
            $table->index('venue_id');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->index('memory_id');
        });
    }

    public function down(): void
    {
        Schema::table('memory_tag', function (Blueprint $table) {
            $table->dropIndex(['tag_id']);
        });

        Schema::table('memory_venue', function (Blueprint $table) {
            $table->dropIndex(['venue_id']);
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex(['memory_id']);
        });
    }
};
