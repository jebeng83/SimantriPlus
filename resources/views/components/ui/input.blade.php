
@props([
    'label' => null,
    'id' => null,
    'type' => 'text',
    'model' => null,
])

{{-- @php $wireModel = $attributes->get('wire:model'); @endphp --}}

<div class="form-group">
    @if ($label)
        <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @endif

    <input
        id="{{ $id }}"
        name="{{ $id }}"
        type="{{ $type }}"
        {{ $attributes->merge(['class' => 'form-control']) }}
        @if ($model)
            wire:model.defer="{{ $model }}"
        @endif
    />

    @error($id)
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
