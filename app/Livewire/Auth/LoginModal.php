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

    protected $listeners = ['open-login-modal' => 'openModal'];

    public function openModal(): void
    {
        $this->resetErrorBag();
        $this->reset(['email', 'password', 'remember']);
        $this->open = true;
    }

    public function login()
    {
        $this->validate();

        if (!Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (!Auth::user()->hasVerifiedEmail()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'You must verify your email address before logging in.',
            ]);
        }

        session()->regenerate();
        $this->dispatch('close-login-modal');
        return redirect()->intended('/');
    }

    public function render()
    {
        return view('livewire.auth.login-modal');
    }
}
