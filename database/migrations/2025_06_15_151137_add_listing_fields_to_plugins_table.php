<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Plugin;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plugins', function (Blueprint $table) {
            if (!Schema::hasColumn('plugins', 'plugin_group_id')) {
                $table->foreignId('plugin_group_id')->nullable()->after('id')->constrained('plugin_groups');
            }
            if (!Schema::hasColumn('plugins', 'github_url')) {
                $table->string('github_url')->nullable()->after('description');
            }
            if (!Schema::hasColumn('plugins', 'view_count')) {
                $table->unsignedInteger('view_count')->default(0)->after('status');
            }
            if (!Schema::hasColumn('plugins', 'download_count')) {
                $table->unsignedInteger('download_count')->default(0)->after('view_count');
            }
            if (!Schema::hasColumn('plugins', 'featured')) {
                $table->boolean('featured')->default(false)->after('download_count');
            }
            if (!Schema::hasColumn('plugins', 'slug')) {
                $table->string('slug')->after('name');
            }
        });

        // Generate slugs for existing plugins if slug column was just added
        if (!Plugin::whereNotNull('slug')->exists()) {
            $plugins = Plugin::all();
            foreach ($plugins as $plugin) {
                $slug = Str::slug($plugin->name);
                $count = 1;
                while (Plugin::where('slug', $slug)->where('id', '!=', $plugin->id)->exists()) {
                    $slug = Str::slug($plugin->name) . '-' . $count;
                    $count++;
                }
                $plugin->slug = $slug;
                $plugin->save();
            }
        }

        // Now add the unique constraint if it doesn't exist
        try {
            Schema::table('plugins', function (Blueprint $table) {
                $table->unique('slug');
            });
        } catch (\Exception $e) {
            // Index already exists, skip
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugins', function (Blueprint $table) {
            $table->dropForeign(['plugin_group_id']);
            $table->dropColumn(['plugin_group_id', 'github_url', 'view_count', 'download_count', 'featured', 'slug']);
        });
    }
};
