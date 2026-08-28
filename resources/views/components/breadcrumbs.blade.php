@props(['items' => [], 'tone' => 'dark'])

@php
    $count = count($items);
    $light = $tone === 'light';
    $breadcrumbLd = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => collect($items)->values()->map(fn ($item, $i) => array_filter([
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $item['label'],
            'item' => (! empty($item['url']) && $i < $count - 1) ? $item['url'] : null,
        ], fn ($v) => $v !== null))->values()->all(),
    ];
@endphp

<nav aria-label="Навігаційний ланцюжок"
     @class(['flex flex-wrap items-center gap-2 text-sm', 'text-slate-500' => $light, 'text-brand-300' => ! $light])>
    @foreach ($items as $i => $item)
        @if ($i > 0)
            <x-ico name="chevron-right" class="h-4 w-4 shrink-0" aria-hidden="true" />
        @endif
        @if (! empty($item['url']) && $i < $count - 1)
            <a href="{{ $item['url'] }}" @class(['hover:text-brand-700' => $light, 'hover:text-white' => ! $light])>{{ $item['label'] }}</a>
        @else
            <span @class(['text-slate-700' => $light, 'text-white' => ! $light]) @if ($i === $count - 1) aria-current="page" @endif>{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
<script type="application/ld+json">{!! json_encode($breadcrumbLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
