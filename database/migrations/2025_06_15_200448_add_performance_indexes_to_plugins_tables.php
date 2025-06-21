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
        // Add indexes to plugins table for better query performance
        Schema::table('plugins', function (Blueprint $table) {
            // Index for status filtering (most common filter)
            $table->index('status', 'plugins_status_index');

            // Index for featured plugins
            $table->index('is_featured', 'plugins_featured_index');

            // Index for group filtering
            $table->index('plugin_group_id', 'plugins_group_id_index');

            // Composite index for status + group (for group plugin queries)
            $table->index(['status', 'plugin_group_id'], 'plugins_status_group_index');

            // Index for sorting by download count
            $table->index('download_count', 'plugins_download_count_index');

            // Index for sorting by view count
            $table->index('view_count', 'plugins_view_count_index');

            // Index for sorting by creation date
            $table->index('created_at', 'plugins_created_at_index');

            // Index for name sorting/searching
            $table->index('name', 'plugins_name_index');

            // Composite index for status + download_count (for popular plugins)
            $table->index(['status', 'download_count'], 'plugins_status_downloads_index');

            // Composite index for status + view_count (for popular plugins)
            $table->index(['status', 'view_count'], 'plugins_status_views_index');
        });

        // Add indexes to plugin_groups table
        Schema::table('plugin_groups', function (Blueprint $table) {
            // Index for name sorting
            $table->index('name', 'plugin_groups_name_index');

            // Index for slug lookups (used in routes)
            $table->index('slug', 'plugin_groups_slug_index');
        });

        // Add indexes to plugin_statistics table for better analytics performance
        Schema::table('plugin_statistics', function (Blueprint $table) {
            // Index for plugin-specific statistics
            $table->index('plugin_id', 'plugin_statistics_plugin_id_index');

            // Index for action filtering
            $table->index('action', 'plugin_statistics_action_index');

            // Composite index for plugin + action queries
            $table->index(['plugin_id', 'action'], 'plugin_statistics_plugin_action_index');

            // Index for date-based queries
            $table->index('created_at', 'plugin_statistics_created_at_index');

            // Composite index for plugin + date (for time-based analytics)
            $table->index(['plugin_id', 'created_at'], 'plugin_statistics_plugin_date_index');

            // Index for user-based statistics
            $table->index('user_id', 'plugin_statistics_user_id_index');

            // Index for IP-based duplicate detection
            $table->index('ip_address', 'plugin_statistics_ip_address_index');

            // Composite index for duplicate view detection
            $table->index(['plugin_id', 'ip_address', 'action', 'created_at'], 'plugin_statistics_duplicate_check_index');
        });

        // Add indexes to plugin_versions table for better version queries
        Schema::table('plugin_versions', function (Blueprint $table) {
            // Index for plugin-specific versions
            $table->index('plugin_id', 'plugin_versions_plugin_id_index');

            // Index for version number sorting
            $table->index('version', 'plugin_versions_version_index');

            // Composite index for plugin + version queries
            $table->index(['plugin_id', 'version'], 'plugin_versions_plugin_version_index');

            // Index for file path lookups
            $table->index('file_path', 'plugin_versions_file_path_index');

            // Index for creation date sorting (latest versions)
            $table->index('created_at', 'plugin_versions_created_at_index');

            // Composite index for plugin + creation date (for latest version queries)
            $table->index(['plugin_id', 'created_at'], 'plugin_versions_plugin_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from plugins table
        Schema::table('plugins', function (Blueprint $table) {
            $table->dropIndex('plugins_status_index');
            $table->dropIndex('plugins_featured_index');
            $table->dropIndex('plugins_group_id_index');
            $table->dropIndex('plugins_status_featured_index');
            $table->dropIndex('plugins_status_group_index');
            $table->dropIndex('plugins_download_count_index');
            $table->dropIndex('plugins_view_count_index');
            $table->dropIndex('plugins_created_at_index');
            $table->dropIndex('plugins_name_index');
            $table->dropIndex('plugins_status_downloads_index');
            $table->dropIndex('plugins_status_views_index');
        });

        // Remove indexes from plugin_groups table
        Schema::table('plugin_groups', function (Blueprint $table) {
            $table->dropIndex('plugin_groups_name_index');
            $table->dropIndex('plugin_groups_slug_index');
        });

        // Remove indexes from plugin_statistics table
        Schema::table('plugin_statistics', function (Blueprint $table) {
            $table->dropIndex('plugin_statistics_plugin_id_index');
            $table->dropIndex('plugin_statistics_action_index');
            $table->dropIndex('plugin_statistics_plugin_action_index');
            $table->dropIndex('plugin_statistics_created_at_index');
            $table->dropIndex('plugin_statistics_plugin_date_index');
            $table->dropIndex('plugin_statistics_user_id_index');
            $table->dropIndex('plugin_statistics_ip_address_index');
            $table->dropIndex('plugin_statistics_duplicate_check_index');
        });

        // Remove indexes from plugin_versions table
        Schema::table('plugin_versions', function (Blueprint $table) {
            $table->dropIndex('plugin_versions_plugin_id_index');
            $table->dropIndex('plugin_versions_version_index');
            $table->dropIndex('plugin_versions_plugin_version_index');
            $table->dropIndex('plugin_versions_file_path_index');
            $table->dropIndex('plugin_versions_created_at_index');
            $table->dropIndex('plugin_versions_plugin_date_index');
        });
    }
};
