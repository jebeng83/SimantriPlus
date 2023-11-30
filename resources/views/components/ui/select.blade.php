
@props([
    'label' => null,
    'id' => null,
    'model' => null,
])

{{-- @php $wireModel = $attributes->get('wire:model'); @endphp --}}

<div class="form-group">
    @if ($label)
        <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @endif

    <select
        id="{{ $id }}"
        name="{{ $id }}"
        {{ $attributes->merge(['class' => 'form-control']) }}
        @if ($model)
            wire:model.defer="{{ $model }}"
        @endif
    >
        {{ $slot }}
    </select>

    @error($id)
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
