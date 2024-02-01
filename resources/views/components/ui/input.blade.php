
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
            @if($attributes->has('live'))
            wire:model="{{ $model }}"
            @else
            wire:model.defer="{{ $model }}"
            @endif
        @endif
        @if($attributes->has('disabled'))
            disabled
        @endif
    />

    @error($id)
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
