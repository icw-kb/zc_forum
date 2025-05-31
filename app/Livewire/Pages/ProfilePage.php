<?php
// app/Livewire/Pages/ProfilePage.php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfilePage extends Component
{
    use WithFileUploads;

    public $activeTab = 'personal';
    public $user;

    // Personal Information
    public $name;
    public $email;
    public $bio;
    public $location;
    public $website;
    public $avatar;
    public $avatarPreview;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'bio' => 'nullable|string|max:500',
        'location' => 'nullable|string|max:100',
        'website' => 'nullable|url|max:255',
        'avatar' => 'nullable|image|max:2048',
    ];

    public function mount()
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->bio = $this->user->bio ?? '';
        $this->location = $this->user->location ?? '';
        $this->website = $this->user->website ?? '';
        
        // Update email validation rule to exclude current user
        $this->rules['email'] = 'required|email|max:255|unique:users,email,' . $this->user->id;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function updatedAvatar()
    {
        $this->validate(['avatar' => 'image|max:2048']);
        
        if ($this->avatar) {
            $this->avatarPreview = $this->avatar->temporaryUrl();
        }
    }

    public function savePersonalInfo()
    {
        $this->validate();

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'bio' => $this->bio,
            'location' => $this->location,
            'website' => $this->website,
        ];

        // Handle avatar upload
        if ($this->avatar) {
            // Delete old avatar if exists
            if ($this->user->avatar) {
                Storage::disk('public')->delete($this->user->avatar);
            }
            
            $avatarPath = $this->avatar->store('avatars', 'public');
            $userData['avatar'] = $avatarPath;
        }

        $this->user->update($userData);
        $this->avatar = null;
        $this->avatarPreview = null;

        session()->flash('success', 'Profile updated successfully!');
    }

    public function removeAvatar()
    {
        if ($this->user->avatar) {
            Storage::disk('public')->delete($this->user->avatar);
            $this->user->update(['avatar' => null]);
            session()->flash('success', 'Avatar removed successfully!');
        }
    }

    public function render()
    {
        return view('livewire.pages.profile-page')
            ->layout('layouts.app', ['title' => 'Profile Settings']);
    }
}