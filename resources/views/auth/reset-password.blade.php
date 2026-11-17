<x-guest-layout>
    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <x-form.input name="email" type="email" :label="__('app.email')" :value="$request->email" required />
        <x-form.input name="password" type="password" :label="__('app.new_password')" required autocomplete="new-password" />
        <x-form.input name="password_confirmation" type="password" :label="__('app.password_confirmation')" required autocomplete="new-password" />
        <button class="btn-primary w-full">{{ __('app.reset_password') }}</button>
    </form>
</x-guest-layout>
