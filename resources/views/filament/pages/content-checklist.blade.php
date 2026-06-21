<x-filament-panels::page>
    @php
        $dismissed = $this->dismissedKeys();
        $isHidden = fn ($item) => in_array($item['key'], $dismissed, true);

        $groups = [
            ['Сторінки без змісту', 'Порожні або дуже короткі сторінки — додайте текст і документи', $this->stubPages()],
            ['Порожні категорії документів', 'Категорії без жодного завантаженого PDF', $this->emptyDocumentCategories()],
            ['Відділення та комісії', 'Без персоналу або з надто коротким описом', $this->departmentsNeedingWork()],
            ['Порожні галереї', 'Альбоми без фотографій', $this->emptyGalleries()],
            ['Незаповнені налаштування', 'Контакти, соцмережі, карта на сторінці контактів', $this->missingSettings()],
        ];

        $activeTotal = collect($groups)->sum(fn ($g) => collect($g[2])->reject($isHidden)->count());
        $hidden = collect($groups)->flatMap(fn ($g) => $g[2])->filter($isHidden)->values();
    @endphp

    <div class="text-sm text-gray-500 dark:text-gray-400">
        @if ($activeTotal === 0)
            🎉 Усе наповнено — порожніх місць немає.
        @else
            Потребують уваги: <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $activeTotal }}</span>.
        @endif
        <span class="mt-1 block text-xs text-gray-400">
            Пункт зникає звідси сам, щойно сторінка отримає зміст. «Приховати» — щоб прибрати вручну (напр., свідомо порожню сезонну сторінку).
        </span>
    </div>

    @foreach ($groups as [$title, $hint, $items])
        @php $active = collect($items)->reject($isHidden)->values(); @endphp
        <x-filament::section>
            <x-slot name="heading">
                {{ $title }}
                <span class="ml-1 text-sm font-normal text-gray-400">({{ $active->count() }})</span>
            </x-slot>
            <x-slot name="description">{{ $hint }}</x-slot>

            @if ($active->isEmpty())
                <p class="text-sm text-success-600 dark:text-success-400">✓ Тут усе заповнено.</p>
            @else
                <ul class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach ($active as $item)
                        <li class="flex items-center justify-between gap-4 py-2">
                            <span class="min-w-0 truncate">
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $item['label'] }}</span>
                                <span class="ml-2 text-xs text-gray-400">{{ $item['meta'] }}</span>
                            </span>
                            <span class="flex shrink-0 items-center gap-3">
                                <a href="{{ $item['url'] }}"
                                   class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-400">Заповнити →</a>
                                <button type="button" wire:click="dismiss(@js($item['key']))" wire:loading.attr="disabled"
                                        class="text-xs text-gray-400 transition hover:text-danger-600 dark:hover:text-danger-400">Приховати</button>
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-filament::section>
    @endforeach

    @if ($hidden->isNotEmpty())
        <x-filament::section collapsible collapsed>
            <x-slot name="heading">
                Приховані вручну
                <span class="ml-1 text-sm font-normal text-gray-400">({{ $hidden->count() }})</span>
            </x-slot>
            <x-slot name="description">Пункти, які ви прибрали вручну. Натисніть «Повернути», щоб показати знову.</x-slot>

            <ul class="divide-y divide-gray-100 dark:divide-white/5">
                @foreach ($hidden as $item)
                    <li class="flex items-center justify-between gap-4 py-2">
                        <span class="min-w-0 truncate text-gray-500 dark:text-gray-400">
                            {{ $item['label'] }}
                            <span class="ml-2 text-xs text-gray-400">{{ $item['meta'] }}</span>
                        </span>
                        <button type="button" wire:click="restore(@js($item['key']))" wire:loading.attr="disabled"
                                class="shrink-0 text-sm font-medium text-primary-600 hover:underline dark:text-primary-400">Повернути</button>
                    </li>
                @endforeach
            </ul>
        </x-filament::section>
    @endif
</x-filament-panels::page>
