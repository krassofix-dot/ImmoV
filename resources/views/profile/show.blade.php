<x-app-layout :title="__('app.nav.profile')">
    <x-page-header :title="__('app.nav.profile')" />

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="card">
            <h2 class="mb-4 text-lg font-semibold">{{ __('app.profile_data') }}</h2>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf @method('PUT')
                <x-form.input name="name" :label="__('app.name')" :value="$user->name" required />
                <x-dl class="sm:grid-cols-2" :items="[__('app.email') => $user->email, __('app.role') => $user->getRoleNames()->map(fn ($r) => __('app.roles.'.$r))->implode(', ')]" />
                <x-form.select name="locale" :label="__('app.language')" :options="['de' => 'Deutsch', 'en' => 'English']" :value="$user->locale" :placeholder="false" />
                <button class="btn-primary">{{ __('app.save') }}</button>
            </form>
        </section>

        <section class="card">
            <h2 class="mb-4 text-lg font-semibold">{{ __('app.change_password') }}</h2>
            @if (session('status') === 'password-updated')
                <p class="mb-3 text-sm text-emerald-700">{{ __('app.password_updated') }}</p>
            @endif
            <form method="POST" action="{{ route('user-password.update') }}" class="space-y-4">
                @csrf @method('PUT')
                <x-form.input name="current_password" type="password" :label="__('app.current_password')" required autocomplete="current-password" />
                <x-form.input name="password" type="password" :label="__('app.new_password')" required autocomplete="new-password" />
                <x-form.input name="password_confirmation" type="password" :label="__('app.password_confirmation')" required autocomplete="new-password" />
                @foreach ($errors->updatePassword->all() as $error)
                    <p class="text-sm text-red-600">{{ $error }}</p>
                @endforeach
                <button class="btn-primary">{{ __('app.save') }}</button>
            </form>
        </section>

        <section class="card lg:col-span-2">
            <h2 class="mb-2 text-lg font-semibold">{{ __('app.two_factor') }}</h2>
            <p class="mb-4 text-sm text-gray-600">{{ __('app.two_factor_help') }}</p>

            @if ($user->hasTwoFactorEnabled())
                <p class="mb-4 text-sm font-medium text-emerald-700">{{ __('app.two_factor_active') }}</p>
                @if (session('status') === 'two-factor-authentication-confirmed' || session('status') === 'recovery-codes-generated')
                    <p class="mb-2 text-sm">{{ __('app.recovery_codes_help') }}</p>
                    <pre class="mb-4 rounded bg-gray-100 p-3 text-sm">{{ implode("\n", $user->recoveryCodes()) }}</pre>
                @endif
                <div class="flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('two-factor.regenerate-recovery-codes') }}">@csrf
                        <button class="btn-secondary">{{ __('app.regenerate_recovery_codes') }}</button>
                    </form>
                    <form method="POST" action="{{ route('two-factor.disable') }}" data-confirm="{{ __('app.confirm_disable_2fa') }}">@csrf @method('DELETE')
                        <button class="btn-danger">{{ __('app.disable') }}</button>
                    </form>
                </div>
            @elseif ($user->two_factor_secret)
                <p class="mb-3 text-sm">{{ __('app.two_factor_scan') }}</p>
                <div class="mb-3 inline-block rounded bg-white p-2">{!! $user->twoFactorQrCodeSvg() !!}</div>
                <form method="POST" action="{{ route('two-factor.confirm') }}" class="flex max-w-sm flex-wrap items-end gap-2">
                    @csrf
                    <x-form.input class="grow" name="code" :label="__('app.two_factor_code')" inputmode="numeric" autocomplete="one-time-code" required />
                    <button class="btn-primary">{{ __('app.confirm') }}</button>
                </form>
                @foreach ($errors->confirmTwoFactorAuthentication->all() as $error)
                    <p class="mt-2 text-sm text-red-600">{{ $error }}</p>
                @endforeach
            @else
                <form method="POST" action="{{ route('two-factor.enable') }}">@csrf
                    <button class="btn-primary">{{ __('app.enable') }}</button>
                </form>
            @endif
        </section>
    </div>
</x-app-layout>
