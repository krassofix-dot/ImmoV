<x-guest-layout>
    <p class="mb-4 text-sm text-gray-700">{{ __('app.forgot_password_help') }}</p>
    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <x-form.input name="email" type="email" :label="__('app.email')" required autofocus />
        <button class="btn-primary w-full">{{ __('app.send_reset_link') }}</button>
        <p class="text-center text-sm"><a class="link" href="{{ route('login') }}">{{ __('app.back_to_login') }}</a></p>
    </form>
</x-guest-layout>
