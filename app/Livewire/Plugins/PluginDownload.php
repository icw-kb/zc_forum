<?php

namespace App\Livewire\Plugins;

use App\Models\Plugin;
use App\Models\PluginVersion;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class PluginDownload extends Component
{
    use AuthorizesRequests;

    public Plugin $plugin;

    public string $version;

    public ?PluginVersion $pluginVersion = null;

    public bool $fileAvailable = false;

    public ?string $errorMessage = null;

    public function mount(Plugin $plugin, string $version)
    {
        // Check if plugin is active
        if ($plugin->status !== 'open') {
            abort(404, 'Plugin not found');
        }

        $this->authorize('download', $plugin);

        $this->plugin = $plugin;
        $this->version = $version;

        // Find the plugin version
        $this->pluginVersion = $this->plugin->versions()
            ->where('version', $this->version)
            ->first();

        if (! $this->pluginVersion) {
            abort(404, 'Version not found');
        }

        // Check for basic security issues (path traversal)
        if (str_contains($this->pluginVersion->file_path, '..')) {
            $this->fileAvailable = false;
            $this->errorMessage = 'Invalid file path';

            return;
        }

        // Check if file exists
        $this->fileAvailable = $this->pluginVersion->hasFile();

        if (! $this->fileAvailable) {
            $this->errorMessage = 'File not available';
        }
    }

    /**
     * Handle the download action.
     */
    public function download()
    {
        if (! $this->fileAvailable || ! $this->pluginVersion) {
            $this->errorMessage = 'File not available';

            return;
        }

        // Check file type
        if (! str_ends_with($this->pluginVersion->file_path, '.zip')) {
            $this->errorMessage = 'Invalid file type';

            return;
        }

        // Validate file hash if provided
        if (! empty($this->pluginVersion->file_hash)) {
            try {
                $actualHash = hash_file('sha256', Storage::path($this->pluginVersion->file_path));
                if ($actualHash !== $this->pluginVersion->file_hash) {
                    $this->errorMessage = 'File integrity check failed';

                    return;
                }
            } catch (\Exception $e) {
                $this->errorMessage = 'File integrity check failed';

                return;
            }
        }

        // Record the download
        $this->plugin->recordDownload(
            auth()->id(),
            request()->ip(),
            request()->userAgent()
        );

        // Return download response
        $filename = $this->pluginVersion->getDownloadFilename();

        $headers = [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="'.addslashes($filename).'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
        ];

        if ($this->pluginVersion->file_size) {
            $headers['Content-Length'] = $this->pluginVersion->file_size;
        }

        return Storage::download($this->pluginVersion->file_path, $filename, $headers);
    }

    public function render()
    {
        return view('livewire.plugins.plugin-download')->layout('components.layouts.app');
    }
}
