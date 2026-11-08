<div class="overflow-x-auto rounded border border-gray-100">
    <table class="table">
        <thead class="bg-gray-50"><tr>
            <th>{{ __('app.unit') }}</th><th class="hidden sm:table-cell">{{ __('app.floor') }}</th><th class="hidden sm:table-cell">{{ __('app.usage_type') }}</th>
            <th>{{ __('app.current_occupancy') }}</th><th class="hidden md:table-cell">{{ __('app.party') }}</th><th class="hidden md:table-cell text-right">{{ __('app.area') }}</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
        @forelse ($units as $unit)
            @php($occ = $unit->occupancyOn())
            <tr>
                <td><a class="link" href="{{ route('units.show', $unit) }}">{{ $unit->name }}</a></td>
                <td class="hidden sm:table-cell">{{ $unit->floor ?? '–' }}</td>
                <td class="hidden sm:table-cell">{{ $unit->usage_type?->label() ?? '–' }}</td>
                <td><x-occupancy-badge :occupancy="$occ" /></td>
                <td class="hidden md:table-cell">{{ $occ?->party?->display_name ?? '–' }}</td>
                <td class="hidden md:table-cell text-right">{{ $unit->area_m2 ? $unit->area_m2.' m²' : '–' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="py-3 text-center text-gray-500">{{ __('app.empty') }}</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
