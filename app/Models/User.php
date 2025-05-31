<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use OwenIt\Auditing\Audit;
use OwenIt\Auditing\Auditable;
use Spatie\Permission\Traits\HasRoles;
use App\Services\UserPreferenceManager;
use App\Notifications\CustomResetPassword;

class User extends Authenticatable implements MustVerifyEmail, \OwenIt\Auditing\Contracts\Auditable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles, Auditable;

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

}
