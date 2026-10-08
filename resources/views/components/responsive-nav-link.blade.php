@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-2 border-ink text-start text-sm font-semibold text-ink bg-neutral-100 focus:outline-none transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-2 border-transparent text-start text-sm font-medium text-neutral-600 hover:text-ink hover:bg-neutral-50 hover:border-neutral-300 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
