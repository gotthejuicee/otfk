<?php

namespace App\Filament\Support;

use App\Models\News;
use App\Models\Page;
use App\Support\AdminPreview;
use Filament\Actions\Action;
use Filament\Support\Enums\MaxWidth;

/**
 * Кнопка «Превʼю» на формах створення/редагування сторінок і новин:
 * поточний (ще не збережений) стан форми кладеться у слепок AdminPreview
 * і публічний шаблон показується в iframe у slide-over поруч із формою
 * (з посиланням «відкрити у новій вкладці») — форма лишається як є.
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
            ->slideOver()
            ->modalWidth(MaxWidth::SixExtraLarge)
            ->modalHeading('Попередній перегляд')
            ->modalDescription('Показано поточний стан форми — нічого не збережено.')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Закрити')
            ->modalContent(function ($livewire) use ($type) {
                $model = $type === 'news' ? News::class : Page::class;
                $base = $livewire->record ?? new $model;

                $token = AdminPreview::store($type, $base, $livewire->form->getRawState());

                return view('filament.support.preview-frame', [
                    'url' => route('admin.preview', $token),
                ]);
            });
    }
}
