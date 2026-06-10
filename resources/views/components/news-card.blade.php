@props(['item'])

<article class="card group flex flex-col overflow-hidden transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-300/40">
    <a href="{{ route('news.show', $item) }}" class="block aspect-[16/9] overflow-hidden">
        @if ($item->cover_image)
            <img src="{{ asset('storage/' . $item->cover_image) }}" alt="{{ $item->title }}" loading="lazy" decoding="async"
                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-700 to-brand-900 text-white/25">
                <x-ico name="newspaper" class="h-12 w-12" />
            </div>
        @endif
    </a>
    <div class="flex flex-1 flex-col p-5">
        <div class="flex flex-wrap items-center gap-2 text-xs">
            @if ($item->category)
                <span class="badge bg-brand-50 text-brand-700">{{ $item->category->title }}</span>
            @endif
            @if ($item->published_at)
                <span class="text-slate-400">{{ $item->published_at->translatedFormat('j F Y') }}</span>
            @endif
        </div>
        <h3 class="mt-2.5 line-clamp-2 text-lg font-bold leading-snug">
            <a href="{{ route('news.show', $item) }}" class="text-slate-900 transition hover:text-brand-700">{{ $item->title }}</a>
        </h3>
        <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-slate-500">{{ $item->excerpt }}</p>
        <div class="mt-4 flex items-center justify-between gap-3">
            <a href="{{ route('news.show', $item) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 transition hover:gap-2.5">
                Читати далі <x-ico name="arrow-right" class="h-4 w-4" />
            </a>
            <span class="inline-flex items-center gap-3 text-xs text-slate-400">
                <span class="inline-flex items-center gap-1" title="Переглядів"><x-ico name="eye" class="h-3.5 w-3.5" /> {{ $item->views }}</span>
                <span class="inline-flex items-center gap-1" title="Вподобайок"><x-ico name="heart" class="h-3.5 w-3.5" /> {{ (int) $item->likes }}</span>
            </span>
        </div>
    </div>
</article>
