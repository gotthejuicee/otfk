<x-layouts.app :title="$category->title">

    <x-page-hero :title="$category->title" :breadcrumbs="[
        ['label' => 'Головна', 'url' => route('home')],
        ['label' => 'Публічна інформація', 'url' => route('documents.index')],
        ['label' => $category->title],
    ]" />

    <section class="container-site grid gap-8 py-12 lg:grid-cols-12">
        <aside class="lg:col-span-3">
            <div class="card p-4 lg:sticky lg:top-28 lg:max-h-[calc(100vh-8rem)] lg:overflow-y-auto">
                <p class="px-2 pb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Категорії</p>
                <nav class="space-y-1">
                    @foreach ($categories as $c)
                        <a href="{{ route('documents.category', $c) }}"
                           @class(['block rounded-lg px-3 py-2 text-sm transition', 'bg-brand-50 font-semibold text-brand-800' => $c->id === $category->id, 'text-slate-600 hover:bg-slate-100' => $c->id !== $category->id])>
                            {{ $c->title }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </aside>

        <div class="lg:col-span-9">
            @if ($documents->isNotEmpty())
                <ul class="space-y-3">
                    @foreach ($documents as $doc)
                        <li class="card flex items-center gap-4 p-4 transition hover:shadow-md">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-rose-50 text-rose-600">
                                <x-ico name="document-text" class="h-6 w-6" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-slate-800">{{ $doc->title }}</p>
                                <div class="mt-0.5 flex flex-wrap items-center gap-3 text-xs text-slate-400">
                                    @if ($doc->published_at)<span>{{ $doc->published_at->translatedFormat('j F Y') }}</span>@endif
                                    @if ($doc->description)<span class="text-slate-500">{{ $doc->description }}</span>@endif
                                </div>
                            </div>
                            @if ($doc->file_url)
                                <a href="{{ $doc->file_url }}" target="_blank" rel="noopener" class="btn-outline shrink-0 px-3 py-2 text-xs">
                                    <x-ico name="arrow-down-tray" class="h-4 w-4" /> Завантажити
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="card p-12 text-center text-slate-500">
                    <x-ico name="folder-open" class="mx-auto h-10 w-10 text-slate-300" />
                    <p class="mt-3">Документи цієї категорії незабаром буде додано.</p>
                </div>
            @endif
        </div>
    </section>

</x-layouts.app>
