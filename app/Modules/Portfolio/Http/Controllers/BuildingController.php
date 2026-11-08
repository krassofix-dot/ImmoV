<?php

namespace App\Modules\Portfolio\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Portfolio\Models\Building;
use App\Modules\Portfolio\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuildingController extends Controller
{
    public function create(Property $property): View
    {
        return view('portfolio.buildings.form', ['building' => new Building(['property_id' => $property->id]), 'property' => $property]);
    }

    public function store(Request $request, Property $property): RedirectResponse
    {
        $building = $property->buildings()->create($this->validated($request));

        return redirect()->route('buildings.show', $building)->with('status', __('app.saved'));
    }

    public function show(Building $building): View
    {
        $building->load(['property.country', 'units.occupancies.party']);

        return view('portfolio.buildings.show', ['building' => $building]);
    }

    public function edit(Building $building): View
    {
        return view('portfolio.buildings.form', ['building' => $building, 'property' => $building->property]);
    }

    public function update(Request $request, Building $building): RedirectResponse
    {
        $building->update($this->validated($request));

        return redirect()->route('buildings.show', $building)->with('status', __('app.saved'));
    }

    public function destroy(Building $building): RedirectResponse
    {
        if ($building->units()->exists()) {
            return back()->withErrors(['delete' => __('app.delete_blocked_children')]);
        }

        $property = $building->property;
        $building->delete();

        return redirect()->route('properties.show', $property)->with('status', __('app.deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'construction_year' => ['nullable', 'integer', 'between:1000,2100'],
            'floors' => ['nullable', 'integer', 'between:0,200'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);
    }
}
