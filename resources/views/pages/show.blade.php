@php $children = $page->children()->published()->ordered()->get(); @endphp

<x-layouts.app :title="$page->meta_title ?: $page->title" :description="$page->meta_description ?: $page->excerpt">

    @php
        $pageBreadcrumbs = [['label' => 'Головна', 'url' => route('home')]];
        if ($page->parent) {
            $pageBreadcrumbs[] = ['label' => $page->parent->title, 'url' => url('/' . $page->parent->slug)];
        }
        $pageBreadcrumbs[] = ['label' => $page->title];
    @endphp
    <x-page-hero :title="$page->title" :breadcrumbs="$pageBreadcrumbs" class="max-w-4xl leading-tight" />

    <section class="container-site py-12">
        @if ($page->cover_image)
            <x-picture :path="$page->cover_image" :alt="$page->title" loading="lazy" decoding="async" class="mb-8 w-full rounded-2xl object-cover" />
        @endif

        @if ($page->excerpt)
            <p class="mb-6 max-w-3xl text-lg font-medium leading-relaxed text-slate-600">{{ $page->excerpt }}</p>
        @endif

        @if (filled($page->body))
            <div class="prose-site">
                {!! $page->body !!}
            </div>
        @endif

        {{-- Підрозділи (якщо це сторінка-розділ); відступ зверху — лише коли вище є контент --}}
        @if ($children->isNotEmpty())
            <div @class(['grid gap-4 sm:grid-cols-2 lg:grid-cols-3', 'mt-10' => $page->cover_image || filled($page->excerpt) || filled($page->body)])>
                @foreach ($children as $child)
                    <a href="{{ url('/' . $child->slug) }}" class="card card-interactive group flex items-center justify-between gap-3 p-5">
                        <span class="font-semibold text-slate-800 group-hover:text-brand-700">{{ $child->title }}</span>
                        <x-ico name="arrow-right" class="h-5 w-5 shrink-0 text-slate-300 transition group-hover:translate-x-1 group-hover:text-brand-600" />
                    </a>
                @endforeach
            </div>
        @endif

        @if (blank($page->body) && $children->isEmpty() && blank($page->excerpt))
            <x-empty-state icon="document-text" title="Матеріали цього розділу незабаром буде додано." />
        @endif
    </section>

</x-layouts.app>
