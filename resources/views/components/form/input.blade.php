@props(['name', 'label', 'value' => null, 'type' => 'text', 'required' => false, 'help' => null])
@php($id = 'f_'.str_replace(['[', ']', '.'], '_', $name))
<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="label">{{ $label }}@if ($required) <span class="text-red-600">*</span>@endif</label>
    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" @required($required)
           {{ $attributes->except('class')->merge(['class' => 'input']) }}>
    @if ($help)<p class="mt-1 text-xs text-gray-500">{{ $help }}</p>@endif
    @error($name)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
