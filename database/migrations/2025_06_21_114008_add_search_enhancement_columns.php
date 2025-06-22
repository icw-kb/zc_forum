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
        // Add columns to posts table
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('is_accepted_answer')->default(false)->after('status');
            $table->index('is_accepted_answer', 'posts_accepted_answer_index');
        });

        // Add columns to threads table
        Schema::table('threads', function (Blueprint $table) {
            $table->json('tags')->nullable()->after('status');
            // Note: JSON column indexing removed due to MariaDB compatibility issues
            // $table->index('tags', 'threads_tags_index');
        });

        // Add columns to forum_groups table if sort_order doesn't exist
        if (!Schema::hasColumn('forum_groups', 'sort_order')) {
            Schema::table('forum_groups', function (Blueprint $table) {
                $table->integer('sort_order')->default(0)->after('description');
                $table->index('sort_order', 'forum_groups_sort_order_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_accepted_answer_index');
            $table->dropColumn('is_accepted_answer');
        });

        Schema::table('threads', function (Blueprint $table) {
            // Note: No index to drop since it was removed for MariaDB compatibility
            // $table->dropIndex('threads_tags_index');
            $table->dropColumn('tags');
        });

        if (Schema::hasColumn('forum_groups', 'sort_order')) {
            Schema::table('forum_groups', function (Blueprint $table) {
                $table->dropIndex('forum_groups_sort_order_index');
                $table->dropColumn('sort_order');
            });
        }
    }
};
