@props(['action', 'label' => null])
<form method="POST" action="{{ $action }}" data-confirm="{{ __('app.confirm_delete') }}">
    @csrf
    @method('DELETE')
    <button class="btn-danger">{{ $label ?? __('app.delete') }}</button>
</form>
