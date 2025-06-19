<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class PluginVersion extends Model implements AuditableContract
{
    use Auditable, HasFactory, Searchable;

    protected $fillable = [
        'description',
        'version',
        'php_version',
        'release_notes',
        'vc_url',
        'count',
        'status',
        'is_encapsulated',
        'is_stable',
        'user_id',
        'plugin_id',
        'file_path',
        'file_size',
        'file_hash',
    ];

    protected $casts = [
        'is_encapsulated' => 'boolean',
        'is_stable' => 'boolean',
        'count' => 'integer',
        'file_size' => 'integer',
    ];

    public function plugin()
    {
        return $this->belongsTo(\App\Models\Plugin::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function compatibleZenCartVersions()
    {
        return $this->belongsToMany(ZencartVersion::class);
    }

    public function zencartVersions()
    {
        return $this->compatibleZenCartVersions();
    }

    /**
     * Check if this version has a file attached.
     */
    public function hasFile(): bool
    {
        return ! empty($this->file_path) && Storage::exists($this->file_path);
    }

    /**
     * Get the formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (empty($this->file_size)) {
            return 'Unknown';
        }

        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;

        $formatted = $bytes / pow(1024, $power);
        // For MB and GB, always show 1 decimal place; for B and KB, no decimals
        if ($power >= 2) {
            return number_format($formatted, 1, '.', ',').' '.$units[$power];
        } else {
            return number_format($formatted, 0, '.', ',').' '.$units[$power];
        }
    }

    /**
     * Get the download filename.
     */
    public function getDownloadFilename(): string
    {
        return $this->plugin->slug.'-v'.$this->version.'.zip';
    }

    /**
     * Generate the storage path for this plugin version.
     */
    public function generateStoragePath(): string
    {
        return 'plugins/'.$this->plugin_id.'/'.$this->version.'/'.$this->getDownloadFilename();
    }

    /**
     * Store a file for this plugin version.
     */
    public function storeFile($file): string
    {
        $path = $this->generateStoragePath();

        // Store the file
        Storage::putFileAs(
            dirname($path),
            $file,
            basename($path)
        );

        // Update file metadata
        $this->update([
            'file_path' => $path,
            'file_size' => Storage::size($path),
            'file_hash' => hash_file('sha256', Storage::path($path)),
        ]);

        return $path;
    }

    /**
     * Delete the associated file.
     */
    public function deleteFile(): bool
    {
        if ($this->file_path && Storage::exists($this->file_path)) {
            return Storage::delete($this->file_path);
        }

        return true;
    }
}
