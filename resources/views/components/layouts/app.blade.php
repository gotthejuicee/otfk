@props(['title' => null, 'description' => null, 'ogImage' => null])

@php
    use App\Models\MenuItem;
    use App\Models\QuickLink;
    use App\Models\Setting;

    $menu = MenuItem::navigation();
    $s = Setting::map();
    $partners = QuickLink::visible()->location('footer_partner')->ordered()->get();
    $logo = ! empty($s['logo']) ? asset('storage/' . $s['logo']) : null;
    $favicon = ! empty($s['favicon']) ? asset('storage/' . $s['favicon']) : asset('favicon.svg');
    $metaDesc = $description ?: ($s['site_description'] ?? 'Офіційний сайт Одеського технічного фахового коледжу ОНТУ.');
    // Святкова тема (адмінка → «Підвал і вигляд»): null = звичайний вигляд
    $holiday = \App\Support\HolidayTheme::config($s['holiday_theme'] ?? null);
    $year = date('Y');
    // Чи веде пункт меню на поточну сторінку (порівнюємо шлях без домену й слешів) — для aria-current
    $currentPath = rtrim(request()->getPathInfo(), '/') ?: '/';
    $navCurrent = function (?string $href) use ($currentPath) {
        if (! $href || $href === '#') {
            return false;
        }
        $path = rtrim(parse_url($href, PHP_URL_PATH) ?: '/', '/') ?: '/';

        return $path === $currentPath;
    };
@endphp

<!DOCTYPE html>
<html lang="uk" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Позначка, що JS активний — лише тоді вмикається scroll-reveal (без FOUC) --}}
    <script>document.documentElement.classList.add('js')</script>
    <title>{{ $title ? $title . ' - ' . config('app.name') : config('app.name') }}</title>
    <link rel="icon" href="{{ $favicon }}"@if (\Illuminate\Support\Str::endsWith($favicon, '.svg')) type="image/svg+xml"@endif>
    <link rel="apple-touch-icon" href="{{ $favicon }}">
    <meta name="description" content="{{ $metaDesc }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ $title ?: config('app.name') }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @php $shareImage = $ogImage ?: $logo; @endphp
    @if ($shareImage)
        <meta property="og:image" content="{{ $shareImage }}">
        <meta property="og:image:alt" content="{{ $title ?: config('app.name') }}">
    @endif
    <meta property="og:locale" content="uk_UA">
    {{-- Велика картка лише коли є справжня обкладинка сторінки (не логотип-фолбек) --}}
    <meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
    <link rel="alternate" type="application/xml" title="Sitemap" href="{{ url('/sitemap.xml') }}">
    <link rel="alternate" type="application/rss+xml" title="RSS — Новини" href="{{ route('news.feed') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @php $jsonld = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'EducationalOrganization',
        'name' => config('app.name'),
        'url' => url('/'),
        'logo' => $logo ?: asset('favicon.svg'),
        'email' => $s['contact_email'] ?? null,
        'telephone' => $s['contact_phone'] ?? null,
        'address' => $s['contact_address'] ?? null,
    ]); @endphp
    <script type="application/ld+json">{!! json_encode($jsonld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|manrope:600,700,800|cormorant-garamond:400,500,600,700|lora:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
{{-- Фон body задається в app.css (брендові засвітки + сітка з крапок), тому без bg-white --}}
<body class="flex min-h-screen flex-col text-slate-700"@if ($holiday) data-holiday="{{ $s['holiday_theme'] }}"@endif>

    {{-- Святкові прикраси (стрічка + частинки), коли в адмінці обрано тему --}}
    <x-holiday-decor :theme="$s['holiday_theme'] ?? null" />

    {{-- Пропустити навігацію (зʼявляється лише при фокусі з клавіатури) --}}
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-brand-700 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
        Перейти до основного вмісту
    </a>

    {{-- ============================ БАНЕР ОГОЛОШЕНЬ ============================ --}}
    @php
        $annText = trim($s['announcement_text'] ?? '');
        $annType = $s['announcement_type'] ?? 'info';
        $annUrl = trim($s['announcement_url'] ?? '');
        $annStyles = [
            'info' => 'bg-brand-700 text-white',
            'warning' => 'bg-gold-500 text-white',
            'danger' => 'bg-red-600 text-white',
        ];
    @endphp
    @if ($annText !== '')
        <div x-data="{ hidden: false }"
             x-init="hidden = localStorage.getItem('ann-closed') === @js(md5($annText))"
             x-show="!hidden" x-cloak role="status" aria-live="polite"
             class="relative {{ $annStyles[$annType] ?? $annStyles['info'] }}">
            <div class="container-site flex items-center justify-center gap-3 py-2.5 pr-10 text-center text-sm font-medium">
                <x-ico name="megaphone" class="h-4 w-4 shrink-0" />
                @if ($annUrl !== '')
                    <a href="{{ $annUrl }}" class="underline decoration-white/50 underline-offset-2 hover:decoration-white">{{ $annText }}</a>
                @else
                    <span>{{ $annText }}</span>
                @endif
            </div>
            <button type="button" aria-label="Закрити оголошення"
                    @click="hidden = true; try { localStorage.setItem('ann-closed', @js(md5($annText))) } catch (e) {}"
                    class="absolute right-2 top-1/2 grid h-7 w-7 -translate-y-1/2 place-items-center rounded-full transition hover:bg-white/15">
                <x-ico name="x-mark" class="h-4 w-4" />
            </button>
        </div>
    @endif

    {{-- ============================ ШАПКА ============================ --}}
    <header x-data="{ mobile: false, scrolled: false }"
            x-effect="document.body.classList.toggle('overflow-hidden', mobile)"
            @keydown.escape.window="mobile = false"
            @scroll.window.throttle.50ms="scrolled = window.scrollY > 40">
        {{-- Утилітарна стрічка --}}
        <div class="hidden bg-brand-950 text-brand-100 lg:block">
            <div class="mx-auto flex h-9 w-full max-w-[1600px] items-center justify-between px-4 text-xs sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    @if (! empty($s['contact_phone']))
                        <a href="tel:{{ preg_replace('/[^+\d]/', '', $s['contact_phone']) }}" class="inline-flex items-center gap-1.5 transition hover:text-white">
                            <x-ico name="phone" class="h-3.5 w-3.5 text-gold-300" /> {{ $s['contact_phone'] }}
                        </a>
                    @endif
                    @if (! empty($s['contact_phone']) && ! empty($s['contact_email']))
                        <span aria-hidden="true" class="h-3.5 w-px bg-white/20"></span>
                    @endif
                    @if (! empty($s['contact_email']))
                        <a href="mailto:{{ $s['contact_email'] }}" class="inline-flex items-center gap-1.5 transition hover:text-white">
                            <x-ico name="envelope" class="h-3.5 w-3.5 text-gold-300" /> {{ $s['contact_email'] }}
                        </a>
                    @endif
                    {{-- Жива позначка «зараз йде пара» (з розкладу дзвінків; вимикається перемикачем в адмінці) --}}
                    @php $bellPeriods = \App\Models\BellPeriod::chipEnabled() ? \App\Models\BellPeriod::active() : collect(); @endphp
                    @if ($bellPeriods->isNotEmpty())
                        <a href="{{ route('bells') }}"
                           x-data="bellChip(@js($bellPeriods->map(fn ($b) => ['n' => $b->number, 'sh' => $b->shift, 's' => substr($b->starts, 0, 5), 'e' => substr($b->ends, 0, 5)])->values()))"
                           x-init="tick(); setInterval(() => tick(), 30000)" x-show="label" x-cloak
                           class="inline-flex items-center gap-1.5 rounded-full bg-gold-400/15 px-2.5 py-0.5 font-medium text-gold-200 ring-1 ring-gold-400/30 transition hover:bg-gold-400/25">
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-gold-300 opacity-75"></span>
                                <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-gold-400"></span>
                            </span>
                            <span x-text="label"></span>
                        </a>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    @if (! empty($s['social_facebook']))
                        <a href="{{ $s['social_facebook'] }}" target="_blank" rel="noopener" aria-label="Facebook коледжу"
                           class="transition hover:text-white"><x-brand-ico name="facebook" class="h-4 w-4" /></a>
                    @endif
                    @if (! empty($s['social_instagram']))
                        <a href="{{ $s['social_instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram коледжу"
                           class="transition hover:text-white"><x-brand-ico name="instagram" class="h-4 w-4" /></a>
                    @endif
                    @if (! empty($s['social_facebook']) || ! empty($s['social_instagram']))
                        <span aria-hidden="true" class="h-3.5 w-px bg-white/20"></span>
                    @endif
                    <a href="{{ url('/admin') }}" class="inline-flex items-center gap-1.5 text-gold-300 transition hover:text-gold-200">
                        <x-ico name="lock-closed" class="h-3.5 w-3.5" /> Адмінпанель
                    </a>
                </div>
            </div>
        </div>

        {{-- Липка частина: бренд (білий) + навігація (темна) — без білої смуги під меню --}}
        <div class="sticky top-0 z-40">
            {{-- Ряд бренду та дій --}}
            <div class="border-b border-transparent bg-white shadow-sm transition-[box-shadow,background-color,border-color] duration-300"
                 :class="scrolled ? 'border-slate-200/80 bg-white/90 shadow-md backdrop-blur-md' : ''">
                <div class="mx-auto flex h-16 w-full max-w-[1600px] items-center justify-between gap-2 px-4 sm:h-20 sm:gap-6 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2 sm:shrink-0 sm:gap-3">
                    <span class="relative shrink-0">
                        @if ($holiday)
                            <span class="holiday-logo-badge" aria-hidden="true">{{ $holiday['badge'] }}</span>
                        @endif
                        @if ($logo)
                            <img src="{{ $logo }}" alt="ОТФК ОНТУ" class="h-9 w-auto shrink-0 sm:h-12 lg:h-16">
                        @else
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-brand-700 to-brand-900 text-white shadow-sm sm:h-12 sm:w-12">
                                <x-ico name="academic-cap" class="h-7 w-7" />
                            </span>
                        @endif
                    </span>
                    <span class="min-w-0 leading-tight">
                        <span class="font-display block truncate text-sm font-extrabold tracking-tight text-brand-900 sm:whitespace-nowrap sm:text-lg">{{ $s['brand_short'] ?? 'ОТФК ОНТУ' }}</span>
                        <span class="hidden whitespace-nowrap text-xs text-slate-500 sm:block">{{ $s['brand_name'] ?? 'Одеський технічний фаховий коледж' }}</span>
                    </span>
                </a>

                <div class="flex items-center gap-1.5 sm:gap-3">
                    {{-- Пошук з миттєвими підказками (десктоп) --}}
                    <div x-data="liveSearch(@js(route('search.suggest')), @js(route('search')))"
                         @click.outside="open = false" @keydown.escape.window="open = false"
                         class="relative hidden lg:block">
                        <form action="{{ route('search') }}" method="GET" class="relative">
                            <x-ico name="magnifying-glass" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                            <input type="search" name="q" placeholder="Пошук..." autocomplete="off"
                                   x-model="q" @input.debounce.250ms="suggest()" @focus="items.length && (open = true)"
                                   class="w-52 rounded-full border-0 bg-white py-2 pl-9 pr-4 text-sm text-slate-700 shadow-sm ring-1 ring-slate-200 transition focus:w-72 focus:ring-2 focus:ring-brand-500" />
                        </form>
                        <div x-show="open" x-cloak
                             class="absolute right-0 top-full z-50 mt-2 w-80 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl">
                            <template x-for="it in items" :key="it.url + it.title">
                                <a :href="it.url" class="flex items-center gap-2.5 px-3.5 py-2.5 transition hover:bg-brand-50">
                                    <span class="shrink-0 rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-slate-500" x-text="it.group"></span>
                                    <span class="min-w-0 truncate text-sm text-slate-700" x-text="it.title"></span>
                                </a>
                            </template>
                            <p x-show="!items.length && !busy" class="px-3.5 py-3 text-sm text-slate-400">Нічого не знайдено.</p>
                            <a x-show="items.length" :href="allUrl()"
                               class="block border-t border-slate-100 px-3.5 py-2.5 text-sm font-semibold text-brand-700 transition hover:bg-brand-50">
                                Усі результати →
                            </a>
                        </div>
                    </div>
                    {{-- CTA --}}
                    {{-- Головна дія абітурієнта — лишається в шапці й на телефоні, лише компактніша --}}
                    <a href="{{ url('/abituriyentu') }}" class="btn-accent group h-11 whitespace-nowrap px-2.5 text-xs sm:h-auto sm:px-5 sm:text-sm">
                        Вступнику
                        <x-ico name="arrow-right" class="ml-1.5 hidden h-4 w-4 transition-transform group-hover:translate-x-0.5 sm:block" />
                    </a>
                    {{-- Мобільні дії --}}
                    <a href="{{ route('search') }}" class="btn-ghost hidden h-11 w-11 p-0 sm:inline-flex lg:hidden" aria-label="Пошук"><x-ico name="magnifying-glass" class="h-5 w-5" /></a>
                    <button @click="mobile = true" class="btn-ghost h-11 w-11 p-0 xl:hidden" aria-label="Меню"
                            aria-controls="mobile-menu" :aria-expanded="mobile ? 'true' : 'false'"><x-ico name="bars-3" class="h-6 w-6" /></button>
                </div>
                </div>
            </div>

            {{-- Навігаційна стрічка (десктоп) — впирається в банер без білої смуги знизу --}}
            <nav class="hidden bg-brand-900 xl:block">
                <div class="mx-auto flex w-full max-w-[1600px] flex-wrap items-stretch px-4 sm:px-6 lg:px-8">
                    @foreach ($menu as $item)
                        @if ($item->children->isNotEmpty())
                            <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative shrink-0">
                                <button type="button" @click="open = ! open"
                                        class="flex items-center gap-1 whitespace-nowrap border-b-2 border-transparent px-2 py-3 text-[13px] font-medium text-white/90 transition hover:bg-white/5 hover:text-white"
                                        :class="open ? 'border-gold-400 bg-white/5 text-white' : ''">
                                    {{ $item->label }}
                                    <x-ico name="chevron-down" class="h-4 w-4 opacity-70 transition" x-bind:class="open && 'rotate-180'" />
                                </button>
                                <div x-show="open" x-cloak x-transition.opacity
                                     class="absolute left-0 top-full z-50 max-h-[75vh] w-72 overflow-y-auto rounded-b-xl border border-slate-200 bg-white p-2 shadow-2xl">
                                    @foreach ($item->children as $child)
                                        <a href="{{ $child->href }}" @if ($child->open_new_tab) target="_blank" @endif
                                           @if ($navCurrent($child->href)) aria-current="page" @endif
                                           @class([
                                               'block rounded-lg px-3 py-2 text-sm transition hover:bg-brand-50 hover:text-brand-800',
                                               'text-slate-600' => ! $navCurrent($child->href),
                                               'bg-brand-50 font-semibold text-brand-800' => $navCurrent($child->href),
                                           ])>{{ $child->label }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $item->href }}" @if ($item->open_new_tab) target="_blank" @endif
                               @if ($navCurrent($item->href)) aria-current="page" @endif
                               @class([
                                   'flex shrink-0 items-center whitespace-nowrap border-b-2 px-2 py-3 text-[13px] font-medium transition hover:bg-white/5',
                                   'border-transparent text-white/90 hover:border-gold-400 hover:text-white' => ! $navCurrent($item->href),
                                   'border-gold-400 text-white' => $navCurrent($item->href),
                               ])>{{ $item->label }}</a>
                        @endif
                    @endforeach
                </div>
            </nav>
        </div>

        {{-- Мобільне меню (off-canvas) --}}
        <div id="mobile-menu" x-show="mobile" x-cloak class="fixed inset-0 z-50 xl:hidden">
            <div @click="mobile = false" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
            <div class="absolute right-0 top-0 flex h-full w-80 max-w-[88%] flex-col bg-white shadow-2xl"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0">
                <div class="flex h-16 items-center justify-between border-b border-slate-200 pl-4 pr-2">
                    <span class="flex min-w-0 items-center gap-2.5">
                        @if ($logo)
                            <img src="{{ $logo }}" alt="" aria-hidden="true" class="h-9 w-auto shrink-0">
                        @endif
                        <span class="font-display truncate font-extrabold text-brand-900">{{ $s['brand_short'] ?? 'ОТФК ОНТУ' }}</span>
                    </span>
                    <button @click="mobile = false" class="btn-ghost h-11 w-11 shrink-0 p-0" aria-label="Закрити меню"><x-ico name="x-mark" class="h-6 w-6" /></button>
                </div>
                <div class="border-b border-slate-100 p-4">
                    <div x-data="liveSearch(@js(route('search.suggest')), @js(route('search')))" class="relative">
                        <form action="{{ route('search') }}" method="GET" class="relative">
                            <x-ico name="magnifying-glass" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                            <input type="search" name="q" placeholder="Пошук по сайту..." autocomplete="off"
                                   x-model="q" @input.debounce.250ms="suggest()" class="input w-full pl-9" />
                        </form>
                        <div x-show="open && items.length" x-cloak
                             class="absolute left-0 right-0 top-full z-50 mt-2 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl">
                            <template x-for="it in items" :key="it.url + it.title">
                                <a :href="it.url" class="flex items-center gap-2.5 px-3.5 py-2.5 transition hover:bg-brand-50">
                                    <span class="shrink-0 rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-slate-500" x-text="it.group"></span>
                                    <span class="min-w-0 truncate text-sm text-slate-700" x-text="it.title"></span>
                                </a>
                            </template>
                            <a :href="allUrl()" class="block border-t border-slate-100 px-3.5 py-2.5 text-sm font-semibold text-brand-700">Усі результати →</a>
                        </div>
                    </div>
                    <a href="{{ url('/abituriyentu') }}" class="btn-accent mt-3 w-full py-3">Вступнику</a>
                    {{-- Три найчастіші дії з телефона — щоб не шукати їх у 12 пунктах меню --}}
                    <div class="mt-3 grid grid-cols-3 gap-2">
                        @foreach ([
                            ['url' => route('specialties.index'), 'icon' => 'academic-cap', 'label' => 'Спеціальності'],
                            ['url' => route('bells'), 'icon' => 'clock', 'label' => 'Дзвінки'],
                            ['url' => route('contacts'), 'icon' => 'map-pin', 'label' => 'Контакти'],
                        ] as $quick)
                            <a href="{{ $quick['url'] }}"
                               class="flex flex-col items-center gap-1.5 rounded-xl bg-slate-50 px-2 py-3 text-center text-[11px] font-semibold leading-tight text-slate-700 ring-1 ring-slate-200 transition hover:bg-brand-50 hover:text-brand-800">
                                <x-ico :name="$quick['icon']" class="h-5 w-5 text-brand-700" />
                                {{ $quick['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <nav class="flex-1 overflow-y-auto p-3">
                    @foreach ($menu as $item)
                        @if ($item->children->isNotEmpty())
                            <div x-data="{ sub: false }" class="border-b border-slate-100">
                                <button @click="sub = !sub" class="flex w-full items-center justify-between px-3 py-3.5 text-sm font-semibold text-slate-800">
                                    {{ $item->label }}
                                    <x-ico name="chevron-down" class="h-4 w-4 transition" ::class="sub && 'rotate-180'" />
                                </button>
                                <div x-show="sub" x-cloak class="pb-2">
                                    @foreach ($item->children as $child)
                                        <a href="{{ $child->href }}" @if ($child->open_new_tab) target="_blank" @endif
                                           @if ($navCurrent($child->href)) aria-current="page" @endif
                                           @class([
                                               'block rounded-lg px-5 py-2.5 text-sm hover:bg-brand-50 hover:text-brand-800',
                                               'text-slate-600' => ! $navCurrent($child->href),
                                               'bg-brand-50 font-semibold text-brand-800' => $navCurrent($child->href),
                                           ])>{{ $child->label }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $item->href }}" @if ($item->open_new_tab) target="_blank" @endif
                               @if ($navCurrent($item->href)) aria-current="page" @endif
                               @class([
                                   'block border-b border-slate-100 px-3 py-3.5 text-sm font-semibold hover:text-brand-800',
                                   'text-slate-800' => ! $navCurrent($item->href),
                                   'text-brand-700' => $navCurrent($item->href),
                               ])>{{ $item->label }}</a>
                        @endif
                    @endforeach
                </nav>
                {{-- Утилітарна стрічка прихована на мобільному — її вміст живе тут --}}
                <div class="border-t border-slate-200 bg-slate-50 px-4 py-4 text-sm">
                    @if (! empty($s['contact_phone']))
                        <a href="tel:{{ preg_replace('/[^+\d]/', '', $s['contact_phone']) }}" class="flex items-center gap-2.5 py-2 font-semibold text-brand-800">
                            <x-ico name="phone" class="h-4 w-4 text-gold-600" /> {{ $s['contact_phone'] }}
                        </a>
                    @endif
                    @if (! empty($s['contact_email']))
                        <a href="mailto:{{ $s['contact_email'] }}" class="flex items-center gap-2.5 py-2 text-slate-600">
                            <x-ico name="envelope" class="h-4 w-4 text-gold-600" /> {{ $s['contact_email'] }}
                        </a>
                    @endif
                    <div class="mt-2 flex items-center gap-3">
                        @if (! empty($s['social_facebook']))
                            <a href="{{ $s['social_facebook'] }}" target="_blank" rel="noopener" aria-label="Facebook коледжу"
                               class="grid h-10 w-10 place-items-center rounded-full bg-white text-brand-700 ring-1 ring-slate-200"><x-brand-ico name="facebook" class="h-4 w-4" /></a>
                        @endif
                        @if (! empty($s['social_instagram']))
                            <a href="{{ $s['social_instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram коледжу"
                               class="grid h-10 w-10 place-items-center rounded-full bg-white text-brand-700 ring-1 ring-slate-200"><x-brand-ico name="instagram" class="h-4 w-4" /></a>
                        @endif
                        <a href="{{ url('/admin') }}" class="ml-auto inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500">
                            <x-ico name="lock-closed" class="h-3.5 w-3.5" /> Адмінпанель
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Вміст --}}
    <main id="main-content" class="flex-1">
        {{ $slot }}
    </main>

    {{-- Підвал --}}
    <footer class="border-t-2 border-gold-500/70 bg-brand-950 text-brand-100">
        <div class="container-site grid gap-10 py-14 md:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-1">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-widest text-gold-300">Про коледж</h3>
                <div class="flex items-center gap-3">
                    @if ($logo)
                        <span class="grid h-11 place-items-center rounded-xl bg-white px-2 ring-1 ring-white/15">
                            <img src="{{ $logo }}" alt="ОТФК ОНТУ" loading="lazy" decoding="async" class="h-8 w-auto">
                        </span>
                    @else
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-white/10 text-white ring-1 ring-white/15">
                            <x-ico name="academic-cap" class="h-6 w-6" />
                        </span>
                    @endif
                    <span class="font-display font-extrabold text-white">{{ $s['brand_short'] ?? 'ОТФК ОНТУ' }}</span>
                </div>
                <p class="mt-4 text-sm leading-relaxed text-brand-200">
                    {{ $s['footer_about'] ?? 'Одеський технічний фаховий коледж Одеського національного технологічного університету.' }}
                </p>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-widest text-gold-300">Розділи</h3>
                @php
                    // Швидкі розділи підвалу: маршрут => підпис
                    $footerLinks = [
                        route('home') => 'Головна',
                        route('news.index') => 'Новини',
                        route('events') => 'Події',
                        route('specialties.index') => 'Спеціальності',
                        route('galleries.index') => 'Галерея',
                        route('contacts') => 'Контакти',
                    ];
                @endphp
                <ul class="mt-4 space-y-2 text-sm text-brand-200">
                    @foreach ($footerLinks as $href => $label)
                        <li>
                            <a href="{{ $href }}" class="group inline-flex items-center gap-1.5 transition hover:text-white">
                                <x-ico name="chevron-right" class="h-3.5 w-3.5 shrink-0 text-gold-400 transition-transform group-hover:translate-x-0.5" />
                                {{ $label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-widest text-gold-300">Контакти</h3>
                <ul class="mt-4 space-y-3 text-sm text-brand-200">
                    @if (! empty($s['contact_address']))
                        <li class="flex gap-2"><x-ico name="map-pin" class="mt-0.5 h-4 w-4 shrink-0 text-gold-300" /> {{ $s['contact_address'] }}</li>
                    @endif
                    @if (! empty($s['contact_phone']))
                        <li class="flex gap-2"><x-ico name="phone" class="mt-0.5 h-4 w-4 shrink-0 text-gold-300" /> {{ $s['contact_phone'] }}</li>
                    @endif
                    @if (! empty($s['contact_email']))
                        <li class="flex gap-2"><x-ico name="envelope" class="mt-0.5 h-4 w-4 shrink-0 text-gold-300" /> {{ $s['contact_email'] }}</li>
                    @endif
                    @if (! empty($s['work_hours']))
                        <li class="flex gap-2"><x-ico name="clock" class="mt-0.5 h-4 w-4 shrink-0 text-gold-300" /> {{ $s['work_hours'] }}</li>
                    @endif
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-widest text-gold-300">Партнери</h3>
                @php
                    // Фолбек, якщо в адмінці ще немає партнерських посилань
                    $partnerList = $partners->isNotEmpty()
                        ? $partners->map(fn ($p) => ['url' => $p->url, 'title' => $p->title, 'blank' => (bool) $p->open_new_tab])
                        : collect([
                            ['url' => 'https://ontu.edu.ua', 'title' => 'ОНТУ', 'blank' => true],
                            ['url' => 'https://mon.gov.ua', 'title' => 'МОН України', 'blank' => true],
                        ]);
                @endphp
                <ul class="mt-4 space-y-2 text-sm text-brand-200">
                    @foreach ($partnerList as $partner)
                        <li>
                            <a href="{{ $partner['url'] }}" @if ($partner['blank']) target="_blank" rel="noopener" @endif
                               class="inline-flex items-center gap-2 rounded-lg bg-white/5 px-2.5 py-1.5 ring-1 ring-white/10 transition hover:bg-white/10 hover:text-white">
                                <x-ico name="academic-cap" class="h-4 w-4 shrink-0 text-gold-300" />
                                {{ $partner['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-5 flex gap-3">
                    @if (! empty($s['social_facebook']))
                        <a href="{{ $s['social_facebook'] }}" target="_blank" rel="noopener" aria-label="Facebook коледжу"
                           class="grid h-9 w-9 place-items-center rounded-lg bg-white/10 text-white transition hover:bg-white/20"><x-brand-ico name="facebook" class="h-4 w-4" /></a>
                    @endif
                    @if (! empty($s['social_instagram']))
                        <a href="{{ $s['social_instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram коледжу"
                           class="grid h-9 w-9 place-items-center rounded-lg bg-white/10 text-white transition hover:bg-white/20"><x-brand-ico name="instagram" class="h-4 w-4" /></a>
                    @endif
                </div>
            </div>
        </div>
        <div class="border-t border-white/10 py-5">
            @php
                // Напис версії сайту: текст і колір редагуються в адмінці
                // (Налаштування: site_version_label / site_version_color).
                $versionLabel = trim($s['site_version_label'] ?? 'Альфа-версія');
                $versionColors = [
                    'gold' => ['badge' => 'bg-gold-400/15 text-gold-200 ring-gold-400/30', 'dot' => 'bg-gold-400'],
                    'green' => ['badge' => 'bg-emerald-400/15 text-emerald-200 ring-emerald-400/30', 'dot' => 'bg-emerald-400'],
                    'blue' => ['badge' => 'bg-sky-400/15 text-sky-200 ring-sky-400/30', 'dot' => 'bg-sky-400'],
                    'red' => ['badge' => 'bg-red-400/15 text-red-200 ring-red-400/30', 'dot' => 'bg-red-400'],
                    'gray' => ['badge' => 'bg-slate-400/15 text-slate-300 ring-slate-400/30', 'dot' => 'bg-slate-400'],
                ];
                $versionColor = $versionColors[$s['site_version_color'] ?? 'gold'] ?? $versionColors['gold'];
            @endphp
            <div class="container-site flex flex-col items-center justify-center gap-2.5 text-center text-xs text-brand-300 sm:flex-row">
                <span>© 2014-{{ $year }} ВСП «ОТФК ОНТУ». Усі права захищено.</span>
                @if ($versionLabel !== '')
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-medium ring-1 {{ $versionColor['badge'] }}"
                          title="Стадія роботи сайту">
                        <span class="h-1.5 w-1.5 rounded-full {{ $versionColor['dot'] }}"></span> {{ $versionLabel }}
                    </span>
                @endif
            </div>
        </div>
    </footer>

    {{-- Логіка «зараз йде пара» (розклад дзвінків): спільна для плашки в шапці та сторінки розкладу --}}
    <script>
        (function () {
            const ORD = { 1: '1-ша', 2: '2-га', 3: '3-тя', 4: '4-та', 5: '5-та', 6: '6-та', 7: '7-ма', 8: '8-ма' };
            const toMin = t => +t.slice(0, 2) * 60 + +t.slice(3, 5);

            // Повертає стан розкладу на зараз:
            //   current — ключі пар, що зараз ідуть («зміна:номер»; у перехресті змін їх дві),
            //   gaps    — ключі пар, після яких просто зараз триває перерва,
            //   status  — повний текст для сторінки, short — короткий для плашки в шапці,
            //   left/pct — скільки хвилин лишилось і відсоток проходження кожної активної пари.
            window.bellState = function (periods) {
                const empty = { current: [], gaps: [], status: '', short: '', left: {}, pct: {} };
                if (!periods.length) return empty;

                const d = new Date();
                const cur = d.getHours() * 60 + d.getMinutes();

                const multi = new Set(periods.map(p => p.sh)).size > 1;
                const key = p => p.sh + ':' + p.n;
                const name = p => (ORD[p.n] ?? p.n) + ' пара' + (multi ? ' (' + p.sh + ' зміна)' : '');

                if (d.getDay() === 0) return { ...empty, status: 'Неділя — вихідний' };

                // Перерва між сусідніми парами однієї зміни; ключ — пара, після якої вона йде.
                const gaps = periods.filter((p, i) => {
                    const nx = periods[i + 1];

                    return nx && nx.sh === p.sh && cur >= toMin(p.e) && cur < toMin(nx.s);
                }).map(key);

                // Зміни накладаються (4-та пара 1-ї зміни йде разом з 1-ю парою 2-ї), тому пар може бути дві.
                const running = periods.filter(p => cur >= toMin(p.s) && cur < toMin(p.e));

                if (running.length) {
                    const left = {}, pct = {};
                    running.forEach(p => {
                        const s = toMin(p.s), e = toMin(p.e);
                        left[key(p)] = e - cur;
                        pct[key(p)] = Math.round((cur - s) / (e - s) * 100);
                    });
                    const soonest = Math.min(...running.map(p => toMin(p.e)));

                    return {
                        current: running.map(key),
                        gaps,
                        status: running.map(name).join(' · ') + ' · до кінця ' + (soonest - cur) + ' хв',
                        short: name(running[0]) + ' · до кінця ' + left[key(running[0])] + ' хв',
                        left, pct,
                    };
                }

                const next = periods.filter(p => toMin(p.s) > cur).sort((a, b) => toMin(a.s) - toMin(b.s))[0];
                if (next) {
                    // зранку показуємо за годину до першої пари; між парами — завжди
                    const started = cur >= Math.min(...periods.map(p => toMin(p.s)));
                    const text = started
                        ? 'Перерва · далі ' + name(next) + ' о ' + next.s
                        : name(next) + ' о ' + next.s;

                    if (started || toMin(next.s) - cur <= 60) return { ...empty, gaps, status: text, short: text };

                    return empty; // задовго до першої пари — мовчимо
                }

                // Наступних пар немає — день уже скінчився. У шапці про це не пишемо, лише на сторінці.
                return { ...empty, status: 'Пари на сьогодні завершено' };
            };

            window.bellChip = periods => ({
                label: '',
                tick() { this.label = window.bellState(periods).short; },
            });

            window.bellSchedule = periods => ({
                current: [],
                gaps: [],
                status: '',
                left: {},
                pct: {},
                isNow(key) { return this.current.includes(key); },
                isGapNow(key) { return this.gaps.includes(key); },
                tick() {
                    const st = window.bellState(periods);
                    this.current = st.current; this.gaps = st.gaps; this.status = st.status;
                    this.left = st.left; this.pct = st.pct;
                },
            });

            // Прелоад сторінок при наведенні: клік відчувається миттєвим.
            // Пропускаємо зовнішні лінки, файли, адмінку та режим економії трафіку.
            (function () {
                const conn = navigator.connection;
                if (conn && (conn.saveData || /2g/.test(conn.effectiveType || ''))) return;
                const seen = new Set();
                let timer = null;
                document.addEventListener('mouseover', e => {
                    const a = e.target.closest('a[href]');
                    if (!a || a.origin !== location.origin || a.target === '_blank') return;
                    const url = a.href.split('#')[0];
                    if (seen.has(url) || url === location.href.split('#')[0]) return;
                    if (/^\/(admin|storage|build|livewire)\b/.test(a.pathname) || /\/ics$/.test(a.pathname)) return;
                    clearTimeout(timer);
                    timer = setTimeout(() => {
                        seen.add(url);
                        const l = document.createElement('link');
                        l.rel = 'prefetch'; l.href = url; l.as = 'document';
                        document.head.appendChild(l);
                    }, 65); // невелика затримка — реагуємо лише на «намір», а не на проліт курсора
                }, { passive: true });
                document.addEventListener('mouseout', () => clearTimeout(timer), { passive: true });
            })();

            // Миттєві підказки пошуку (шапка + мобільне меню)
            window.liveSearch = (suggestUrl, searchUrl) => ({
                q: '', items: [], open: false, busy: false,
                suggest() {
                    const q = this.q.trim();
                    if (q.length < 2) { this.items = []; this.open = false; return; }
                    this.busy = true;
                    fetch(suggestUrl + '?q=' + encodeURIComponent(q), { headers: { Accept: 'application/json' } })
                        .then(r => r.json())
                        .then(d => { this.items = d.results || []; this.open = true; })
                        .catch(() => {})
                        .finally(() => this.busy = false);
                },
                allUrl() { return searchUrl + '?q=' + encodeURIComponent(this.q.trim()); },
            });

            // Поява секцій при прокручуванні. Вимикається, якщо немає підтримки
            // або користувач у системі обрав «зменшити рух» — тоді все видно одразу.
            (function () {
                const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
                if (reduce || !('IntersectionObserver' in window)) {
                    document.querySelectorAll('[data-reveal]').forEach(el => el.classList.add('is-visible'));
                    return;
                }
                const io = new IntersectionObserver((entries) => {
                    entries.forEach(e => {
                        if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); }
                    });
                }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
                document.querySelectorAll('[data-reveal]').forEach(el => io.observe(el));
            })();
        })();
    </script>
</body>
</html>
