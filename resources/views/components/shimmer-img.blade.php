@props([
    'src',
    'alt' => '',
    'class' => 'w-full h-full object-cover',
    'lazy' => true,
])

<span {{ $attributes->class(['img-shimmer-wrap block w-full h-full']) }}>
    <span class="img-shimmer skeleton" aria-hidden="true"></span>
    <img src="{{ $src }}" alt="{{ $alt }}" @class([$class]) @if($lazy) loading="lazy" @endif>
</span>
