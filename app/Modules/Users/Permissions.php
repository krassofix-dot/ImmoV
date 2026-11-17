<?php

namespace App\Modules\Users;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Zentrale Liste der Rechte und Standardrollen.
 *
 * Neue Module ergänzen hier ihre Rechte; `sync()` legt fehlende Rechte an,
 * ohne bestehende Rollenzuweisungen anzutasten (Ausnahme: Administrator
 * erhält immer alle Rechte).
 */
final class Permissions
{
    public const ADMIN_ROLE = 'administrator';

    /** @var array<string, list<string>> Modul => Rechte */
    public const ALL = [
        'dashboard' => ['dashboard.view'],
        'portfolio' => ['portfolio.view', 'portfolio.create', 'portfolio.update', 'portfolio.delete'],
        'parties' => ['parties.view', 'parties.create', 'parties.update', 'parties.delete'],
        'audit' => ['audit.view'],
        'users' => ['users.manage', 'roles.manage'],
        'settings' => ['settings.manage'],
    ];

    /** @var array<string, list<string>> Standardrechte je Rolle beim ersten Anlegen */
    public const DEFAULT_ROLES = [
        'administrator' => ['*'],
        'verwalter' => [
            'dashboard.view',
            'portfolio.view', 'portfolio.create', 'portfolio.update', 'portfolio.delete',
            'parties.view', 'parties.create', 'parties.update', 'parties.delete',
            'audit.view',
        ],
        'mitarbeiter' => [
            'dashboard.view',
            'portfolio.view', 'portfolio.create', 'portfolio.update',
            'parties.view', 'parties.create', 'parties.update',
        ],
    ];

    /** @return list<string> */
    public static function flat(): array
    {
        return array_merge(...array_values(self::ALL));
    }

    public static function sync(string $guard = 'web'): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::flat() as $name) {
            Permission::findOrCreate($name, $guard);
        }

        foreach (self::DEFAULT_ROLES as $roleName => $perms) {
            $role = Role::where('name', $roleName)->where('guard_name', $guard)->first();
            $isNew = $role === null;
            $role ??= Role::create(['name' => $roleName, 'guard_name' => $guard]);

            if ($perms === ['*']) {
                $role->syncPermissions(self::flat());
            } elseif ($isNew) {
                $role->syncPermissions($perms);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
