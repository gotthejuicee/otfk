<x-layouts.app title="Новини">

    <x-page-hero title="Новини коледжу" :breadcrumbs="[
        ['label' => 'Головна', 'url' => route('home')],
        ['label' => 'Новини'],
    ]">
        <p class="mt-3 max-w-2xl text-brand-100">Актуальні події, оголошення та досягнення коледжу</p>
    </x-page-hero>

    <section class="container-site py-12">
        {{-- Фільтр за категоріями та роком --}}
        @if ($categories->isNotEmpty())
            <div class="mb-8 flex flex-wrap items-center gap-2">
                <a href="{{ route('news.index') }}"
                   @class(['rounded-full px-4 py-1.5 text-sm font-medium transition', 'bg-brand-700 text-white' => ! $activeCategory, 'bg-slate-100 text-slate-600 hover:bg-slate-200' => $activeCategory])>
                    Усі
                </a>
                @foreach ($categories as $cat)
                    <a href="{{ route('news.index', ['category' => $cat->slug]) }}"
                       @class(['rounded-full px-4 py-1.5 text-sm font-medium transition', 'bg-brand-700 text-white' => $activeCategory?->id === $cat->id, 'bg-slate-100 text-slate-600 hover:bg-slate-200' => $activeCategory?->id !== $cat->id])>
                        {{ $cat->title }}
                    </a>
                @endforeach

                {{-- Рік (архів) --}}
                @if ($years->count() > 1)
                    <form method="GET" action="{{ route('news.index') }}" class="ml-auto">
                        @if ($activeCategory)
                            <input type="hidden" name="category" value="{{ $activeCategory->slug }}">
                        @endif
                        <select name="year" onchange="this.form.submit()" aria-label="Рік"
                                class="rounded-full border-0 bg-slate-100 py-1.5 pl-4 pr-9 text-sm font-medium text-slate-600 ring-0 transition hover:bg-slate-200 focus:ring-2 focus:ring-brand-500">
                            <option value="">Усі роки</option>
                            @foreach ($years as $y)
                                <option value="{{ $y }}" @selected($activeYear === $y)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>
        @endif

        @if ($news->isNotEmpty())
            @php
                // Головна новина — лише на першій сторінці без фільтрів, щоб не «губити» картки в сітці
                $items = $news->getCollection();
                $showFeatured = $news->onFirstPage() && ! $activeCategory && ! $activeYear && $items->count() > 1;
                $featured = $showFeatured ? $items->first() : null;
                $rest = $showFeatured ? $items->slice(1) : $items;
            @endphp

            @if ($featured)
                <div class="mb-6">
                    <x-news-card :item="$featured" featured />
                </div>
            @endif

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($rest as $item)
                    <x-news-card :item="$item" />
                @endforeach
            </div>
            <div class="mt-10">{{ $news->onEachSide(1)->links() }}</div>
        @else
            <x-empty-state icon="newspaper" title="Новин поки немає." />
        @endif
    </section>

</x-layouts.app>
