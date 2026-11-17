<?php

namespace App\Modules\Parties\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Parties\Enums\OccupancyType;
use App\Modules\Parties\Models\Occupancy;
use App\Modules\Parties\Models\Party;
use App\Modules\Portfolio\Models\Unit;
use App\Support\Currencies;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OccupancyController extends Controller
{
    public function create(Unit $unit): View
    {
        $currency = $unit->building->property->country->default_currency ?? null;

        return view('parties.occupancies.form', $this->formData(
            new Occupancy(['unit_id' => $unit->id, 'currency' => $currency]),
            $unit,
        ));
    }

    public function store(Request $request, Unit $unit): RedirectResponse
    {
        $occupancy = $unit->occupancies()->create($this->validated($request));

        return $this->redirectWithOverlapCheck($occupancy);
    }

    public function edit(Occupancy $occupancy): View
    {
        return view('parties.occupancies.form', $this->formData($occupancy, $occupancy->unit));
    }

    public function update(Request $request, Occupancy $occupancy): RedirectResponse
    {
        $occupancy->update($this->validated($request));

        return $this->redirectWithOverlapCheck($occupancy);
    }

    public function destroy(Occupancy $occupancy): RedirectResponse
    {
        $unit = $occupancy->unit;
        $occupancy->delete();

        return redirect()->route('units.show', $unit)->with('status', __('app.deleted'));
    }

    /**
     * Überschneidungen werden nicht verhindert (z. B. Wohngemeinschaft mit
     * zwei Mietparteien), aber deutlich angezeigt.
     */
    private function redirectWithOverlapCheck(Occupancy $occupancy): RedirectResponse
    {
        $overlaps = Occupancy::query()
            ->where('unit_id', $occupancy->unit_id)
            ->whereKeyNot($occupancy->id)
            // andere Nutzung endet nicht vor unserem Beginn …
            ->when($occupancy->start_date, fn ($q, $start) => $q->where(fn ($w) => $w->whereNull('end_date')->orWhereDate('end_date', '>=', $start)))
            // … und beginnt nicht nach unserem Ende
            ->when($occupancy->end_date, fn ($q, $end) => $q->where(fn ($w) => $w->whereNull('start_date')->orWhereDate('start_date', '<=', $end)))
            ->exists();

        $redirect = redirect()->route('units.show', $occupancy->unit_id)->with('status', __('app.saved'));

        return $overlaps ? $redirect->with('warning', __('app.occupancy_overlap')) : $redirect;
    }

    private function formData(Occupancy $occupancy, Unit $unit): array
    {
        $unit->loadMissing('building.property');

        return [
            'occupancy' => $occupancy,
            'unit' => $unit,
            'types' => OccupancyType::options(),
            'parties' => Party::orderBy('last_name')->orderBy('company_name')->get(),
            'currencies' => Currencies::options(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'type' => ['required', Rule::enum(OccupancyType::class)],
            'party_id' => ['nullable', 'exists:parties,id', Rule::requiredIf(fn () => in_array($request->input('type'), ['rental', 'owner_occupied'], true))],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'rent_net' => ['nullable', 'decimal:0,2', 'min:0', 'max:9999999999'],
            'advance_payment' => ['nullable', 'decimal:0,2', 'min:0', 'max:9999999999'],
            'currency' => ['nullable', Rule::in(Currencies::CODES), 'required_with:rent_net,advance_payment'],
            'contract_reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);
    }
}
