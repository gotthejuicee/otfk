<x-layouts.app title="Адміністрація">

    <section class="bg-brand-950">
        <div class="container-site py-12 lg:py-14">
            <nav class="flex flex-wrap items-center gap-2 text-sm text-brand-300">
                <a href="{{ route('home') }}" class="hover:text-white">Головна</a>
                <x-ico name="chevron-right" class="h-4 w-4" />
                <span class="text-white">Адміністрація</span>
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold text-white sm:text-4xl">Адміністрація коледжу</h1>
            <div class="accent-rule"></div>
        </div>
    </section>

    <section class="container-site py-12">
        @if ($staff->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($staff as $person)
                    <x-staff-card :person="$person" />
                @endforeach
            </div>
        @else
            <div class="card p-12 text-center text-slate-500">Інформацію про адміністрацію незабаром буде додано.</div>
        @endif
    </section>

</x-layouts.app>
