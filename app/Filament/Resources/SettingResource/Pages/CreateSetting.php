<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    // Зберігаємо шлях завантаженого зображення у колонку value.
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['type'] ?? null) === 'image') {
            $data['value'] = $data['image_value'] ?? null;
        }
        unset($data['image_value']);

        return $data;
    }
}
