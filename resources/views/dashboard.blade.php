<x-app-layout :title="__('app.nav.dashboard')">
    <x-page-header :title="__('app.nav.dashboard')" />

    <form method="GET" class="card mb-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-6 lg:items-end">
        <x-form.select name="country" :label="__('app.country')" :options="$countries->pluck('name', 'id')" :value="$filters['country']" :placeholder="__('app.whole_portfolio')" />
        <x-form.select name="property" :label="__('app.property')" :options="$allProperties->when($filters['country'], fn ($c) => $c->where('country_id', $filters['country']))->pluck('name', 'id')" :value="$filters['property']" :placeholder="__('app.all')" />
        <x-form.select name="building" :label="__('app.building')" :options="$allBuildings->when($filters['property'], fn ($c) => $c->where('property_id', $filters['property']))->mapWithKeys(fn ($b) => [$b->id => $b->property->name.' · '.$b->name])" :value="$filters['building']" :placeholder="__('app.all')" />
        <x-form.select name="unit" :label="__('app.unit')" :options="$allUnits->when($filters['building'], fn ($c) => $c->where('building_id', $filters['building']))->mapWithKeys(fn ($u) => [$u->id => $u->building->name.' · '.$u->name])" :value="$filters['unit']" :placeholder="__('app.all')" />
        <div class="text-xs text-gray-500">{{ __('app.filters_later') }}</div>
        <div class="flex gap-2">
            <button class="btn-primary">{{ __('app.filter') }}</button>
            <a href="{{ route('dashboard') }}" class="btn-secondary">{{ __('app.reset') }}</a>
        </div>
    </form>

    <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
        @foreach (['properties', 'buildings', 'units', 'parties'] as $key)
            <div class="card">
                <div class="text-sm text-gray-500">{{ __('app.kpi.'.$key) }}</div>
                <div class="mt-1 text-2xl font-semibold">{{ $counts[$key] }}</div>
            </div>
        @endforeach
    </div>

    <div class="mb-6 grid gap-6 lg:grid-cols-3">
        <section class="card">
            <h2 class="mb-3 text-lg font-semibold">{{ __('app.kpi.usage_today') }}</h2>
            <ul class="space-y-2 text-sm">
                @foreach (\App\Modules\Parties\Enums\OccupancyType::cases() as $type)
                    <li class="flex justify-between"><span>{{ $type->label() }}</span><span class="font-medium">{{ $byType[$type->value] }}</span></li>
                @endforeach
                <li class="flex justify-between text-gray-500"><span>{{ __('app.no_occupancy') }}</span><span>{{ $byType['none'] }}</span></li>
            </ul>
        </section>

        <section class="card">
            <h2 class="mb-1 text-lg font-semibold">{{ __('app.kpi.contract_monthly') }}</h2>
            <p class="mb-3 text-xs text-gray-500">{{ __('app.kpi.contract_monthly_help') }}</p>
            @forelse ($monthly as $currency => $sums)
                <dl class="mb-2 grid grid-cols-2 gap-1 text-sm">
                    <dt>{{ __('app.rent_net') }}</dt><dd class="text-right font-medium">{{ \App\Support\Currencies::format($sums['rent'], $currency) }}</dd>
                    <dt>{{ __('app.advance_payment') }}</dt><dd class="text-right font-medium">{{ \App\Support\Currencies::format($sums['advance'], $currency) }}</dd>
                </dl>
            @empty
                <p class="text-sm text-gray-500">{{ __('app.empty') }}</p>
            @endforelse
        </section>

        <section class="card">
            <h2 class="mb-3 text-lg font-semibold">{{ __('app.kpi.finance') }}</h2>
            <ul class="space-y-1 text-sm text-gray-500">
                @foreach (['running_costs', 'income', 'investments', 'maintenance_costs', 'open_invoices', 'due_payments', 'upcoming_maintenance', 'recent_imports'] as $k)
                    <li class="flex justify-between"><span>{{ __('app.kpi.'.$k) }}</span><span>{{ __('app.coming_soon') }}</span></li>
                @endforeach
            </ul>
        </section>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="lg:col-span-2">
            <h2 class="mb-3 text-lg font-semibold">{{ __('app.kpi.by_property') }}</h2>
            <div class="grid gap-3 sm:grid-cols-2">
                @forelse ($propertyCards as $p)
                    <a href="{{ route('properties.show', $p) }}" class="card block transition hover:border-emerald-300">
                        <div class="text-xs text-gray-500">{{ $p->country->name }}</div>
                        <div class="font-medium">{{ $p->name }}</div>
                        <div class="mt-1 text-sm text-gray-600">{{ $p->property_type?->label() }}</div>
                        <div class="mt-2 text-xs text-gray-500">{{ trans_choice('app.n_buildings', $p->buildings_count) }} · {{ trans_choice('app.n_units', $p->units_count) }}</div>
                    </a>
                @empty
                    <p class="text-sm text-gray-500">{{ __('app.empty') }}</p>
                @endforelse
            </div>
        </section>

        @can('audit.view')
            <section class="card">
                <h2 class="mb-3 text-lg font-semibold">{{ __('app.kpi.recent_changes') }}</h2>
                @include('system._audit_list', ['audits' => $recent, 'labels' => $labels])
            </section>
        @endcan
    </div>
</x-app-layout>
