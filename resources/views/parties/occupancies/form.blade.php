@php($editing = $occupancy->exists)
<x-app-layout :title="__('app.occupancy')">
    <x-page-header :title="$editing ? __('app.edit_occupancy') : __('app.new_occupancy')" :subtitle="$unit->building->property->name.' · '.$unit->name" />
    <form method="POST" action="{{ $editing ? route('occupancies.update', $occupancy) : route('occupancies.store', $unit) }}" class="card grid max-w-3xl gap-4 sm:grid-cols-2">
        @csrf
        @if ($editing) @method('PUT') @endif
        <x-form.select name="type" :label="__('app.type')" :options="$types" :value="$occupancy->type" required />
        <x-form.select name="party_id" :label="__('app.party')" :options="$parties->mapWithKeys(fn ($p) => [$p->id => $p->display_name.' ('.$p->role->label().')'])" :value="$occupancy->party_id" />
        <x-form.input name="start_date" type="date" :label="__('app.start_date')" :value="$occupancy->start_date?->format('Y-m-d')" :help="__('app.start_date_help')" />
        <x-form.input name="end_date" type="date" :label="__('app.end_date')" :value="$occupancy->end_date?->format('Y-m-d')" :help="__('app.end_date_help')" />
        <x-form.input name="rent_net" type="number" step="0.01" min="0" :label="__('app.rent_net_monthly')" :value="$occupancy->rent_net" />
        <x-form.input name="advance_payment" type="number" step="0.01" min="0" :label="__('app.advance_payment_monthly')" :value="$occupancy->advance_payment" />
        <x-form.select name="currency" :label="__('app.currency')" :options="$currencies" :value="$occupancy->currency" />
        <x-form.input name="contract_reference" :label="__('app.contract_reference')" :value="$occupancy->contract_reference" />
        <x-form.textarea class="sm:col-span-2" name="notes" :label="__('app.notes')" :value="$occupancy->notes" />
        <div class="flex flex-wrap gap-2 sm:col-span-2">
            <button class="btn-primary">{{ __('app.save') }}</button>
            <a href="{{ route('units.show', $unit) }}" class="btn-secondary">{{ __('app.cancel') }}</a>
        </div>
    </form>
    @if ($editing)
        @can('parties.delete')
            <div class="mt-4 max-w-3xl"><x-delete-button :action="route('occupancies.destroy', $occupancy)" /></div>
        @endcan
    @endif
</x-app-layout>
