<?php

namespace App\Modules\System\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use OwenIt\Auditing\Models\Audit;

/**
 * Schreibt Protokolleinträge, die nicht aus einem Eloquent-Ereignis
 * entstehen (Anmeldungen, Rollenänderungen, 2FA …), in dieselbe
 * audits-Tabelle wie owen-it/laravel-auditing.
 */
class AuditLogger
{
    public static function log(Model $subject, string $event, array $old = [], array $new = [], ?Model $actor = null): Audit
    {
        $actor ??= Auth::user();

        return Audit::create([
            'user_type' => $actor?->getMorphClass(),
            'user_id' => $actor?->getKey(),
            'event' => $event,
            'auditable_type' => $subject->getMorphClass(),
            'auditable_id' => $subject->getKey(),
            'old_values' => $old,
            'new_values' => $new,
            'url' => app()->runningInConsole() ? 'console' : Request::fullUrl(),
            'ip_address' => Request::ip(),
            'user_agent' => mb_substr((string) Request::userAgent(), 0, 1023),
        ]);
    }
}
