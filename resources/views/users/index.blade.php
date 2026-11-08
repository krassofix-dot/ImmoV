<x-app-layout :title="__('app.nav.users')">
    <x-page-header :title="__('app.nav.users')">
        <x-slot:actions><a href="{{ route('users.create') }}" class="btn-primary">{{ __('app.new_user') }}</a></x-slot:actions>
    </x-page-header>
    <div class="card overflow-x-auto p-0 sm:p-0">
        <table class="table">
            <thead class="bg-gray-50"><tr><th>{{ __('app.name') }}</th><th>{{ __('app.email') }}</th><th>{{ __('app.role') }}</th><th class="hidden sm:table-cell">{{ __('app.two_factor') }}</th><th class="hidden md:table-cell">{{ __('app.last_login') }}</th><th>{{ __('app.status') }}</th><th></th></tr></thead>
            <tbody class="divide-y divide-gray-100">
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td><td>{{ $user->email }}</td>
                    <td>{{ $user->roles->map(fn ($r) => __('app.roles.'.$r->name))->implode(', ') }}</td>
                    <td class="hidden sm:table-cell">{{ $user->hasTwoFactorEnabled() ? __('app.active') : '–' }}</td>
                    <td class="hidden md:table-cell">{{ $user->last_login_at?->format('d.m.Y H:i') ?? '–' }}</td>
                    <td>{{ $user->is_active ? __('app.active') : __('app.inactive') }}</td>
                    <td class="text-right"><a class="link" href="{{ route('users.edit', $user) }}">{{ __('app.edit') }}</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
