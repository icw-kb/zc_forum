<?php

namespace App\Livewire\Plugins;

use App\Models\Plugin;
use App\Models\PluginVersion;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PluginDownload extends Component
{
    use AuthorizesRequests;

    public Plugin $plugin;

    public string $version;

    public function mount(Plugin $plugin, string $version)
    {
        $this->authorize('download', $plugin);

        $this->plugin = $plugin;
        $this->version = $version;

        // Find the plugin version
        $pluginVersion = $this->plugin->versions()
            ->where('version', $this->version)
            ->firstOrFail();

        // Record the download
        $this->plugin->recordDownload(
            auth()->id(),
            request()->ip(),
            request()->userAgent()
        );

        // Trigger the download
        return $this->downloadFile($pluginVersion);
    }

    /**
     * Handle the file download.
     */
    private function downloadFile(PluginVersion $pluginVersion): StreamedResponse
    {
        // Check if file exists
        if (! $pluginVersion->file_path || ! Storage::exists($pluginVersion->file_path)) {
            abort(404, 'Plugin file not found.');
        }

        $filename = $this->plugin->slug.'-v'.$pluginVersion->version.'.zip';

        return Storage::download($pluginVersion->file_path, $filename, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function render()
    {
        // This component primarily handles downloads and redirects
        // The view is minimal as the main action happens in mount()
        return view('livewire.plugins.plugin-download');
    }
}
