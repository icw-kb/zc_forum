<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Verified;
use Livewire\Component;

class VerifyEmailPage extends Component
{
    public function resend()
    {
        if (Auth::user()?->hasVerifiedEmail()) {
            return redirect()->intended('/');
        }

        Auth::user()?->sendEmailVerificationNotification();
        session()->flash('status', 'Verification email sent!');
    }

    public function render()
    {
        return view('livewire.auth.verify-email-page')->layout('layouts.app', [
            'title' => 'Verify Email',
        ]);
    }
}
