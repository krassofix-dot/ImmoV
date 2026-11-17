@props(['name', 'label', 'options' => [], 'value' => null, 'required' => false, 'placeholder' => '—'])
@php($id = 'f_'.str_replace(['[', ']', '.'], '_', $name))
@php($current = (string) old($name, $value instanceof \BackedEnum ? $value->value : $value))
<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="label">{{ $label }}@if ($required) <span class="text-red-600">*</span>@endif</label>
    <select id="{{ $id }}" name="{{ $name }}" @required($required) {{ $attributes->except('class')->merge(['class' => 'input']) }}>
        @if ($placeholder !== false)<option value="">{{ $placeholder }}</option>@endif
        @foreach ($options as $key => $text)
            <option value="{{ $key }}" @selected($current === (string) $key)>{{ $text }}</option>
        @endforeach
    </select>
    @error($name)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
