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
            if (!Schema::hasColumn('plugins', 'website_url')) {
                $table->string('website_url')->nullable()->after('github_url');
            }
            if (!Schema::hasColumn('plugins', 'documentation_url')) {
                $table->string('documentation_url')->nullable()->after('website_url');
            }
            if (!Schema::hasColumn('plugins', 'support_url')) {
                $table->string('support_url')->nullable()->after('documentation_url');
            }
            if (!Schema::hasColumn('plugins', 'tags')) {
                $table->json('tags')->nullable()->after('support_url');
            }
            if (!Schema::hasColumn('plugins', 'is_approved')) {
                $table->boolean('is_approved')->default(false)->after('featured');
            }
            if (Schema::hasColumn('plugins', 'featured') && !Schema::hasColumn('plugins', 'is_featured')) {
                $table->renameColumn('featured', 'is_featured');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugins', function (Blueprint $table) {
            if (Schema::hasColumn('plugins', 'is_featured')) {
                $table->renameColumn('is_featured', 'featured');
            }
            $table->dropColumn(['website_url', 'documentation_url', 'support_url', 'tags', 'is_approved']);
        });
    }
};
