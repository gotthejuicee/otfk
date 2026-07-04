@props(['excerpt' => null, 'body' => null, 'heritage' => false])

@php
    // excerpt часто = обрізаний початок body (автогенерація при імпорті новин),
    // тож НЕ показуємо лід-абзац, якщо тіло вже починається з того самого тексту —
    // інакше перший абзац дублюється на сторінці.
    $lead = \Illuminate\Support\Str::of((string) $excerpt)->stripTags()->squish();
    $bodyStart = \Illuminate\Support\Str::of((string) $body)->stripTags()->squish();
    $show = $lead->isNotEmpty() && ! $bodyStart->startsWith((string) $lead->limit(60, ''));
@endphp

@if ($show)
    <p @class([
        'mb-6 max-w-3xl text-lg leading-relaxed',
        'font-heritage-display italic text-brand-800/90' => $heritage,
        'font-medium text-slate-600' => ! $heritage,
    ])>{{ $excerpt }}</p>
@endif
