<x-filament-panels::page>
    @php
        $groups = [
            ['Сторінки без змісту', 'Порожні або дуже короткі сторінки — додайте текст і документи', $this->stubPages()],
            ['Порожні категорії документів', 'Категорії без жодного завантаженого PDF', $this->emptyDocumentCategories()],
            ['Відділення та комісії', 'Без персоналу або з надто коротким описом', $this->departmentsNeedingWork()],
            ['Порожні галереї', 'Альбоми без фотографій', $this->emptyGalleries()],
            ['Незаповнені налаштування', 'Контакти, соцмережі, карта на сторінці контактів', $this->missingSettings()],
        ];
        $total = collect($groups)->sum(fn ($g) => count($g[2]));
    @endphp

    <div class="text-sm text-gray-500 dark:text-gray-400">
        @if ($total === 0)
            🎉 Усе наповнено — порожніх місць не знайдено.
        @else
            Потребують уваги: <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $total }}</span>.
            Натискайте «Заповнити», щоб одразу відкрити редактор.
        @endif
    </div>

    @foreach ($groups as [$title, $hint, $items])
        <x-filament::section>
            <x-slot name="heading">
                {{ $title }}
                <span class="ml-1 text-sm font-normal text-gray-400">({{ count($items) }})</span>
            </x-slot>
            <x-slot name="description">{{ $hint }}</x-slot>

            @if (count($items) === 0)
                <p class="text-sm text-success-600 dark:text-success-400">✓ Тут усе заповнено.</p>
            @else
                <ul class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach ($items as $item)
                        <li class="flex items-center justify-between gap-4 py-2">
                            <span class="min-w-0 truncate">
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $item['label'] }}</span>
                                <span class="ml-2 text-xs text-gray-400">{{ $item['meta'] }}</span>
                            </span>
                            <a href="{{ $item['url'] }}"
                               class="shrink-0 text-sm font-medium text-primary-600 hover:underline dark:text-primary-400">Заповнити →</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-filament::section>
    @endforeach
</x-filament-panels::page>
