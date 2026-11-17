@php($editing = $building->exists)
<x-app-layout :title="$editing ? $building->name : __('app.new_building')">
    <x-page-header :title="$editing ? __('app.edit_building') : __('app.new_building')" :subtitle="$property->name" />
    <form method="POST" action="{{ $editing ? route('buildings.update', $building) : route('buildings.store', $property) }}" class="card grid max-w-3xl gap-4 sm:grid-cols-2">
        @csrf
        @if ($editing) @method('PUT') @endif
        <x-form.input class="sm:col-span-2" name="name" :label="__('app.name')" :value="$building->name" required />
        <x-form.input name="construction_year" type="number" :label="__('app.construction_year')" :value="$building->construction_year" />
        <x-form.input name="floors" type="number" min="0" :label="__('app.floors')" :value="$building->floors" />
        <x-form.textarea class="sm:col-span-2" name="notes" :label="__('app.notes')" :value="$building->notes" />
        <div class="flex gap-2 sm:col-span-2">
            <button class="btn-primary">{{ __('app.save') }}</button>
            <a href="{{ $editing ? route('buildings.show', $building) : route('properties.show', $property) }}" class="btn-secondary">{{ __('app.cancel') }}</a>
        </div>
    </form>
</x-app-layout>
