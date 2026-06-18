<x-layouts.app title="Адміністрація">

    <x-page-hero title="Адміністрація коледжу" :breadcrumbs="[
        ['label' => 'Головна', 'url' => route('home')],
        ['label' => 'Адміністрація'],
    ]" />

    <section class="container-site py-12">
        @if ($staff->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($staff as $person)
                    <x-staff-card :person="$person" />
                @endforeach
            </div>
        @else
            <x-empty-state icon="users" title="Інформацію про адміністрацію незабаром буде додано." />
        @endif
    </section>

</x-layouts.app>
