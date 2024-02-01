@props([
'label' => null,
'id' => null,
'type' => 'text',
'model' => null,
'placeholder' => '',
'value' => null,
])

<div class="form-group row">
    @if ($label)
    <label for="{{ $id }}" class="col-sm-11 col-form-label">{{ $label }}</label>
    @endif

    <div class="col-sm-1">
        <input id="{{ $id }}" name="{{ $id }}" type="{{ $type }}" placeholder="{{ $placeholder }}" {{
            $attributes->merge(['class' => 'form-control']) }}
        @if ($value)
        value="{{ $value }}"
        @endif
        @if ($model)
        wire:model.defer="{{ $model }}"
        @endif
        />

        @error($id)
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>