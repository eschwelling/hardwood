<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resonates', function (Blueprint $table) {
            $table->string('type')->default('fire')->after('ip_hash');
        });
    }

    public function down(): void
    {
        Schema::table('resonates', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
