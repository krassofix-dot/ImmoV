<x-app-layout :title="__('app.nav.roles')">
    <x-page-header :title="__('app.nav.roles')" :subtitle="__('app.roles_help')" />
    <form method="POST" action="{{ route('roles.update') }}">
        @csrf @method('PUT')
        <div class="card overflow-x-auto p-0 sm:p-0">
            <table class="table">
                <thead class="bg-gray-50"><tr>
                    <th>{{ __('app.permission') }}</th>
                    @foreach ($roles as $role)<th class="text-center">{{ __('app.roles.'.$role->name) }}</th>@endforeach
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                @foreach ($groups as $group => $permissions)
                    <tr class="bg-gray-50"><td colspan="{{ $roles->count() + 1 }}" class="font-semibold">{{ __('app.permission_groups.'.$group) }}</td></tr>
                    @foreach ($permissions as $perm)
                        <tr>
                            <td>{{ __('app.permissions.'.str_replace('.', '_', $perm)) }} <span class="font-mono text-xs text-gray-400">{{ $perm }}</span></td>
                            @foreach ($roles as $role)
                                <td class="text-center">
                                    @if ($role->name === \App\Modules\Users\Permissions::ADMIN_ROLE)
                                        <input type="checkbox" checked disabled aria-label="{{ $perm }}">
                                    @else
                                        <input type="checkbox" name="permissions[{{ $role->id }}][]" value="{{ $perm }}" @checked($role->permissions->contains('name', $perm)) aria-label="{{ $role->name }} {{ $perm }}">
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
        <button class="btn-primary mt-4">{{ __('app.save') }}</button>
    </form>
</x-app-layout>
