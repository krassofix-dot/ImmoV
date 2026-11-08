<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Sprache: Benutzereinstellung → Session → Standard (de). */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->user()?->locale ?? $request->session()->get('locale');

        if (in_array($locale, User::LOCALES, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
