<?php

namespace App\Filament\Pages;

use App\Filament\Support\SettingsFormPage;
use App\Services\TelegramPoster;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

/**
 * Налаштування автопостингу новин у Telegram. Токен — поле-пароль
 * (маскується на екрані; у БД, як і раніше, зберігається як текст).
 * Кнопка «Надіслати тестове повідомлення» бере токен і канал прямо
 * з форми — можна перевірити ще до збереження.
 */
class TelegramSettings extends SettingsFormPage
{
    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Telegram';

    protected static ?string $title = 'Telegram';

    protected static string $settingsGroup = 'telegram';

    protected static function keys(): array
    {
        return ['telegram_autopost', 'telegram_bot_token', 'telegram_channel'];
    }

    protected function fromSettings(array $state): array
    {
        $state['telegram_autopost'] = ($state['telegram_autopost'] ?? '') === '1';

        return $state;
    }

    protected function toSettings(array $state): array
    {
        $state['telegram_autopost'] = ($state['telegram_autopost'] ?? false) ? '1' : '0';

        return $state;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Автопостинг новин')
                    ->description('Опублікована новина автоматично надсилається в Telegram-канал коледжу — одноразово, при першій появі на сайті.')
                    ->schema([
                        Forms\Components\Toggle::make('telegram_autopost')
                            ->label('Надсилати нові новини в Telegram')
                            ->helperText('Працює лише коли заповнені токен бота і канал нижче.'),
                        Forms\Components\TextInput::make('telegram_bot_token')
                            ->label('Токен бота')
                            ->password()
                            ->revealable()
                            ->helperText('Токен від @BotFather (вигляд: 1234567890:AA…). Бот має бути адміністратором каналу.'),
                        Forms\Components\TextInput::make('telegram_channel')
                            ->label('Канал')
                            ->helperText('@назва_каналу або числовий ID (-100…).'),
                    ]),
            ])
            ->statePath('data');
    }

    /** @return array<Action> */
    public function getFormActions(): array
    {
        return [
            Action::make('save')->label('Зберегти')->submit('save'),
            Action::make('sendTest')
                ->label('Надіслати тестове повідомлення')
                ->color('gray')
                ->action('sendTest'),
        ];
    }

    /** Тестова відправка з поточних (навіть незбережених) значень форми. */
    public function sendTest(): void
    {
        $state = $this->form->getState();

        $token = trim((string) ($state['telegram_bot_token'] ?? ''));
        $channel = trim((string) ($state['telegram_channel'] ?? ''));

        if ($token === '' || $channel === '') {
            Notification::make()
                ->title('Заповніть токен бота і канал')
                ->warning()
                ->send();

            return;
        }

        $error = TelegramPoster::sendTest($token, $channel);

        if ($error === null) {
            Notification::make()
                ->title('Тестове повідомлення надіслано')
                ->body('Перевірте канал — повідомлення від бота вже там.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Не вдалося надіслати')
                ->body($error)
                ->danger()
                ->send();
        }
    }
}
