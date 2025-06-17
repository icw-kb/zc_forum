<?php

namespace App\Http\Controllers;

use App\Models\Plugin;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PluginDownloadController extends Controller
{
    use AuthorizesRequests;

    /**
     * Handle direct plugin download.
     */
    public function download(Request $request, Plugin $plugin, string $version)
    {
        // Check if plugin is active
        if ($plugin->status !== 'open') {
            abort(404, 'Plugin not found');
        }

        $this->authorize('download', $plugin);

        // Find the plugin version
        $pluginVersion = $plugin->versions()
            ->where('version', $version)
            ->first();

        if (! $pluginVersion) {
            abort(404, 'Version not found');
        }

        // Check for basic security issues (path traversal)
        if (str_contains($pluginVersion->file_path, '..')) {
            abort(404, 'Invalid file path');
        }

        // Check if file exists
        if (! $pluginVersion->hasFile()) {
            abort(404, 'File not available');
        }

        // Check file type
        if (! str_ends_with($pluginVersion->file_path, '.zip')) {
            abort(404, 'Invalid file type');
        }

        // Validate file hash if provided
        if (! empty($pluginVersion->file_hash)) {
            try {
                $actualHash = hash_file('sha256', Storage::path($pluginVersion->file_path));
                if ($actualHash !== $pluginVersion->file_hash) {
                    abort(500, 'File integrity check failed');
                }
            } catch (\Exception $e) {
                abort(500, 'File integrity check failed');
            }
        }

        // Record the download
        $plugin->recordDownload(
            auth()->id(),
            $request->ip(),
            $request->userAgent()
        );

        // Return download response
        $filename = $pluginVersion->getDownloadFilename();

        $headers = [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="'.addslashes($filename).'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
        ];

        if ($pluginVersion->file_size) {
            $headers['Content-Length'] = $pluginVersion->file_size;
        }

        return Storage::download($pluginVersion->file_path, $filename, $headers);
    }
}
