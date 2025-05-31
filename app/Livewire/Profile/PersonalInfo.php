<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;

class PersonalInfo extends Component implements Forms\Contracts\HasForms
{
    use InteractsWithForms;

    public array $formData = [];

    public function mount(): void
    {
        $this->form->fill([
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
        ]);
    }

    protected function getFormStatePath(): string
    {
        return 'formData';
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('Name')
                ->required(),
            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->required(),
        ];
    }

    public function updateProfile(): void
    {
        $data = $this->form->getState();

        $user = Auth::user();
        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->save();

            if ($user instanceof MustVerifyEmail) {
                $user->sendEmailVerificationNotification();
            }

            Notification::make()
                ->title('Profile updated. Please verify your new email address.')
                ->success()
                ->send();
        } else {
            $user->save();
            Notification::make()
                ->title('Profile updated.')
                ->success()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.profile.personal-info');
    }
}
