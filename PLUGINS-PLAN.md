# Plugin Listing Feature Implementation Plan

## Overview
This document outlines the plan for implementing a user-facing route to list plugins in the ZC Forum application. The implementation will use Livewire for the frontend and follow the existing architectural patterns.

## Current State Analysis

### Existing Implementation:
- **Database Tables**: `plugins`, `plugin_versions`, `zencart_versions`, and pivot table
- **Models**: Plugin, PluginVersion, ZencartVersion with relationships defined
- **Search**: Models already implement Laravel Scout (Searchable trait)
- **Admin Panel**: Filament resources exist for managing plugins (not part of this phase)

### Missing Components (per PLUGINS.md requirements):
- Plugin groups/categories system
- GitHub repository link field
- Public-facing routes and controllers
- File storage implementation for zip files
- View/download statistics tracking
- Frontend listing and search interface
- Authentication checks for downloads

## Branch Strategy

We'll use feature branches for development:
- Main feature branch: `feature/plugin-listing`
- Sub-branches for major components:
  - `feature/plugin-listing-db` - Database changes
  - `feature/plugin-listing-models` - Model updates
  - `feature/plugin-listing-components` - Livewire components
  - `feature/plugin-listing-views` - Blade templates

## Comprehensive Todo List

### Phase 1: Setup & Database (Branch: feature/plugin-listing-db)
1. ✅ Create feature branch for plugin listing development
2. ⬜ Create plugin groups migration - add plugin_groups table
3. ⬜ Update plugins table migration - add github_url, view_count, download_count, featured fields
4. ⬜ Create plugin statistics migration - add plugin_statistics table
5. ⬜ Run migrations and verify database schema

### Phase 2: Model Layer (Branch: feature/plugin-listing-models)
6. ⬜ Create PluginGroup model with relationships and sluggable trait
7. ⬜ Update Plugin model - add group relationship, scopes, and statistics methods
8. ⬜ Create PluginStatistic model with relationships

### Phase 3: Routing & Authorization
9. ⬜ Add plugin routes to routes/web.php
10. ⬜ Create PluginPolicy for authorization rules
11. ⬜ Add rate limiting middleware for downloads

### Phase 4: Core Livewire Components (Branch: feature/plugin-listing-components)
12. ⬜ Create PluginIndex Livewire component for listing all plugins
13. ⬜ Create PluginShow Livewire component for plugin details
14. ⬜ Create PluginDownload Livewire component with auth middleware
15. ⬜ Create PluginsByGroup Livewire component for group filtering
16. ⬜ Create PluginSearch Livewire component for search functionality

### Phase 5: View Templates (Branch: feature/plugin-listing-views)
17. ⬜ Create plugin-index blade template with pagination
18. ⬜ Create plugin-show blade template with version listing
19. ⬜ Create plugins-by-group blade template
20. ⬜ Create plugin-search blade template
21. ⬜ Create reusable plugin-card blade component
22. ⬜ Create plugin-filters blade component
23. ⬜ Create version-list blade component

### Phase 6: File Storage & Downloads
24. ⬜ Implement file storage configuration for plugin downloads
25. ⬜ Implement download tracking in PluginDownload component

### Phase 7: Search & Statistics
26. ⬜ Configure Meilisearch for plugin search
27. ⬜ Implement view tracking on plugin show page

### Phase 8: Testing
28. ⬜ Create plugin seeder with sample data for testing
29. ⬜ Write feature tests for plugin listing
30. ⬜ Write feature tests for plugin search
31. ⬜ Write feature tests for authenticated downloads
32. ⬜ Write unit tests for Plugin model methods

### Phase 9: UI/UX & Polish
33. ⬜ Style plugin listing page with Tailwind CSS
34. ⬜ Implement responsive design for mobile
35. ⬜ Add loading states and error handling

### Phase 10: Performance & Optimization
36. ⬜ Implement caching for plugin listings
37. ⬜ Add database indexes for performance

### Phase 11: Final Review
38. ⬜ Create pull request for review

## Implementation Plan Details

### Phase 1: Database Schema Updates

1. **Create Plugin Groups Table**
   - Migration: `create_plugin_groups_table`
   - Fields: id, name, slug, description, timestamps
   - Add `plugin_group_id` foreign key to plugins table

2. **Update Plugins Table**
   - Add `github_url` field (nullable string)
   - Add `view_count` field (integer, default 0)
   - Add `download_count` field (integer, default 0)
   - Add `featured` field (boolean, default false)

3. **Create Plugin Statistics Table** (for detailed tracking)
   - Migration: `create_plugin_statistics_table`
   - Fields: id, plugin_id, user_id (nullable), action (view/download), ip_address, user_agent, timestamps

### Phase 2: Model Updates

1. **Create PluginGroup Model**
   - Relationships: hasMany plugins
   - Use Sluggable trait for SEO-friendly URLs

2. **Update Plugin Model**
   - Add relationship: belongsTo PluginGroup
   - Add scopes: featured(), byGroup(), withStatistics()
   - Add methods for incrementing view/download counts

3. **Create PluginStatistic Model**
   - Relationships: belongsTo Plugin, belongsTo User (optional)

### Phase 3: Routes Structure

```php
// routes/web.php

Route::prefix('plugins')->name('plugins.')->group(function () {
    Route::get('/', PluginIndex::class)->name('index');
    Route::get('/group/{group:slug}', PluginsByGroup::class)->name('group');
    Route::get('/{plugin:slug}', PluginShow::class)->name('show');
    Route::get('/{plugin:slug}/download/{version}', PluginDownload::class)
        ->middleware('auth')
        ->name('download');
});
```

### Phase 4: Livewire Components Structure

1. **PluginIndex Component** (`app/Livewire/Plugins/PluginIndex.php`)
   - List all plugins with pagination
   - Search functionality
   - Filter by group
   - Sort options (newest, most downloaded, most viewed)

2. **PluginsByGroup Component** (`app/Livewire/Plugins/PluginsByGroup.php`)
   - List plugins filtered by group
   - Maintain search and sort functionality

3. **PluginShow Component** (`app/Livewire/Plugins/PluginShow.php`)
   - Display plugin details
   - List all versions
   - Show compatible Zen Cart versions
   - Display statistics
   - Download buttons (auth check)

4. **PluginSearch Component** (`app/Livewire/Plugins/PluginSearch.php`)
   - Real-time search functionality
   - Search across name, description
   - Filter results

5. **PluginDownload Component** (`app/Livewire/Plugins/PluginDownload.php`)
   - Handle authenticated downloads
   - Track download statistics
   - Serve file from storage

### Phase 5: View Templates Structure

```
resources/views/
├── livewire/
│   └── plugins/
│       ├── plugin-index.blade.php
│       ├── plugins-by-group.blade.php
│       ├── plugin-show.blade.php
│       ├── plugin-search.blade.php
│       └── components/
│           ├── plugin-card.blade.php
│           ├── plugin-filters.blade.php
│           └── version-list.blade.php
└── plugins/
    └── layout.blade.php
```

### Phase 6: File Storage Implementation

1. **Storage Configuration**
   - Use Laravel Storage facade
   - Store in `storage/app/plugins/{plugin_id}/{version}/`
   - Implement cleanup for old versions

2. **Download Handling**
   - Stream file through authenticated route
   - Set appropriate headers
   - Track download event

### Phase 7: Search Implementation

1. **Meilisearch Configuration**
   - Configure searchable attributes
   - Set up filters for groups, versions
   - Implement faceted search

2. **Search Features**
   - Full-text search on name and description
   - Filter by group
   - Filter by compatible Zen Cart version
   - Sort by relevance, downloads, date

### Phase 8: Statistics & Analytics

1. **View Tracking**
   - Track on plugin detail page load
   - Store anonymized data
   - Rate limit to prevent abuse

2. **Download Tracking**
   - Track authenticated downloads
   - Link to user for history

### Phase 9: Authorization & Policies

1. **PluginPolicy**
   - `view`: Anyone can view
   - `download`: Authenticated users only
   - `create`: Authenticated users (future)
   - `update`: Plugin owner or admin

2. **Middleware**
   - Apply auth middleware to download routes
   - Rate limiting for downloads

### Phase 10: Testing Strategy

1. **Feature Tests**
   - Test plugin listing pagination
   - Test search functionality
   - Test group filtering
   - Test authenticated downloads
   - Test statistics tracking

2. **Unit Tests**
   - Test model relationships
   - Test scopes and methods
   - Test file storage operations

## UI/UX Considerations

1. **Plugin Cards** should display:
   - Plugin name and description
   - Group/category badge
   - Download count
   - Latest version
   - Compatible Zen Cart versions
   - Author information

2. **Search Experience**:
   - Instant search with debouncing
   - Clear filters indication
   - No results messaging

3. **Mobile Responsiveness**:
   - Card layout on mobile
   - Simplified filters
   - Touch-friendly download buttons

## Performance Considerations

1. **Caching Strategy**:
   - Cache plugin listings
   - Cache group counts
   - Cache popular plugins

2. **Query Optimization**:
   - Eager load relationships
   - Use database indexes
   - Implement query scopes

3. **File Serving**:
   - Progress indicators for downloads

## Security Considerations

1. **Download Protection**:
   - Authenticated routes
   - Rate limiting

2. **XSS Prevention**:
   - Sanitize plugin descriptions
   - Escape user-generated content

## Future Enhancements (Not in this phase)

1. Plugin Reviews & Ratings
2. Plugin Author Profiles
3. Version Comparison
4. Automated Compatibility Testing
5. Plugin Dependencies Management
6. REST API for External Integration
7. Webhook Notifications for Updates
8. Admin panel updates for plugin management

This plan provides a comprehensive approach to implementing the plugin listing feature while maintaining consistency with the existing codebase architecture and meeting all requirements specified in PLUGINS.md.