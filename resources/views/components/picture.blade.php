@props(['path', 'alt' => ''])

@php
    use App\Support\ImageOptimizer;

    $webpPath = ImageOptimizer::webpPath($path);
@endphp

<picture>
    @if ($webpPath)
        <source srcset="{{ asset('storage/' . $webpPath) }}" type="image/webp">
    @endif
    <img src="{{ asset('storage/' . $path) }}" alt="{{ $alt }}" {{ $attributes }}>
</picture>