<?php

namespace App\Livewire\Plugins;

use App\Models\Plugin;
use App\Models\PluginVersion;
use App\Models\ZencartVersion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class PluginVersionUpload extends Component
{
    use WithFileUploads;

    public Plugin $plugin;
    public $showModal = false;
    
    // Version fields
    public $version;
    public $selectedZenCartVersions = [];
    public $php_version;
    public $description;
    public $uploadedFile;

    protected $rules = [
        'version' => 'required|string|max:50',
        'selectedZenCartVersions' => 'required|array|min:1',
        'selectedZenCartVersions.*' => 'exists:zencart_versions,id',
        'php_version' => 'nullable|string|max:50',
        'description' => 'required|string|max:5000',
        'uploadedFile' => 'required|file|mimes:zip|max:10240', // 10MB max
    ];

    protected $messages = [
        'uploadedFile.required' => 'Please select a plugin file to upload.',
        'uploadedFile.mimes' => 'The plugin file must be a ZIP file.',
        'uploadedFile.max' => 'The plugin file may not be greater than 10MB.',
        'version.required' => 'Version number is required.',
        'selectedZenCartVersions.required' => 'Please select at least one compatible Zen Cart version.',
        'description.required' => 'Please describe what changed in this version.',
    ];

    public function mount(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function updatedVersion($value)
    {
        // Check if this version already exists for this plugin
        $exists = PluginVersion::where('plugin_id', $this->plugin->id)
            ->where('version', $value)
            ->exists();
            
        if ($exists) {
            $this->addError('version', 'This version already exists for this plugin.');
        }
    }


    public function addZenCartVersion($versionId)
    {
        if (!Auth::check() || !$this->canUploadVersion()) {
            return;
        }
        
        if (!in_array($versionId, $this->selectedZenCartVersions)) {
            $this->selectedZenCartVersions[] = $versionId;
        }
    }

    public function removeZenCartVersion($versionId)
    {
        if (!Auth::check() || !$this->canUploadVersion()) {
            return;
        }
        
        $this->selectedZenCartVersions = array_values(
            array_filter($this->selectedZenCartVersions, fn($id) => $id != $versionId)
        );
    }

    public function canUploadVersion()
    {
        if (!Auth::check()) {
            return false;
        }

        // Plugin owner can upload new versions
        if ($this->plugin->user_id === Auth::id()) {
            return true;
        }

        // Users with create_plugin permission can upload versions
        return Auth::user()->can('create_plugin');
    }

    public function submit()
    {
        if (!$this->canUploadVersion()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You do not have permission to upload versions for this plugin.'
            ]);
            return;
        }
        
        $this->validate();

        // Final check for version uniqueness
        $exists = PluginVersion::where('plugin_id', $this->plugin->id)
            ->where('version', $this->version)
            ->exists();
            
        if ($exists) {
            $this->addError('version', 'This version already exists for this plugin.');
            return;
        }

        try {
            DB::beginTransaction();

            // Create the plugin version
            $pluginVersion = PluginVersion::create([
                'plugin_id' => $this->plugin->id,
                'version' => $this->version,
                'php_version' => $this->php_version,
                'user_id' => Auth::id(),
                'status' => 'locked', // Initially locked, requires admin approval
                'is_stable' => true,
                'description' => $this->description,
            ]);
            
            // Store the uploaded file using the model's method
            $pluginVersion->storeFile($this->uploadedFile);

            // Attach ZenCart versions
            $pluginVersion->compatibleZenCartVersions()->sync($this->selectedZenCartVersions);

            DB::commit();

            // Reset form
            $this->reset(['version', 'selectedZenCartVersions', 'php_version', 'description', 'uploadedFile']);
            $this->showModal = false;
            
            // Emit event to refresh plugin details
            $this->dispatch('versionUploaded');
            
            // Show success message
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'New version uploaded successfully! It will be visible after admin approval.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // If plugin version was created with a file, clean it up
            if (isset($pluginVersion) && $pluginVersion->file_path) {
                $pluginVersion->deleteFile();
            }
            
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to upload version: ' . $e->getMessage()
            ]);
            
            // Log the error for debugging
            Log::error('Plugin version upload failed', [
                'plugin_id' => $this->plugin->id,
                'version' => $this->version,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.plugins.plugin-version-upload', [
            'zenCartVersions' => ZencartVersion::orderBy('version')->get(),
        ]);
    }
}
