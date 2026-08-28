<x-layouts.app title="Яка спеціальність тобі підходить?" description="Короткий профорієнтаційний тест: кілька питань — і дізнаєшся, яка спеціальність коледжу пасує саме тобі.">

    @php
        $s = \App\Models\Setting::map();

        // Українське відмінювання слова «питання» для лічильника
        $questionWord = function (int $n): string {
            $mod100 = $n % 100;
            $mod10 = $n % 10;

            if ($mod100 >= 11 && $mod100 <= 14) {
                return 'питань';
            }

            return $mod10 >= 1 && $mod10 <= 4 ? 'питання' : 'питань';
        };

        $ready = $questions->isNotEmpty() && $specialties->isNotEmpty();
        $count = $questions->count();

        // Спеціальність за замовчуванням — коли жоден варіант не дав балів
        $fallbackId = $specialties->first()?->id ?? 0;

        $hasContacts = ! empty($s['contact_phone']) || ! empty($s['contact_email']);
    @endphp

    {{-- Світла шапка розділу — у стилі новин, відео, галереї, спеціальностей та FAQ --}}
    <section class="border-b border-slate-200/70 bg-slate-50/80">
        <div class="container-site py-8 lg:py-10">
            <x-breadcrumbs tone="light" :items="[
                ['label' => 'Головна', 'url' => route('home')],
                ['label' => 'Абітурієнту', 'url' => url('/abituriyentu')],
                ['label' => 'Тест на спеціальність'],
            ]" />

            <div class="relative mt-4 overflow-hidden rounded-2xl bg-white px-6 py-8 shadow-sm ring-1 ring-slate-200/80 sm:px-10 sm:py-10">
                {{-- Декоративний контур — заповнює порожнечу праворуч на великих екранах --}}
                <x-ico name="puzzle-piece" aria-hidden="true"
                       class="pointer-events-none absolute -right-8 top-1/2 hidden h-64 w-64 -translate-y-1/2 text-brand-50 lg:block" />

                <div class="relative max-w-3xl">
                    <h1 class="text-3xl font-extrabold leading-tight text-brand-950 sm:text-4xl lg:text-[2.75rem]">Яка спеціальність тобі підходить?</h1>
                    <div class="accent-rule"></div>
                    <p class="mt-5 text-lg leading-relaxed text-slate-500">
                        Короткий профорієнтаційний тест: відповідай чесно — і ми підкажемо, з якої спеціальності коледжу варто почати знайомство.
                    </p>
                    @if ($ready)
                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gold-50 px-3 py-1 text-sm font-semibold text-gold-700 ring-1 ring-gold-300/70">
                                <x-ico name="question-mark-circle" class="h-4 w-4" />
                                {{ $count }} {{ $questionWord($count) }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-sm font-semibold text-brand-700 ring-1 ring-brand-100">
                                <x-ico name="clock" class="h-4 w-4" />
                                близько хвилини
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-sm font-semibold text-brand-700 ring-1 ring-brand-100">
                                <x-ico name="lock-open" class="h-4 w-4" />
                                без реєстрації
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="container-site py-10 lg:py-14">
        @if (! $ready)
            <x-empty-state icon="puzzle-piece" title="Тест ще готується — завітайте пізніше." />
        @else
            <div x-data="quiz({{ $count }}, {{ $fallbackId }})" @keydown.window="onKey($event)">

                {{-- ІНТРО --}}
                <div x-show="step === 'intro'" class="grid gap-8 lg:grid-cols-3 lg:items-start">
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/80 sm:p-8 lg:col-span-2">
                        <span class="grid h-14 w-14 place-items-center rounded-2xl bg-gold-100 text-gold-600">
                            <x-ico name="puzzle-piece" class="h-7 w-7" aria-hidden="true" />
                        </span>
                        <h2 class="mt-5 text-2xl font-extrabold text-brand-950 sm:text-3xl">Не знаєш, куди вступати?</h2>
                        <p class="mt-3 max-w-xl text-slate-500">
                            Дай відповідь на {{ $count }} {{ $questionWord($count) }} про свої інтереси — тест порахує збіги
                            і покаже спеціальність, яка пасує саме тобі. Правильних чи неправильних відповідей тут немає.
                        </p>

                        <ol class="mt-8 grid gap-5 sm:grid-cols-3">
                            @foreach ([
                                ['cursor-arrow-rays', 'Обираєш відповіді', 'По одному варіанту на питання — те, що ближче саме тобі.'],
                                ['chart-bar', 'Рахуємо збіги', 'Кожен варіант додає бал спеціальності коледжу.'],
                                ['academic-cap', 'Отримуєш результат', 'Спеціальність, посилання на опис і кнопку заявки.'],
                            ] as $i => [$icon, $stepTitle, $stepText])
                                <li class="relative rounded-xl bg-slate-50 p-5 ring-1 ring-slate-200/70">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-sm font-bold text-brand-700 shadow-sm ring-1 ring-slate-200">{{ $i + 1 }}</span>
                                    <p class="mt-3 flex items-center gap-2 font-bold text-brand-950">
                                        <x-ico :name="$icon" class="h-5 w-5 shrink-0 text-gold-600" aria-hidden="true" />
                                        {{ $stepTitle }}
                                    </p>
                                    <p class="mt-1.5 text-sm leading-relaxed text-slate-500">{{ $stepText }}</p>
                                </li>
                            @endforeach
                        </ol>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <button type="button" @click="start()" class="btn-accent w-full justify-center px-8 py-3.5 text-base sm:w-auto">
                                Почати тест <x-ico name="arrow-right" class="h-4 w-4" />
                            </button>
                            <p class="text-sm text-slate-400">Нічого не потрібно заповнювати — просто натисни.</p>
                        </div>
                    </div>

                    {{-- Що можна отримати — реальні спеціальності з бази --}}
                    <aside class="rounded-2xl bg-brand-50/70 p-6 ring-1 ring-brand-100">
                        <h2 class="text-lg font-extrabold text-brand-950">Спеціальності в тесті</h2>
                        <p class="mt-1.5 text-sm text-slate-600">Одна з них стане твоїм результатом.</p>
                        <ul class="mt-5 space-y-3">
                            @foreach ($specialties as $specialty)
                                <li>
                                    <a href="{{ route('specialties.show', $specialty) }}"
                                       class="flex items-center gap-3 rounded-xl bg-white p-3 ring-1 ring-slate-200/80 transition hover:ring-gold-300">
                                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-brand-50 text-brand-700">
                                            <x-ico :name="$specialty->icon_name" class="h-5 w-5" aria-hidden="true" />
                                        </span>
                                        <span class="min-w-0">
                                            @if ($specialty->code)
                                                <span class="block text-xs font-bold text-gold-700">{{ $specialty->code }}</span>
                                            @endif
                                            <span class="block text-sm font-semibold leading-snug text-brand-950">{{ $specialty->title }}</span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('specialties.index') }}" class="btn-outline mt-6 w-full justify-center border-gold-300 text-gold-700 ring-gold-300 hover:bg-gold-50">
                            Усі спеціальності <x-ico name="arrow-right" class="h-4 w-4" />
                        </a>
                    </aside>
                </div>

                {{-- ПИТАННЯ --}}
                <div x-show="typeof step === 'number'" x-cloak class="mx-auto max-w-3xl">
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/80 sm:p-8">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-semibold text-slate-500">
                                Питання <span class="text-brand-800" x-text="stepNumber"></span> з {{ $count }}
                            </p>
                            <button type="button" x-show="history.length > 0" @click="back()"
                                    class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1 text-sm font-medium text-slate-500 transition hover:bg-slate-50 hover:text-brand-700">
                                <x-ico name="arrow-left" class="h-4 w-4" aria-hidden="true" /> Назад
                            </button>
                        </div>

                        {{-- Прогрес: смуга + сегменти по одному на питання --}}
                        <div class="mt-4" role="progressbar" aria-label="Прогрес тесту"
                             aria-valuemin="0" aria-valuemax="{{ $count }}" :aria-valuenow="history.length">
                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-gold-400 transition-all duration-300" :style="'width:' + progress + '%'"></div>
                            </div>
                            <div class="mt-2 flex gap-1.5">
                                @for ($i = 0; $i < $count; $i++)
                                    <span class="h-1.5 flex-1 rounded-full transition"
                                          :class="history.length > {{ $i }} ? 'bg-gold-400' : (stepIndex === {{ $i }} ? 'bg-brand-300' : 'bg-slate-100')"></span>
                                @endfor
                            </div>
                        </div>

                        {{-- Питання рендеряться сервером: працюють без JS-даних і видні пошуковим системам --}}
                        @foreach ($questions as $qi => $question)
                            <div x-show="stepIndex === {{ $qi }}" x-cloak aria-live="polite" aria-atomic="true">
                                <h2 class="mt-7 text-xl font-extrabold leading-snug text-brand-950 sm:text-2xl">{{ $question->question }}</h2>

                                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                    @foreach ($question->options as $oi => $option)
                                        <button type="button"
                                                data-step="{{ $qi }}" data-opt="{{ $oi }}"
                                                @click="answer({{ (int) $option->specialty_id }}, {{ (int) $option->points }})"
                                                class="flex min-h-[3.75rem] w-full items-center gap-3 rounded-xl bg-slate-50 px-4 py-4 text-left text-base font-medium text-slate-700 ring-1 ring-slate-200 transition hover:bg-brand-50 hover:ring-brand-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 active:scale-[.99]">
                                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-white text-sm font-bold text-slate-400 ring-1 ring-slate-200">{{ chr(65 + $oi) }}</span>
                                            <span>{{ $option->label }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <p class="mt-6 hidden items-center gap-2 text-xs text-slate-400 sm:flex">
                            <x-ico name="command-line" class="h-4 w-4" aria-hidden="true" />
                            Підказка: варіант можна обрати клавішами 1–{{ $questions->max(fn ($q) => $q->options->count()) }}.
                        </p>
                    </div>

                    <p class="mt-4 text-center text-sm text-slate-400">
                        Тест ні до чого не зобовʼязує — результат побачиш лише ти.
                    </p>
                </div>

                {{-- РЕЗУЛЬТАТ --}}
                <div x-show="step === 'result'" x-cloak aria-live="polite" aria-atomic="true" class="mx-auto max-w-4xl">
                    @foreach ($specialties as $specialty)
                        <div x-show="winnerId === {{ $specialty->id }}" x-cloak
                             class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80">
                            <div class="relative overflow-hidden bg-gradient-to-br from-brand-800 to-brand-950 px-6 py-9 text-center sm:px-10">
                                <svg aria-hidden="true" class="pointer-events-none absolute inset-0 h-full w-full text-white/[0.07]">
                                    <defs>
                                        <pattern id="quiz-grid-{{ $specialty->id }}" width="28" height="28" patternUnits="userSpaceOnUse">
                                            <path d="M28 0H0V28" fill="none" stroke="currentColor" stroke-width="1" />
                                        </pattern>
                                    </defs>
                                    <rect width="100%" height="100%" fill="url(#quiz-grid-{{ $specialty->id }})" />
                                </svg>

                                <div class="relative">
                                    <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-white/10 text-gold-300 ring-1 ring-white/15">
                                        <x-ico :name="$specialty->icon_name" class="h-8 w-8" aria-hidden="true" />
                                    </span>
                                    <p class="mt-5 text-sm font-semibold uppercase tracking-wide text-gold-300">Твій результат</p>
                                    <h2 class="mt-2 text-2xl font-extrabold text-white sm:text-3xl">{{ $specialty->title }}</h2>
                                    @if ($specialty->code)
                                        <p class="mt-2 text-sm text-brand-200">Код спеціальності: {{ $specialty->code }}</p>
                                    @endif
                                    <p class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-gold-400/15 px-3 py-1 text-sm font-semibold text-gold-200 ring-1 ring-gold-300/40">
                                        <x-ico name="sparkles" class="h-4 w-4" aria-hidden="true" />
                                        Збіг <span x-text="percentOf({{ $specialty->id }})"></span>%
                                    </p>
                                </div>
                            </div>

                            <div class="p-6 text-center sm:p-8">
                                @if ($specialty->short_description)
                                    <p class="mx-auto max-w-2xl leading-relaxed text-slate-600">{{ $specialty->short_description }}</p>
                                @endif

                                <div class="mt-7 flex flex-col justify-center gap-3 sm:flex-row">
                                    <a href="{{ route('applicants.create') }}?specialty_id={{ $specialty->id }}" class="btn-accent justify-center px-8 py-3.5 text-base">
                                        Залишити заявку <x-ico name="arrow-right" class="h-4 w-4" />
                                    </a>
                                    <a href="{{ route('specialties.show', $specialty) }}" class="btn-outline justify-center px-8 py-3.5 text-base">
                                        Про спеціальність <x-ico name="arrow-right" class="h-4 w-4" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Як розподілилися відповіді --}}
                    <div x-show="answered > 0" x-cloak class="mt-8 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/80 sm:p-8">
                        <h2 class="text-lg font-extrabold text-brand-950">Як розподілилися твої відповіді</h2>
                        <p class="mt-1.5 text-sm text-slate-500">Другий і третій результат теж варто роздивитися — вступ можливий на будь-яку спеціальність.</p>

                        <ul class="mt-6 space-y-4">
                            @foreach ($specialties as $specialty)
                                <li x-show="scoreOf({{ $specialty->id }}) > 0" x-cloak>
                                    <div class="flex items-baseline justify-between gap-4 text-sm">
                                        <a href="{{ route('specialties.show', $specialty) }}" class="font-semibold text-brand-900 hover:text-brand-600">
                                            @if ($specialty->code)<span class="text-gold-700">{{ $specialty->code }}</span> @endif{{ $specialty->title }}
                                        </a>
                                        <span class="shrink-0 font-semibold text-slate-500"><span x-text="percentOf({{ $specialty->id }})"></span>%</span>
                                    </div>
                                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full transition-all duration-500"
                                             :class="winnerId === {{ $specialty->id }} ? 'bg-gold-400' : 'bg-brand-300'"
                                             :style="'width:' + percentOf({{ $specialty->id }}) + '%'"></div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-7 flex flex-wrap items-center justify-center gap-x-6 gap-y-3">
                            <button type="button" @click="restart()" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-brand-700">
                                <x-ico name="arrow-path" class="h-4 w-4" aria-hidden="true" /> Пройти ще раз
                            </button>
                            <a href="{{ route('specialties.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-brand-700">
                                <x-ico name="squares-2x2" class="h-4 w-4" aria-hidden="true" /> Подивитися всі спеціальності
                            </a>
                        </div>
                    </div>

                    {{-- Фінальна смуга — живий контакт після результату --}}
                    <div class="mt-8 overflow-hidden rounded-2xl bg-gradient-to-br from-brand-50 to-white px-6 py-8 ring-1 ring-brand-100 sm:px-10">
                        <div class="flex flex-wrap items-center justify-between gap-6">
                            <span class="hidden h-16 w-16 shrink-0 place-items-center rounded-full bg-gold-100 text-gold-700 sm:grid">
                                <x-ico name="chat-bubble-left-right" class="h-8 w-8" aria-hidden="true" />
                            </span>
                            <div class="max-w-2xl flex-1">
                                <h2 class="text-xl font-extrabold text-brand-950 sm:text-2xl">Сумніваєшся у результаті?</h2>
                                <p class="mt-2 text-slate-600">
                                    Приймальна комісія допоможе зважити всі варіанти та розповість про вступ на кожну спеціальність.
                                    @if ($hasContacts)
                                        @if (! empty($s['contact_phone']))
                                            <a href="tel:{{ preg_replace('/[^+\d]/', '', $s['contact_phone']) }}" class="font-semibold text-brand-800 hover:text-brand-600">{{ $s['contact_phone'] }}</a>
                                        @endif
                                        @if (! empty($s['contact_email']))
                                            <a href="mailto:{{ $s['contact_email'] }}" class="break-all font-semibold text-brand-800 hover:text-brand-600">{{ $s['contact_email'] }}</a>
                                        @endif
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('contacts') }}" class="btn-outline px-6 py-3">
                                Контакти <x-ico name="arrow-right" class="h-4 w-4" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function quiz(total, fallbackId) {
                    return {
                        total,
                        fallbackId,
                        step: 'intro',      // 'intro' | номер питання | 'result'
                        scores: {},         // specialty_id => бали
                        history: [],        // [specialty_id, бали] — щоб працювала кнопка «Назад»

                        start() { this.step = 0 },

                        // Індекс поточного питання (-1 на інтро та результаті) і людський номер
                        get stepIndex() { return typeof this.step === 'number' ? this.step : -1 },
                        get stepNumber() { return this.stepIndex + 1 },
                        get answered() { return this.history.length },
                        get progress() { return this.total ? Math.round(this.answered / this.total * 100) : 0 },

                        answer(sid, pts) {
                            if (sid) this.scores[sid] = (this.scores[sid] || 0) + pts
                            this.history.push([sid, pts])
                            this.step = (this.step + 1 < this.total) ? this.step + 1 : 'result'
                        },

                        back() {
                            const prev = this.history.pop()
                            if (prev && prev[0]) this.scores[prev[0]] -= prev[1]
                            this.step = this.history.length
                        },

                        scoreOf(id) { return this.scores[id] || 0 },
                        percentOf(id) { return this.answered ? Math.round(this.scoreOf(id) / this.answered * 100) : 0 },

                        get winnerId() {
                            const top = Object.entries(this.scores).sort((a, b) => b[1] - a[1])[0]
                            return top && top[1] > 0 ? Number(top[0]) : this.fallbackId
                        },

                        // Клавіші 1–9 обирають варіант поточного питання
                        onKey(e) {
                            if (typeof this.step !== 'number' || e.metaKey || e.ctrlKey || e.altKey) return
                            const n = parseInt(e.key, 10)
                            if (!n) return
                            const btn = document.querySelector('[data-step="' + this.step + '"][data-opt="' + (n - 1) + '"]')
                            if (btn) { e.preventDefault(); btn.click() }
                        },

                        restart() { this.scores = {}; this.history = []; this.step = 'intro' },
                    }
                }
            </script>
        @endif
    </section>

</x-layouts.app>
