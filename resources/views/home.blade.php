<x-layouts.app>

    {{-- ===================== ГЕРОЙ / БАНЕР ===================== --}}
    @php $b = $banners->first(); @endphp
    @if ($b)
        {{-- Один статичний банер (без слайдера) - стабільна висота. Фото/слайди додамо пізніше через адмінку. --}}
        <section class="relative overflow-hidden bg-brand-950">
            <div class="pointer-events-none absolute inset-0">
                @if ($b->image)
                    <img src="{{ asset('storage/' . $b->image) }}" alt="" fetchpriority="high" class="h-full w-full object-cover">
                    {{-- Затемнення фото: зліва темніше (під текст) + рівномірне приглушення для контрасту --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-brand-950/95 via-brand-950/80 to-brand-950/55"></div>
                    <div class="absolute inset-0 bg-brand-950/25"></div>
                @else
                    <div class="h-full w-full bg-gradient-to-br from-brand-800 via-brand-900 to-brand-950"></div>
                    <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-brand-600/30 blur-3xl"></div>
                    <div class="absolute bottom-0 left-1/4 h-72 w-72 rounded-full bg-gold-500/10 blur-3xl"></div>
                @endif
            </div>
            <div class="container-site relative flex min-h-[460px] flex-col justify-center py-20 lg:py-28">
                <div class="max-w-2xl">
                    @if ($b->title)
                        <h1 class="text-4xl font-extrabold leading-[1.1] text-white sm:text-5xl lg:text-6xl">{{ $b->title }}</h1>
                    @endif
                    @if ($b->subtitle)
                        <p class="mt-5 max-w-xl text-lg leading-relaxed text-brand-100">{{ $b->subtitle }}</p>
                    @endif
                    @if ($b->link_url)
                        <a href="{{ $b->link_url }}" class="btn-accent mt-8">{{ $b->link_label ?: 'Детальніше' }} <x-ico name="arrow-right" class="h-4 w-4" /></a>
                    @endif
                </div>
            </div>
        </section>
    @else
        {{-- Запасний герой (поки немає банерів) --}}
        <section class="relative overflow-hidden bg-brand-950">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-brand-600/30 blur-3xl"></div>
                <div class="absolute bottom-0 left-1/4 h-72 w-72 rounded-full bg-gold-500/10 blur-3xl"></div>
            </div>
            <div class="container-site relative py-20 lg:py-28">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1.5 text-xs font-medium text-brand-100 ring-1 ring-white/15">
                    <span class="h-1.5 w-1.5 rounded-full bg-gold-400"></span> Структурний підрозділ ОНТУ
                </span>
                <h1 class="mt-6 max-w-3xl text-4xl font-extrabold leading-[1.1] text-white sm:text-5xl lg:text-6xl">
                    Одеський технічний фаховий коледж
                </h1>
                <p class="mt-6 max-w-xl text-lg leading-relaxed text-brand-100">
                    Сучасна фахова передвища освіта: технічні спеціальності, досвідчені викладачі та практична підготовка.
                </p>
                <div class="mt-9 flex flex-wrap gap-3">
                    <a href="{{ url('/abituriyentu') }}" class="btn-accent">Абітурієнту <x-ico name="arrow-right" class="h-4 w-4" /></a>
                    <a href="{{ route('news.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-white/10 px-5 py-2.5 text-sm font-semibold text-white ring-1 ring-white/20 backdrop-blur transition hover:bg-white/15">Новини коледжу</a>
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== ШВИДКІ РОЗДІЛИ ===================== --}}
    @if ($tiles->isNotEmpty())
        <section class="container-site mt-12">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($tiles as $tile)
                    <a href="{{ $tile->url }}" @if ($tile->open_new_tab) target="_blank" rel="noopener" @endif
                       class="card group flex flex-col p-5 transition hover:-translate-y-1 hover:shadow-lg">
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

    {{-- ===================== КОЛЕДЖ У ЦИФРАХ ===================== --}}
    @if ($stats->isNotEmpty())
        <section data-reveal class="mt-16 bg-brand-950"
                 x-data="{ shown: false }"
                 x-init="if ('IntersectionObserver' in window) new IntersectionObserver((entries, obs) => {
                     if (!entries[0].isIntersecting) return;
                     obs.disconnect(); shown = true;
                     $el.querySelectorAll('[data-count]').forEach(el => {
                         const target = parseInt(el.dataset.count, 10);
                         if (isNaN(target)) return;
                         const t0 = performance.now(), dur = 1400;
                         const step = now => {
                             const p = Math.min((now - t0) / dur, 1);
                             el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3))).toLocaleString('uk-UA');
                             if (p < 1) requestAnimationFrame(step);
                         };
                         requestAnimationFrame(step);
                     });
                 }, { threshold: 0.35 }).observe($el)">
            <div class="container-site grid grid-cols-2 gap-x-6 gap-y-10 py-14 text-center lg:grid-cols-4 lg:py-16">
                @foreach ($stats as $stat)
                    @php
                        // «1000+» → число 1000 і суфікс «+»; нечислові значення показуються як є
                        preg_match('/^([^\d]*)(\d[\d\s]*)(.*)$/u', $stat->value, $m);
                        $num = isset($m[2]) ? (int) str_replace(' ', '', $m[2]) : null;
                    @endphp
                    <div>
                        @if ($stat->icon)
                            <x-ico :name="$stat->icon" class="mx-auto mb-3 h-8 w-8 text-gold-400/80" />
                        @endif
                        <div class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl" style="font-family:var(--font-display)">
                            @if ($num !== null)
                                {{-- Початково — фінальне число (працює і без JS); анімація обнуляє його сама --}}
                                {{ $m[1] }}<span data-count="{{ $num }}">{{ number_format($num, 0, ',', ' ') }}</span>{{ $m[3] }}
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

    {{-- ===================== ЦЬОГО ДНЯ В КОЛЕДЖІ ===================== --}}
    @if ($onThisDay)
        <section data-reveal class="container-site mt-16">
            <a href="{{ route('news.show', $onThisDay) }}"
               class="card group mx-auto flex max-w-3xl items-center gap-4 overflow-hidden p-4 transition hover:-translate-y-0.5 hover:shadow-lg sm:gap-5">
                @if ($onThisDay->cover_image)
                    <img src="{{ asset('storage/' . $onThisDay->cover_image) }}" alt="" loading="lazy" decoding="async"
                         class="hidden h-20 w-28 shrink-0 rounded-xl object-cover sm:block">
                @else
                    <span class="hidden h-20 w-28 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-brand-700 to-brand-900 text-white/30 sm:grid">
                        <x-ico name="clock" class="h-8 w-8" />
                    </span>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-gold-600">
                        <x-ico name="sparkles" class="h-3.5 w-3.5" />
                        Цього дня у {{ $onThisDay->published_at->year }} році
                    </p>
                    <h3 class="mt-1 line-clamp-2 font-bold text-slate-900 transition group-hover:text-brand-700">{{ $onThisDay->title }}</h3>
                    <p class="mt-0.5 text-xs text-slate-400">{{ $onThisDay->published_at->translatedFormat('j F Y') }} · з архіву коледжу</p>
                </div>
                <x-ico name="arrow-right" class="h-5 w-5 shrink-0 text-slate-300 transition group-hover:translate-x-1 group-hover:text-brand-600" />
            </a>
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
                                <img src="{{ asset('storage/' . $t->photo) }}" alt="{{ $t->name }}" loading="lazy" decoding="async"
                                     class="h-11 w-11 rounded-full object-cover ring-2 ring-brand-100">
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

</x-layouts.app>
