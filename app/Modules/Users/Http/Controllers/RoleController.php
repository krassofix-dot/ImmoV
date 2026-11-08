<?php

namespace App\Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\System\Support\AuditLogger;
use App\Modules\Users\Permissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index(): View
    {
        return view('users.roles', [
            'roles' => Role::with('permissions')->orderBy('id')->get(),
            'groups' => Permissions::ALL,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['array'],
            'permissions.*.*' => ['string', 'in:'.implode(',', Permissions::flat())],
        ]);

        $actor = $request->user();
        foreach (Role::with('permissions')->where('name', '!=', Permissions::ADMIN_ROLE)->get() as $role) {
            $old = $role->permissions->pluck('name')->sort()->values()->all();
            $new = collect($request->input("permissions.{$role->id}", []))->unique()->sort()->values()->all();
            if ($old !== $new) {
                $role->syncPermissions($new);
                // Rollen sind keine auditierbaren Modelle; Änderung beim handelnden Benutzer festhalten.
                AuditLogger::log($actor instanceof User ? $actor : $role, 'role_permissions_updated',
                    ['role' => $role->name, 'permissions' => $old],
                    ['role' => $role->name, 'permissions' => $new]);
            }
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('status', __('app.saved'));
    }
}
