
@props([
    'label' => null,
    'id' => null,
    'rows' => '3',
    'model' => null,
])

{{-- @php $wireModel = $attributes->get('wire:model'); @endphp --}}

<div class="form-group">
    @if ($label)
        <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @endif

    <textarea
        id="{{ $id }}"
        name="{{ $id }}"
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => 'form-control']) }}
        @if ($model)
            wire:model.defer="{{ $model }}"
        @endif
    >
    </textarea>

    @error($id)
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
