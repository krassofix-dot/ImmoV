<x-app-layout :title="__('app.nav.audit')">
    <x-page-header :title="__('app.nav.audit')" :subtitle="__('app.audit_help')" />
    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3">
        <x-form.select name="type" :label="__('app.type')" :options="collect($types)->mapWithKeys(fn ($t) => [$t => __('app.audit_types.'.$t)])" :value="request('type')" :placeholder="__('app.all')" />
        <x-form.select name="event" :label="__('app.event')" :options="$events->mapWithKeys(fn ($e) => [$e => __('app.audit_events.'.$e)])" :value="request('event')" :placeholder="__('app.all')" />
        <x-form.select name="user" :label="__('app.user')" :options="$users->pluck('name', 'id')" :value="request('user')" :placeholder="__('app.all')" />
        <x-form.input name="from" type="date" :label="__('app.from')" :value="request('from')" />
        <x-form.input name="to" type="date" :label="__('app.to')" :value="request('to')" />
        <button class="btn-secondary">{{ __('app.filter') }}</button>
    </form>
    <div class="card">
        @include('system._audit_list', ['audits' => $audits, 'labels' => $labels])
    </div>
    <div class="mt-4">{{ $audits->links() }}</div>
</x-app-layout>
