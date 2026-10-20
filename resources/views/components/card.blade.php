@props(['title', 'url' => null, 'badge' => null, 'image' => null])

<article {{ $attributes->merge(['class' => 'card']) }}>
    @if ($image)
        <img src="{{ $image }}" alt="{{ $title }}">
    @endif
    <h3>
        @if ($url)
            <a href="{{ $url }}">{{ $title }}</a>
        @else
            {{ $title }}
        @endif
        @if ($badge)
            <x-badge>{{ $badge }}</x-badge>
        @endif
    </h3>
    {{ $slot }}
</article>
