
@props([
    'label' => null,
    'id' => null,
    'model' => null,
])

<div>
    @if ($label)
        <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @endif

    <div wire:ignore.self class="input-group date" id="{{ $id }}" data-target-input="nearest">
        <input 
            {{ $attributes->merge(['class' => 'form-control datetimepicker-input']) }}
            @if ($model)
                wire:model.defer="{{ $model }}"
            @endif
            type="text" 
            data-target="#{{ $id }}" 
        />
        <div class="input-group-append" data-target="#{{ $id }}" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
        </div>
    </div>

    @error($id)
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

@push('js')
    <script>
        $(function () {
            $('#{{ $id }}').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss',
                allowInputToggle: true,
                icons: {
                    time: "fa fa-clock",
                    date: "fa fa-calendar",
                    up: "fa fa-chevron-up",
                    down: "fa fa-chevron-down",
                    previous: "fa fa-chevron-left",
                    next: "fa fa-chevron-right",
                    today: "fa fa-screenshot",
                    clear: "fa fa-trash",
                    close: "fa fa-remove"
                }
            });

            $('#{{ $id }}').on('change.datetimepicker', function(e) {
                // console.log(e.date);
                if(!e.date){
                    @this.set('{{ $model }}', '', true);
                }else{
                    @this.set('{{ $model }}', e.date.format('YYYY-MM-DD HH:mm:ss'), true);
                }
            });
        });
    </script>
@endpush
