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
        // Check if file exists using the new model method
        if (! $pluginVersion->hasFile()) {
            abort(404, 'Plugin file not found.');
        }

        $filename = $pluginVersion->getDownloadFilename();

        // Set comprehensive headers for security and proper handling
        $headers = [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="'.addslashes($filename).'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
        ];

        // Add file size if available
        if ($pluginVersion->file_size) {
            $headers['Content-Length'] = $pluginVersion->file_size;
        }

        return Storage::download($pluginVersion->file_path, $filename, $headers);
    }

    public function render()
    {
        // This component primarily handles downloads and redirects
        // The view is minimal as the main action happens in mount()
        return view('livewire.plugins.plugin-download');
    }
}
