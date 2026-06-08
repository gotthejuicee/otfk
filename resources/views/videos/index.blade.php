<x-layouts.app title="Відео">

    <section class="bg-brand-950">
        <div class="container-site py-12 lg:py-14">
            <nav class="flex items-center gap-2 text-sm text-brand-300">
                <a href="{{ route('home') }}" class="hover:text-white">Головна</a>
                <x-ico name="chevron-right" class="h-4 w-4" />
                <span class="text-white">Відео</span>
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold text-white sm:text-4xl">Відеоматеріали</h1>
            <div class="accent-rule"></div>
        </div>
    </section>

    <section class="container-site py-12">
        @if ($videos->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($videos as $video)
                    <a href="{{ $video->watch_url }}" target="_blank" rel="noopener" class="card group overflow-hidden">
                        <div class="relative aspect-video overflow-hidden bg-slate-900">
                            <img src="{{ $video->thumbnail }}" alt="{{ $video->title }}" loading="lazy" decoding="async" class="h-full w-full object-cover opacity-90 transition group-hover:scale-105 group-hover:opacity-100">
                            <span class="absolute inset-0 grid place-items-center">
                                <span class="grid h-14 w-14 place-items-center rounded-full bg-white/90 text-brand-700 shadow-lg transition group-hover:scale-110">
                                    <x-ico name="play" variant="solid" class="h-6 w-6" />
                                </span>
                            </span>
                        </div>
                        <div class="p-4">
                            <h3 class="line-clamp-2 font-semibold text-slate-800 group-hover:text-brand-700">{{ $video->title }}</h3>
                            @if ($video->published_at)
                                <p class="mt-1 text-xs text-slate-400">{{ $video->published_at->translatedFormat('j F Y') }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-10">{{ $videos->links() }}</div>
        @else
            <div class="card p-12 text-center text-slate-500">Відео поки немає.</div>
        @endif
    </section>

</x-layouts.app>
