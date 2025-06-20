<?php

namespace App\View\Components\Plugins;

use App\Models\Plugin;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class FeaturedCard extends Component
{
    public $plugins;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Cache the featured plugins for 1 hour
        $this->plugins = Cache::remember('plugins.featured-sidebar', 3600, function () {
            return Plugin::query()
                ->where('status', 'open')
                ->where('is_featured', true)
                ->orderBy('download_count', 'desc')
                ->take(5)
                ->get(['id', 'name', 'slug', 'download_count']);
        });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.plugins.featured-card');
    }
}