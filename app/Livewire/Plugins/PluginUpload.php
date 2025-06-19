<?php

namespace App\Livewire\Plugins;

use App\Models\Plugin;
use App\Models\PluginGroup;
use App\Models\PluginVersion;
use App\Models\ZencartVersion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class PluginUpload extends Component
{
    use WithFileUploads;

    public $showModal = false;
    
    // Plugin fields
    public $name;
    public $slug;
    public $description;
    public $plugin_group_id;
    public $website_url;
    public $documentation_url;
    public $support_url;
    public $tags = [];
    
    // Version fields
    public $version;
    public $selectedZenCartVersions = [];
    public $php_version;
    public $release_notes;
    public $uploadedFile;
    
    // UI state
    public $currentStep = 1;
    public $totalSteps = 3;

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:plugins,slug',
        'description' => 'required|string|max:1000',
        'plugin_group_id' => 'required|exists:plugin_groups,id',
        'website_url' => 'nullable|url|max:255',
        'documentation_url' => 'nullable|url|max:255',
        'support_url' => 'nullable|url|max:255',
        'version' => 'required|string|max:50',
        'selectedZenCartVersions' => 'required|array|min:1',
        'selectedZenCartVersions.*' => 'exists:zencart_versions,id',
        'php_version' => 'nullable|string|max:50',
        'release_notes' => 'nullable|string|max:5000',
        'uploadedFile' => 'required|file|mimes:zip|max:10240', // 10MB max
    ];

    protected $messages = [
        'uploadedFile.required' => 'Please select a plugin file to upload.',
        'uploadedFile.mimes' => 'The plugin file must be a ZIP file.',
        'uploadedFile.max' => 'The plugin file may not be greater than 10MB.',
    ];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('plugins.index')
                ->with('error', 'You must be logged in to upload plugins.');
        }
        
        if (!Auth::user()->can('create_plugin')) {
            return redirect()->route('plugins.index')
                ->with('error', 'You do not have permission to upload plugins.');
        }
    }

    public function updatedName($value)
    {
        $this->slug = \Str::slug($value);
    }

    public function nextStep()
    {
        if ($this->currentStep === 1) {
            $this->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:plugins,slug',
                'description' => 'required|string|max:1000',
                'plugin_group_id' => 'required|exists:plugin_groups,id',
            ]);
        } elseif ($this->currentStep === 2) {
            $this->validate([
                'version' => 'required|string|max:50',
                'selectedZenCartVersions' => 'required|array|min:1',
                'selectedZenCartVersions.*' => 'exists:zencart_versions,id',
                'uploadedFile' => 'required|file|mimes:zip|max:10240',
            ]);
        }
        
        $this->currentStep++;
    }

    public function previousStep()
    {
        $this->currentStep--;
    }

    public function addZenCartVersion($versionId)
    {
        if (!in_array($versionId, $this->selectedZenCartVersions)) {
            $this->selectedZenCartVersions[] = $versionId;
        }
    }

    public function removeZenCartVersion($versionId)
    {
        $this->selectedZenCartVersions = array_values(
            array_filter($this->selectedZenCartVersions, fn($id) => $id != $versionId)
        );
    }

    public function submit()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Create the plugin
            $plugin = Plugin::create([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'plugin_group_id' => $this->plugin_group_id,
                'website_url' => $this->website_url,
                'documentation_url' => $this->documentation_url,
                'support_url' => $this->support_url,
                'tags' => $this->tags,
                'user_id' => Auth::id(),
                'is_approved' => false, // Requires admin approval
                'is_featured' => false,
            ]);

            // Store the uploaded file
            $fileName = $this->slug . '-' . $this->version . '.zip';
            $filePath = $this->uploadedFile->storeAs('plugins/' . $this->slug, $fileName, 'public');
            
            // Create the plugin version
            $pluginVersion = PluginVersion::create([
                'plugin_id' => $plugin->id,
                'version' => $this->version,
                'php_version' => $this->php_version,
                'release_notes' => $this->release_notes,
                'file_path' => $filePath,
                'file_size' => $this->uploadedFile->getSize(),
                'file_hash' => hash_file('sha256', $this->uploadedFile->getRealPath()),
                'is_stable' => true,
            ]);

            // Attach ZenCart versions
            $pluginVersion->compatibleZenCartVersions()->sync($this->selectedZenCartVersions);

            DB::commit();

            // Reset form
            $this->reset();
            $this->showModal = false;
            
            // Emit event to refresh plugin list
            $this->dispatch('pluginUploaded');
            
            // Show success message
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Plugin uploaded successfully! It will be visible after admin approval.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to upload plugin. Please try again.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.plugins.plugin-upload', [
            'groups' => PluginGroup::orderBy('name')->get(),
            'zenCartVersions' => ZencartVersion::orderBy('version')->get(),
        ]);
    }
}