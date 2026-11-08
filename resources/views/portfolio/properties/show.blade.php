<x-app-layout :title="$property->name">
    <x-page-header :title="$property->name" :subtitle="collect([$property->country->name, $property->address_line])->filter()->implode(' · ')">
        <x-slot:breadcrumbs><a class="link" href="{{ route('properties.index') }}">{{ __('app.nav.properties') }}</a></x-slot:breadcrumbs>
        <x-slot:actions>
            @can('portfolio.update')<a class="btn-secondary" href="{{ route('properties.edit', $property) }}">{{ __('app.edit') }}</a>@endcan
            @can('portfolio.create')<a class="btn-primary" href="{{ route('buildings.create', $property) }}">{{ __('app.new_building') }}</a>@endcan
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="card lg:col-span-2">
            <h2 class="mb-4 text-lg font-semibold">{{ __('app.buildings_and_units') }}</h2>
            @forelse ($property->buildings as $building)
                <div class="mb-5 last:mb-0">
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <a class="link font-medium" href="{{ route('buildings.show', $building) }}">{{ $building->name }}</a>
                        @can('portfolio.create')<a class="text-sm link" href="{{ route('units.create', $building) }}">+ {{ __('app.new_unit') }}</a>@endcan
                    </div>
                    @include('portfolio.units._table', ['units' => $building->units])
                </div>
            @empty
                <p class="text-sm text-gray-500">{{ __('app.empty') }}</p>
            @endforelse
        </section>

        <section class="card">
            <h2 class="mb-4 text-lg font-semibold">{{ __('app.master_data') }}</h2>
            <x-dl :items="[
                __('app.property_type') => $property->property_type?->label(),
                __('app.ownership_type') => $property->ownership_type?->label(),
                __('app.construction_year') => $property->construction_year,
                __('app.region') => $property->region,
                __('app.plot_number') => $property->plot_number,
                __('app.plot_area_m2') => $property->plot_area_m2 ? $property->plot_area_m2.' m²' : null,
                __('app.purchase_price') => $property->purchase_price ? \App\Support\Currencies::format($property->purchase_price, $property->purchase_currency) : null,
                __('app.purchase_date') => $property->purchase_date?->format('d.m.Y'),
            ]" />
            <div class="mt-4 space-y-3 text-sm">
                <x-dl :items="[
                    __('app.land_registry_info') => $property->land_registry_info,
                    __('app.energy_supply') => $property->energy_supply,
                    __('app.technical_systems') => $property->technical_systems,
                    __('app.notes') => $property->notes,
                ]" />
            </div>
            @can('portfolio.delete')
                <div class="mt-6 border-t border-gray-200 pt-4"><x-delete-button :action="route('properties.destroy', $property)" /></div>
            @endcan
        </section>
    </div>
</x-app-layout>
