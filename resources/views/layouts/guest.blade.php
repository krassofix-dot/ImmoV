<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-gray-50 px-4 text-gray-900 antialiased">
<div class="w-full max-w-sm">
    <h1 class="mb-6 text-center text-2xl font-semibold text-emerald-800">{{ config('app.name') }}</h1>
    <div class="card">
        @if (session('status'))
            <div class="mb-4 rounded-md bg-emerald-50 px-3 py-2 text-sm text-emerald-900">{{ session('status') }}</div>
        @endif
        {{ $slot }}
    </div>
    <form method="POST" action="{{ route('locale') }}" class="mt-4 flex justify-center gap-3 text-xs text-gray-500">
        @csrf
        @foreach (\App\Models\User::LOCALES as $loc)
            <button name="locale" value="{{ $loc }}" @class(['underline' => app()->getLocale() !== $loc, 'font-semibold' => app()->getLocale() === $loc])>{{ strtoupper($loc) }}</button>
        @endforeach
    </form>
</div>
</body>
</html>
