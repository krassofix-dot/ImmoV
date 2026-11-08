<x-app-layout :title="$unit->name">
    <x-page-header :title="$unit->name" :subtitle="$unit->usage_type?->label()">
        <x-slot:breadcrumbs>
            <a class="link" href="{{ route('properties.show', $unit->building->property) }}">{{ $unit->building->property->name }}</a> /
            <a class="link" href="{{ route('buildings.show', $unit->building) }}">{{ $unit->building->name }}</a>
        </x-slot:breadcrumbs>
        <x-slot:actions>
            @can('portfolio.update')<a class="btn-secondary" href="{{ route('units.edit', $unit) }}">{{ __('app.edit') }}</a>@endcan
            @can('parties.create')<a class="btn-primary" href="{{ route('occupancies.create', $unit) }}">{{ __('app.new_occupancy') }}</a>@endcan
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="card lg:col-span-2">
            <h2 class="mb-4 text-lg font-semibold">{{ __('app.occupancies') }}</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead class="bg-gray-50"><tr>
                        <th>{{ __('app.type') }}</th><th>{{ __('app.party') }}</th><th>{{ __('app.period') }}</th>
                        <th class="text-right">{{ __('app.rent_net') }}</th><th class="text-right">{{ __('app.advance_payment') }}</th><th></th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse ($unit->occupancies as $occ)
                        <tr>
                            <td><x-occupancy-badge :occupancy="$occ" /></td>
                            <td>@if ($occ->party)<a class="link" href="{{ route('parties.show', $occ->party) }}">{{ $occ->party->display_name }}</a>@else – @endif</td>
                            <td class="whitespace-nowrap">{{ $occ->start_date?->format('d.m.Y') ?? __('app.open') }} – {{ $occ->end_date?->format('d.m.Y') ?? __('app.ongoing') }}</td>
                            <td class="text-right whitespace-nowrap">{{ \App\Support\Currencies::format($occ->rent_net, $occ->currency) }}</td>
                            <td class="text-right whitespace-nowrap">{{ \App\Support\Currencies::format($occ->advance_payment, $occ->currency) }}</td>
                            <td class="text-right">@can('parties.update')<a class="link text-sm" href="{{ route('occupancies.edit', $occ) }}">{{ __('app.edit') }}</a>@endcan</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-3 text-center text-gray-500">{{ __('app.empty') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <p class="mt-3 text-xs text-gray-500">{{ __('app.later_phase_unit') }}</p>
        </section>

        <section class="card">
            <x-dl :items="[__('app.floor') => $unit->floor, __('app.area_m2') => $unit->area_m2, __('app.rooms') => $unit->rooms, __('app.notes') => $unit->notes]" />
            @can('portfolio.delete')
                <div class="mt-6 border-t border-gray-200 pt-4"><x-delete-button :action="route('units.destroy', $unit)" /></div>
            @endcan
        </section>

        @if ($history->isNotEmpty())
            <section class="card lg:col-span-3">
                <h2 class="mb-4 text-lg font-semibold">{{ __('app.history') }}</h2>
                @include('system._audit_list', ['audits' => $history, 'labels' => \App\Modules\System\Http\Controllers\AuditController::subjectLabels($history)])
            </section>
        @endif
    </div>
</x-app-layout>
