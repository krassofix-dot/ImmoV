@props(['occupancy' => null])
@php
    $type = $occupancy?->type?->value ?? 'none';
    $colors = ['rental' => 'bg-blue-100 text-blue-800', 'owner_occupied' => 'bg-emerald-100 text-emerald-800', 'private_use' => 'bg-violet-100 text-violet-800', 'vacant' => 'bg-amber-100 text-amber-800', 'none' => 'bg-gray-100 text-gray-600'];
@endphp
<span class="badge {{ $colors[$type] }}">{{ $occupancy ? $occupancy->type->label() : __('app.no_occupancy') }}</span>
