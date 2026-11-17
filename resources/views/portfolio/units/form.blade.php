@php($editing = $unit->exists)
<x-app-layout :title="$editing ? $unit->name : __('app.new_unit')">
    <x-page-header :title="$editing ? __('app.edit_unit') : __('app.new_unit')" :subtitle="$building->property->name.' · '.$building->name" />
    <form method="POST" action="{{ $editing ? route('units.update', $unit) : route('units.store', $building) }}" class="card grid max-w-3xl gap-4 sm:grid-cols-2">
        @csrf
        @if ($editing) @method('PUT') @endif
        <x-form.input name="name" :label="__('app.name')" :value="$unit->name" required />
        <x-form.input name="floor" :label="__('app.floor')" :value="$unit->floor" />
        <x-form.input name="area_m2" type="number" step="0.01" min="0" :label="__('app.area_m2')" :value="$unit->area_m2" />
        <x-form.input name="rooms" type="number" step="0.5" min="0" :label="__('app.rooms')" :value="$unit->rooms" />
        <x-form.select name="usage_type" :label="__('app.usage_type')" :options="$usageTypes" :value="$unit->usage_type" />
        <x-form.textarea class="sm:col-span-2" name="notes" :label="__('app.notes')" :value="$unit->notes" />
        <div class="flex gap-2 sm:col-span-2">
            <button class="btn-primary">{{ __('app.save') }}</button>
            <a href="{{ $editing ? route('units.show', $unit) : route('buildings.show', $building) }}" class="btn-secondary">{{ __('app.cancel') }}</a>
        </div>
    </form>
</x-app-layout>
