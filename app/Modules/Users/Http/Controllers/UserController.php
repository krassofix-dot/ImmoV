<?php

namespace App\Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\System\Support\AuditLogger;
use App\Modules\Users\Permissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', ['users' => User::with('roles')->orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('users.form', ['user' => new User(['locale' => 'de', 'is_active' => true]), 'roles' => Role::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'locale' => ['required', Rule::in(User::LOCALES)],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => Str::lower($data['email']),
            'password' => $data['password'],
            'locale' => $data['locale'],
            'is_active' => true,
        ]);
        $user->syncRoles([$data['role']]);
        AuditLogger::log($user, 'roles_updated', ['roles' => []], ['roles' => [$data['role']]]);

        return redirect()->route('users.index')->with('status', __('app.saved'));
    }

    public function edit(User $user): View
    {
        return view('users.form', ['user' => $user, 'roles' => Role::orderBy('name')->get()]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'locale' => ['required', Rule::in(User::LOCALES)],
            'role' => ['required', 'exists:roles,name'],
        ]);
        $isActive = $request->boolean('is_active');

        // Schutz vor Aussperren: das eigene Konto und den letzten aktiven Administrator nicht entmachten
        $losesAdmin = $user->hasRole(Permissions::ADMIN_ROLE) && ($data['role'] !== Permissions::ADMIN_ROLE || ! $isActive);
        if ($losesAdmin) {
            $activeAdmins = User::role(Permissions::ADMIN_ROLE)->where('is_active', true)->count();
            if ($user->is($request->user()) || $activeAdmins <= 1) {
                return back()->withInput()->withErrors(['role' => __('app.last_admin')]);
            }
        }

        $user->fill([
            'name' => $data['name'],
            'email' => Str::lower($data['email']),
            'locale' => $data['locale'],
            'is_active' => $isActive,
        ]);
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        $oldRoles = $user->getRoleNames()->all();
        if ($oldRoles !== [$data['role']]) {
            $user->syncRoles([$data['role']]);
            AuditLogger::log($user, 'roles_updated', ['roles' => $oldRoles], ['roles' => [$data['role']]]);
        }

        return redirect()->route('users.index')->with('status', __('app.saved'));
    }

    /** Setzt die Zwei-Faktor-Anmeldung zurück (z. B. bei Verlust des Geräts). */
    public function resetTwoFactor(User $user): RedirectResponse
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->saveQuietly();
        AuditLogger::log($user, 'two_factor_reset');

        return back()->with('status', __('app.two_factor_reset_done'));
    }
}
