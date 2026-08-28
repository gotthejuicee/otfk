@php
    $facts = $staff->bioFacts();
    $links = $staff->bioLinks();
@endphp

<x-layouts.app :title="$staff->full_name" :description="$staff->position"
               :og-image="$staff->photo ? asset('storage/' . $staff->photo) : null">

    <section class="bg-brand-950">
        <div class="container-site py-12 lg:py-14">
            <x-breadcrumbs :items="array_values(array_filter([
                ['label' => 'Головна', 'url' => route('home')],
                $staff->department
                    ? ['label' => 'Структура', 'url' => route('structure.index')]
                    : ['label' => 'Адміністрація', 'url' => route('staff.administration')],
                $staff->department
                    ? ['label' => $staff->department->title, 'url' => route('structure.show', $staff->department)]
                    : null,
                ['label' => $staff->full_name],
            ]))" />
            <h1 class="mt-3 max-w-4xl text-3xl font-extrabold leading-tight text-white sm:text-4xl">{{ $staff->full_name }}</h1>
            @if ($staff->position)
                <p class="mt-3 max-w-3xl text-brand-100">{{ $staff->position }}</p>
            @endif
            <div class="accent-rule"></div>
        </div>
    </section>

    <section class="container-site grid gap-10 py-12 lg:grid-cols-12">
        <aside class="lg:col-span-4">
            <div class="card p-6 text-center">
                @if ($staff->photo)
                    <x-picture :path="$staff->photo" :alt="$staff->full_name" decoding="async"
                               class="mx-auto h-40 w-40 rounded-full object-cover ring-4 ring-brand-50" />
                @else
                    <span class="mx-auto grid h-40 w-40 place-items-center rounded-full bg-gradient-to-br from-brand-600 to-brand-900 text-4xl font-bold text-white">
                        {{ $staff->initials() ?: '-' }}
                    </span>
                @endif
                @if ($staff->academic_degree)
                    <p class="mt-4 text-sm text-slate-500">{{ $staff->academic_degree }}</p>
                @endif
                @if ($staff->department)
                    <a href="{{ route('structure.show', $staff->department) }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:underline">
                        <x-ico name="building-office-2" class="h-4 w-4" /> {{ $staff->department->title }}
                    </a>
                @endif
                @if ($staff->email || $staff->phone)
                    <div class="mt-5 space-y-2 border-t border-slate-100 pt-5 text-sm text-slate-600">
                        @if ($staff->email)
                            <a href="mailto:{{ $staff->email }}" class="flex items-center justify-center gap-1.5 hover:text-brand-700">
                                <x-ico name="envelope" class="h-4 w-4" /> {{ $staff->email }}
                            </a>
                        @endif
                        @if ($staff->phone)
                            <p class="flex items-center justify-center gap-1.5"><x-ico name="phone" class="h-4 w-4" /> {{ $staff->phone }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </aside>

        <div class="lg:col-span-8">
            @if ($facts)
                <h2 class="text-xl font-bold text-slate-900">Загальні відомості</h2>
                <div class="accent-rule"></div>
                <dl class="mt-5 divide-y divide-slate-100 rounded-2xl bg-white ring-1 ring-slate-100">
                    @foreach ($facts as $fact)
                        <div class="grid gap-1 p-4 sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-semibold text-slate-500">{{ $fact['label'] }}</dt>
                            <dd class="text-sm text-slate-800 sm:col-span-2">{{ $fact['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            @elseif (filled($staff->bio))
                <div class="prose prose-slate max-w-none prose-headings:font-display prose-a:text-brand-700">
                    {!! $staff->bio !!}
                </div>
            @else
                <x-empty-state icon="user" title="Детальну інформацію про працівника незабаром буде додано." />
            @endif

            @if ($links)
                <div class="mt-10">
                    <h2 class="text-xl font-bold text-slate-900">Додаткові відомості</h2>
                    <div class="accent-rule"></div>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        @foreach ($links as $link)
                            <a href="{{ $link['url'] }}" class="card flex items-center gap-3 p-4 text-sm font-semibold text-slate-800 transition hover:-translate-y-0.5 hover:text-brand-700 hover:shadow-lg">
                                <x-ico name="document-text" class="h-5 w-5 shrink-0 text-brand-700" />
                                <span>{{ $link['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($colleagues->isNotEmpty())
                <div class="mt-12">
                    <h2 class="text-xl font-bold text-slate-900">Інші працівники підрозділу</h2>
                    <div class="accent-rule"></div>
                    <div class="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($colleagues as $person)
                            <x-staff-card :person="$person" />
                        @endforeach
                    </div>
                </div>
            @endif

            <a href="{{ $staff->department ? route('structure.show', $staff->department) : route('staff.administration') }}" class="btn-outline mt-10">
                <x-ico name="arrow-left" class="h-4 w-4" /> {{ $staff->department ? 'До підрозділу' : 'До адміністрації' }}
            </a>
        </div>
    </section>

</x-layouts.app>
