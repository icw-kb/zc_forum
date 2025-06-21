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
        Schema::table('plugins', function (Blueprint $table) {
            if (!Schema::hasColumn('plugins', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('plugin_group_id')->constrained('users');
            }
            if (!Schema::hasColumn('plugins', 'tags')) {
                $table->json('tags')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugins', function (Blueprint $table) {
            if (Schema::hasColumn('plugins', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('plugins', 'tags')) {
                $table->dropColumn('tags');
            }
        });
    }
};