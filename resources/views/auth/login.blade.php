<x-guest-layout>
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <x-form.input name="email" type="email" :label="__('app.email')" required autofocus autocomplete="username" />
        <x-form.input name="password" type="password" :label="__('app.password')" required autocomplete="current-password" />
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" class="rounded border-gray-300"> {{ __('app.remember_me') }}</label>
        <button class="btn-primary w-full">{{ __('app.login') }}</button>
        <p class="text-center text-sm"><a class="link" href="{{ route('password.request') }}">{{ __('app.forgot_password') }}</a></p>
    </form>
</x-guest-layout>
