@props(['title', 'subtitle' => null])
<div class="mb-6 flex flex-wrap items-start justify-between gap-3">
    <div>
        @isset($breadcrumbs)<nav class="mb-1 text-sm text-gray-500">{{ $breadcrumbs }}</nav>@endisset
        <h1 class="text-2xl font-semibold">{{ $title }}</h1>
        @if ($subtitle)<p class="mt-1 text-sm text-gray-600">{{ $subtitle }}</p>@endif
    </div>
    @isset($actions)<div class="flex flex-wrap gap-2">{{ $actions }}</div>@endisset
</div>
