<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ForgotPasswordModal extends Component
{
    public bool $open = false;

    public string $email = '';

    protected $rules = [
        'email' => ['required', 'email'],
    ];

    protected $listeners = [
        'open-forgot-password' => 'openModal',
        'close-forgot-password-modal' => 'closeModal'
    ];

    public function openModal(): void
    {
        $this->resetErrorBag();
        $this->reset(['email']);
        $this->open = true;
        
        // Close other modals
        $this->dispatch('close-login-modal');
        $this->dispatch('close-register-modal');
    }
    
    public function closeModal(): void
    {
        $this->open = false;
    }

    public function sendResetLink()
    {
        $this->validate();

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        session()->flash('status', __($status));
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.auth.forgot-password-modal');
    }
}
