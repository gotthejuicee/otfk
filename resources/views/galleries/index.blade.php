<x-layouts.app title="Фотогалерея">

    <x-page-hero title="Фотогалерея" :breadcrumbs="[
        ['label' => 'Головна', 'url' => route('home')],
        ['label' => 'Галерея'],
    ]" />

    <section class="container-site py-12">
        @if ($galleries->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($galleries as $gallery)
                    <a href="{{ route('galleries.show', $gallery) }}" class="card group overflow-hidden transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="relative aspect-[16/10] overflow-hidden bg-brand-50">
                            @if ($gallery->cover_url)
                                <img src="{{ $gallery->cover_url }}" alt="{{ $gallery->title }}" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-700 to-brand-900 text-white/25"><x-ico name="photo" class="h-14 w-14" /></div>
                            @endif
                            <span class="absolute bottom-3 right-3 badge bg-white/90 text-brand-800"><x-ico name="photo" class="h-3.5 w-3.5" /> {{ $gallery->photos_count }}</span>
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold text-slate-900 group-hover:text-brand-700">{{ $gallery->title }}</h3>
                            @if ($gallery->published_at)
                                <p class="mt-1 text-xs text-slate-400">{{ $gallery->published_at->translatedFormat('j F Y') }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="card p-12 text-center text-slate-500">Фотоальбоми незабаром буде додано.</div>
        @endif
    </section>

</x-layouts.app>
