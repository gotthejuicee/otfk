<?php

namespace App\Filament\Support;

use App\Models\News;
use App\Models\Page;
use App\Support\AdminPreview;
use Filament\Actions\Action;

/**
 * Кнопка «Превʼю» на формах створення/редагування сторінок і новин:
 * поточний (ще не збережений) стан форми кладеться у слепок AdminPreview,
 * і публічний шаблон відкривається в новій вкладці — форма лишається як є.
 */
class PreviewFormAction
{
    /** @param 'page'|'news' $type */
    public static function make(string $type): Action
    {
        return Action::make('previewDraft')
            ->label('Превʼю')
            ->icon('heroicon-o-eye')
            ->color('gray')
            ->action(function ($livewire) use ($type) {
                $model = $type === 'news' ? News::class : Page::class;
                $base = $livewire->record ?? new $model;

                $token = AdminPreview::store($type, $base, $livewire->form->getRawState());

                $livewire->js('window.open(' . json_encode(route('admin.preview', $token)) . ", '_blank')");
            });
    }
}
