<x-app-layout :title="__('app.nav.countries')">
    <x-page-header :title="__('app.nav.countries')">
        <x-slot:actions><a href="{{ route('countries.create') }}" class="btn-primary">{{ __('app.new_country') }}</a></x-slot:actions>
    </x-page-header>
    <div class="card overflow-x-auto p-0 sm:p-0">
        <table class="table">
            <thead class="bg-gray-50"><tr><th>{{ __('app.code') }}</th><th>{{ __('app.name') }}</th><th>{{ __('app.currency') }}</th><th class="text-right">{{ __('app.nav.properties') }}</th><th>{{ __('app.status') }}</th><th></th></tr></thead>
            <tbody class="divide-y divide-gray-100">
            @foreach ($countries as $country)
                <tr>
                    <td class="font-mono">{{ $country->code }}</td><td>{{ $country->name }}</td><td>{{ $country->default_currency }}</td>
                    <td class="text-right">{{ $country->properties_count }}</td>
                    <td>{{ $country->is_active ? __('app.active') : __('app.inactive') }}</td>
                    <td class="text-right"><a class="link" href="{{ route('countries.edit', $country) }}">{{ __('app.edit') }}</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
