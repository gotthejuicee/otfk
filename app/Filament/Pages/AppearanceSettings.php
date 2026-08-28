<?php

namespace App\Filament\Pages;

use App\Filament\Support\SettingsFormPage;
use App\Filament\Support\ViewOnSite;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;

/**
 * Тексти підвалу та позначка версії сайту. Затемнення фото банерів
 * сюди свідомо не винесено — воно редагується в розділі «Банери»
 * (одне місце керування, див. ADMIN-UX-PLAN Етап 2 п. 4).
 */
class AppearanceSettings extends SettingsFormPage
{
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Підвал і вигляд';

    protected static ?string $title = 'Підвал і вигляд';

    protected static string $settingsGroup = 'appearance';

    protected static function keys(): array
    {
        return ['footer_about', 'site_version_label', 'site_version_color'];
    }

    protected function fromSettings(array $state): array
    {
        $state['site_version_color'] = $state['site_version_color'] ?: 'gold';

        return $state;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Підвал сайту')
                    ->schema([
                        Forms\Components\Textarea::make('footer_about')
                            ->label('Текст «Про коледж»')
                            ->rows(3)
                            ->helperText('Абзац під назвою коледжу в підвалі. Посилання-партнери редагуються у розділі «Швидкі посилання» (локація «Партнер у підвалі»).'),
                    ]),
                Forms\Components\Section::make('Позначка версії сайту')
                    ->description('Маленький бейдж у нижньому рядку підвалу (напр. «Альфа-версія»). Порожній напис — бейдж приховано.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('site_version_label')
                            ->label('Напис'),
                        Forms\Components\Select::make('site_version_color')
                            ->label('Колір')
                            ->options([
                                'gold' => 'Золотий',
                                'green' => 'Зелений',
                                'blue' => 'Синій',
                                'red' => 'Червоний',
                                'gray' => 'Сірий',
                            ])
                            ->selectablePlaceholder(false),
                    ]),
                Forms\Components\Section::make('Банери')
                    ->schema([
                        Forms\Components\Placeholder::make('banner_note')
                            ->hiddenLabel()
                            ->content('Затемнення фото банерів головної редагується в розділі «Контент → Банери» (поле «Сила затемнення» над таблицею).'),
                    ]),
            ])
            ->statePath('data');
    }

    /** @return array<Action> */
    protected function getHeaderActions(): array
    {
        return [
            ViewOnSite::header(url('/')),
        ];
    }
}
