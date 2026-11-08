{{-- $audits: Collection von Audit-Einträgen, $labels: ["typ:id" => Bezeichnung] --}}
<ul class="divide-y divide-gray-100 text-sm">
    @forelse ($audits as $audit)
        @php
            $old = $audit->old_values ?? [];
            $new = $audit->new_values ?? [];
            $keys = array_unique(array_merge(array_keys($old), array_keys($new)));
            $label = $labels[$audit->auditable_type.':'.$audit->auditable_id] ?? '#'.$audit->auditable_id;
        @endphp
        <li class="py-2">
            <div class="flex flex-wrap items-baseline gap-x-2">
                <span class="text-gray-500">{{ $audit->created_at->format('d.m.Y H:i') }}</span>
                <span class="font-medium">{{ $audit->user?->name ?? __('app.system') }}</span>
                <span>{{ __('app.audit_events.'.$audit->event) }}</span>
                <span class="text-gray-700">{{ __('app.audit_types.'.$audit->auditable_type) }} «{{ $label }}»</span>
            </div>
            @if ($keys)
                <details class="mt-1">
                    <summary class="cursor-pointer text-xs text-gray-500">{{ trans_choice('app.changed_fields', count($keys)) }}</summary>
                    <table class="mt-1 text-xs">
                        @foreach ($keys as $key)
                            <tr>
                                <td class="pr-3 font-mono text-gray-500">{{ $key }}</td>
                                <td class="pr-3 text-red-700 line-through">{{ is_scalar($old[$key] ?? null) ? $old[$key] : json_encode($old[$key] ?? null, JSON_UNESCAPED_UNICODE) }}</td>
                                <td class="text-emerald-800">{{ is_scalar($new[$key] ?? null) ? $new[$key] : json_encode($new[$key] ?? null, JSON_UNESCAPED_UNICODE) }}</td>
                            </tr>
                        @endforeach
                    </table>
                </details>
            @endif
        </li>
    @empty
        <li class="py-3 text-gray-500">{{ __('app.empty') }}</li>
    @endforelse
</ul>
