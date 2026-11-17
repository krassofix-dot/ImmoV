<x-app-layout :title="__('app.nav.parties')">
    <x-page-header :title="__('app.nav.parties')">
        @can('parties.create')
            <x-slot:actions><a href="{{ route('parties.create') }}" class="btn-primary">{{ __('app.new_party') }}</a></x-slot:actions>
        @endcan
    </x-page-header>
    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3">
        <x-form.select name="role" :label="__('app.party_role')" :options="$roles" :value="request('role')" :placeholder="__('app.all')" />
        <x-form.input name="q" :label="__('app.search')" :value="request('q')" />
        <button class="btn-secondary">{{ __('app.filter') }}</button>
    </form>
    <div class="card overflow-x-auto p-0 sm:p-0">
        <table class="table">
            <thead class="bg-gray-50"><tr><th>{{ __('app.name') }}</th><th>{{ __('app.party_role') }}</th><th class="hidden sm:table-cell">{{ __('app.email') }}</th><th class="hidden md:table-cell">{{ __('app.phone') }}</th><th class="hidden md:table-cell">{{ __('app.city') }}</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
            @forelse ($parties as $party)
                <tr>
                    <td><a class="link font-medium" href="{{ route('parties.show', $party) }}">{{ $party->display_name }}</a></td>
                    <td>{{ $party->role->label() }}</td>
                    <td class="hidden sm:table-cell">{{ $party->email ?? '–' }}</td>
                    <td class="hidden md:table-cell">{{ $party->phone ?? '–' }}</td>
                    <td class="hidden md:table-cell">{{ $party->city ?? '–' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-6 text-center text-gray-500">{{ __('app.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $parties->links() }}</div>
</x-app-layout>
