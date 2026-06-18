<x-layouts.app title="Пошук">

    <x-page-hero title="Пошук по сайту" :show-rule="false" :breadcrumbs="[
        ['label' => 'Головна', 'url' => route('home')],
        ['label' => 'Пошук'],
    ]">
        <form action="{{ route('search') }}" method="GET" class="mt-6 flex max-w-xl gap-2">
            <input type="search" name="q" value="{{ $q }}" autofocus placeholder="Що шукаєте?"
                   class="input flex-1" />
            <button type="submit" class="btn-accent"><x-ico name="magnifying-glass" class="h-4 w-4" /> Знайти</button>
        </form>
    </x-page-hero>

    <section class="container-site py-12">
        @if ($q === '')
            <x-empty-state icon="magnifying-glass" title="Введіть запит, щоб знайти новини, сторінки, спеціальності та документи." />
        @elseif ($results->isEmpty())
            <x-empty-state icon="magnifying-glass" :title="'За запитом «' . $q . '» нічого не знайдено.'" />
        @else
            <p class="mb-6 text-sm text-slate-500">Знайдено результатів: <span class="font-semibold text-slate-800">{{ $results->count() }}</span></p>
            <ul class="space-y-3">
                @foreach ($results as $r)
                    <li class="card card-interactive p-5">
                        <a href="{{ $r['url'] }}" class="group block">
                            <span class="badge bg-brand-50 text-brand-700">{{ $r['type'] }}</span>
                            <p class="mt-2 font-bold text-slate-900 group-hover:text-brand-700">{{ $r['title'] }}</p>
                            @if (! empty($r['excerpt']))
                                <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $r['excerpt'] }}</p>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

</x-layouts.app>
