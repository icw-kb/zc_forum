<?php

namespace App\Livewire\Profile;

use Filament\Notifications\Notification;
use Livewire\Component;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Services\UserPreferenceManager;
use Illuminate\Support\Arr;

class UserPreferencesForm extends Component implements HasForms
{
    use InteractsWithForms;

    public array $formData = [];

    public function mount(UserPreferenceManager $prefs): void
    {
        $this->formData = $prefs->all(auth()->user());
    }

    public function submit(UserPreferenceManager $prefs): void
    {
        $this->form->validate();
        $prefs->set(auth()->user(), $this->formData);
        Notification::make()
        ->title('Preferences saved')
        ->success()
        ->duration(3000)
        ->send();
    }

    public function render()
    {
        return view('livewire.profile.user-preferences-form');
    }

    protected function getFormSchema(): array
    {
        $prefs = app(UserPreferenceManager::class)->types();
        $schema = [];

        foreach ($prefs as $group => $fields) {
            $groupFields = [];

            foreach ($fields as $key => $type) {
                $fieldName = "{$group}.{$key}";

                if ($type === 'boolean') {
                    $groupFields[] = Forms\Components\Toggle::make($fieldName)->label(ucwords(str_replace('_', ' ', $key)));
                } elseif (str_starts_with($type, 'select:')) {
                    $options = explode(',', substr($type, 7));
                    $groupFields[] = Forms\Components\Select::make($fieldName)
                        ->label(ucwords(str_replace('_', ' ', $key)))
                        ->options(array_combine($options, $options));
                } else {
                    $groupFields[] = Forms\Components\TextInput::make($fieldName)->label(ucwords(str_replace('_', ' ', $key)));
                }
            }

            $schema[] = Forms\Components\Fieldset::make(ucfirst($group))->schema($groupFields);
        }

        return $schema;
    }

    protected function getFormModel(): ?array
    {
        return null;
    }

    protected function getFormStatePath(): string
    {
        return 'formData';
    }
}
