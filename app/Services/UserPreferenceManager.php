<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

class UserPreferenceManager
{
    protected array $defaultPreferences = [
        'notifications' => [
            'email_alerts' => true,
            'sms_alerts' => false,
        ],
        'ui' => [
            'dark_mode' => false,
            'language' => 'en',
        ],
    ];

    protected array $preferenceTypes = [
        'notifications' => [
            'email_alerts' => 'boolean',
            'sms_alerts' => 'boolean',
        ],
        'ui' => [
            'dark_mode' => 'boolean',
            'language' => 'select:en,es,de,fr',
        ],
    ];

    public function get(User $user, string $key, mixed $default = null): mixed
    {
        $cached = Cache::remember("user.preferences.{$user->id}", 86400, fn () => $user->preferences ?? []);

        return Arr::get($cached, $key, Arr::get($this->defaultPreferences, $key, $default));
    }

    public function all(User $user): array
    {
        $cached = Cache::remember("user.preferences.{$user->id}", 86400, fn () => $user->preferences ?? []);
        return array_replace_recursive($this->defaultPreferences, $cached);
    }

    public function set(User $user, array $preferences): void
    {
        $current = $user->preferences ?? [];
        $merged = array_replace_recursive($current, $preferences);
        $user->preferences = $merged;
        $user->save();

        Cache::put("user.preferences.{$user->id}", $merged, 86400);
    }

    public function types(): array
    {
        return $this->preferenceTypes;
    }
}
