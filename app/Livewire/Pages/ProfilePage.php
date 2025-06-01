<?php
// app/Livewire/Pages/ProfilePage.php

namespace App\Livewire\Pages;

use App\Traits\WithToast;
use App\Services\UserPreferenceManager;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Auth\MustVerifyEmail;

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

    // Password Update
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';

    // Two-Factor Authentication
    public $show2FAModal = false;
    public $qrCode = '';
    public $manualEntryKey = '';
    public $twoFactorCode = '';

    // Account Deletion
    public $showDeleteModal = false;
    public $deletePassword = '';

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

    // Personal Information Methods
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
            if ($this->user instanceof MustVerifyEmail) {
                $this->user->sendEmailVerificationNotification();
            }
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

        if ($this->user instanceof MustVerifyEmail) {
            $this->user->sendEmailVerificationNotification();
        }
        $this->toastSuccess('Verification email sent! Please check your inbox.');
    }

    public function removeAvatar()
    {
        if ($this->user->avatar) {
            Storage::disk('public')->delete($this->user->avatar);
            $this->user->update(['avatar' => null]);
            $this->toastSuccess('Avatar removed successfully!');
        }
    }

    // Password Methods
    public function updatePassword()
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.current_password' => 'The current password is incorrect.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $this->user->update([
            'password' => Hash::make($this->password),
        ]);

        // Clear password fields
        $this->reset(['current_password', 'password', 'password_confirmation']);

        $this->toastSuccess('Password updated successfully!');
    }

    // Two-Factor Authentication Methods
    public function enable2FA()
    {
        $google2fa = app('pragmarx.google2fa');
        $secretKey = $google2fa->generateSecretKey();
        
        // Store temporarily in session until confirmed
        session(['2fa_temp_secret' => $secretKey]);
        
        $this->manualEntryKey = $secretKey;
        $this->qrCode = $google2fa->getQRCodeInline(
            config('app.name'),
            $this->user->email,
            $secretKey
        );
        
        $this->show2FAModal = true;
    }

    public function confirm2FA()
    {
        $this->validate([
            'twoFactorCode' => 'required|digits:6',
        ]);

        $google2fa = app('pragmarx.google2fa');
        $secretKey = session('2fa_temp_secret');
        
        if (!$google2fa->verifyKey($secretKey, $this->twoFactorCode)) {
            $this->addError('twoFactorCode', 'The two factor code is invalid.');
            return;
        }

        // Save the secret and generate recovery codes
        $recoveryCodes = collect(range(1, 8))->map(function () {
            return \Illuminate\Support\Str::random(10) . '-' . \Illuminate\Support\Str::random(10);
        })->toArray();

        $this->user->update([
            'two_factor_secret' => encrypt($secretKey),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ]);

        session()->forget('2fa_temp_secret');
        $this->cancel2FA();
        $this->toastSuccess('Two-factor authentication has been enabled!');
    }

    public function disable2FA()
    {
        $this->user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        $this->toastSuccess('Two-factor authentication has been disabled.');
    }

    public function cancel2FA()
    {
        $this->show2FAModal = false;
        $this->qrCode = '';
        $this->manualEntryKey = '';
        $this->twoFactorCode = '';
        session()->forget('2fa_temp_secret');
    }

    public function showRecoveryCodes()
    {
        if ($this->user->two_factor_recovery_codes) {
            $codes = json_decode(decrypt($this->user->two_factor_recovery_codes));
            // You could show these in a modal or redirect to a dedicated page
            $this->toastInfo('Recovery codes: ' . implode(', ', $codes));
        }
    }

    public function regenerateRecoveryCodes()
    {
        $recoveryCodes = collect(range(1, 8))->map(function () {
            return \Illuminate\Support\Str::random(10) . '-' . \Illuminate\Support\Str::random(10);
        })->toArray();

        $this->user->update([
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ]);

        $this->toastSuccess('Recovery codes have been regenerated.');
    }

    // Session Management
    public function logoutOtherSessions()
    {
        try {
            if (empty($this->current_password)) {
                $this->toastError('Please enter your current password first in the password section.');
                return;
            }
            
            Auth::logoutOtherDevices($this->current_password);
            $this->toastSuccess('All other browser sessions have been logged out.');
        } catch (\Exception $e) {
            $this->toastError('The password is incorrect.');
        }
    }

    // Account Deletion Methods
    public function confirmAccountDeletion()
    {
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deletePassword = '';
    }

    public function deleteAccount()
    {
        $this->validate([
            'deletePassword' => ['required', 'current_password'],
        ], [
            'deletePassword.current_password' => 'The password is incorrect.',
        ]);

        try {
            // Delete user avatar if exists
            if ($this->user->avatar) {
                Storage::disk('public')->delete($this->user->avatar);
            }

            // Store user email for confirmation message
            $userEmail = $this->user->email;

            // Delete the user (you might want to soft delete instead)
            $this->user->delete();

            // Logout and redirect
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();

            return redirect()->route('login')->with('status', 'Your account has been successfully deleted.');
            
        } catch (\Exception $e) {
            $this->toastError('An error occurred while deleting your account. Please try again.');
        }
    }

    // Preferences Methods
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

    // Computed Properties
    public function getUserProperty()
    {
        return $this->user;
    }

    public function getActiveSessions()
    {
        // This would typically fetch from a sessions table or cache
        // For now, return current session info
        return collect([
            [
                'id' => session()->getId(),
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
                'last_activity' => now(),
                'is_current' => true,
            ]
        ]);
    }

    public function render()
    {
        return view('livewire.pages.profile-page')
            ->layout('layouts.app', ['title' => 'Profile Settings']);
    }
}