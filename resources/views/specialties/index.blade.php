<x-layouts.app title="Спеціальності">

    <x-page-hero title="Наші спеціальності" :breadcrumbs="[
        ['label' => 'Головна', 'url' => route('home')],
        ['label' => 'Спеціальності'],
    ]">
        <p class="mt-3 max-w-2xl text-brand-100">Оберіть напрям підготовки — і дізнайтеся більше про навчання в коледжі.</p>
    </x-page-hero>

    <section class="container-site py-12">
        @if ($specialties->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($specialties as $sp)
                    <a href="{{ route('specialties.show', $sp) }}" class="card card-interactive group flex flex-col overflow-hidden">
                        <div class="relative aspect-[16/9] overflow-hidden">
                            @if ($sp->cover_image)
                                <x-picture :path="$sp->cover_image" :alt="$sp->title" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-700 to-brand-900 text-white/25">
                                    <x-ico name="academic-cap" class="h-14 w-14" />
                                </div>
                            @endif
                            @if ($sp->code)
                                <span class="absolute left-3 top-3 badge bg-white/90 text-brand-800">Код {{ $sp->code }}</span>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col p-5">
                            <h3 class="text-lg font-bold leading-snug text-slate-900 group-hover:text-brand-700">{{ $sp->title }}</h3>
                            @if ($sp->degree)
                                <p class="mt-1 text-sm font-medium text-gold-700">{{ $sp->degree }}</p>
                            @endif
                            <p class="mt-2 line-clamp-3 text-sm text-slate-500">{{ $sp->short_description }}</p>
                            <span class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 transition group-hover:gap-2.5">
                                Детальніше <x-ico name="arrow-right" class="h-4 w-4" />
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <x-empty-state icon="academic-cap" title="Перелік спеціальностей незабаром буде додано." />
        @endif
    </section>

</x-layouts.app>
