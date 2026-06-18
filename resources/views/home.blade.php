<x-layouts.app>

    {{-- ===================== ГЕРОЙ / БАНЕР (слайдер) ===================== --}}
    <x-home.hero-slider :banners="$banners" />

    {{-- ===================== ШВИДКІ РОЗДІЛИ ===================== --}}
    @if ($tiles->isNotEmpty())
        <section class="container-site mt-12">
            <h2 class="sr-only">Швидкі розділи</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($tiles as $tile)
                    <a href="{{ $tile->url }}" @if ($tile->open_new_tab) target="_blank" rel="noopener" @endif
                       class="card card-interactive group flex flex-col p-5">
                        <span class="grid h-12 w-12 place-items-center rounded-xl bg-{{ $tile->color }}-50 text-{{ $tile->color }}-600 transition group-hover:bg-{{ $tile->color }}-600 group-hover:text-white">
                            <x-ico :name="$tile->icon ?: 'academic-cap'" class="h-6 w-6" />
                        </span>
                        <h3 class="mt-4 font-bold text-slate-900">{{ $tile->title }}</h3>
                        @if ($tile->description)
                            <p class="mt-1 text-sm text-slate-500">{{ $tile->description }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ===================== НАЙБЛИЖЧІ ПОДІЇ ===================== --}}
    @if ($events->isNotEmpty())
        <section data-reveal class="container-site py-16">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-900">Найближчі події</h2>
                    <div class="accent-rule"></div>
                </div>
                <a href="{{ route('events') }}" class="btn-outline shrink-0">Усі події <x-ico name="arrow-right" class="h-4 w-4" /></a>
            </div>
            <div class="mt-9 grid gap-5 md:grid-cols-3">
                @foreach ($events as $event)
                    <div class="card flex gap-4 p-5">
                        <div class="grid h-16 w-16 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-800">
                            <div class="text-center leading-none">
                                <div class="text-2xl font-extrabold">{{ $event->starts_at->format('d') }}</div>
                                <div class="mt-0.5 text-[11px] font-semibold uppercase">{{ $event->starts_at->translatedFormat('M') }}</div>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold leading-snug text-slate-900">{{ $event->title }}</h3>
                            <p class="mt-1 text-xs text-slate-500">
                                <x-ico name="clock" class="-mt-0.5 inline h-3.5 w-3.5" /> {{ $event->starts_at->format('H:i') }}
                                @if ($event->location)
                                    · <x-ico name="map-pin" class="-mt-0.5 inline h-3.5 w-3.5" /> {{ $event->location }}
                                @endif
                            </p>
                            @if ($event->description)
                                <p class="mt-1.5 line-clamp-2 text-sm text-slate-500">{{ $event->description }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ===================== НОВИНИ ===================== --}}
    @if ($news->isNotEmpty())
        <section data-reveal class="container-site py-16">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-900">Останні новини</h2>
                    <div class="accent-rule"></div>
                </div>
                <a href="{{ route('news.index') }}" class="btn-outline shrink-0">Усі новини <x-ico name="arrow-right" class="h-4 w-4" /></a>
            </div>
            <div class="mt-9 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($news as $item)
                    <x-news-card :item="$item" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- ===================== ВІДЕО ===================== --}}
    @if ($videos->isNotEmpty())
        <section data-reveal class="bg-slate-50 py-16">
            <div class="container-site">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 class="text-3xl font-extrabold text-slate-900">Останні відео</h2>
                        <div class="accent-rule"></div>
                    </div>
                    <a href="{{ route('video.index') }}" class="btn-outline shrink-0">Усі відео <x-ico name="arrow-right" class="h-4 w-4" /></a>
                </div>
                <div class="mt-9 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($videos as $video)
                        <a href="{{ $video->watch_url }}" target="_blank" rel="noopener" class="card card-interactive group overflow-hidden">
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
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== ВІДГУКИ ===================== --}}
    @if ($testimonials->isNotEmpty())
        <section data-reveal class="container-site py-16">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-slate-900">Відгуки студентів та випускників</h2>
                <div class="accent-rule mx-auto"></div>
            </div>
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                @foreach ($testimonials as $t)
                    <figure class="card flex flex-col p-6">
                        <x-ico name="chat-bubble-bottom-center-text" variant="solid" class="h-7 w-7 text-gold-400" />
                        <blockquote class="mt-4 flex-1 text-sm leading-relaxed text-slate-600">«{{ $t->quote }}»</blockquote>
                        <figcaption class="mt-5 flex items-center gap-3 border-t border-slate-100 pt-4">
                            @if ($t->photo)
                                <x-picture :path="$t->photo" :alt="$t->name" loading="lazy" decoding="async"
                                           class="h-11 w-11 rounded-full object-cover ring-2 ring-brand-100" />
                            @else
                                <span class="grid h-11 w-11 place-items-center rounded-full bg-brand-700 text-sm font-bold text-white">{{ $t->initials }}</span>
                            @endif
                            <span>
                                <span class="block text-sm font-bold text-slate-900">{{ $t->name }}</span>
                                @if ($t->role)
                                    <span class="block text-xs text-slate-500">{{ $t->role }}</span>
                                @endif
                            </span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ===================== ЦЬОГО ДНЯ В КОЛЕДЖІ ===================== --}}
    @if ($onThisDay)
        <section data-reveal class="container-site mt-16">
            <a href="{{ route('news.show', $onThisDay) }}"
               class="card card-interactive group mx-auto flex max-w-3xl items-center gap-4 overflow-hidden p-4 sm:gap-5">
                @if ($onThisDay->cover_image)
                    <x-picture :path="$onThisDay->cover_image" :alt="$onThisDay->title" loading="lazy" decoding="async"
                               class="h-16 w-24 shrink-0 rounded-xl object-cover sm:h-20 sm:w-28" />
                @else
                    <span class="grid h-16 w-24 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-brand-700 to-brand-900 text-white/30 sm:h-20 sm:w-28">
                        <x-ico name="clock" class="h-8 w-8" />
                    </span>
                @endif
                <div class="min-w-0 flex-1">
                    @php $sameDay = $onThisDay->published_at->format('m-d') === now()->format('m-d'); @endphp
                    <p class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-gold-600">
                        <x-ico name="sparkles" class="h-3.5 w-3.5" />
                        {{ $sameDay ? 'Цього дня' : 'Цими днями' }} у {{ $onThisDay->published_at->year }} році
                    </p>
                    <h3 class="mt-1 line-clamp-2 font-bold text-slate-900 transition group-hover:text-brand-700">{{ $onThisDay->title }}</h3>
                    <p class="mt-0.5 text-xs text-slate-400">{{ $onThisDay->published_at->translatedFormat('j F Y') }} · з архіву коледжу</p>
                </div>
                <x-ico name="arrow-right" class="h-5 w-5 shrink-0 text-slate-300 transition group-hover:translate-x-1 group-hover:text-brand-600" />
            </a>
        </section>
    @endif

    {{-- ===================== КОЛЕДЖ У ЦИФРАХ ===================== --}}
    @if ($stats->isNotEmpty())
        <section data-reveal class="mt-16 bg-brand-950">
            <div class="container-site grid grid-cols-2 gap-x-6 gap-y-10 py-14 text-center lg:grid-cols-4 lg:py-16">
                @foreach ($stats as $stat)
                    @php
                        // «1000+» → «1 000+» (розряди), решта значень — як є; без анімації
                        preg_match('/^([^\d]*)(\d[\d\s]*)(.*)$/u', $stat->value, $m);
                        $num = isset($m[2]) ? (int) str_replace(' ', '', $m[2]) : null;
                    @endphp
                    <div>
                        @if ($stat->icon)
                            <x-ico :name="$stat->icon" class="mx-auto mb-3 h-8 w-8 text-gold-400/80" />
                        @endif
                        <div class="font-display text-4xl font-extrabold tracking-tight text-white sm:text-5xl">
                            @if ($num !== null)
                                {{ $m[1] }}{{ number_format($num, 0, ',', ' ') }}{{ $m[3] }}
                            @else
                                {{ $stat->value }}
                            @endif
                        </div>
                        <div class="mt-2 text-sm font-medium uppercase tracking-wide text-brand-200">{{ $stat->label }}</div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

</x-layouts.app>
