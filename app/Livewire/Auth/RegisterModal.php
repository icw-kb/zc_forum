<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Component;

class RegisterModal extends Component
{
    public bool $open = false;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    protected $listeners = [
        'open-register-modal' => 'openModal',
        'open-register' => 'openModal',
        'close-register-modal' => 'closeModal'
    ];

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }

    public function openModal(): void
    {
        $this->resetErrorBag();
        $this->reset(['name', 'email', 'password', 'password_confirmation']);
        $this->open = true;
        
        // Close other modals
        $this->dispatch('close-login-modal');
        $this->dispatch('close-forgot-password-modal');
    }
    
    public function closeModal(): void
    {
        $this->open = false;
    }

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        event(new Registered($user));

        Auth::login($user);
        
        $this->open = false;
        session()->flash('status', 'Registration successful! Please verify your email.');

        $this->redirect(session()->pull('url.intended', '/'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register-modal');
    }
}
