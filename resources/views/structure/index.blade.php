<x-layouts.app title="Структура">

    <x-page-hero title="Структура коледжу" :breadcrumbs="[
        ['label' => 'Головна', 'url' => route('home')],
        ['label' => 'Структура'],
    ]" />

    <section class="container-site space-y-14 py-12">
        @forelse ($groups as $type => $group)
            <div id="{{ $type }}" class="scroll-mt-28">
                <h2 class="text-2xl font-extrabold text-slate-900">{{ $group['label'] }}</h2>
                <div class="accent-rule"></div>
                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($group['items'] as $dep)
                        <a href="{{ route('structure.show', $dep) }}" class="card group flex items-start gap-4 p-5 transition hover:-translate-y-1 hover:shadow-lg">
                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-700 transition group-hover:bg-brand-700 group-hover:text-white">
                                <x-ico name="building-office-2" class="h-6 w-6" />
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block font-bold leading-snug text-slate-900 group-hover:text-brand-700">{{ $dep->title }}</span>
                                @if ($dep->staff_count)
                                    <span class="mt-1 block text-sm text-slate-500">{{ $dep->staff_count }} співробітників</span>
                                @endif
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="card p-12 text-center text-slate-500">Інформацію про структурні підрозділи незабаром буде додано.</div>
        @endforelse
    </section>

</x-layouts.app>
