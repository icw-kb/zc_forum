<?php
// app/Livewire/Pages/ProfilePage.php

namespace App\Livewire\Pages;

use App\Traits\WithToast;
use App\Services\UserPreferenceManager;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfilePage extends Component
{
    use WithFileUploads, WithToast;

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

    // Preferences
    public $preferences = [];

    // Email verification state
    public $originalEmail;
    public $emailChanged = false;

    public function mount(UserPreferenceManager $prefs)
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->originalEmail = $this->user->email;
        $this->bio = $this->user->bio ?? '';
        $this->location = $this->user->location ?? '';
        $this->website = $this->user->website ?? '';
        
        // Load all user preferences using the UserPreferenceManager
        $this->preferences = $prefs->all($this->user);
    }

    public function updatedEmail()
    {
        $this->emailChanged = $this->email !== $this->originalEmail;
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
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user->id)
            ],
            'bio' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $userData = [
            'name' => $this->name,
            'bio' => $this->bio,
            'location' => $this->location,
            'website' => $this->website,
        ];

        // Handle email change
        if ($this->emailChanged) {
            $userData['email'] = $this->email;
            $userData['email_verified_at'] = null; // Reset email verification
        }

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
        
        // Send email verification if email was changed
        if ($this->emailChanged) {
            $this->user->sendEmailVerificationNotification();
            $this->originalEmail = $this->email;
            $this->emailChanged = false;
            $this->toastWarning('Profile updated! Please check your new email address to verify it.');
        } else {
            $this->toastSuccess('Profile updated successfully!');
        }

        $this->avatar = null;
        $this->avatarPreview = null;
    }

    public function resendEmailVerification()
    {
        if ($this->user->hasVerifiedEmail()) {
            $this->toastInfo('Your email is already verified.');
            return;
        }

        $this->user->sendEmailVerificationNotification();
        $this->toastSuccess('Verification email sent! Please check your inbox.');
    }

    public function savePreferences(UserPreferenceManager $prefs)
    {
        try {
            $prefs->set($this->user, $this->preferences);
            $this->toastSuccess('Preferences saved successfully!');
        } catch (\Exception $e) {
            $this->toastError('Failed to save preferences: ' . $e->getMessage());
        }
    }

    public function resetAllPreferences(UserPreferenceManager $prefs)
    {
        try {
            $prefs->reset($this->user);
            $this->preferences = $prefs->all($this->user);
            $this->toastSuccess('All preferences reset to defaults!');
        } catch (\Exception $e) {
            $this->toastError('Failed to reset preferences: ' . $e->getMessage());
        }
    }

    public function resetGroupPreferences(string $group, UserPreferenceManager $prefs)
    {
        try {
            $prefs->reset($this->user, $group);
            $this->preferences = $prefs->all($this->user);
            $this->toastSuccess("Preferences for {$group} reset to defaults!");
        } catch (\Exception $e) {
            $this->toastError('Failed to reset group preferences: ' . $e->getMessage());
        }
    }

    public function removeAvatar()
    {
        if ($this->user->avatar) {
            Storage::disk('public')->delete($this->user->avatar);
            $this->user->update(['avatar' => null]);
            $this->toastSuccess('Avatar removed successfully!');
        }
    }

    public function render()
    {
        return view('livewire.pages.profile-page')
            ->layout('layouts.app', ['title' => 'Profile Settings']);
    }
}