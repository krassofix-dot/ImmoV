@props(['items'])
<dl {{ $attributes->merge(['class' => 'grid grid-cols-1 gap-x-6 gap-y-3 text-sm break-words']) }}>
    @foreach ($items as $label => $value)
        <div>
            <dt class="text-gray-500">{{ $label }}</dt>
            <dd class="whitespace-pre-line">{{ filled($value) ? $value : '–' }}</dd>
        </div>
    @endforeach
</dl>
