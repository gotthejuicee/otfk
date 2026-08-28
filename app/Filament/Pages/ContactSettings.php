<?php

namespace App\Filament\Pages;

use App\Filament\Support\SettingsFormPage;
use App\Filament\Support\ViewOnSite;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;

/**
 * Контакти та соцмережі одним екраном — замість пошуку ключів
 * contact_* / social_* у сирому списку налаштувань.
 */
class ContactSettings extends SettingsFormPage
{
    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Контакти та соцмережі';

    protected static ?string $title = 'Контакти та соцмережі';

    protected static string $settingsGroup = 'contacts';

    protected static function keys(): array
    {
        return [
            'contact_address', 'contact_phone', 'contact_email', 'work_hours',
            'map_embed',
            'social_facebook', 'social_instagram', 'social_youtube',
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Контактні дані')
                    ->description('Показуються у шапці, підвалі та на сторінці «Контакти».')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('contact_address')
                            ->label('Адреса')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('contact_phone')
                            ->label('Телефон')
                            ->helperText('Формат вільний, напр. +38 (048) 123-45-67.'),
                        Forms\Components\TextInput::make('contact_email')
                            ->label('E-mail')
                            ->email(),
                        Forms\Components\TextInput::make('work_hours')
                            ->label('Години роботи')
                            ->helperText('Напр. «Пн–Пт 8:30–17:00».')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Карта')
                    ->schema([
                        Forms\Components\Textarea::make('map_embed')
                            ->label('Код вбудованої карти')
                            ->rows(3)
                            ->helperText('Google Maps → «Поділитися» → «Вставлення карти» → скопіювати код iframe. Порожнє — карти на сторінці «Контакти» немає.'),
                    ]),
                Forms\Components\Section::make('Соцмережі')
                    ->description('Іконки у шапці та підвалі сайту; порожнє посилання — іконку приховано.')
                    ->schema([
                        Forms\Components\TextInput::make('social_facebook')
                            ->label('Facebook')
                            ->url(),
                        Forms\Components\TextInput::make('social_instagram')
                            ->label('Instagram')
                            ->url(),
                        Forms\Components\TextInput::make('social_youtube')
                            ->label('YouTube')
                            ->url()
                            ->helperText('Показується також блоком-закликом на сторінці «Відео»; порожнє — блок приховано.'),
                    ]),
            ])
            ->statePath('data');
    }

    /** @return array<Action> */
    protected function getHeaderActions(): array
    {
        return [
            ViewOnSite::header(route('contacts')),
        ];
    }
}
