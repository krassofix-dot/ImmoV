@php($editing = $party->exists)
<x-app-layout :title="$editing ? $party->display_name : __('app.new_party')">
    <x-page-header :title="$editing ? __('app.edit_party') : __('app.new_party')" :subtitle="$editing ? $party->display_name : null" />
    <form method="POST" action="{{ $editing ? route('parties.update', $party) : route('parties.store') }}" class="card grid max-w-3xl gap-4 sm:grid-cols-2">
        @csrf
        @if ($editing) @method('PUT') @endif
        <x-form.select name="kind" :label="__('app.party_kind')" :options="$kinds" :value="$party->kind" :placeholder="false" required />
        <x-form.select name="role" :label="__('app.party_role')" :options="$roles" :value="$party->role" :placeholder="false" required />
        <x-form.input name="first_name" :label="__('app.first_name')" :value="$party->first_name" />
        <x-form.input name="last_name" :label="__('app.last_name')" :value="$party->last_name" />
        <x-form.input class="sm:col-span-2" name="company_name" :label="__('app.company_name')" :value="$party->company_name" :help="__('app.company_name_help')" />
        <x-form.input name="email" type="email" :label="__('app.email')" :value="$party->email" />
        <x-form.input name="phone" :label="__('app.phone')" :value="$party->phone" />
        <x-form.input class="sm:col-span-2" name="street" :label="__('app.street')" :value="$party->street" />
        <x-form.input name="postal_code" :label="__('app.postal_code')" :value="$party->postal_code" />
        <x-form.input name="city" :label="__('app.city')" :value="$party->city" />
        <x-form.input name="country_code" :label="__('app.country_code')" :value="$party->country_code" maxlength="2" />
        <x-form.input name="iban" :label="__('app.iban')" :value="$party->iban" />
        <x-form.textarea class="sm:col-span-2" name="notes" :label="__('app.notes')" :value="$party->notes" />
        <div class="flex gap-2 sm:col-span-2">
            <button class="btn-primary">{{ __('app.save') }}</button>
            <a href="{{ $editing ? route('parties.show', $party) : route('parties.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
        </div>
    </form>
</x-app-layout>
