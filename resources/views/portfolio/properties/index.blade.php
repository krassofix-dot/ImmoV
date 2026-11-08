<x-app-layout :title="__('app.nav.properties')">
    <x-page-header :title="__('app.nav.properties')">
        @can('portfolio.create')
            <x-slot:actions><a href="{{ route('properties.create') }}" class="btn-primary">{{ __('app.new_property') }}</a></x-slot:actions>
        @endcan
    </x-page-header>

    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3">
        <x-form.select name="country" :label="__('app.country')" :options="$countries->pluck('name', 'id')" :value="request('country')" :placeholder="__('app.all')" />
        <x-form.input name="q" :label="__('app.search')" :value="request('q')" />
        <button class="btn-secondary">{{ __('app.filter') }}</button>
    </form>

    <div class="card overflow-x-auto p-0 sm:p-0">
        <table class="table">
            <thead class="bg-gray-50"><tr>
                <th>{{ __('app.name') }}</th><th>{{ __('app.country') }}</th><th class="hidden md:table-cell">{{ __('app.address') }}</th>
                <th class="hidden sm:table-cell">{{ __('app.property_type') }}</th><th class="text-right">{{ __('app.buildings') }}</th><th class="text-right">{{ __('app.units') }}</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
            @forelse ($properties as $property)
                <tr>
                    <td><a class="link font-medium" href="{{ route('properties.show', $property) }}">{{ $property->name }}</a></td>
                    <td>{{ $property->country->name }}</td>
                    <td class="hidden md:table-cell">{{ $property->address_line ?: '–' }}</td>
                    <td class="hidden sm:table-cell">{{ $property->property_type?->label() ?? '–' }}</td>
                    <td class="text-right">{{ $property->buildings_count }}</td>
                    <td class="text-right">{{ $property->units_count }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-6 text-center text-gray-500">{{ __('app.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
