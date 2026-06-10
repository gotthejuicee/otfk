<x-layouts.app title="Новини">

    <section class="bg-brand-950">
        <div class="container-site py-12 lg:py-14">
            <nav class="flex items-center gap-2 text-sm text-brand-300">
                <a href="{{ route('home') }}" class="hover:text-white">Головна</a>
                <x-ico name="chevron-right" class="h-4 w-4" />
                <span class="text-white">Новини</span>
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold text-white sm:text-4xl">Новини коледжу</h1>
            <div class="accent-rule"></div>
        </div>
    </section>

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
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($news as $item)
                    <x-news-card :item="$item" />
                @endforeach
            </div>
            <div class="mt-10">{{ $news->links() }}</div>
        @else
            <div class="card p-12 text-center text-slate-500">Новин поки немає.</div>
        @endif
    </section>

</x-layouts.app>
