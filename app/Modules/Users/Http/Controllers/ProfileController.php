<?php

namespace App\Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('profile.show', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'locale' => ['required', Rule::in(User::LOCALES)],
        ]);
        $request->user()->update($data);

        return back()->with('status', __('app.saved'));
    }

    /** Sprachwahl, auch vor der Anmeldung möglich. */
    public function locale(Request $request): RedirectResponse
    {
        $data = $request->validate(['locale' => ['required', Rule::in(User::LOCALES)]]);
        $request->session()->put('locale', $data['locale']);
        $request->user()?->update(['locale' => $data['locale']]);

        return back();
    }
}
