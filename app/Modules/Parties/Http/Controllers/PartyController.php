<?php

namespace App\Modules\Parties\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Parties\Enums\PartyKind;
use App\Modules\Parties\Enums\PartyRole;
use App\Modules\Parties\Models\Party;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PartyController extends Controller
{
    public function index(Request $request): View
    {
        $parties = Party::query()
            ->when($request->string('role')->value(), fn ($q, $role) => $q->where('role', $role))
            ->when($request->string('q')->trim()->value(), fn ($q, $term) => $q->where(fn ($w) => $w
                ->where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('company_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")))
            ->orderBy('last_name')->orderBy('company_name')->orderBy('first_name')
            ->paginate(50)->withQueryString();

        return view('parties.index', ['parties' => $parties, 'roles' => PartyRole::options()]);
    }

    public function create(): View
    {
        return view('parties.form', $this->formData(new Party(['kind' => PartyKind::Person, 'role' => PartyRole::Tenant])));
    }

    public function store(Request $request): RedirectResponse
    {
        $party = Party::create($this->validated($request));

        return redirect()->route('parties.show', $party)->with('status', __('app.saved'));
    }

    public function show(Party $party): View
    {
        $party->load('occupancies.unit.building.property');

        return view('parties.show', ['party' => $party]);
    }

    public function edit(Party $party): View
    {
        return view('parties.form', $this->formData($party));
    }

    public function update(Request $request, Party $party): RedirectResponse
    {
        $party->update($this->validated($request));

        return redirect()->route('parties.show', $party)->with('status', __('app.saved'));
    }

    public function destroy(Party $party): RedirectResponse
    {
        if ($party->occupancies()->exists()) {
            return back()->withErrors(['delete' => __('app.delete_blocked_children')]);
        }

        $party->delete();

        return redirect()->route('parties.index')->with('status', __('app.deleted'));
    }

    private function formData(Party $party): array
    {
        return ['party' => $party, 'kinds' => PartyKind::options(), 'roles' => PartyRole::options()];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'kind' => ['required', Rule::enum(PartyKind::class)],
            'role' => ['required', Rule::enum(PartyRole::class)],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'required_if:kind,person', 'string', 'max:255'],
            'company_name' => ['nullable', 'required_if:kind,company', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'street' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
            'country_code' => ['nullable', 'alpha', 'size:2'],
            'iban' => ['nullable', 'string', 'max:42'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);

        if (! empty($data['iban'])) {
            $data['iban'] = strtoupper(preg_replace('/\s+/', '', $data['iban']));
        }
        if (! empty($data['country_code'])) {
            $data['country_code'] = strtoupper($data['country_code']);
        }

        return $data;
    }
}
