# Forum Implementation Plan

## ⚠️ TEMPORARY: Permission Checks Disabled

**Note:** Permission checks have been temporarily disabled for testing purposes in the following files:
- `app/Policies/ForumPolicy.php` - All permission checks commented out but structure preserved
- `app/Policies/ThreadPolicy.php` - All permission checks commented out but structure preserved  
- `app/Policies/PostPolicy.php` - All permission checks commented out but structure preserved
- `app/Livewire/Thread/Create.php` - Authorization checks commented out
- `app/Livewire/Post/Create.php` - Authorization checks commented out
- `app/Livewire/Post/Edit.php` - Authorization checks commented out
- `resources/views/livewire/forum/show.blade.php` - @can directives commented out
- `resources/views/livewire/thread/show.blade.php` - @can directives commented out

To re-enable permissions, uncomment the relevant code blocks marked with "TEMPORARY: Permissions disabled for testing".

## Project Status Overview

### ✅ **COMPLETED - Backend Foundation**
All backend infrastructure is production-ready:

- **Database Layer**: All models (ForumGroup, Forum, Thread, Post, User) with proper relationships
- **Migrations**: Complete schema including advanced features (post_reads, thread_subscriptions, post_likes)
- **Admin Interface**: Full Filament resources for ForumGroup and Forum management
- **Authorization**: Complete policies (ForumPolicy, ForumGroupPolicy) 
- **Model Features**: Auditable, Searchable, SoftDeletes, Sluggable, Restrictable traits
- **Database Factories**: Complete factories for testing/seeding

### ❌ **MISSING - Frontend Interface**
The user-facing forum interface needs to be built entirely.

---

## Implementation Phases

### **PHASE 1: Core Forum Pages** 🚀 *HIGH PRIORITY*

#### Essential Components
- [ ] **Forum Index Page** (`app/Livewire/Forum/Index.php`)
  - Display forum groups and forums hierarchy
  - Show thread counts and last post info
  - Implement permission-based visibility

- [ ] **Forum Show Page** (`app/Livewire/Forum/Show.php`) 
  - List threads for specific forum
  - Display thread metadata (author, replies, last post)
  - Pagination and sorting options

- [ ] **Thread Show Page** (`app/Livewire/Thread/Show.php`)
  - Display all posts in a thread
  - Implement pagination for posts
  - Show post metadata and user info

- [ ] **Routes Configuration** (`routes/web.php`)
  - RESTful forum routes with slug support
  - Thread viewing routes
  - Permission middleware integration

#### Deliverables
- Working forum navigation hierarchy
- Thread browsing and viewing functionality
- Basic forum structure visible to users

---

### **PHASE 2: Content Creation & Management** ✏️ *MEDIUM PRIORITY*

#### Interactive Features  
- [ ] **Thread Creation** (`app/Livewire/Thread/Create.php`)
  - Modal/form for starting new threads
  - Permission checks for thread creation
  - Integration with existing auth system

- [ ] **Post Creation & Replies** (`app/Livewire/Post/Create.php`)
  - Reply form component for threads
  - Rich text editor integration
  - Real-time post updates via Livewire

- [ ] **Breadcrumb Navigation** (`resources/views/components/forum-breadcrumb.blade.php`)
  - Forum Group → Forum → Thread hierarchy
  - SEO-friendly navigation structure

- [ ] **Post Editing** (`app/Livewire/Post/Edit.php`)
  - Edit functionality with permission checks
  - Edit history tracking (leverage existing audit system)
  - Time-limited editing windows

#### Deliverables
- Users can create threads and posts
- Complete CRUD operations for forum content
- Intuitive navigation throughout forum

---

### **PHASE 3: Enhanced User Experience** 🔥 *LOW PRIORITY*

#### Advanced Features
- [ ] **Thread Subscription System**
  - Follow/unfollow threads
  - Email notifications for new posts
  - Subscription management in user preferences

- [ ] **Post Reaction System**  
  - Like/react to posts
  - Reaction counts and user tracking
  - Integration with existing post_likes table

- [ ] **Read Tracking System**
  - Mark posts/threads as read/unread
  - Visual indicators for new content
  - Integration with existing post_reads table

- [ ] **Forum Search** 
  - Full-text search across forums/threads/posts
  - Integration with Laravel Scout (models already searchable)
  - Advanced filtering options

- [ ] **Additional Features**
  - Thread pinning and locking
  - Post reporting system
  - User reputation/karma system
  - Forum statistics and analytics

#### Deliverables
- Rich interactive forum experience
- User engagement features
- Advanced forum moderation tools

---

## Technical Implementation Details

### **Architecture Standards**
- **Frontend**: Livewire 3 components with Blade templates
- **Styling**: Tailwind CSS (consistent with existing codebase)
- **Modals**: Follow existing auth modal patterns
- **Permissions**: Leverage Spatie Permission package
- **URLs**: SEO-friendly slugs (already implemented in models)
- **State Management**: Server-side via Livewire properties

### **Key Livewire Components Structure**
```
app/Livewire/
├── Forum/
│   ├── Index.php          # Main forum listing
│   └── Show.php           # Individual forum threads
├── Thread/
│   ├── Show.php           # Thread with posts
│   ├── Create.php         # New thread form
│   └── Edit.php           # Thread editing
└── Post/
    ├── Create.php         # Post/reply form
    ├── Edit.php           # Post editing
    └── Like.php           # Post reactions
```

### **Route Structure**
```php
// Forum routes with slug support
Route::get('/forums', Forum\Index::class)->name('forums.index');
Route::get('/forums/{forumGroup:slug}', Forum\Show::class)->name('forums.show');
Route::get('/forums/{forumGroup:slug}/{forum:slug}', Thread\Index::class)->name('threads.index');
Route::get('/forums/{forumGroup:slug}/{forum:slug}/{thread:slug}', Thread\Show::class)->name('threads.show');
```

### **Permission Integration**
- Forum viewing: `view_forum`
- Thread creation: `create_thread` 
- Post creation: `create_post`
- Post editing: `edit_post` (own posts) / `edit_any_post` (moderation)
- Thread management: `manage_thread`

---

## Development Commands

### **Setup & Testing**
```bash
# Database setup
ddev artisan migrate:fresh --seed

# Development server
ddev composer dev  # Runs Laravel + Vite + Queue + Logs

# Code quality
ddev npm run lint
ddev exec ./vendor/bin/pint
ddev composer test
```

### **Development Workflow**
1. Start with Phase 1 core components
2. Create Livewire component classes
3. Build corresponding Blade templates  
4. Add routes and test functionality
5. Implement permission checks
6. Style with Tailwind CSS
7. Test with different user roles

---

## Success Criteria

### **Phase 1 Complete**
- [ ] Users can browse forum hierarchy
- [ ] Threads display correctly with posts
- [ ] Navigation works end-to-end
- [ ] Permissions properly enforced

### **Phase 2 Complete** 
- [ ] Users can create threads and posts
- [ ] Editing works with proper permissions  
- [ ] Real-time updates via Livewire
- [ ] Breadcrumb navigation functional

### **Phase 3 Complete**
- [ ] Advanced features enhance user experience
- [ ] Search functionality works across all content
- [ ] Subscription/notification system active
- [ ] Forum analytics and moderation tools ready

---

## Notes

- **Database foundation is solid** - Focus entirely on frontend/UI implementation
- **Follow existing patterns** - Use auth modals and Livewire structure as reference
- **Leverage existing systems** - Permission policies, audit trails, slugs all ready
- **Mobile-first approach** - Ensure responsive design with Tailwind
- **Performance considerations** - Implement pagination and lazy loading for large forums

This plan builds upon your excellent backend foundation to create a complete, modern forum experience.