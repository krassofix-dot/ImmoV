<?php

namespace App\Modules\Users\Listeners;

use App\Models\User;
use App\Modules\System\Support\AuditLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;
use Laravel\Fortify\Events\PasswordUpdatedViaController;
use Laravel\Fortify\Events\TwoFactorAuthenticationConfirmed;
use Laravel\Fortify\Events\TwoFactorAuthenticationDisabled;
use Laravel\Fortify\Events\TwoFactorAuthenticationFailed;

class RecordAuthenticationEvents
{
    public function handleLogin(Login $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        $event->user->forceFill(['last_login_at' => now()])->saveQuietly();
        AuditLogger::log($event->user, 'login', [], [], $event->user);
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user instanceof User) {
            AuditLogger::log($event->user, 'logout', [], [], $event->user);
        }
    }

    public function handleFailed(Failed $event): void
    {
        // Nur bekannte Konten protokollieren; keine Passwörter speichern.
        if ($event->user instanceof User) {
            AuditLogger::log($event->user, 'login_failed', [], [], $event->user);
        }
    }

    public function handleTwoFactor(object $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        $name = match (true) {
            $event instanceof TwoFactorAuthenticationConfirmed => 'two_factor_enabled',
            $event instanceof TwoFactorAuthenticationDisabled => 'two_factor_disabled',
            $event instanceof TwoFactorAuthenticationFailed => 'two_factor_failed',
            default => null,
        };
        if ($name) {
            AuditLogger::log($event->user, $name, [], [], $event->user);
        }
    }

    public function handlePasswordUpdated(PasswordUpdatedViaController $event): void
    {
        if ($event->user instanceof User) {
            AuditLogger::log($event->user, 'password_changed', [], [], $event->user);
        }
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            TwoFactorAuthenticationConfirmed::class => 'handleTwoFactor',
            TwoFactorAuthenticationDisabled::class => 'handleTwoFactor',
            TwoFactorAuthenticationFailed::class => 'handleTwoFactor',
            PasswordUpdatedViaController::class => 'handlePasswordUpdated',
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
            Failed::class => 'handleFailed',
        ];
    }
}
