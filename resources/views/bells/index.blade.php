<x-layouts.app title="Розклад дзвінків" description="Розклад дзвінків Одеського технічного фахового коледжу ОНТУ: час початку та закінчення пар.">

    <section class="bg-brand-950">
        <div class="container-site py-12 lg:py-14">
            <nav class="flex items-center gap-2 text-sm text-brand-300">
                <a href="{{ route('home') }}" class="hover:text-white">Головна</a>
                <x-ico name="chevron-right" class="h-4 w-4" />
                <span class="text-white">Розклад дзвінків</span>
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold text-white sm:text-4xl">Розклад дзвінків</h1>
            <div class="accent-rule"></div>
        </div>
    </section>

    <section class="container-site py-12"
             x-data="bellSchedule(@js($periods->map(fn ($b) => ['n' => $b->number, 's' => substr($b->starts, 0, 5), 'e' => substr($b->ends, 0, 5)])->values()))"
             x-init="tick(); setInterval(() => tick(), 15000)">

        @if ($periods->isEmpty())
            <div class="card p-12 text-center text-slate-500">Розклад дзвінків ще не налаштовано.</div>
        @else
            {{-- Живий статус --}}
            <div class="mx-auto max-w-2xl">
                <div x-show="status" x-cloak
                     class="mb-6 flex items-center justify-center gap-2 rounded-2xl bg-brand-50 px-5 py-4 text-center font-semibold text-brand-800 ring-1 ring-brand-100">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-brand-400 opacity-75"></span>
                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-brand-600"></span>
                    </span>
                    <span x-text="status"></span>
                </div>

                <div class="card overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-brand-950 text-left text-xs uppercase tracking-wide text-brand-200">
                                <th class="px-5 py-3.5 font-semibold">Пара</th>
                                <th class="px-5 py-3.5 font-semibold">Початок</th>
                                <th class="px-5 py-3.5 font-semibold">Кінець</th>
                                <th class="px-5 py-3.5 text-right font-semibold">Стан</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($periods->values() as $i => $p)
                                <tr :class="current === {{ $p->number }} ? 'bg-gold-50/70' : ''">
                                    <td class="px-5 py-3.5 font-bold text-slate-900">{{ $p->number }}-{{ [1 => 'ша', 2 => 'га', 3 => 'тя', 4 => 'та', 5 => 'та', 6 => 'та', 7 => 'ма', 8 => 'ма'][$p->number] ?? 'та' }} пара</td>
                                    <td class="px-5 py-3.5 tabular-nums text-slate-600">{{ substr($p->starts, 0, 5) }}</td>
                                    <td class="px-5 py-3.5 tabular-nums text-slate-600">{{ substr($p->ends, 0, 5) }}</td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span x-show="current === {{ $p->number }}" x-cloak class="badge bg-gold-100 text-gold-800">зараз</span>
                                        <span x-show="current !== {{ $p->number }}" class="text-slate-300">—</span>
                                    </td>
                                </tr>
                                @php
                                    $next = $periods->values()->get($i + 1);
                                    $gap = $next ? \Carbon\Carbon::parse($p->ends)->diffInMinutes(\Carbon\Carbon::parse($next->starts)) : 0;
                                @endphp
                                @if ($next && $gap > 0)
                                    <tr>
                                        <td colspan="4" class="px-5 py-2 text-center text-xs {{ $gap >= 20 ? 'bg-gold-50 font-semibold text-gold-700' : 'bg-slate-50 text-slate-400' }}">
                                            {{ $gap >= 20 ? 'Велика перерва' : 'Перерва' }} · {{ $gap }} хв
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="mt-5 text-center text-sm text-slate-400">
                    Тривалість пари — {{ $periods->first() ? \Carbon\Carbon::parse($periods->first()->starts)->diffInMinutes(\Carbon\Carbon::parse($periods->first()->ends)) : 80 }} хвилин.
                </p>
            </div>
        @endif
    </section>

</x-layouts.app>
