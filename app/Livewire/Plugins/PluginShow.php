<?php

namespace App\Livewire\Plugins;

use App\Models\Plugin;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class PluginShow extends Component
{
    use AuthorizesRequests;

    public Plugin $plugin;

    public function mount(Plugin $plugin)
    {
        $this->authorize('view', $plugin);

        $this->plugin = $plugin;

        // Record the view
        $this->plugin->recordView(
            auth()->id(),
            request()->ip(),
            request()->userAgent()
        );
    }

    /**
     * Get the plugin's versions with Zen Cart compatibility.
     */
    public function getVersionsProperty()
    {
        return $this->plugin->versions()
            ->with('zencartVersions')
            ->orderBy('version', 'desc')
            ->get();
    }

    /**
     * Get related plugins from the same group.
     */
    public function getRelatedPluginsProperty()
    {
        if (! $this->plugin->group) {
            return collect([]);
        }

        return Plugin::where('plugin_group_id', $this->plugin->plugin_group_id)
            ->where('id', '!=', $this->plugin->id)
            ->where('status', 'active')
            ->with(['group', 'versions'])
            ->take(4)
            ->get();
    }

    /**
     * Check if user can download plugins.
     */
    public function getCanDownloadProperty()
    {
        return auth()->check() && $this->plugin->hasVersions();
    }

    public function render()
    {
        return view('livewire.plugins.plugin-show', [
            'versions' => $this->versions,
            'relatedPlugins' => $this->relatedPlugins,
            'canDownload' => $this->canDownload,
        ])->layout('layouts.app', [
            'title' => $this->plugin->name.' - Plugin Details',
        ]);
    }
}
