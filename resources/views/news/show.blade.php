<x-layouts.app :title="$news->title" :description="$news->excerpt">

    <section class="bg-brand-950">
        <div class="container-site py-12 lg:py-14">
            <nav class="flex flex-wrap items-center gap-2 text-sm text-brand-300">
                <a href="{{ route('home') }}" class="hover:text-white">Головна</a>
                <x-ico name="chevron-right" class="h-4 w-4" />
                <a href="{{ route('news.index') }}" class="hover:text-white">Новини</a>
            </nav>
            <h1 class="mt-3 max-w-4xl text-3xl font-extrabold leading-tight text-white sm:text-4xl">{{ $news->title }}</h1>
            <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-brand-200">
                @if ($news->category)
                    <span class="badge bg-white/10 text-brand-100">{{ $news->category->title }}</span>
                @endif
                @if ($news->published_at)
                    <span class="inline-flex items-center gap-1.5"><x-ico name="calendar-days" class="h-4 w-4" /> {{ $news->published_at->translatedFormat('j F Y') }}</span>
                @endif
                <span class="inline-flex items-center gap-1.5"><x-ico name="eye" class="h-4 w-4" /> {{ $news->views }}</span>
            </div>
        </div>
    </section>

    <section class="container-site grid gap-10 py-12 lg:grid-cols-12">
        <article class="lg:col-span-8">
            @if ($news->cover_image)
                <img src="{{ asset('storage/' . $news->cover_image) }}" alt="{{ $news->title }}" class="mb-8 w-full rounded-2xl object-cover">
            @endif
            @if ($news->excerpt)
                <p class="mb-6 text-lg font-medium leading-relaxed text-slate-600">{{ $news->excerpt }}</p>
            @endif
            <div class="prose prose-slate max-w-none prose-headings:font-display prose-a:text-brand-700">
                {!! $news->body !!}
            </div>

            <a href="{{ route('news.index') }}" class="btn-outline mt-10">
                <x-ico name="arrow-left" class="h-4 w-4" /> До всіх новин
            </a>
        </article>

        <aside class="lg:col-span-4">
            @if ($related->isNotEmpty())
                <div class="card p-6 lg:sticky lg:top-28">
                    <h2 class="text-lg font-bold text-slate-900">Інші новини</h2>
                    <div class="accent-rule"></div>
                    <ul class="mt-5 space-y-4">
                        @foreach ($related as $r)
                            <li>
                                <a href="{{ route('news.show', $r) }}" class="group block">
                                    <p class="line-clamp-2 text-sm font-semibold text-slate-800 transition group-hover:text-brand-700">{{ $r->title }}</p>
                                    @if ($r->published_at)
                                        <p class="mt-1 text-xs text-slate-400">{{ $r->published_at->translatedFormat('j F Y') }}</p>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </aside>
    </section>

</x-layouts.app>
