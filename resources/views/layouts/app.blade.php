<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ isset($title) ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
@php
    $nav = [
        ['route' => 'dashboard', 'label' => __('app.nav.dashboard'), 'can' => 'dashboard.view', 'active' => 'dashboard'],
        ['route' => 'properties.index', 'label' => __('app.nav.properties'), 'can' => 'portfolio.view', 'active' => 'properties.*|buildings.*|units.*|occupancies.*'],
        ['route' => 'parties.index', 'label' => __('app.nav.parties'), 'can' => 'parties.view', 'active' => 'parties.*'],
    ];
    $admin = [
        ['route' => 'users.index', 'label' => __('app.nav.users'), 'can' => 'users.manage', 'active' => 'users.*'],
        ['route' => 'roles.index', 'label' => __('app.nav.roles'), 'can' => 'roles.manage', 'active' => 'roles.*'],
        ['route' => 'countries.index', 'label' => __('app.nav.countries'), 'can' => 'settings.manage', 'active' => 'countries.*'],
        ['route' => 'audits.index', 'label' => __('app.nav.audit'), 'can' => 'audit.view', 'active' => 'audits.*'],
    ];
    $isActive = fn ($pattern) => request()->routeIs(...explode('|', $pattern));
@endphp
<header class="border-b border-gray-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3">
        <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-emerald-800">{{ config('app.name') }}</a>

        <nav class="hidden items-center gap-1 lg:flex" aria-label="{{ __('app.nav.main') }}">
            @foreach (array_merge($nav, $admin) as $item)
                @can($item['can'])
                    <a href="{{ route($item['route']) }}" @class(['rounded-md px-3 py-2 text-sm font-medium', 'bg-emerald-50 text-emerald-800' => $isActive($item['active']), 'text-gray-700 hover:bg-gray-100' => ! $isActive($item['active'])])>{{ $item['label'] }}</a>
                @endcan
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ route('profile.show') }}" class="hidden text-sm text-gray-700 hover:underline sm:inline">{{ auth()->user()->name }}</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn-secondary">{{ __('app.logout') }}</button>
            </form>
            <details class="relative lg:hidden">
                <summary class="btn-secondary list-none" aria-label="{{ __('app.nav.menu') }}">☰</summary>
                <div class="absolute right-0 z-20 mt-2 w-64 rounded-md border border-gray-200 bg-white p-2 shadow-lg">
                    @foreach (array_merge($nav, $admin) as $item)
                        @can($item['can'])
                            <a href="{{ route($item['route']) }}" @class(['block rounded px-3 py-2 text-sm', 'bg-emerald-50 text-emerald-800' => $isActive($item['active']), 'hover:bg-gray-100' => ! $isActive($item['active'])])>{{ $item['label'] }}</a>
                        @endcan
                    @endforeach
                    <a href="{{ route('profile.show') }}" class="block rounded px-3 py-2 text-sm hover:bg-gray-100">{{ __('app.nav.profile') }}</a>
                </div>
            </details>
        </div>
    </div>
</header>

<main class="mx-auto max-w-7xl px-4 py-6">
    @if (session('status'))
        <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" role="status">{{ session('status') }}</div>
    @endif
    @if (session('warning'))
        <div class="mb-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900" role="alert">{{ session('warning') }}</div>
    @endif
    @if ($errors->has('delete'))
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900" role="alert">{{ $errors->first('delete') }}</div>
    @endif

    {{ $slot }}
</main>

<footer class="mx-auto max-w-7xl px-4 pb-8 text-xs text-gray-500">
    <form method="POST" action="{{ route('locale') }}" class="flex items-center gap-2">
        @csrf
        <span>{{ __('app.language') }}:</span>
        @foreach (\App\Models\User::LOCALES as $loc)
            <button name="locale" value="{{ $loc }}" @class(['underline' => app()->getLocale() !== $loc, 'font-semibold' => app()->getLocale() === $loc])>{{ strtoupper($loc) }}</button>
        @endforeach
    </form>
</footer>
</body>
</html>
