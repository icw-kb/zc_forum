<?php

use App\Livewire\Pages\HomePage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class)->name('home');

// routes/web.php

Route::middleware(['auth'])->group(function () {
    Route::get('/my-profile', \App\Livewire\Pages\ProfilePage::class)->name('profile');
});

// Footer menu routes
Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('pages.terms');
})->name('terms');

// Newsletter subscription
Route::post('/newsletter/subscribe', function () {
    // Handle newsletter subscription logic
    return back()->with('success', 'Thank you for subscribing!');
})->name('newsletter.subscribe');

// Plugin routes
Route::prefix('plugins')->name('plugins.')->group(function () {
    Route::get('/', \App\Livewire\Plugins\PluginIndex::class)->name('index');
    Route::get('/search', \App\Livewire\Plugins\PluginSearch::class)->name('search');
    Route::get('/group/{group:slug}', \App\Livewire\Plugins\PluginsByGroup::class)->name('group');
    Route::get('/{plugin:slug}', \App\Livewire\Plugins\PluginShow::class)->name('show');

    // Authenticated download routes with rate limiting
    Route::middleware(['auth', 'throttle:downloads'])->group(function () {
        Route::get('/{plugin:slug}/download/{version}', \App\Livewire\Plugins\PluginDownload::class)
            ->name('download');
        Route::get('/{plugin:slug}/download/{version}/direct', [\App\Http\Controllers\PluginDownloadController::class, 'download'])
            ->name('download.direct');
    });
});

// Forum routes
Route::prefix('forums')->name('forums.')->group(function () {
    Route::get('/', \App\Livewire\Forum\Index::class)->name('index');
    Route::get('/search', \App\Livewire\Forum\Search::class)->name('search');
    Route::get('/{forumGroup:slug}/{forum:slug}', \App\Livewire\Forum\Show::class)->name('show');
    Route::get('/{forumGroup:slug}/{forum:slug}/search', \App\Livewire\Forum\Search::class)->name('forum.search');
    Route::get('/{forumGroup:slug}/{forum:slug}/{thread:slug}', \App\Livewire\Thread\Show::class)->name('threads.show');
});

// Add other routes as needed...

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
