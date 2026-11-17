<?php

namespace App\Modules\Portfolio\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Portfolio\Enums\UsageType;
use App\Modules\Portfolio\Models\Building;
use App\Modules\Portfolio\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use OwenIt\Auditing\Models\Audit;

class UnitController extends Controller
{
    public function create(Building $building): View
    {
        return view('portfolio.units.form', [
            'unit' => new Unit(['building_id' => $building->id]),
            'building' => $building,
            'usageTypes' => UsageType::options(),
        ]);
    }

    public function store(Request $request, Building $building): RedirectResponse
    {
        $unit = $building->units()->create($this->validated($request));

        return redirect()->route('units.show', $unit)->with('status', __('app.saved'));
    }

    public function show(Request $request, Unit $unit): View
    {
        $unit->load(['building.property.country', 'occupancies.party']);

        // Historie: Änderungen an der Einheit und ihren Nutzungen
        $history = $request->user()->can('audit.view')
            ? Audit::query()
                ->with('user')
                ->where(fn ($q) => $q
                    ->where(fn ($w) => $w->where('auditable_type', 'unit')->where('auditable_id', $unit->id))
                    ->orWhere(fn ($w) => $w->where('auditable_type', 'occupancy')->whereIn('auditable_id', $unit->occupancies->pluck('id'))))
                ->latest('id')->limit(25)->get()
            : collect();

        return view('portfolio.units.show', ['unit' => $unit, 'history' => $history]);
    }

    public function edit(Unit $unit): View
    {
        return view('portfolio.units.form', [
            'unit' => $unit,
            'building' => $unit->building,
            'usageTypes' => UsageType::options(),
        ]);
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $unit->update($this->validated($request));

        return redirect()->route('units.show', $unit)->with('status', __('app.saved'));
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        if ($unit->occupancies()->exists()) {
            return back()->withErrors(['delete' => __('app.delete_blocked_children')]);
        }

        $building = $unit->building;
        $unit->delete();

        return redirect()->route('buildings.show', $building)->with('status', __('app.deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'floor' => ['nullable', 'string', 'max:40'],
            'area_m2' => ['nullable', 'decimal:0,2', 'min:0', 'max:99999999'],
            'rooms' => ['nullable', 'decimal:0,1', 'min:0', 'max:999'],
            'usage_type' => ['nullable', Rule::enum(UsageType::class)],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);
    }
}
