<?php

namespace App\Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Parties\Enums\OccupancyType;
use App\Modules\Parties\Models\Occupancy;
use App\Modules\Parties\Models\Party;
use App\Modules\Portfolio\Models\Building;
use App\Modules\Portfolio\Models\Country;
use App\Modules\Portfolio\Models\Property;
use App\Modules\Portfolio\Models\Unit;
use App\Modules\System\Http\Controllers\AuditController;
use Brick\Math\BigDecimal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use OwenIt\Auditing\Models\Audit;

/**
 * Portfolio-Übersicht. Finanzkennzahlen folgen mit dem Finanzmodul (Phase 3);
 * bis dahin zeigt das Dashboard nur Werte, die aus Stammdaten und
 * Nutzungsverträgen eindeutig ableitbar sind.
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = [
            'country' => $request->integer('country') ?: null,
            'property' => $request->integer('property') ?: null,
            'building' => $request->integer('building') ?: null,
            'unit' => $request->integer('unit') ?: null,
        ];
        $today = Carbon::today();

        $properties = Property::query()
            ->when($filters['country'], fn ($q, $id) => $q->where('country_id', $id))
            ->when($filters['property'], fn ($q, $id) => $q->whereKey($id));

        $buildings = Building::query()
            ->whereIn('property_id', (clone $properties)->select('id'))
            ->when($filters['building'], fn ($q, $id) => $q->whereKey($id));

        $units = Unit::query()
            ->whereIn('building_id', (clone $buildings)->select('id'))
            ->when($filters['unit'], fn ($q, $id) => $q->whereKey($id));

        $activeOccupancies = Occupancy::query()
            ->whereIn('unit_id', (clone $units)->select('id'))
            ->activeOn($today)
            ->get(['id', 'unit_id', 'type', 'rent_net', 'advance_payment', 'currency']);

        // Status je Einheit (bei mehreren gültigen Nutzungen zählt die Einheit einmal je Typ)
        $unitIds = (clone $units)->pluck('id');
        $byType = [];
        foreach (OccupancyType::cases() as $type) {
            $byType[$type->value] = $activeOccupancies->where('type', $type)->pluck('unit_id')->unique()->count();
        }
        $byType['none'] = $unitIds->diff($activeOccupancies->pluck('unit_id'))->count();

        // Vertragliche Soll-Werte pro Monat, je Währung getrennt (keine Umrechnung)
        $monthly = [];
        foreach ($activeOccupancies->where('type', OccupancyType::Rental) as $o) {
            $cur = $o->currency ?? '—';
            $monthly[$cur] ??= ['rent' => '0.00', 'advance' => '0.00'];
            $monthly[$cur]['rent'] = (string) BigDecimal::of($monthly[$cur]['rent'])->plus((string) ($o->rent_net ?? '0'))->toScale(2);
            $monthly[$cur]['advance'] = (string) BigDecimal::of($monthly[$cur]['advance'])->plus((string) ($o->advance_payment ?? '0'))->toScale(2);
        }
        ksort($monthly);

        $propertyCards = (clone $properties)
            ->with('country')
            ->withCount(['buildings', 'units'])
            ->orderBy('country_id')->orderBy('name')
            ->get();

        $recent = collect();
        $labels = [];
        if ($request->user()->can('audit.view')) {
            $recent = Audit::query()->with('user')
                ->whereNotIn('event', ['login', 'logout', 'login_failed'])
                ->latest('id')->limit(10)->get();
            $labels = AuditController::subjectLabels($recent);
        }

        return view('dashboard', [
            'filters' => $filters,
            'counts' => [
                'properties' => (clone $properties)->count(),
                'buildings' => (clone $buildings)->count(),
                'units' => $unitIds->count(),
                'parties' => Party::count(),
            ],
            'byType' => $byType,
            'monthly' => $monthly,
            'propertyCards' => $propertyCards,
            'recent' => $recent,
            'labels' => $labels,
            'countries' => Country::ordered()->get(),
            'allProperties' => Property::orderBy('name')->get(['id', 'name', 'country_id']),
            'allBuildings' => Building::with('property:id,name')->orderBy('name')->get(['id', 'name', 'property_id']),
            'allUnits' => Unit::with('building:id,name')->orderBy('name')->get(['id', 'name', 'building_id']),
        ]);
    }
}
