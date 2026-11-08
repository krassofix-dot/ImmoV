<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements AuditableContract
{
    /** @use HasFactory<UserFactory> */
    use Auditable, HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

    public const LOCALES = ['de', 'en'];

    /** Standardwerte, solange der Datensatz noch nicht neu geladen wurde. */
    protected $attributes = [
        'locale' => 'de',
        'is_active' => true,
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /** Geheimnisse nie ins Änderungsprotokoll schreiben. */
    protected array $auditExclude = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'last_login_at',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_secret !== null && $this->two_factor_confirmed_at !== null;
    }
}
