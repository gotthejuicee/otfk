<x-filament-panels::page>
    <x-filament::section
        heading="Затемнення фото"
        description="Наскільки затемнюється зображення під текстом на головній сторінці. Зміни застосовуються одразу після переміщення повзунка."
    >
        {{ $this->overlayForm }}
    </x-filament::section>

    {{ $this->table }}
</x-filament-panels::page>