<?php

namespace App\Modules\Portfolio\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Portfolio\Models\Country;
use App\Support\Currencies;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CountryController extends Controller
{
    public function index(): View
    {
        return view('portfolio.countries.index', [
            'countries' => Country::ordered()->withCount('properties')->get(),
        ]);
    }

    public function create(): View
    {
        return view('portfolio.countries.form', ['country' => new Country(['is_active' => true]), 'currencies' => Currencies::options()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Country::create($this->validated($request));

        return redirect()->route('countries.index')->with('status', __('app.saved'));
    }

    public function edit(Country $country): View
    {
        return view('portfolio.countries.form', ['country' => $country, 'currencies' => Currencies::options()]);
    }

    public function update(Request $request, Country $country): RedirectResponse
    {
        $country->update($this->validated($request, $country));

        return redirect()->route('countries.index')->with('status', __('app.saved'));
    }

    private function validated(Request $request, ?Country $country = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'alpha', 'size:2', Rule::unique('countries', 'code')->ignore($country)],
            'name_de' => ['required', 'string', 'max:100'],
            'name_en' => ['required', 'string', 'max:100'],
            'default_currency' => ['required', Rule::in(Currencies::CODES)],
            'sort_order' => ['nullable', 'integer', 'between:0,65535'],
        ]);
        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] ??= 0;

        return $data;
    }
}
