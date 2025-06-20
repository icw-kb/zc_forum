<?php

namespace App\View\Components\Plugins;

use App\Models\Plugin;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class TopDownloadedCard extends Component
{
    public $plugins;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Cache the top downloaded plugins for 1 hour
        $this->plugins = Cache::remember('plugins.top-downloaded', 3600, function () {
            return Plugin::query()
                ->where('status', 'open')
                ->orderBy('download_count', 'desc')
                ->take(10)
                ->get(['id', 'name', 'slug', 'download_count']);
        });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.plugins.top-downloaded-card');
    }
}