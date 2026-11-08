@php($editing = $country->exists)
<x-app-layout :title="__('app.nav.countries')">
    <x-page-header :title="$editing ? $country->name : __('app.new_country')" />
    <form method="POST" action="{{ $editing ? route('countries.update', $country) : route('countries.store') }}" class="card grid max-w-2xl gap-4 sm:grid-cols-2">
        @csrf
        @if ($editing) @method('PUT') @endif
        <x-form.input name="code" :label="__('app.code_iso')" :value="$country->code" maxlength="2" required />
        <x-form.select name="default_currency" :label="__('app.currency')" :options="$currencies" :value="$country->default_currency" required />
        <x-form.input name="name_de" :label="__('app.name_de')" :value="$country->name_de" required />
        <x-form.input name="name_en" :label="__('app.name_en')" :value="$country->name_en" required />
        <x-form.input name="sort_order" type="number" min="0" :label="__('app.sort_order')" :value="$country->sort_order" />
        <label class="flex items-center gap-2 self-end text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $country->is_active))> {{ __('app.active') }}</label>
        <div class="flex gap-2 sm:col-span-2">
            <button class="btn-primary">{{ __('app.save') }}</button>
            <a href="{{ route('countries.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
        </div>
    </form>
</x-app-layout>
