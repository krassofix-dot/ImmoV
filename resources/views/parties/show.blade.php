<x-app-layout :title="$party->display_name">
    <x-page-header :title="$party->display_name" :subtitle="$party->role->label()">
        <x-slot:breadcrumbs><a class="link" href="{{ route('parties.index') }}">{{ __('app.nav.parties') }}</a></x-slot:breadcrumbs>
        <x-slot:actions>
            @can('parties.update')<a class="btn-secondary" href="{{ route('parties.edit', $party) }}">{{ __('app.edit') }}</a>@endcan
        </x-slot:actions>
    </x-page-header>
    <div class="grid gap-6 lg:grid-cols-3">
        <section class="card lg:col-span-2">
            <h2 class="mb-4 text-lg font-semibold">{{ __('app.occupancies') }}</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead class="bg-gray-50"><tr><th>{{ __('app.unit') }}</th><th>{{ __('app.type') }}</th><th>{{ __('app.period') }}</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse ($party->occupancies as $occ)
                        <tr>
                            <td><a class="link" href="{{ route('units.show', $occ->unit) }}">{{ $occ->unit->building->property->name }} · {{ $occ->unit->name }}</a></td>
                            <td><x-occupancy-badge :occupancy="$occ" /></td>
                            <td class="whitespace-nowrap">{{ $occ->start_date?->format('d.m.Y') ?? __('app.open') }} – {{ $occ->end_date?->format('d.m.Y') ?? __('app.ongoing') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-3 text-center text-gray-500">{{ __('app.empty') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
        <section class="card">
            @php($address = collect([$party->street, trim($party->postal_code.' '.$party->city), $party->country_code])->filter()->implode(PHP_EOL))
            <x-dl :items="[
                __('app.party_kind') => $party->kind->label(),
                __('app.email') => $party->email,
                __('app.phone') => $party->phone,
                __('app.address') => $address,
                __('app.iban') => $party->iban,
                __('app.notes') => $party->notes,
            ]" />
            @can('parties.delete')
                <div class="mt-6 border-t border-gray-200 pt-4"><x-delete-button :action="route('parties.destroy', $party)" /></div>
            @endcan
        </section>
    </div>
</x-app-layout>
