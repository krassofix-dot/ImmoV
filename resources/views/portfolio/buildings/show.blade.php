<x-app-layout :title="$building->name">
    <x-page-header :title="$building->name" :subtitle="__('app.building')">
        <x-slot:breadcrumbs>
            <a class="link" href="{{ route('properties.index') }}">{{ __('app.nav.properties') }}</a> /
            <a class="link" href="{{ route('properties.show', $building->property) }}">{{ $building->property->name }}</a>
        </x-slot:breadcrumbs>
        <x-slot:actions>
            @can('portfolio.update')<a class="btn-secondary" href="{{ route('buildings.edit', $building) }}">{{ __('app.edit') }}</a>@endcan
            @can('portfolio.create')<a class="btn-primary" href="{{ route('units.create', $building) }}">{{ __('app.new_unit') }}</a>@endcan
        </x-slot:actions>
    </x-page-header>
    <div class="grid gap-6 lg:grid-cols-3">
        <section class="card lg:col-span-2">
            <h2 class="mb-4 text-lg font-semibold">{{ __('app.units') }}</h2>
            @include('portfolio.units._table', ['units' => $building->units])
        </section>
        <section class="card">
            <x-dl :items="[__('app.construction_year') => $building->construction_year, __('app.floors') => $building->floors, __('app.notes') => $building->notes]" />
            @can('portfolio.delete')
                <div class="mt-6 border-t border-gray-200 pt-4"><x-delete-button :action="route('buildings.destroy', $building)" /></div>
            @endcan
        </section>
    </div>
</x-app-layout>
