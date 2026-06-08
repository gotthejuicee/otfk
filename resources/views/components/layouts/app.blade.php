@props(['title' => null])

@php
    use App\Models\MenuItem;
    use App\Models\QuickLink;
    use App\Models\Setting;

    $menu = MenuItem::roots()->visible()
        ->with(['children' => fn ($q) => $q->visible()->orderBy('sort_order'), 'page', 'children.page'])
        ->get();
    $s = Setting::map();
    $partners = QuickLink::visible()->location('footer_partner')->ordered()->get();
    $logo = ! empty($s['logo']) ? asset('storage/' . $s['logo']) : null;
    $favicon = ! empty($s['favicon']) ? asset('storage/' . $s['favicon']) : asset('favicon.svg');
    $year = date('Y');
@endphp

<!DOCTYPE html>
<html lang="uk" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title . ' - ' . config('app.name') : config('app.name') }}</title>
    <link rel="icon" href="{{ $favicon }}"@if (\Illuminate\Support\Str::endsWith($favicon, '.svg')) type="image/svg+xml"@endif>
    <link rel="apple-touch-icon" href="{{ $favicon }}">
    <meta name="description" content="{{ $s['site_description'] ?? 'Офіційний сайт Одеського технічного фахового коледжу ОНТУ.' }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ $title ?: config('app.name') }}">
    <meta property="og:description" content="{{ $s['site_description'] ?? 'Офіційний сайт Одеського технічного фахового коледжу ОНТУ.' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($logo)<meta property="og:image" content="{{ $logo }}">@endif
    <meta property="og:locale" content="uk_UA">
    <meta name="twitter:card" content="summary">
    <link rel="alternate" type="application/xml" title="Sitemap" href="{{ url('/sitemap.xml') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|manrope:600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-white text-slate-700">

    {{-- ============================ ШАПКА ============================ --}}
    <header x-data="{ mobile: false }">
        {{-- Утилітарна стрічка --}}
        <div class="hidden bg-brand-950 text-brand-100 lg:block">
            <div class="mx-auto flex h-9 w-full max-w-[1600px] items-center justify-between px-4 text-xs sm:px-6 lg:px-8">
                <div class="flex items-center gap-5">
                    @if (! empty($s['contact_phone']))
                        <a href="tel:{{ preg_replace('/[^+\d]/', '', $s['contact_phone']) }}" class="inline-flex items-center gap-1.5 hover:text-white">
                            <x-ico name="phone" class="h-3.5 w-3.5" /> {{ $s['contact_phone'] }}
                        </a>
                    @endif
                    @if (! empty($s['contact_email']))
                        <a href="mailto:{{ $s['contact_email'] }}" class="inline-flex items-center gap-1.5 hover:text-white">
                            <x-ico name="envelope" class="h-3.5 w-3.5" /> {{ $s['contact_email'] }}
                        </a>
                    @endif
                </div>
                <div class="flex items-center gap-4">
                    @if (! empty($s['social_facebook']))
                        <a href="{{ $s['social_facebook'] }}" target="_blank" rel="noopener" class="hover:text-white">Facebook</a>
                    @endif
                    @if (! empty($s['social_instagram']))
                        <a href="{{ $s['social_instagram'] }}" target="_blank" rel="noopener" class="hover:text-white">Instagram</a>
                    @endif
                    <a href="{{ url('/admin') }}" class="inline-flex items-center gap-1.5 text-gold-300 hover:text-gold-200">
                        <x-ico name="lock-closed" class="h-3.5 w-3.5" /> Адмінпанель
                    </a>
                </div>
            </div>
        </div>

        {{-- Липка частина: бренд + навігація --}}
        <div class="sticky top-0 z-40 bg-white shadow-sm">
            {{-- Ряд бренду та дій --}}
            <div class="mx-auto flex h-20 w-full max-w-[1600px] items-center justify-between gap-6 px-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3">
                    @if ($logo)
                        <img src="{{ $logo }}" alt="ОТФК ОНТУ" class="h-12 w-auto shrink-0 lg:h-16">
                    @else
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-brand-700 to-brand-900 text-white shadow-sm">
                            <x-ico name="academic-cap" class="h-7 w-7" />
                        </span>
                    @endif
                    <span class="leading-tight">
                        <span class="block whitespace-nowrap text-lg font-extrabold tracking-tight text-brand-900" style="font-family:var(--font-display)">{{ $s['brand_short'] ?? 'ОТФК ОНТУ' }}</span>
                        <span class="hidden whitespace-nowrap text-xs text-slate-500 sm:block">{{ $s['brand_name'] ?? 'Одеський технічний фаховий коледж' }}</span>
                    </span>
                </a>

                <div class="flex items-center gap-2 sm:gap-3">
                    {{-- Пошук (десктоп) --}}
                    <form action="{{ route('search') }}" method="GET" class="relative hidden lg:block">
                        <x-ico name="magnifying-glass" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input type="search" name="q" placeholder="Пошук..."
                               class="w-48 rounded-full border-0 bg-slate-100 py-2 pl-9 pr-4 text-sm text-slate-700 ring-1 ring-transparent transition focus:w-64 focus:bg-white focus:ring-2 focus:ring-brand-500" />
                    </form>
                    {{-- CTA --}}
                    <a href="{{ url('/abituriyentu') }}" class="btn-accent hidden whitespace-nowrap sm:inline-flex">Вступнику</a>
                    {{-- Мобільні дії --}}
                    <a href="{{ route('search') }}" class="btn-ghost p-2 lg:hidden" aria-label="Пошук"><x-ico name="magnifying-glass" class="h-5 w-5" /></a>
                    <button @click="mobile = true" class="btn-ghost p-2 min-[1560px]:hidden" aria-label="Меню"><x-ico name="bars-3" class="h-6 w-6" /></button>
                </div>
            </div>

            {{-- Навігаційна стрічка (десктоп) --}}
            <nav class="hidden bg-brand-900 min-[1560px]:block">
                <div class="mx-auto flex w-full max-w-[1600px] flex-wrap items-stretch px-4 sm:px-6 lg:px-8">
                    @foreach ($menu as $item)
                        @if ($item->children->isNotEmpty())
                            <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative shrink-0">
                                <button type="button" @click="open = ! open"
                                        class="flex items-center gap-1 whitespace-nowrap border-b-2 border-transparent px-2.5 py-3 text-[13px] font-medium text-white/90 transition hover:bg-white/5 hover:text-white"
                                        :class="open ? 'border-gold-400 bg-white/5 text-white' : ''">
                                    {{ $item->label }}
                                    <x-ico name="chevron-down" class="h-4 w-4 opacity-70 transition" x-bind:class="open && 'rotate-180'" />
                                </button>
                                <div x-show="open" x-cloak x-transition.opacity
                                     class="absolute left-0 top-full z-50 max-h-[75vh] w-72 overflow-y-auto rounded-b-xl border border-slate-200 bg-white p-2 shadow-2xl">
                                    @foreach ($item->children as $child)
                                        <a href="{{ $child->href }}" @if ($child->open_new_tab) target="_blank" @endif
                                           class="block rounded-lg px-3 py-2 text-sm text-slate-600 transition hover:bg-brand-50 hover:text-brand-800">{{ $child->label }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $item->href }}" @if ($item->open_new_tab) target="_blank" @endif
                               class="flex shrink-0 items-center whitespace-nowrap border-b-2 border-transparent px-2.5 py-3 text-[13px] font-medium text-white/90 transition hover:border-gold-400 hover:bg-white/5 hover:text-white">{{ $item->label }}</a>
                        @endif
                    @endforeach
                </div>
            </nav>
        </div>

        {{-- Мобільне меню (off-canvas) --}}
        <div x-show="mobile" x-cloak class="fixed inset-0 z-50 min-[1560px]:hidden">
            <div @click="mobile = false" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
            <div class="absolute right-0 top-0 flex h-full w-80 max-w-[88%] flex-col bg-white shadow-2xl"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0">
                <div class="flex h-16 items-center justify-between border-b border-slate-200 px-5">
                    <span class="font-extrabold text-brand-900" style="font-family:var(--font-display)">Меню</span>
                    <button @click="mobile = false" class="btn-ghost p-2"><x-ico name="x-mark" class="h-6 w-6" /></button>
                </div>
                <div class="border-b border-slate-100 p-4">
                    <form action="{{ route('search') }}" method="GET" class="relative">
                        <x-ico name="magnifying-glass" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input type="search" name="q" placeholder="Пошук по сайту..." class="input w-full pl-9" />
                    </form>
                    <a href="{{ url('/abituriyentu') }}" class="btn-accent mt-3 w-full">Вступнику</a>
                </div>
                <nav class="flex-1 overflow-y-auto p-3">
                    @foreach ($menu as $item)
                        @if ($item->children->isNotEmpty())
                            <div x-data="{ sub: false }" class="border-b border-slate-100">
                                <button @click="sub = !sub" class="flex w-full items-center justify-between px-3 py-3 text-sm font-semibold text-slate-800">
                                    {{ $item->label }}
                                    <x-ico name="chevron-down" class="h-4 w-4 transition" ::class="sub && 'rotate-180'" />
                                </button>
                                <div x-show="sub" x-cloak class="pb-2">
                                    @foreach ($item->children as $child)
                                        <a href="{{ $child->href }}" @if ($child->open_new_tab) target="_blank" @endif
                                           class="block rounded-lg px-5 py-2 text-sm text-slate-600 hover:bg-brand-50 hover:text-brand-800">{{ $child->label }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $item->href }}" @if ($item->open_new_tab) target="_blank" @endif
                               class="block border-b border-slate-100 px-3 py-3 text-sm font-semibold text-slate-800 hover:text-brand-800">{{ $item->label }}</a>
                        @endif
                    @endforeach
                </nav>
            </div>
        </div>
    </header>

    {{-- Вміст --}}
    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- Підвал --}}
    <footer class="mt-20 bg-brand-950 text-brand-100">
        <div class="container-site grid gap-10 py-14 md:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-1">
                <div class="flex items-center gap-3">
                    @if ($logo)
                        <span class="grid h-11 place-items-center rounded-xl bg-white px-2 ring-1 ring-white/15">
                            <img src="{{ $logo }}" alt="ОТФК ОНТУ" class="h-8 w-auto">
                        </span>
                    @else
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-white/10 text-white ring-1 ring-white/15">
                            <x-ico name="academic-cap" class="h-6 w-6" />
                        </span>
                    @endif
                    <span class="font-extrabold text-white" style="font-family:var(--font-display)">{{ $s['brand_short'] ?? 'ОТФК ОНТУ' }}</span>
                </div>
                <p class="mt-4 text-sm leading-relaxed text-brand-200">
                    {{ $s['footer_about'] ?? 'Одеський технічний фаховий коледж Одеського національного технологічного університету.' }}
                </p>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wide text-white">Розділи</h3>
                <ul class="mt-4 space-y-2 text-sm text-brand-200">
                    <li><a href="{{ route('home') }}" class="hover:text-white">Головна</a></li>
                    <li><a href="{{ route('news.index') }}" class="hover:text-white">Новини</a></li>
                    <li><a href="{{ route('specialties.index') }}" class="hover:text-white">Спеціальності</a></li>
                    <li><a href="{{ route('galleries.index') }}" class="hover:text-white">Галерея</a></li>
                    <li><a href="{{ route('contacts') }}" class="hover:text-white">Контакти</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wide text-white">Контакти</h3>
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
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wide text-white">Партнери</h3>
                <ul class="mt-4 space-y-2 text-sm text-brand-200">
                    @forelse ($partners as $partner)
                        <li><a href="{{ $partner->url }}" @if ($partner->open_new_tab) target="_blank" rel="noopener" @endif class="hover:text-white">{{ $partner->title }}</a></li>
                    @empty
                        <li><a href="https://onaft.edu.ua" target="_blank" rel="noopener" class="hover:text-white">ОНТУ</a></li>
                        <li><a href="https://mon.gov.ua" target="_blank" rel="noopener" class="hover:text-white">МОН України</a></li>
                    @endforelse
                </ul>
                <div class="mt-5 flex gap-3">
                    @if (! empty($s['social_facebook']))
                        <a href="{{ $s['social_facebook'] }}" target="_blank" rel="noopener" class="grid h-9 w-9 place-items-center rounded-lg bg-white/10 hover:bg-white/20">f</a>
                    @endif
                    @if (! empty($s['social_instagram']))
                        <a href="{{ $s['social_instagram'] }}" target="_blank" rel="noopener" class="grid h-9 w-9 place-items-center rounded-lg bg-white/10 hover:bg-white/20">ig</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="border-t border-white/10 py-5">
            <div class="container-site flex flex-col items-center justify-center gap-2.5 text-center text-xs text-brand-300 sm:flex-row">
                <span>© 2014-{{ $year }} ВСП «ОТФК ОНТУ». Усі права захищено.</span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-gold-400/15 px-2.5 py-1 font-medium text-gold-200 ring-1 ring-gold-400/30"
                      title="Сайт працює в режимі тестування (альфа-версія)">
                    <span class="h-1.5 w-1.5 rounded-full bg-gold-400"></span> Альфа-версія
                </span>
            </div>
        </div>
    </footer>
</body>
</html>
