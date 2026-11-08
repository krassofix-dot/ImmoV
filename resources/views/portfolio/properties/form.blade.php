@php($editing = $property->exists)
<x-app-layout :title="$editing ? $property->name : __('app.new_property')">
    <x-page-header :title="$editing ? __('app.edit_property') : __('app.new_property')" :subtitle="$editing ? $property->name : null" />

    <form method="POST" action="{{ $editing ? route('properties.update', $property) : route('properties.store') }}" class="space-y-6">
        @csrf
        @if ($editing) @method('PUT') @endif

        <section class="card grid gap-4 sm:grid-cols-2">
            <h2 class="text-lg font-semibold sm:col-span-2">{{ __('app.master_data') }}</h2>
            <x-form.input name="name" :label="__('app.name')" :value="$property->name" required />
            <x-form.select name="country_id" :label="__('app.country')" :options="$countries->pluck('name', 'id')" :value="$property->country_id" required />
            <x-form.select name="property_type" :label="__('app.property_type')" :options="$propertyTypes" :value="$property->property_type" />
            <x-form.select name="ownership_type" :label="__('app.ownership_type')" :options="$ownershipTypes" :value="$property->ownership_type" />
            <x-form.input name="construction_year" type="number" :label="__('app.construction_year')" :value="$property->construction_year" min="1000" max="2100" />
        </section>

        <section class="card grid gap-4 sm:grid-cols-2">
            <h2 class="text-lg font-semibold sm:col-span-2">{{ __('app.address') }}</h2>
            <x-form.input class="sm:col-span-2" name="street" :label="__('app.street')" :value="$property->street" />
            <x-form.input name="postal_code" :label="__('app.postal_code')" :value="$property->postal_code" />
            <x-form.input name="city" :label="__('app.city')" :value="$property->city" />
            <x-form.input name="region" :label="__('app.region')" :value="$property->region" />
        </section>

        <section class="card grid gap-4 sm:grid-cols-2">
            <h2 class="text-lg font-semibold sm:col-span-2">{{ __('app.plot_and_purchase') }}</h2>
            <x-form.input name="plot_number" :label="__('app.plot_number')" :value="$property->plot_number" />
            <x-form.input name="plot_area_m2" type="number" step="0.01" min="0" :label="__('app.plot_area_m2')" :value="$property->plot_area_m2" />
            <x-form.textarea class="sm:col-span-2" name="land_registry_info" :label="__('app.land_registry_info')" :value="$property->land_registry_info" />
            <x-form.input name="purchase_price" type="number" step="0.01" min="0" :label="__('app.purchase_price')" :value="$property->purchase_price" />
            <x-form.select name="purchase_currency" :label="__('app.currency')" :options="$currencies" :value="$property->purchase_currency" />
            <x-form.input name="purchase_date" type="date" :label="__('app.purchase_date')" :value="$property->purchase_date?->format('Y-m-d')" />
        </section>

        <section class="card grid gap-4">
            <h2 class="text-lg font-semibold">{{ __('app.technical') }}</h2>
            <x-form.textarea name="energy_supply" :label="__('app.energy_supply')" :value="$property->energy_supply" />
            <x-form.textarea name="technical_systems" :label="__('app.technical_systems')" :value="$property->technical_systems" />
            <x-form.textarea name="notes" :label="__('app.notes')" :value="$property->notes" rows="4" />
            <p class="text-xs text-gray-500">{{ __('app.later_phase_property') }}</p>
        </section>

        <div class="flex gap-2">
            <button class="btn-primary">{{ __('app.save') }}</button>
            <a href="{{ $editing ? route('properties.show', $property) : route('properties.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
        </div>
    </form>
</x-app-layout>
