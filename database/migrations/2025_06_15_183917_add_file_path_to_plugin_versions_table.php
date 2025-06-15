<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plugin_versions', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('is_encapsulated');
            $table->string('file_size')->nullable()->after('file_path');
            $table->string('file_hash')->nullable()->after('file_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugin_versions', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_size', 'file_hash']);
        });
    }
};
