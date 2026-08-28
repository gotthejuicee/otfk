<x-layouts.app title="Контакти"
               description="Контакти Одеського технічного фахового коледжу ОНТУ: адреса, телефон, електронна пошта, графік роботи, карта та форма зворотного звʼязку.">

    @php
        $s = \App\Models\Setting::map();

        $phoneHref = ! empty($s['contact_phone']) ? 'tel:' . preg_replace('/[^+\d]/', '', $s['contact_phone']) : null;
        $mapSearch = ! empty($s['contact_address'])
            ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($s['contact_address'])
            : null;

        // Картки контактів — тільки заповнені в адмінці ключі
        $cards = array_values(array_filter([
            ['icon' => 'map-pin', 'label' => 'Адреса', 'value' => $s['contact_address'] ?? null, 'href' => $mapSearch, 'external' => true],
            ['icon' => 'phone', 'label' => 'Телефон', 'value' => $s['contact_phone'] ?? null, 'href' => $phoneHref, 'external' => false],
            ['icon' => 'envelope', 'label' => 'Електронна пошта', 'value' => $s['contact_email'] ?? null, 'href' => ! empty($s['contact_email']) ? 'mailto:' . $s['contact_email'] : null, 'external' => false],
            ['icon' => 'clock', 'label' => 'Графік роботи', 'value' => $s['work_hours'] ?? null, 'href' => null, 'external' => false],
        ], fn ($card) => ! empty($card['value'])));

        // Кроки після звернення — статичний UI-текст, у БД таких сутностей немає
        $steps = [
            ['title' => 'Отримуємо звернення', 'text' => 'Повідомлення потрапляє до відповідального працівника коледжу.'],
            ['title' => 'Опрацьовуємо в робочий час', 'text' => 'Відповідаємо телефоном або листом на вказані контакти.'],
            ['title' => 'Допомагаємо з питанням', 'text' => 'Підкажемо документи, терміни та наступні кроки.'],
        ];

        $socials = array_values(array_filter([
            ['icon' => 'facebook', 'label' => 'Facebook', 'url' => $s['social_facebook'] ?? null],
            ['icon' => 'instagram', 'label' => 'Instagram', 'url' => $s['social_instagram'] ?? null],
            ['icon' => 'youtube', 'label' => 'YouTube', 'url' => $s['social_youtube'] ?? null],
        ], fn ($item) => ! empty($item['url'])));
    @endphp

    {{-- Світла шапка розділу — у стилі решти внутрішніх сторінок --}}
    <section class="border-b border-slate-200/70 bg-slate-50/80">
        <div class="container-site py-8 lg:py-10">
            <x-breadcrumbs tone="light" :items="[
                ['label' => 'Головна', 'url' => route('home')],
                ['label' => 'Контакти'],
            ]" />

            <div class="relative mt-4 overflow-hidden rounded-2xl bg-white px-6 py-8 shadow-sm ring-1 ring-slate-200/80 sm:px-10 sm:py-10">
                <x-ico name="chat-bubble-left-right" aria-hidden="true"
                       class="pointer-events-none absolute -right-8 top-1/2 hidden h-64 w-64 -translate-y-1/2 text-brand-50 lg:block" />

                <div class="relative max-w-3xl">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-brand-700 ring-1 ring-brand-100">
                        <x-ico name="map-pin" class="h-4 w-4" aria-hidden="true" /> Контакти
                    </span>
                    <h1 class="mt-3 text-3xl font-extrabold leading-tight text-brand-950 sm:text-4xl">Звʼяжіться з коледжем</h1>
                    <div class="accent-rule"></div>
                    <p class="mt-5 text-lg leading-relaxed text-slate-500">
                        Питання щодо вступу, навчання чи документів? Зателефонуйте, напишіть листа або залиште звернення у формі — відповімо в робочий час.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        @if ($phoneHref)
                            <a href="{{ $phoneHref }}" class="btn-primary">
                                <x-ico name="phone" class="h-4 w-4" aria-hidden="true" /> {{ $s['contact_phone'] }}
                            </a>
                        @endif
                        <a href="#forma" class="btn-outline border-gold-300 text-gold-700 ring-gold-300 hover:bg-gold-50">
                            <x-ico name="paper-airplane" class="h-4 w-4" aria-hidden="true" /> Написати звернення
                        </a>
                        @if ($mapSearch)
                            <a href="{{ $mapSearch }}" target="_blank" rel="noopener" class="btn-outline">
                                <x-ico name="map" class="h-4 w-4" aria-hidden="true" /> Прокласти маршрут
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container-site space-y-10 py-10 lg:py-12">
        {{-- Картки контактів --}}
        @if ($cards)
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($cards as $card)
                    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/80">
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-brand-50 text-brand-700 ring-1 ring-brand-100">
                            <x-ico :name="$card['icon']" class="h-5 w-5" aria-hidden="true" />
                        </span>
                        <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $card['label'] }}</p>
                        @if ($card['href'])
                            <a href="{{ $card['href'] }}" @if ($card['external']) target="_blank" rel="noopener" @endif
                               class="mt-1 block font-semibold leading-snug text-brand-950 hover:text-brand-700">{{ $card['value'] }}</a>
                        @else
                            <p class="mt-1 font-semibold leading-snug text-brand-950">{{ $card['value'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="grid gap-8 lg:grid-cols-3 lg:items-start">
            {{-- Форма зворотного звʼязку --}}
            <div id="forma" class="scroll-mt-28 lg:col-span-2">
                <div class="card overflow-hidden">
                    <div class="flex items-start gap-3 border-b border-slate-200/80 bg-slate-50/70 px-6 py-5 sm:px-8">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gold-100 text-gold-700">
                            <x-ico name="chat-bubble-left-right" class="h-6 w-6" aria-hidden="true" />
                        </span>
                        <p class="mt-1.5 text-sm font-semibold text-brand-900 sm:text-base">
                            Форма зворотного звʼязку — відповідаємо на вказані контакти
                        </p>
                    </div>

                    <div class="p-6 sm:p-8">
                        @if (session('status'))
                            <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-800">
                                <x-ico name="check-circle" variant="solid" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-500" aria-hidden="true" />
                                <span>{{ session('status') }}</span>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-800">
                                <x-ico name="exclamation-triangle" variant="solid" class="mt-0.5 h-5 w-5 shrink-0 text-rose-500" aria-hidden="true" />
                                <span>Перевірте, будь ласка, підсвічені поля — форму не надіслано.</span>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('contacts.store') }}" class="space-y-5"
                              x-data="{ sending: false, message: @js(old('message', '')) }" @submit="sending = true">
                            @csrf
                            {{-- Honeypot (антиспам): приховане поле, яке заповнюють лише боти --}}
                            <div class="hidden" aria-hidden="true">
                                <label for="website">Не заповнюйте це поле</label>
                                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="name" class="label">Ім'я <span class="text-rose-500">*</span></label>
                                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name"
                                           class="input px-4 py-3 text-base @error('name') ring-rose-400 @enderror" placeholder="Шевченко Тарас">
                                    @error('name') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="phone" class="label">Телефон</label>
                                    <input id="phone" name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" autocomplete="tel"
                                           class="input px-4 py-3 text-base @error('phone') ring-rose-400 @enderror" placeholder="+380 __ ___ __ __">
                                    @error('phone') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="email" class="label">Електронна пошта</label>
                                    <input id="email" name="email" type="email" inputmode="email" value="{{ old('email') }}" autocomplete="email"
                                           class="input px-4 py-3 text-base @error('email') ring-rose-400 @enderror" placeholder="name@example.com">
                                    @error('email') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="subject" class="label">Тема</label>
                                    <input id="subject" name="subject" type="text" value="{{ old('subject') }}"
                                           class="input px-4 py-3 text-base @error('subject') ring-rose-400 @enderror" placeholder="Напр.: питання щодо вступу">
                                    @error('subject') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label for="message" class="label">Повідомлення <span class="text-rose-500">*</span></label>
                                <textarea id="message" name="message" rows="5" maxlength="5000" required x-model="message"
                                          class="input px-4 py-3 text-base @error('message') ring-rose-400 @enderror"
                                          placeholder="Опишіть питання — так ми відповімо швидше">{{ old('message') }}</textarea>
                                <div class="mt-1.5 flex items-start justify-between gap-3">
                                    @error('message')
                                        <p class="text-sm text-rose-600">{{ $message }}</p>
                                    @else
                                        <span></span>
                                    @enderror
                                    <p class="shrink-0 text-xs text-slate-400"><span x-text="message.length">0</span>/5000</p>
                                </div>
                            </div>

                            <button type="submit" class="btn-primary w-full px-6 py-3.5 text-base sm:w-auto" :disabled="sending">
                                <span x-show="!sending" class="inline-flex items-center gap-2">
                                    <x-ico name="paper-airplane" class="h-5 w-5" aria-hidden="true" /> Надіслати звернення
                                </span>
                                <span x-show="sending" x-cloak class="inline-flex items-center gap-2">
                                    <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z" /></svg> Надсилаємо…
                                </span>
                            </button>

                            <p class="flex items-start gap-1.5 text-xs text-slate-400">
                                <x-ico name="lock-closed" class="mt-px h-3.5 w-3.5 shrink-0" aria-hidden="true" />
                                <span>Надсилаючи форму, ви даєте згоду на обробку вказаних контактних даних для звʼязку з вами.</span>
                            </p>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Сайдбар --}}
            <aside class="space-y-6 lg:sticky lg:top-28">
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

                @if ($socials)
                    <div class="card p-6">
                        <h2 class="text-lg font-bold text-brand-950">Ми в соцмережах</h2>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">Новини коледжу та життя студентів — щодня.</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($socials as $social)
                                <a href="{{ $social['url'] }}" target="_blank" rel="noopener"
                                   class="inline-flex min-h-11 items-center gap-2 rounded-full bg-slate-50 px-4 text-sm font-semibold text-brand-800 ring-1 ring-slate-200 transition hover:bg-brand-50">
                                    <x-brand-ico :name="$social['icon']" class="h-4 w-4" aria-hidden="true" /> {{ $social['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="rounded-2xl bg-gold-50 p-6 ring-1 ring-gold-200/80">
                    <h2 class="text-lg font-bold text-brand-950">Плануєте вступати?</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">
                        Залиште заявку — приймальна комісія зателефонує та відповість на питання про вступ.
                    </p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('applicants.create') }}" class="btn-accent">
                            Залишити заявку <x-ico name="arrow-right" class="h-4 w-4" aria-hidden="true" />
                        </a>
                        <a href="{{ route('faq') }}" class="btn-outline">
                            Часті запитання <x-ico name="arrow-right" class="h-4 w-4" aria-hidden="true" />
                        </a>
                    </div>
                </div>
            </aside>
        </div>

        {{-- Карта --}}
        @if (! empty($s['map_embed']))
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200/80 px-6 py-4">
                    <div>
                        <h2 class="text-lg font-bold text-brand-950">Як нас знайти</h2>
                        @if (! empty($s['contact_address']))
                            <p class="mt-0.5 text-sm text-slate-500">{{ $s['contact_address'] }}</p>
                        @endif
                    </div>
                    @if ($mapSearch)
                        <a href="{{ $mapSearch }}" target="_blank" rel="noopener"
                           class="inline-flex min-h-11 items-center gap-1.5 rounded-full px-4 text-sm font-semibold text-brand-700 ring-1 ring-slate-200 transition hover:bg-slate-50">
                            <x-ico name="arrow-top-right-on-square" class="h-4 w-4" aria-hidden="true" /> Відкрити в Google Maps
                        </a>
                    @endif
                </div>
                <iframe src="{{ $s['map_embed'] }}" title="Карта розташування коледжу"
                        class="h-80 w-full lg:h-[26rem]" style="border:0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        @endif
    </section>

</x-layouts.app>
