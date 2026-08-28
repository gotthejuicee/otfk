<?php

namespace App\Filament\Pages;

use App\Filament\Support\SettingsFormPage;
use App\Filament\Support\ViewOnSite;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\HtmlString;

/**
 * Термінове оголошення — кольорова смуга над шапкою на всіх сторінках сайту.
 * Кольори прев'ю продубльовано з app.blade.php (bg-brand-700 / gold-500 /
 * red-600), бо стилів публічного сайту в адмінці немає.
 */
class AnnouncementSettings extends SettingsFormPage
{
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Оголошення';

    protected static ?string $title = 'Оголошення';

    protected static string $settingsGroup = 'announcement';

    /** Приблизні кольори смуги для прев'ю (як на сайті). */
    protected const PREVIEW_COLORS = [
        'info' => '#284aaa',
        'warning' => '#d98e1e',
        'danger' => '#dc2626',
    ];

    protected static function keys(): array
    {
        return ['announcement_text', 'announcement_type', 'announcement_url'];
    }

    protected function fromSettings(array $state): array
    {
        $state['announcement_type'] = $state['announcement_type'] ?: 'info';

        return $state;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Смуга оголошення')
                    ->description('Кольорова смуга над шапкою на всіх сторінках сайту. Порожній текст — смуги немає. Відвідувач може закрити смугу хрестиком; після зміни тексту вона з’явиться знову.')
                    ->schema([
                        Forms\Components\Textarea::make('announcement_text')
                            ->label('Текст')
                            ->rows(2)
                            ->live(debounce: 400),
                        Forms\Components\Select::make('announcement_type')
                            ->label('Колір смуги')
                            ->options([
                                'info' => 'Синя — інформація',
                                'warning' => 'Золота — важливо',
                                'danger' => 'Червона — терміново',
                            ])
                            ->selectablePlaceholder(false)
                            ->live(),
                        Forms\Components\TextInput::make('announcement_url')
                            ->label('Посилання (необов’язково)')
                            ->url()
                            ->helperText('Куди веде клік по оголошенню, напр. адреса новини.'),
                        Forms\Components\Placeholder::make('preview')
                            ->label('Попередній перегляд')
                            ->visible(fn (Forms\Get $get) => filled(trim((string) $get('announcement_text'))))
                            ->content(function (Forms\Get $get) {
                                $color = self::PREVIEW_COLORS[$get('announcement_type')] ?? self::PREVIEW_COLORS['info'];

                                return new HtmlString(
                                    '<div style="background:'.$color.';color:#fff;padding:10px 16px;border-radius:8px;text-align:center;font-size:14px;font-weight:500;">📢 '
                                    .e(trim((string) $get('announcement_text')))
                                    .'</div>'
                                );
                            }),
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
