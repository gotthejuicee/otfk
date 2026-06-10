<x-layouts.app title="Питання та відповіді" description="Відповіді на найчастіші питання вступників та студентів Одеського технічного фахового коледжу ОНТУ.">

    {{-- Розмітка FAQPage для розширених результатів Google --}}
    @if ($faqs->isNotEmpty())
        @php
            $jsonLd = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $faqs->map(fn ($f) => [
                    '@type' => 'Question',
                    'name' => $f->question,
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f->answer],
                ])->values()->all(),
            ];
        @endphp
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endif

    <section class="bg-brand-950">
        <div class="container-site py-12 lg:py-14">
            <nav class="flex flex-wrap items-center gap-2 text-sm text-brand-300">
                <a href="{{ route('home') }}" class="hover:text-white">Головна</a>
                <x-ico name="chevron-right" class="h-4 w-4" />
                <a href="{{ url('/abituriyentu') }}" class="hover:text-white">Абітурієнту</a>
                <x-ico name="chevron-right" class="h-4 w-4" />
                <span class="text-white">Питання та відповіді</span>
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold text-white sm:text-4xl">Питання та відповіді</h1>
            <div class="accent-rule"></div>
        </div>
    </section>

    <section class="container-site py-12">
        <div class="mx-auto max-w-3xl">
            @if ($faqs->isEmpty())
                <div class="card p-12 text-center text-slate-500">
                    <x-ico name="question-mark-circle" class="mx-auto h-10 w-10 text-slate-300" />
                    <p class="mt-3">Питання та відповіді скоро зʼявляться.</p>
                </div>
            @else
                <div class="space-y-3" x-data="{ open: null }">
                    @foreach ($faqs as $i => $faq)
                        <div class="card overflow-hidden">
                            <button type="button" @click="open = open === {{ $i }} ? null : {{ $i }}"
                                    class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                                <span class="font-semibold text-slate-900">{{ $faq->question }}</span>
                                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-brand-50 text-brand-700 transition"
                                      :class="open === {{ $i }} && 'rotate-180 bg-brand-700 text-white'">
                                    <x-ico name="chevron-down" class="h-4 w-4" />
                                </span>
                            </button>
                            <div x-show="open === {{ $i }}" x-transition.opacity.duration.200ms x-cloak>
                                <div class="border-t border-slate-100 px-5 py-4 text-sm leading-relaxed text-slate-600">
                                    {!! nl2br(e($faq->answer)) !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10 rounded-2xl bg-brand-50 p-6 text-center ring-1 ring-brand-100">
                    <p class="font-semibold text-brand-900">Не знайшли відповідь?</p>
                    <p class="mt-1 text-sm text-brand-700">Залиште заявку — приймальна комісія зателефонує та все розповість.</p>
                    <a href="{{ route('applicants.create') }}" class="btn-primary mt-4">Залишити заявку</a>
                </div>
            @endif
        </div>
    </section>

</x-layouts.app>
