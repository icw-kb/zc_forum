<?php


namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ResetPasswordPage extends Component
{
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $token = '';

    public function mount($token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ];
    }

    public function resetPassword()
    {
        $this->validate();

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation') + ['token' => $this->token],
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                ])->save();

                Auth::login($user);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => __($status),
            ]);
        }

        return redirect()->intended('/')->with('status', __($status));
    }

    public function render()
    {
        return view('livewire.auth.reset-password-page')->layout('layouts.app', [
            'title' => 'Reset Password',
        ]);
    }
}
