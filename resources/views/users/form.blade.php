@php($editing = $user->exists)
<x-app-layout :title="__('app.nav.users')">
    <x-page-header :title="$editing ? $user->name : __('app.new_user')" />
    <form method="POST" action="{{ $editing ? route('users.update', $user) : route('users.store') }}" class="card grid max-w-3xl gap-4 sm:grid-cols-2">
        @csrf
        @if ($editing) @method('PUT') @endif
        <x-form.input name="name" :label="__('app.name')" :value="$user->name" required />
        <x-form.input name="email" type="email" :label="__('app.email')" :value="$user->email" required />
        <x-form.select name="role" :label="__('app.role')" :options="$roles->mapWithKeys(fn ($r) => [$r->name => __('app.roles.'.$r->name)])" :value="$user->roles->first()?->name" required />
        <x-form.select name="locale" :label="__('app.language')" :options="['de' => 'Deutsch', 'en' => 'English']" :value="$user->locale" :placeholder="false" />
        <x-form.input name="password" type="password" :label="$editing ? __('app.new_password_optional') : __('app.password')" :required="! $editing" autocomplete="new-password" :help="__('app.password_rules')" />
        <x-form.input name="password_confirmation" type="password" :label="__('app.password_confirmation')" :required="! $editing" autocomplete="new-password" />
        @if ($editing)
            <label class="flex items-center gap-2 text-sm"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active))> {{ __('app.active') }}</label>
        @endif
        <div class="flex gap-2 sm:col-span-2">
            <button class="btn-primary">{{ __('app.save') }}</button>
            <a href="{{ route('users.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
        </div>
    </form>
    @if ($editing && $user->two_factor_secret)
        <form method="POST" action="{{ route('users.two-factor.reset', $user) }}" class="mt-4" data-confirm="{{ __('app.confirm_disable_2fa') }}">
            @csrf @method('DELETE')
            <button class="btn-secondary">{{ __('app.reset_two_factor') }}</button>
        </form>
    @endif
</x-app-layout>
