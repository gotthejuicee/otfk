<x-layouts.app title="Залишити заявку" description="Онлайн-заявка для вступників до Одеського технічного фахового коледжу ОНТУ: залиште контакти — приймальна комісія звʼяжеться з вами.">

    @php
        $s = \App\Models\Setting::map();

        // Кроки після подання заявки — статичний UI-текст, у БД таких сутностей немає
        $steps = [
            ['title' => 'Отримуємо вашу заявку', 'text' => 'Ми уважно розглянемо вашу заявку в робочий час.'],
            ['title' => 'Телефонуємо або пишемо вам', 'text' => 'Уточнимо деталі та відповімо на ваші запитання.'],
            ['title' => 'Підказуємо зі спеціальністю та вступом', 'text' => 'Допоможемо з вибором і розкажемо про наступні кроки.'],
        ];

        $benefits = [
            ['icon' => 'chat-bubble-left-right', 'title' => 'Безкоштовна консультація', 'text' => 'Відповідаємо на будь-які питання про вступ і спеціальності.'],
            ['icon' => 'bolt', 'title' => 'Швидкий зворотний звʼязок', 'text' => 'Опрацьовуємо заявки в робочий час коледжу.'],
            ['icon' => 'academic-cap', 'title' => 'Допомога з вибором', 'text' => 'Підкажемо напрям, який пасує саме вам.'],
            ['icon' => 'hand-raised', 'title' => 'Підтримка на етапі вступу', 'text' => 'Супроводжуємо на кожному кроці до зарахування.'],
        ];
    @endphp

    {{-- Світла шапка розділу — у стилі новин, відео та галереї --}}
    <section class="border-b border-slate-200/70 bg-slate-50/80">
        <div class="container-site py-8 lg:py-10">
            <x-breadcrumbs tone="light" :items="[
                ['label' => 'Головна', 'url' => route('home')],
                ['label' => 'Абітурієнту', 'url' => url('/abituriyentu')],
                ['label' => 'Залишити заявку'],
            ]" />

            <div class="relative mt-4 overflow-hidden rounded-2xl bg-white px-6 py-8 shadow-sm ring-1 ring-slate-200/80 sm:px-10 sm:py-10">
                {{-- Декоративний контур — заповнює порожнечу праворуч на великих екранах --}}
                <x-ico name="academic-cap" aria-hidden="true"
                       class="pointer-events-none absolute -right-8 top-1/2 hidden h-64 w-64 -translate-y-1/2 text-brand-50 lg:block" />

                <div class="relative max-w-3xl">
                    <h1 class="text-3xl font-extrabold leading-tight text-brand-950 sm:text-4xl lg:text-[2.75rem]">Залишити заявку</h1>
                    <div class="accent-rule"></div>
                    <p class="mt-5 text-lg leading-relaxed text-slate-500">
                        Заповніть коротку форму — приймальна комісія зателефонує вам, відповість на питання та допоможе зі вступом.
                    </p>
                    <p class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-sm font-semibold text-brand-700 ring-1 ring-brand-200/70">
                        <x-ico name="check-badge" class="h-4 w-4" />
                        Консультація безкоштовна
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="container-site py-10 lg:py-14">
        <div class="grid gap-8 lg:grid-cols-3 lg:items-start">

            {{-- Форма --}}
            <div class="lg:col-span-2">
                <div class="card overflow-hidden">
                    <div class="flex items-start gap-3 border-b border-slate-200/80 bg-slate-50/70 px-6 py-5 sm:px-8">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gold-100 text-gold-700">
                            <x-ico name="user-circle" class="h-6 w-6" />
                        </span>
                        <p class="mt-1.5 text-sm font-semibold text-brand-900 sm:text-base">
                            Заповніть коротку форму — ми звʼяжемося з вами найближчим часом
                        </p>
                    </div>

                    <div class="p-6 sm:p-8">
                        @if (session('status'))
                            <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-800">
                                <x-ico name="check-circle" variant="solid" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-500" />
                                <span>{{ session('status') }}</span>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-800">
                                <x-ico name="exclamation-triangle" variant="solid" class="mt-0.5 h-5 w-5 shrink-0 text-rose-500" />
                                <span>Перевірте, будь ласка, підсвічені поля — форму не надіслано.</span>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('applicants.store') }}" class="space-y-5"
                              x-data="{ sending: false, message: @js(old('message', '')) }" @submit="sending = true">
                            @csrf
                            {{-- Honeypot (антиспам) --}}
                            <div class="hidden" aria-hidden="true">
                                <label for="website">Не заповнюйте це поле</label>
                                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                            </div>

                            <div>
                                <label for="name" class="label">Прізвище та імʼя <span class="text-rose-500">*</span></label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name"
                                       class="input px-4 py-3 text-base @error('name') ring-rose-400 @enderror" placeholder="Шевченко Тарас">
                                @error('name') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="phone" class="label">Телефон <span class="text-rose-500">*</span></label>
                                    <input id="phone" name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" required autocomplete="tel"
                                           class="input px-4 py-3 text-base @error('phone') ring-rose-400 @enderror" placeholder="+380 __ ___ __ __">
                                    @error('phone') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="email" class="label">Email</label>
                                    <input id="email" name="email" type="email" inputmode="email" value="{{ old('email') }}" autocomplete="email"
                                           class="input px-4 py-3 text-base @error('email') ring-rose-400 @enderror" placeholder="name@example.com">
                                    @error('email') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label for="specialty_id" class="label">Яка спеціальність цікавить?</label>
                                <select id="specialty_id" name="specialty_id" class="input px-4 py-3 text-base">
                                    <option value="">— Ще не визначився / не визначилась —</option>
                                    @foreach ($specialties as $sp)
                                        <option value="{{ $sp->id }}" @selected(old('specialty_id', request('specialty_id')) == $sp->id)>{{ $sp->title }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1.5 text-sm text-slate-500">
                                    Ще не обрали? <a href="{{ route('quiz') }}" class="font-semibold text-brand-700 underline-offset-2 hover:underline">пройдіть короткий квіз</a> — він підкаже напрям.
                                </p>
                            </div>

                            <div>
                                <label for="message" class="label">Питання чи коментар</label>
                                <textarea id="message" name="message" rows="4" maxlength="2000" x-model="message"
                                          class="input px-4 py-3 text-base" placeholder="Напр.: чи є місця на бюджет після 9 класу?">{{ old('message') }}</textarea>
                                <p class="mt-1.5 text-right text-xs text-slate-400"><span x-text="message.length">0</span>/2000</p>
                            </div>

                            <button type="submit" class="btn-primary w-full px-6 py-3.5 text-base" :disabled="sending">
                                <span x-show="!sending" class="inline-flex items-center gap-2">
                                    <x-ico name="paper-airplane" class="h-5 w-5" /> Надіслати заявку
                                </span>
                                <span x-show="sending" x-cloak class="inline-flex items-center gap-2">
                                    <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z" /></svg> Надсилаємо…
                                </span>
                            </button>

                            <p class="flex items-start justify-center gap-1.5 text-center text-xs text-slate-400">
                                <x-ico name="lock-closed" class="mt-px h-3.5 w-3.5 shrink-0" />
                                <span>Надсилаючи форму, ви даєте згоду на обробку вказаних контактних даних для звʼязку з вами.</span>
                            </p>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Сайдбар --}}
            <aside class="space-y-6">
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-brand-950">Що буде далі</h2>
                    <ol class="mt-5 space-y-5">
                        @foreach ($steps as $i => $step)
                            <li class="relative flex gap-4 @if (! $loop->last) pb-5 @endif">
                                @if (! $loop->last)
                                    <span aria-hidden="true" class="absolute left-4 top-9 h-full w-px -translate-x-1/2 bg-gold-200"></span>
                                @endif
                                <span class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gold-100 text-sm font-bold text-gold-700 ring-4 ring-white">{{ $i + 1 }}</span>
                                <div>
                                    <p class="font-semibold text-brand-900">{{ $step['title'] }}</p>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-500">{{ $step['text'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>

                @if (! empty($s['contact_phone']) || ! empty($s['contact_email']) || ! empty($s['contact_address']) || ! empty($s['work_hours']))
                    <div class="card p-6">
                        <h2 class="text-lg font-bold text-brand-950">Контакти приймальної комісії</h2>
                        <ul class="mt-4 space-y-3 text-sm text-slate-600">
                            @if (! empty($s['contact_phone']))
                                <li class="flex gap-3">
                                    <x-ico name="phone" class="mt-0.5 h-4 w-4 shrink-0 text-gold-600" />
                                    <a href="tel:{{ preg_replace('/[^+\d]/', '', $s['contact_phone']) }}" class="font-semibold text-brand-800 hover:text-brand-600">{{ $s['contact_phone'] }}</a>
                                </li>
                            @endif
                            @if (! empty($s['contact_email']))
                                <li class="flex gap-3">
                                    <x-ico name="envelope" class="mt-0.5 h-4 w-4 shrink-0 text-gold-600" />
                                    <a href="mailto:{{ $s['contact_email'] }}" class="break-all hover:text-brand-700">{{ $s['contact_email'] }}</a>
                                </li>
                            @endif
                            @if (! empty($s['contact_address']))
                                <li class="flex gap-3">
                                    <x-ico name="map-pin" class="mt-0.5 h-4 w-4 shrink-0 text-gold-600" />
                                    <span>{{ $s['contact_address'] }}</span>
                                </li>
                            @endif
                            @if (! empty($s['work_hours']))
                                <li class="flex gap-3">
                                    <x-ico name="clock" class="mt-0.5 h-4 w-4 shrink-0 text-gold-600" />
                                    <span>{{ $s['work_hours'] }}</span>
                                </li>
                            @endif
                        </ul>
                        <a href="{{ route('contacts') }}" class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-600">
                            Усі контакти <x-ico name="arrow-right" class="h-4 w-4" />
                        </a>
                    </div>
                @endif

                <div class="rounded-2xl bg-gold-50 p-6 ring-1 ring-gold-200/80">
                    <h2 class="text-lg font-bold text-brand-950">Яка спеціальність мені підходить?</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">
                        Пройдіть короткий квіз та отримайте персональну рекомендацію щодо спеціальності.
                    </p>
                    <a href="{{ route('quiz') }}" class="btn-accent mt-4">
                        Пройти квіз <x-ico name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>
            </aside>
        </div>
    </section>

    {{-- Смуга переваг --}}
    <section class="border-t border-slate-200/70 bg-slate-50/80">
        <div class="container-site grid gap-8 py-10 sm:grid-cols-2 lg:grid-cols-4 lg:py-12">
            @foreach ($benefits as $benefit)
                <div class="flex gap-4">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white text-gold-600 ring-1 ring-gold-200/80">
                        <x-ico name="{{ $benefit['icon'] }}" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="font-semibold text-brand-900">{{ $benefit['title'] }}</p>
                        <p class="mt-1 text-sm leading-relaxed text-slate-500">{{ $benefit['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

</x-layouts.app>
