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
            $table->string('php_version')->nullable()->after('version');
            $table->text('release_notes')->nullable()->after('php_version');
            $table->boolean('is_stable')->default(true)->after('is_encapsulated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugin_versions', function (Blueprint $table) {
            $table->dropColumn(['php_version', 'release_notes', 'is_stable']);
        });
    }
};
