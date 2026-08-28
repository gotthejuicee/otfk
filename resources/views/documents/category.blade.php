<x-layouts.app :title="$category->title"
               :description="$category->title . ' — офіційні документи Одеського технічного фахового коледжу ОНТУ.'">

    @php
        $s = \App\Models\Setting::map();

        // Українське відмінювання лічильників
        $plural = function (int $n, string $one, string $few, string $many): string {
            $mod100 = $n % 100;
            $mod10 = $n % 10;

            if ($mod100 >= 11 && $mod100 <= 14) {
                return $many;
            }

            return match (true) {
                $mod10 === 1 => $one,
                $mod10 >= 2 && $mod10 <= 4 => $few,
                default => $many,
            };
        };

        $documentWord = fn (int $n) => $plural($n, 'документ', 'документи', 'документів');
        // Родовий відмінок для конструкції «із N документів»
        $documentGenitive = fn (int $n) => $plural($n, 'документа', 'документів', 'документів');

        $hasContacts = ! empty($s['contact_phone']) || ! empty($s['contact_email'])
            || ! empty($s['contact_address']) || ! empty($s['work_hours']);
    @endphp

    {{-- Світла шапка розділу — у стилі решти внутрішніх сторінок --}}
    <section class="border-b border-slate-200/70 bg-slate-50/80">
        <div class="container-site py-8 lg:py-10">
            <x-breadcrumbs tone="light" :items="[
                ['label' => 'Головна', 'url' => route('home')],
                ['label' => 'Публічна інформація', 'url' => route('documents.index')],
                ['label' => $category->title],
            ]" />

            <div class="relative mt-4 overflow-hidden rounded-2xl bg-white px-6 py-8 shadow-sm ring-1 ring-slate-200/80 sm:px-10 sm:py-10">
                {{-- Декоративний контур документа праворуч --}}
                <x-ico name="document-text" aria-hidden="true"
                       class="pointer-events-none absolute -right-6 top-1/2 hidden h-64 w-64 -translate-y-1/2 text-brand-50 lg:block" />

                <div class="relative lg:flex lg:items-start lg:justify-between lg:gap-10">
                    <div class="max-w-3xl">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-brand-700 ring-1 ring-brand-100">
                            <x-ico name="folder" class="h-4 w-4" aria-hidden="true" /> Публічна інформація
                        </span>
                        <h1 class="mt-3 text-3xl font-extrabold leading-tight text-brand-950 sm:text-4xl">{{ $category->title }}</h1>
                        <div class="accent-rule"></div>
                    </div>

                    {{-- Пошук по назвах документів усередині категорії --}}
                    @if ($totalCount)
                        <form method="get" action="{{ route('documents.category', $category) }}"
                              class="relative mt-6 w-full shrink-0 lg:mt-2 lg:max-w-sm">
                            <label for="document-search" class="sr-only">Пошук документа в категорії</label>
                            <x-ico name="magnifying-glass" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" aria-hidden="true" />
                            <input id="document-search" type="search" name="q" value="{{ $search }}"
                                   placeholder="Пошук документа…"
                                   class="w-full rounded-full border-0 bg-white py-3 pl-12 pr-28 text-base text-slate-800 shadow-sm ring-1 ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-brand-600">
                            <button type="submit" class="absolute right-1.5 top-1/2 inline-flex min-h-11 -translate-y-1/2 items-center rounded-full bg-brand-900 px-4 text-sm font-semibold text-white transition hover:bg-brand-800">
                                Знайти
                            </button>
                        </form>
                    @endif
                </div>

                @if ($totalCount)
                    <div class="relative mt-6 flex flex-wrap items-center gap-2 text-sm">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gold-50 px-3 py-1 font-semibold text-gold-700 ring-1 ring-gold-300/70">
                            <x-ico name="document-text" class="h-4 w-4" aria-hidden="true" />
                            {{ $totalCount }} {{ $documentWord($totalCount) }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 font-semibold text-rose-600 ring-1 ring-rose-100">
                            <x-ico name="arrow-down-tray" class="h-4 w-4" aria-hidden="true" />
                            Формат PDF
                        </span>
                        @if ($documents->hasPages())
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 font-semibold text-brand-800 ring-1 ring-brand-100">
                                <x-ico name="bars-3-bottom-left" class="h-4 w-4" aria-hidden="true" />
                                Сторінка {{ $documents->currentPage() }} з {{ $documents->lastPage() }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="container-site space-y-10 py-10 lg:py-12">
        <div class="grid gap-8 lg:grid-cols-12">
            {{-- Навігація по всіх розділах: на мобільному — під списком документів,
                 щоб два десятки посилань не відсували самі документи на екран нижче --}}
            <aside class="order-2 lg:order-1 lg:col-span-3">
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/80 lg:sticky lg:top-28 lg:max-h-[calc(100vh-8rem)] lg:overflow-y-auto">
                    <p class="px-2 pb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Усі розділи</p>
                    <nav class="space-y-1">
                        @foreach ($categories as $c)
                            <a href="{{ route('documents.category', $c) }}"
                               @if ($c->id === $category->id) aria-current="page" @endif
                               @class([
                                   'flex min-h-11 items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm transition',
                                   'bg-brand-50 font-semibold text-brand-800 ring-1 ring-brand-100' => $c->id === $category->id,
                                   'text-slate-600 hover:bg-slate-50' => $c->id !== $category->id,
                               ])>
                                <span>{{ $c->title }}</span>
                                <span @class([
                                    'shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold',
                                    'bg-brand-100 text-brand-700' => $c->id === $category->id,
                                    'bg-slate-100 text-slate-500' => $c->id !== $category->id,
                                ])>{{ $c->documents_count }}</span>
                            </a>
                        @endforeach
                    </nav>
                </div>
            </aside>

            <div class="order-1 lg:order-2 lg:col-span-9">
                @if ($documents->total())
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-4 text-sm text-slate-500">
                        <p>
                            Показано {{ $documents->firstItem() }}–{{ $documents->lastItem() }} із {{ $documents->total() }} {{ $documentGenitive($documents->total()) }}
                            @if ($search !== '')
                                за запитом «<span class="font-semibold text-brand-800">{{ $search }}</span>»
                            @endif
                        </p>
                        @if ($search !== '')
                            <a href="{{ route('documents.category', $category) }}" class="inline-flex items-center gap-1.5 font-semibold text-brand-700 hover:text-gold-600">
                                <x-ico name="x-mark" class="h-4 w-4" aria-hidden="true" /> Скинути пошук
                            </a>
                        @endif
                    </div>

                    <ul class="space-y-3">
                        @foreach ($documents as $doc)
                            <li class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/80 transition hover:shadow-md hover:ring-brand-200 sm:p-5">
                                <div class="flex items-start gap-4">
                                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-rose-50 text-rose-600 ring-1 ring-rose-100">
                                        <x-ico name="document-text" class="h-6 w-6" aria-hidden="true" />
                                    </span>

                                    {{-- На вузькому екрані кнопки йдуть під назвою, від lg — праворуч від неї --}}
                                    <div class="min-w-0 flex-1 lg:flex lg:items-start lg:justify-between lg:gap-6">
                                        <div class="min-w-0">
                                            @if ($doc->file_url)
                                                <a href="{{ $doc->file_url }}" target="_blank" rel="noopener"
                                                   class="font-semibold leading-snug text-brand-950 hover:text-brand-700">{{ $doc->title }}</a>
                                            @else
                                                <p class="font-semibold leading-snug text-brand-950">{{ $doc->title }}</p>
                                            @endif

                                            <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-400">
                                                @if ($doc->published_at)
                                                    <span>{{ $doc->published_at->translatedFormat('j F Y') }}</span>
                                                @endif
                                                @if ($doc->file_extension)
                                                    <span class="font-semibold text-rose-500">{{ $doc->file_extension }}</span>
                                                @endif
                                                @if ($doc->file_size_label)
                                                    <span>{{ $doc->file_size_label }}</span>
                                                @endif
                                            </div>

                                            @if ($doc->description)
                                                <p class="mt-2 text-sm text-slate-500">{{ $doc->description }}</p>
                                            @endif
                                        </div>

                                        @if ($doc->file_url)
                                            <div class="mt-3 flex flex-wrap gap-2 lg:mt-0 lg:shrink-0 lg:flex-nowrap">
                                                {{-- На вузькому екрані перегляд відкриває сама назва документа --}}
                                                <a href="{{ $doc->file_url }}" target="_blank" rel="noopener"
                                                   class="hidden min-h-11 items-center justify-center gap-1.5 rounded-full px-4 text-sm font-semibold text-brand-700 ring-1 ring-slate-200 transition hover:bg-slate-50 sm:inline-flex">
                                                    <x-ico name="eye" class="h-4 w-4" aria-hidden="true" /> Переглянути
                                                </a>
                                                <a href="{{ $doc->file_url }}" @if ($doc->file_path) download @endif
                                                   class="inline-flex min-h-11 items-center justify-center gap-1.5 rounded-full bg-brand-900 px-4 text-sm font-semibold text-white transition hover:bg-brand-800">
                                                    <x-ico name="arrow-down-tray" class="h-4 w-4" aria-hidden="true" /> Завантажити
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    @if ($documents->hasPages())
                        <div class="pt-8">{{ $documents->links() }}</div>
                    @endif
                @elseif ($search !== '')
                    <div class="rounded-2xl bg-white p-10 text-center ring-1 ring-slate-200/80">
                        <x-ico name="magnifying-glass" class="mx-auto h-10 w-10 text-slate-300" aria-hidden="true" />
                        <p class="mt-3 font-semibold text-brand-950">За запитом «{{ $search }}» документів не знайдено</p>
                        <p class="mt-1 text-sm text-slate-500">Спробуйте коротший запит або перегляньте весь розділ.</p>
                        <a href="{{ route('documents.category', $category) }}" class="btn-outline mt-4">Показати всі документи</a>
                    </div>
                @else
                    <x-empty-state icon="folder-open" title="Документи цієї категорії незабаром буде додано." />
                @endif
            </div>
        </div>

        {{-- Фінальний блок: куди звертатися, якщо потрібного документа немає --}}
        <div class="overflow-hidden rounded-2xl bg-gradient-to-br from-brand-50 to-white px-6 py-8 ring-1 ring-brand-100 sm:px-10">
            <div class="grid gap-8 lg:grid-cols-2 lg:items-center">
                <div>
                    <h2 class="text-2xl font-extrabold text-brand-950">Не знайшли потрібний документ?</h2>
                    <p class="mt-2 text-slate-600">
                        Зверніться до нас — ми підкажемо, у якому розділі шукати, або надамо копію документа.
                    </p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('contacts') }}" class="btn-primary">
                            Написати нам <x-ico name="arrow-right" class="h-4 w-4" aria-hidden="true" />
                        </a>
                        <a href="{{ route('documents.index') }}" class="btn-outline border-gold-300 text-gold-700 ring-gold-300 hover:bg-gold-50">
                            Усі розділи <x-ico name="arrow-right" class="h-4 w-4" aria-hidden="true" />
                        </a>
                    </div>
                </div>

                @if ($hasContacts)
                    <dl class="grid gap-4 sm:grid-cols-2">
                        @if (! empty($s['contact_phone']))
                            <div class="flex items-start gap-3">
                                <x-ico name="phone" class="mt-0.5 h-5 w-5 shrink-0 text-gold-600" aria-hidden="true" />
                                <span>
                                    <dt class="text-xs uppercase tracking-wide text-slate-400">Телефон</dt>
                                    <dd><a href="tel:{{ preg_replace('/[^+\d]/', '', $s['contact_phone']) }}" class="font-semibold text-brand-800 hover:text-brand-600">{{ $s['contact_phone'] }}</a></dd>
                                </span>
                            </div>
                        @endif
                        @if (! empty($s['contact_email']))
                            <div class="flex items-start gap-3">
                                <x-ico name="envelope" class="mt-0.5 h-5 w-5 shrink-0 text-gold-600" aria-hidden="true" />
                                <span class="min-w-0">
                                    <dt class="text-xs uppercase tracking-wide text-slate-400">Email</dt>
                                    <dd><a href="mailto:{{ $s['contact_email'] }}" class="break-words font-semibold text-brand-800 hover:text-brand-600">{{ $s['contact_email'] }}</a></dd>
                                </span>
                            </div>
                        @endif
                        @if (! empty($s['contact_address']))
                            <div class="flex items-start gap-3">
                                <x-ico name="map-pin" class="mt-0.5 h-5 w-5 shrink-0 text-gold-600" aria-hidden="true" />
                                <span class="min-w-0">
                                    <dt class="text-xs uppercase tracking-wide text-slate-400">Адреса</dt>
                                    <dd class="text-slate-600">{{ $s['contact_address'] }}</dd>
                                </span>
                            </div>
                        @endif
                        @if (! empty($s['work_hours']))
                            <div class="flex items-start gap-3">
                                <x-ico name="clock" class="mt-0.5 h-5 w-5 shrink-0 text-gold-600" aria-hidden="true" />
                                <span class="min-w-0">
                                    <dt class="text-xs uppercase tracking-wide text-slate-400">Графік роботи</dt>
                                    <dd class="text-slate-600">{{ $s['work_hours'] }}</dd>
                                </span>
                            </div>
                        @endif
                    </dl>
                @endif
            </div>
        </div>
    </section>

</x-layouts.app>
