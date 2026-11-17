<x-guest-layout>
    <p class="mb-4 text-sm text-gray-700">{{ __('app.two_factor_challenge_help') }}</p>
    <form method="POST" action="{{ url('/two-factor-challenge') }}" class="space-y-4">
        @csrf
        <x-form.input name="code" :label="__('app.two_factor_code')" inputmode="numeric" autocomplete="one-time-code" autofocus />
        <details class="text-sm">
            <summary class="cursor-pointer text-gray-600">{{ __('app.use_recovery_code') }}</summary>
            <x-form.input class="mt-2" name="recovery_code" :label="__('app.recovery_code')" autocomplete="off" />
        </details>
        <button class="btn-primary w-full">{{ __('app.login') }}</button>
    </form>
</x-guest-layout>
