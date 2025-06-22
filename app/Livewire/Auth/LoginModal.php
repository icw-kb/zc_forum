<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class LoginModal extends Component
{
    public bool $open = false;

    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    protected $rules = [
        'email' => ['required', 'email'],
        'password' => ['required'],
    ];

    protected $listeners = [
        'open-login-modal' => 'openModal',
        'open-login' => 'openModal',
        'close-login-modal' => 'closeModal'
    ];

    public function openModal(): void
    {
        $this->resetErrorBag();
        $this->reset(['email', 'password', 'remember']);
        $this->open = true;
        
        // Close other modals
        $this->dispatch('close-register-modal');
        $this->dispatch('close-forgot-password-modal');
    }
    
    public function closeModal(): void
    {
        $this->open = false;
    }

    public function login()
    {
        $this->validate();

        if (! Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (! Auth::user()->hasVerifiedEmail()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'You must verify your email address before logging in.',
            ]);
        }

        session()->regenerate();
        $this->open = false;

        $this->redirect(session()->pull('url.intended', '/'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login-modal');
    }
}
