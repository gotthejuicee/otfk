{{-- Варіант шаблону: звичайна контентна сторінка з довгим імпортованим текстом --}}

@php
    $headings = $page->headings();
    $showToc = count($headings) >= 3;

    // Попередня/наступна сторінка того самого розділу — у порядку, заданому в адмінці
    $position = $siblings->search(fn ($item) => $item->is($page));
    $previous = $position !== false && $position > 0 ? $siblings[$position - 1] : null;
    $next = $position !== false && $position < $siblings->count() - 1 ? $siblings[$position + 1] : null;
@endphp

<section class="container-site grid gap-10 py-12 lg:grid-cols-3 lg:items-start">
    <div class="lg:col-span-2">
        @if ($page->cover_image)
            <x-picture :path="$page->cover_image" :alt="$page->title" loading="lazy" decoding="async"
                       class="mb-8 w-full rounded-2xl object-cover" />
        @endif

        <x-lead-excerpt :excerpt="$page->excerpt" :body="$page->body" />

        {{-- На мобільному сайдбар опиняється в самому низу довгої сторінки, тож якорі дублюються тут --}}
        @if ($showToc)
            <details class="card mb-6 p-5 lg:hidden">
                <summary class="flex cursor-pointer items-center justify-between gap-3 font-bold text-brand-950">
                    Навігація по сторінці
                    <x-ico name="chevron-down" class="h-5 w-5 shrink-0 text-slate-400" aria-hidden="true" />
                </summary>
                <div class="mt-4 border-t border-slate-200/70 pt-4">
                    @include('pages.partials.toc-list')
                </div>
            </details>
        @endif

        @if (filled($page->body))
            <x-prose.article :drop-cap="$page->slug === 'istoriya'">
                {!! $page->bodyWithAnchors() !!}
            </x-prose.article>
        @else
            <x-empty-state icon="document-text" title="Матеріали цієї сторінки незабаром буде додано." />
        @endif

        {{-- Перехід до сусідніх сторінок розділу — щоб не повертатися до меню --}}
        @if ($previous || $next)
            <nav class="mt-10 grid gap-4 sm:grid-cols-2" aria-label="Сусідні сторінки розділу">
                @if ($previous)
                    <a href="{{ url('/' . $previous->slug) }}" class="card card-interactive group flex items-center gap-3 p-5">
                        <x-ico name="arrow-left" class="h-5 w-5 shrink-0 text-slate-300 transition group-hover:-translate-x-1 group-hover:text-brand-600" aria-hidden="true" />
                        <span class="min-w-0">
                            <span class="block text-xs uppercase tracking-wide text-slate-400">Попередня сторінка</span>
                            <span class="mt-0.5 block font-semibold leading-snug text-slate-800 group-hover:text-brand-700">{{ $previous->title }}</span>
                        </span>
                    </a>
                @endif
                @if ($next)
                    <a href="{{ url('/' . $next->slug) }}" @class(['card card-interactive group flex items-center justify-end gap-3 p-5 text-right', 'sm:col-start-2' => ! $previous])>
                        <span class="min-w-0">
                            <span class="block text-xs uppercase tracking-wide text-slate-400">Наступна сторінка</span>
                            <span class="mt-0.5 block font-semibold leading-snug text-slate-800 group-hover:text-brand-700">{{ $next->title }}</span>
                        </span>
                        <x-ico name="arrow-right" class="h-5 w-5 shrink-0 text-slate-300 transition group-hover:translate-x-1 group-hover:text-brand-600" aria-hidden="true" />
                    </a>
                @endif
            </nav>
        @endif
    </div>

    {{-- Липкий сайдбар — задіює порожню праву частину великого екрана --}}
    <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">
        @if ($showToc)
            <nav class="card hidden p-6 lg:block" aria-label="Навігація по сторінці">
                <h2 class="text-lg font-bold text-brand-950">Навігація по сторінці</h2>
                <div class="accent-rule"></div>
                <div class="mt-5 max-h-96 overflow-y-auto pr-1">
                    @include('pages.partials.toc-list')
                </div>
            </nav>
        @endif

        @if ($neighbours->isNotEmpty())
            <div class="card p-6">
                <h2 class="text-lg font-bold text-brand-950">Сусідні сторінки розділу</h2>
                <div class="accent-rule"></div>
                <ul class="mt-5 space-y-3 text-sm">
                    @foreach ($neighbours->take(8) as $neighbour)
                        <li>
                            <a href="{{ url('/' . $neighbour->slug) }}" class="group flex items-start gap-2.5 font-semibold text-slate-700 transition hover:text-brand-700">
                                <x-ico name="document-text" class="mt-0.5 h-4 w-4 shrink-0 text-brand-400" aria-hidden="true" />
                                <span>{{ $neighbour->title }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                @if ($page->parent)
                    <a href="{{ url('/' . $page->parent->slug) }}" class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 transition hover:gap-2.5">
                        Увесь розділ «{{ $page->parent->title }}» <x-ico name="arrow-right" class="h-4 w-4" aria-hidden="true" />
                    </a>
                @endif
            </div>
        @endif

        @if ($hasContacts)
            <div class="card bg-brand-50/60 p-6 ring-brand-100">
                <h2 class="text-lg font-bold text-brand-950">Потрібна допомога?</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Звертайтеся до коледжу — підкажемо, де знайти потрібний матеріал.</p>
                <ul class="mt-5 space-y-3 border-t border-brand-100 pt-5 text-sm text-slate-600">
                    @if (! empty($s['contact_phone']))
                        <li class="flex gap-3">
                            <x-ico name="phone" class="mt-0.5 h-4 w-4 shrink-0 text-gold-600" aria-hidden="true" />
                            <a href="tel:{{ preg_replace('/[^+\d]/', '', $s['contact_phone']) }}" class="font-semibold text-brand-800 hover:text-brand-600">{{ $s['contact_phone'] }}</a>
                        </li>
                    @endif
                    @if (! empty($s['contact_email']))
                        <li class="flex gap-3">
                            <x-ico name="envelope" class="mt-0.5 h-4 w-4 shrink-0 text-gold-600" aria-hidden="true" />
                            <a href="mailto:{{ $s['contact_email'] }}" class="break-all font-semibold text-brand-800 hover:text-brand-600">{{ $s['contact_email'] }}</a>
                        </li>
                    @endif
                    @if (! empty($s['work_hours']))
                        <li class="flex gap-3">
                            <x-ico name="clock" class="mt-0.5 h-4 w-4 shrink-0 text-gold-600" aria-hidden="true" />
                            <span>{{ $s['work_hours'] }}</span>
                        </li>
                    @endif
                </ul>
                <a href="{{ route('contacts') }}" class="btn-outline mt-6 w-full border-gold-300 text-gold-700 ring-gold-300 hover:bg-gold-50">
                    Контакти <x-ico name="arrow-right" class="h-4 w-4" />
                </a>
            </div>
        @endif

        {{-- Довгі імпортовані сторінки — до 20 000px заввишки, тож повернення нагору обовʼязкове --}}
        <a href="#top" class="btn-ghost w-full ring-1 ring-slate-200">
            <x-ico name="arrow-up" class="h-4 w-4" aria-hidden="true" /> Нагору
        </a>
    </aside>
</section>
