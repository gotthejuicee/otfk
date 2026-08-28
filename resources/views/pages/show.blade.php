@php
    $children = $page->children()->published()->ordered()->get();

    // Три варіанти одного шаблону: хаб із дочірніми сторінками, урочиста heritage-сторінка
    // та звичайна контентна сторінка з довгим імпортованим текстом.
    $isHub = $children->isNotEmpty();
    $isHeritage = (bool) $page->is_heritage;

    $featured = $children->where('is_featured', true)->values();
    $rest = $children->where('is_featured', false)->values();

    // Сусідні сторінки того самого розділу — і для сайдбару, і для переходів «попередня/наступна»
    $siblings = $page->parent_id
        ? $page->parent->children()->published()->ordered()->get()
        : collect();
    $neighbours = $siblings->where('id', '!=', $page->id)->values();

    $s = \App\Models\Setting::map();
    $hasContacts = ! empty($s['contact_phone']) || ! empty($s['contact_email'])
        || ! empty($s['contact_address']) || ! empty($s['work_hours']);

    // Українське відмінювання лічильника сторінок
    $pageWord = function (int $n): string {
        $mod100 = $n % 100;
        $mod10 = $n % 10;

        if ($mod100 >= 11 && $mod100 <= 14) {
            return 'сторінок';
        }

        return match (true) {
            $mod10 === 1 => 'сторінка',
            $mod10 >= 2 && $mod10 <= 4 => 'сторінки',
            default => 'сторінок',
        };
    };

    $pageBreadcrumbs = [['label' => 'Головна', 'url' => route('home')]];
    if ($page->parent) {
        $pageBreadcrumbs[] = ['label' => $page->parent->title, 'url' => url('/' . $page->parent->slug)];
    }
    $pageBreadcrumbs[] = ['label' => $page->title];
@endphp

<x-layouts.app :title="$page->meta_title ?: $page->title" :description="$page->meta_description ?: $page->excerpt">

    @unless ($page->is_published)
        <x-draft-notice />
    @endunless

    @if ($isHeritage)
        {{-- Урочиста сторінка — темна шапка з серифним заголовком лишається частиною стилю «листа» --}}
        <x-page-hero :title="$page->title" :breadcrumbs="$pageBreadcrumbs" heritage class="max-w-4xl leading-tight" />
    @else
        {{-- Світла шапка-картка — у стилі решти внутрішніх сторінок --}}
        @php
            $headerIcon = $isHub ? 'folder-open' : 'document-text';
            $readingMinutes = (int) max(1, round(mb_strlen(strip_tags((string) $page->body)) / 1100));
            $fileLinks = preg_match_all('/href="[^"]+\.(?:pdf|docx?|xlsx?|pptx?|zip|rar)(?:\?[^"]*)?"/i', (string) $page->body);
        @endphp
        <section class="border-b border-slate-200/70 bg-slate-50/80">
            <div class="container-site py-8 lg:py-10">
                <x-breadcrumbs tone="light" :items="$pageBreadcrumbs" />

                <div class="relative mt-4 overflow-hidden rounded-2xl bg-white px-6 py-8 shadow-sm ring-1 ring-slate-200/80 sm:px-10 sm:py-10">
                    {{-- Декоративний контур — заповнює порожнечу праворуч на великих екранах --}}
                    <x-ico :name="$headerIcon" aria-hidden="true"
                           class="pointer-events-none absolute -right-8 top-1/2 hidden h-64 w-64 -translate-y-1/2 text-brand-50 lg:block" />

                    <div class="relative max-w-3xl">
                        @if ($page->parent)
                            <a href="{{ url('/' . $page->parent->slug) }}"
                               class="badge bg-brand-50 text-brand-800 ring-1 ring-brand-100 transition hover:bg-brand-100">
                                <x-ico name="folder" class="h-4 w-4" aria-hidden="true" />
                                {{ $page->parent->title }}
                            </a>
                        @endif
                        <h1 @class(['text-3xl font-extrabold leading-tight text-brand-950 sm:text-4xl', 'mt-3' => $page->parent])>{{ $page->title }}</h1>
                        <div class="accent-rule"></div>

                        @if (filled($page->excerpt))
                            <p class="mt-5 max-w-2xl text-lg leading-relaxed text-slate-500">{{ $page->excerpt }}</p>
                        @endif

                        <div class="mt-5 flex flex-wrap items-center gap-2 text-sm">
                            @if ($isHub)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-gold-50 px-3 py-1 font-semibold text-gold-700 ring-1 ring-gold-300/70">
                                    <x-ico name="document-duplicate" class="h-4 w-4" aria-hidden="true" />
                                    {{ $children->count() }} {{ $pageWord($children->count()) }}
                                </span>
                                @if ($featured->isNotEmpty())
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 font-semibold text-brand-800 ring-1 ring-brand-100">
                                        <x-ico name="star" class="h-4 w-4" aria-hidden="true" />
                                        {{ $featured->count() }} {{ $featured->count() === 1 ? 'ключова дія' : 'ключові дії' }}
                                    </span>
                                @endif
                                {{-- Чип про пошук — лише коли поле пошуку справді показується (див. partials/hub) --}}
                                @if ($rest->count() > 5)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 font-semibold text-slate-600">
                                        <x-ico name="magnifying-glass" class="h-4 w-4" aria-hidden="true" />
                                        Швидкий пошук у розділі
                                    </span>
                                @endif
                            @else
                                @if (filled($page->body))
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gold-50 px-3 py-1 font-semibold text-gold-700 ring-1 ring-gold-300/70">
                                        <x-ico name="clock" class="h-4 w-4" aria-hidden="true" />
                                        ~{{ $readingMinutes }} хв читання
                                    </span>
                                @endif
                                @if ($fileLinks)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 font-semibold text-brand-800 ring-1 ring-brand-100">
                                        <x-ico name="paper-clip" class="h-4 w-4" aria-hidden="true" />
                                        {{ $fileLinks }} {{ $fileLinks === 1 ? 'файл' : ($fileLinks >= 2 && $fileLinks <= 4 ? 'файли' : 'файлів') }}
                                    </span>
                                @endif
                                @if ($page->parent)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 font-semibold text-slate-600">
                                        <x-ico name="folder" class="h-4 w-4" aria-hidden="true" />
                                        {{ $neighbours->count() + 1 }} {{ $pageWord($neighbours->count() + 1) }} у розділі
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if ($isHub)
        @include('pages.partials.hub')
    @elseif ($isHeritage)
        <section class="container-site py-12">
            @if ($page->cover_image)
                <x-picture :path="$page->cover_image" :alt="$page->title" loading="lazy" decoding="async"
                           class="mx-auto mb-8 max-w-3xl rounded-2xl object-cover" />
            @endif

            <div class="mx-auto max-w-3xl">
                <x-lead-excerpt :excerpt="$page->excerpt" :body="$page->body" heritage />
            </div>

            @if (filled($page->body))
                <x-prose.article heritage :drop-cap="$page->slug === 'istoriya'">
                    {!! $page->body !!}
                </x-prose.article>
            @else
                <x-empty-state icon="document-text" title="Матеріали цього розділу незабаром буде додано." />
            @endif
        </section>

        @include('pages.partials.neighbours')
    @else
        @include('pages.partials.content')
    @endif

    {{-- Спільна фінальна смуга — однакова для всіх трьох варіантів шаблону --}}
    <section class="border-t border-slate-200/70 bg-slate-50/60">
        <div class="container-site py-12">
            <div class="relative overflow-hidden rounded-2xl bg-brand-950 px-6 py-8 sm:px-10">
                <x-ico name="academic-cap" aria-hidden="true"
                       class="pointer-events-none absolute -right-6 -top-6 hidden h-44 w-44 text-white/5 sm:block" />
                <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-2xl">
                        <h2 class="text-2xl font-extrabold text-white">Не знайшли потрібну інформацію?</h2>
                        <p class="mt-2 text-brand-200">
                            Приймальна комісія коледжу відповість на запитання про вступ, навчання та документи.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('contacts') }}" class="btn-accent">
                            Контакти <x-ico name="arrow-right" class="h-4 w-4" />
                        </a>
                        <a href="{{ route('faq') }}" class="btn-outline border-white/30 bg-white/5 text-white ring-white/30 hover:bg-white/10">
                            Часті запитання <x-ico name="arrow-right" class="h-4 w-4" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-layouts.app>
