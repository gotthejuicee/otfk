<x-layouts.app>

    {{-- ===================== ГЕРОЙ / БАНЕР ===================== --}}
    @php $b = $banners->first(); @endphp
    @if ($b)
        {{-- Один статичний банер (без слайдера) - стабільна висота. Фото/слайди додамо пізніше через адмінку. --}}
        <section class="relative overflow-hidden bg-brand-950">
            <div class="pointer-events-none absolute inset-0">
                @if ($b->image)
                    <img src="{{ asset('storage/' . $b->image) }}" alt="" class="h-full w-full object-cover">
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

    {{-- ===================== НОВИНИ ===================== --}}
    @if ($news->isNotEmpty())
        <section class="container-site py-16">
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
        <section class="bg-slate-50 py-16">
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
                                <img src="{{ $video->thumbnail }}" alt="{{ $video->title }}" class="h-full w-full object-cover opacity-90 transition group-hover:scale-105 group-hover:opacity-100">
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

</x-layouts.app>
