@props(['headers'])

<div class="table-responsive">
    <table {{ $attributes->merge(['class' => 'table table-bordered']) }}>
        <thead
            @if ($attributes->has('dark'))
                class="thead-dark"
            @endif
        >
            <tr>
                @foreach ($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
