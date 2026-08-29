<x-layouts.app title="Фотогалерея" description="Фотоальбоми коледжу: студентське життя, події, досягнення та архівні знімки.">

    @php
        // Головний альбом — лише на першій сторінці й лише коли є що показати нижче
        $items = $galleries->getCollection();
        $featured = ($galleries->onFirstPage() && $items->count() > 1) ? $items->first() : null;
        $rest = $featured ? $items->slice(1) : $items;

        // Українське відмінювання слова «альбом» для лічильника
        $albumWord = function (int $n): string {
            $mod100 = $n % 100;
            $mod10 = $n % 10;

            if ($mod100 >= 11 && $mod100 <= 14) {
                return 'альбомів';
            }

            return match (true) {
                $mod10 === 1 => 'альбом',
                $mod10 >= 2 && $mod10 <= 4 => 'альбоми',
                default => 'альбомів',
            };
        };
    @endphp

    {{-- Світла шапка розділу — у стилі детальної новини та сторінки відео --}}
    <section class="border-b border-slate-200/70 bg-slate-50/80">
        <div class="container-site py-8 lg:py-10">
            <x-breadcrumbs tone="light" :items="[
                ['label' => 'Головна', 'url' => route('home')],
                ['label' => 'Галерея'],
            ]" />

            <div class="relative mt-4 overflow-hidden rounded-2xl bg-white px-6 py-8 shadow-sm ring-1 ring-slate-200/80 sm:px-10 sm:py-10">
                {{-- Декоративний контур фото — заповнює порожнечу праворуч на великих екранах --}}
                <x-ico name="photo" aria-hidden="true"
                       class="pointer-events-none absolute -right-8 top-1/2 hidden h-64 w-64 -translate-y-1/2 text-brand-50 lg:block" />

                <div class="relative max-w-3xl">
                    <h1 class="text-3xl font-extrabold leading-tight text-brand-950 sm:text-4xl lg:text-[2.75rem]">Фотогалерея</h1>
                    <div class="accent-rule"></div>
                    <p class="mt-5 text-lg leading-relaxed text-slate-500">
                        Яскраві моменти студентського життя, подій та досягнень нашого коледжу.
                    </p>
                    @if ($galleries->total() > 0)
                        <p class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-sm font-semibold text-brand-700 ring-1 ring-brand-200/70">
                            <x-ico name="photo" class="h-4 w-4" />
                            {{ $galleries->total() }} {{ $albumWord($galleries->total()) }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if ($galleries->isNotEmpty())
        <section class="container-site py-12">
            @if ($featured)
                {{-- Рекомендований альбом: велика обкладинка ліворуч, опис праворуч --}}
                <article @class([
                    'card group mb-10 grid overflow-hidden lg:grid-cols-2',
                    'ring-gold-200/80' => $featured->is_archive,
                ])>
                    <a href="{{ route('galleries.show', $featured) }}" tabindex="-1" aria-hidden="true"
                       class="relative block aspect-[16/10] overflow-hidden bg-brand-50">
                        @if ($featured->cover_url)
                            <img src="{{ $featured->cover_url }}" alt="" loading="eager" decoding="async"
                                 @class([
                                     'h-full w-full object-cover transition duration-500 group-hover:scale-105',
                                     'sepia-[0.18] contrast-[1.03]' => $featured->is_archive,
                                 ])>
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-700 to-brand-900 text-white/25">
                                <x-ico name="photo" class="h-20 w-20" />
                            </div>
                        @endif
                        <span class="badge absolute left-4 top-4 bg-gold-500 text-slate-900 shadow-sm">
                            <x-ico name="{{ $featured->is_archive ? 'archive-box' : 'star' }}" variant="solid" class="h-3.5 w-3.5" />
                            {{ $featured->is_archive ? 'Архівний альбом' : 'Рекомендований альбом' }}
                        </span>
                    </a>

                    <div class="flex flex-col justify-center p-6 lg:p-10">
                        <h2 class="text-2xl font-extrabold leading-tight text-slate-900 lg:text-3xl">
                            <a href="{{ route('galleries.show', $featured) }}" class="transition hover:text-brand-700">{{ $featured->title }}</a>
                        </h2>
                        <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-500">
                            @if ($featured->published_at)
                                <span class="inline-flex items-center gap-1.5">
                                    <x-ico name="calendar-days" class="h-4 w-4 text-brand-400" />
                                    {{ $featured->published_at->translatedFormat('j F Y') }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1.5">
                                <x-ico name="photo" class="h-4 w-4 text-brand-400" />
                                {{ $featured->photos_count }} фото
                            </span>
                        </div>
                        @if (filled($featured->description))
                            <p class="mt-5 line-clamp-4 leading-relaxed text-slate-500">{{ $featured->description }}</p>
                        @endif
                        <div class="mt-7">
                            <a href="{{ route('galleries.show', $featured) }}" class="btn-accent">
                                Переглянути альбом <x-ico name="arrow-right" class="h-4 w-4" />
                            </a>
                        </div>
                    </div>
                </article>
            @endif

            @if ($rest->isNotEmpty())
                <h2 class="text-xl font-bold text-slate-900 sm:text-2xl">{{ $featured ? 'Усі альбоми' : 'Фотоальбоми' }}</h2>
                <div class="accent-rule"></div>

                <div class="mt-7 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
                    @foreach ($rest as $gallery)
                        <a href="{{ route('galleries.show', $gallery) }}"
                           @class([
                               'card card-interactive group flex flex-col overflow-hidden',
                               'ring-gold-200/80' => $gallery->is_archive,
                           ])>
                            <div class="relative aspect-[16/10] overflow-hidden bg-brand-50">
                                @if ($gallery->cover_url)
                                    <img src="{{ $gallery->cover_url }}" alt="" loading="lazy" decoding="async"
                                         @class([
                                             'h-full w-full object-cover transition duration-500 group-hover:scale-105',
                                             'sepia-[0.18] contrast-[1.03]' => $gallery->is_archive,
                                         ])>
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-700 to-brand-900 text-white/25">
                                        <x-ico name="photo" class="h-14 w-14" />
                                    </div>
                                @endif
                                @if ($gallery->is_archive)
                                    <span class="badge absolute left-3 top-3 bg-gold-500 text-slate-900 shadow-sm">
                                        <x-ico name="archive-box" variant="solid" class="h-3.5 w-3.5" /> Архів
                                    </span>
                                @endif
                                <span class="absolute inset-0 bg-brand-950/0 transition group-hover:bg-brand-950/15"></span>
                            </div>

                            <div class="flex flex-1 flex-col p-5">
                                <h3 class="line-clamp-2 text-base font-bold leading-snug text-slate-900 transition group-hover:text-brand-700">{{ $gallery->title }}</h3>
                                <div class="mt-auto flex items-center justify-between gap-3 pt-4 text-xs text-slate-400">
                                    @if ($gallery->published_at)
                                        <span class="inline-flex items-center gap-1.5">
                                            <x-ico name="calendar-days" class="h-3.5 w-3.5" />
                                            {{ $gallery->published_at->translatedFormat('j F Y') }}
                                        </span>
                                    @else
                                        <span></span>
                                    @endif
                                    <span class="inline-flex shrink-0 items-center gap-1.5">
                                        <x-ico name="photo" class="h-3.5 w-3.5" /> {{ $gallery->photos_count }} фото
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            @if ($galleries->hasPages())
                <div class="mt-10">{{ $galleries->onEachSide(1)->links() }}</div>
            @endif
        </section>
    @else
        <section class="container-site py-12">
            <x-empty-state icon="photo" title="Фотоальбоми незабаром буде додано." />
        </section>
    @endif

</x-layouts.app>
