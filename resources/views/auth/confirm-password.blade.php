<x-guest-layout>
    <p class="mb-4 text-sm text-gray-700">{{ __('app.confirm_password_help') }}</p>
    <form method="POST" action="{{ route('password.confirm.store') }}" class="space-y-4">
        @csrf
        <x-form.input name="password" type="password" :label="__('app.password')" required autofocus autocomplete="current-password" />
        <button class="btn-primary w-full">{{ __('app.confirm') }}</button>
    </form>
</x-guest-layout>
