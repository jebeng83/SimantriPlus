@props([
    'id' => null,
    'label' => null,
    'model' => null,
    'ajax' => null,
])

<div class="form-group">
    @if($label)
        <label for="{{$id}}">{{$label}}</label>
    @endif
    <select 
        {{ $attributes->merge(['class' => 'form-control']) }}
        id="{{$id}}"
        name="{{$id}}" 
        @if($attributes->has('multiple')) multiple @endif
        @if($model)
        wire:model="{{$model}}"
        @endif
    >
        {{$slot}}
    </select>
</div>

@push('js')
    <script>
        $(document).ready(function() {
            $('#{{$id}}').select2({
                theme: 'bootstrap4',
                ajax: {
                    url: '{{$ajax}}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
@endpush