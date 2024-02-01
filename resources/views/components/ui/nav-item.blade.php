@props([
    'label' => null,
    'id' => null,
])

<li class="nav-item">
    <a 
        {{ $attributes->merge(['class' => 'nav-link']) }}
        id="{{ $id }}"
        data-toggle="pill" 
        href="#{{ $id }}" 
        role="tab" 
        aria-controls="{{ $id }}"
        aria-selected="false"
    >
            {{ $label }}
    </a>
</li>