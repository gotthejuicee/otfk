<x-layouts.app :title="$department->title">

    <section class="bg-brand-950">
        <div class="container-site py-12 lg:py-14">
            <x-breadcrumbs :items="[
                ['label' => 'Головна', 'url' => route('home')],
                ['label' => 'Структура', 'url' => route('structure.index')],
                ['label' => $department->title],
            ]" />
            <span class="mt-4 inline-block badge bg-white/10 text-brand-100 ring-1 ring-white/15">{{ $department->type_label }}</span>
            <h1 class="mt-3 max-w-4xl text-3xl font-extrabold leading-tight text-white sm:text-4xl">{{ $department->title }}</h1>
            <div class="accent-rule"></div>
        </div>
    </section>

    <section class="container-site py-12">
        @if (filled($department->description))
            <div class="prose prose-slate max-w-none prose-headings:font-display prose-a:text-brand-700">
                {!! $department->description !!}
            </div>
        @endif

        @if ($department->staff->isNotEmpty())
            <div class="mt-10">
                <h2 class="text-2xl font-extrabold text-slate-900">Склад підрозділу</h2>
                <div class="accent-rule"></div>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($department->staff as $person)
                        <x-staff-card :person="$person" />
                    @endforeach
                </div>
            </div>
        @endif

        @if (blank($department->description) && $department->staff->isEmpty())
            <div class="card p-12 text-center text-slate-500">
                <x-ico name="building-office-2" class="mx-auto h-10 w-10 text-slate-300" />
                <p class="mt-3">Інформацію про цей підрозділ незабаром буде додано.</p>
            </div>
        @endif

        <a href="{{ route('structure.index') }}" class="btn-outline mt-10"><x-ico name="arrow-left" class="h-4 w-4" /> До структури</a>
    </section>

</x-layouts.app>
