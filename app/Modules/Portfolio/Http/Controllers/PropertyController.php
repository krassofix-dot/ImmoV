<?php

namespace App\Modules\Portfolio\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Portfolio\Enums\OwnershipType;
use App\Modules\Portfolio\Enums\PropertyType;
use App\Modules\Portfolio\Models\Country;
use App\Modules\Portfolio\Models\Property;
use App\Support\Currencies;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        $properties = Property::query()
            ->with('country')
            ->withCount(['buildings', 'units'])
            ->when($request->integer('country'), fn ($q, $id) => $q->where('country_id', $id))
            ->when($request->string('q')->trim()->value(), fn ($q, $term) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$term}%")
                ->orWhere('city', 'like', "%{$term}%")))
            ->orderBy('country_id')->orderBy('name')
            ->get();

        return view('portfolio.properties.index', [
            'properties' => $properties,
            'countries' => Country::ordered()->get(),
        ]);
    }

    public function create(): View
    {
        return view('portfolio.properties.form', $this->formData(new Property));
    }

    public function store(Request $request): RedirectResponse
    {
        $property = Property::create($this->validated($request));

        return redirect()->route('properties.show', $property)->with('status', __('app.saved'));
    }

    public function show(Property $property): View
    {
        $property->load(['country', 'buildings.units.occupancies.party']);

        return view('portfolio.properties.show', ['property' => $property]);
    }

    public function edit(Property $property): View
    {
        return view('portfolio.properties.form', $this->formData($property));
    }

    public function update(Request $request, Property $property): RedirectResponse
    {
        $property->update($this->validated($request));

        return redirect()->route('properties.show', $property)->with('status', __('app.saved'));
    }

    public function destroy(Property $property): RedirectResponse
    {
        if ($property->buildings()->exists()) {
            return back()->withErrors(['delete' => __('app.delete_blocked_children')]);
        }

        $property->delete();

        return redirect()->route('properties.index')->with('status', __('app.deleted'));
    }

    private function formData(Property $property): array
    {
        return [
            'property' => $property,
            'countries' => Country::ordered()->where('is_active', true)->get(),
            'ownershipTypes' => OwnershipType::options(),
            'propertyTypes' => PropertyType::options(),
            'currencies' => Currencies::options(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'country_id' => ['required', 'exists:countries,id'],
            'name' => ['required', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'ownership_type' => ['nullable', Rule::enum(OwnershipType::class)],
            'property_type' => ['nullable', Rule::enum(PropertyType::class)],
            'construction_year' => ['nullable', 'integer', 'between:1000,2100'],
            'plot_number' => ['nullable', 'string', 'max:255'],
            'plot_area_m2' => ['nullable', 'decimal:0,2', 'min:0', 'max:9999999999'],
            'land_registry_info' => ['nullable', 'string', 'max:5000'],
            'purchase_price' => ['nullable', 'decimal:0,2', 'min:0', 'max:9999999999999', 'required_with:purchase_currency'],
            'purchase_currency' => ['nullable', Rule::in(Currencies::CODES), 'required_with:purchase_price'],
            'purchase_date' => ['nullable', 'date'],
            'energy_supply' => ['nullable', 'string', 'max:5000'],
            'technical_systems' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);
    }
}
