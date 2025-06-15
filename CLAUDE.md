# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Common Development Commands

### Quick Start
```bash
# Start DDEV environment
ddev start

# Initial setup after cloning
cp .env.example .env
ddev artisan key:generate
ddev composer install
ddev npm install
ddev artisan migrate

# Start full development environment (recommended)
ddev composer dev  # Runs Laravel server, queue, logs, and Vite concurrently
```

### Development Commands
```bash
# Frontend (if needed for assets)
ddev npm run dev               # Start Vite development server
ddev npm run build             # Build production assets

# Backend
ddev artisan serve             # Laravel development server
ddev artisan queue:listen      # Process queued jobs
ddev artisan pail              # Tail application logs

# Database
ddev artisan migrate           # Run migrations
ddev artisan migrate:fresh     # Drop all tables and re-run migrations
ddev artisan db:seed           # Seed the database

# Cache Management
ddev artisan optimize:clear    # Clear all cached data
```

### Code Quality
```bash
# Linting and Formatting
ddev npm run lint              # ESLint with auto-fix
ddev npm run format            # Prettier formatting
ddev exec ./vendor/bin/pint    # Laravel Pint (PHP formatting)

# Testing
ddev composer test             # Run Pest tests
ddev artisan test              # Alternative test command
```

## High-Level Architecture

### Technology Stack
- **Backend**: Laravel 12 with PHP 8.2+
- **Frontend**: Livewire 3 with Blade templates for reactive UI
- **Admin Panel**: Filament 3.3 at `/dashboard`
- **Database**: SQLite (configurable to MySQL/PostgreSQL)
- **Styling**: Tailwind CSS
- **Development Environment**: DDEV for containerization

### Key Architectural Patterns

#### 1. Livewire-Powered Frontend
The application uses Livewire for all frontend interactivity:
- **Full-page components**: Main application pages using Livewire
- **Modal components**: Authentication, forms, and dialogs
- **Real-time updates**: Reactive UI without JavaScript frameworks
- Controllers return Blade views with embedded Livewire components

#### 2. Authentication System
- Livewire-based authentication with modal components
- Email verification and 2FA support via FilamentBreezy
- Middleware: `HandleAppearance` manages theme preferences

#### 3. Permission System
- Spatie Permission package for role-based access control
- Permissions follow pattern: `{action}_{model}` (e.g., `view_forum`, `create_thread`)
- Each model has a corresponding policy class
- FilamentShield auto-generates admin panel permissions

#### 4. Data Flow
```
User Request → Route → Controller → Blade View
                                  ↓
                            Livewire Component
                                  ↓
                            Model/Service Layer
                                  ↓
                              Database
```

#### 5. Custom Restriction System
- `Restrictable` trait enables flexible access control
- `RestrictionRule` service with handlers for different restriction types
- Polymorphic relationship via `ModelHasRestriction` model

#### 6. User Preference System
- `UserPreferenceManager` service centralizes preference handling
- Preferences cached for performance
- Validation and default values handled centrally

### Key Models and Relationships
- **ForumGroup** → has many **Forums** → has many **Threads** → has many **Posts**
- **User** → has roles, permissions, preferences, posts
- **Plugin** → has many **PluginVersions**
- All models implement auditing and most support soft deletes

### Frontend Structure
- **Views**: `resources/views/` - Blade templates
- **Livewire Components**: `app/Livewire/` - Component classes
- **Component Views**: `resources/views/livewire/` - Component templates
- **Layouts**: `resources/views/components/layouts/` - Page layouts

### Important Conventions
- All models are auditable via OwenIt\Auditing package
- SEO-friendly URLs generated automatically via Spatie Sluggable
- Toast notifications handled via Livewire toast component
- Server-side state management through Livewire properties
- Tailwind CSS for all styling
- DDEV for consistent development environment across team