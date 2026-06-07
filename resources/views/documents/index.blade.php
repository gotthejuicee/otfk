<x-layouts.app title="Публічна інформація">

    <section class="bg-brand-950">
        <div class="container-site py-12 lg:py-14">
            <nav class="flex items-center gap-2 text-sm text-brand-300">
                <a href="{{ route('home') }}" class="hover:text-white">Головна</a>
                <x-ico name="chevron-right" class="h-4 w-4" />
                <span class="text-white">Публічна інформація</span>
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold text-white sm:text-4xl">Публічна інформація</h1>
            <div class="accent-rule"></div>
        </div>
    </section>

    <section class="container-site py-12">
        @if ($categories->isNotEmpty())
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $cat)
                    <a href="{{ route('documents.category', $cat) }}" class="card group flex items-start gap-4 p-6 transition hover:-translate-y-1 hover:shadow-lg">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-700 transition group-hover:bg-brand-700 group-hover:text-white">
                            <x-ico name="folder" class="h-6 w-6" />
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block font-bold text-slate-900 group-hover:text-brand-700">{{ $cat->title }}</span>
                            <span class="mt-1 block text-sm text-slate-500">{{ $cat->documents_count }} документів</span>
                        </span>
                        <x-ico name="arrow-right" class="h-5 w-5 shrink-0 text-slate-300 transition group-hover:translate-x-1 group-hover:text-brand-600" />
                    </a>
                @endforeach
            </div>
        @else
            <div class="card p-12 text-center text-slate-500">Категорії документів незабаром буде додано.</div>
        @endif
    </section>

</x-layouts.app>
