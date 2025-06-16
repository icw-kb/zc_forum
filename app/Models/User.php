<?php

namespace App\Models;

use App\Notifications\CustomResetPassword;
use App\Services\UserPreferenceManager;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use OwenIt\Auditing\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements \OwenIt\Auditing\Contracts\Auditable, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Auditable, HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferences' => 'array',
        ];
    }

    public function getPreference(string $key, mixed $default = null): mixed
    {
        return app(UserPreferenceManager::class)->get($this, $key, $default);
    }

    public function getAllPreferences(): array
    {
        return app(UserPreferenceManager::class)->all($this);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
