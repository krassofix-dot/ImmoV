@props(['name', 'label', 'value' => null, 'rows' => 3])
@php($id = 'f_'.str_replace(['[', ']', '.'], '_', $name))
<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="label">{{ $label }}</label>
    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" {{ $attributes->except('class')->merge(['class' => 'input']) }}>{{ old($name, $value) }}</textarea>
    @error($name)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
