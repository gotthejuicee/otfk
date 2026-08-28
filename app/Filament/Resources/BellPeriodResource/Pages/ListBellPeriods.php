<?php

namespace App\Filament\Resources\BellPeriodResource\Pages;

use App\Filament\Resources\BellPeriodResource;
use App\Models\BellPeriod;
use App\Models\Setting;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListBellPeriods extends ListRecords
{
    protected static string $resource = BellPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Швидкий перемикач другої зміни: пари лишаються в базі, просто зникають із сайту.
            Actions\Action::make('toggleSecondShift')
                ->label(fn () => BellPeriod::secondShiftEnabled() ? 'Вимкнути другу зміну' : 'Увімкнути другу зміну')
                ->icon(fn () => BellPeriod::secondShiftEnabled() ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                ->color(fn () => BellPeriod::secondShiftEnabled() ? 'gray' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn () => BellPeriod::secondShiftEnabled() ? 'Сховати другу зміну?' : 'Показати другу зміну?')
                ->modalDescription('Пари другої зміни лишаються в базі — змінюється лише те, чи видно їх на сайті.')
                ->action(function () {
                    $enabled = BellPeriod::secondShiftEnabled();

                    Setting::updateOrCreate(
                        ['key' => BellPeriod::SECOND_SHIFT_SETTING],
                        ['value' => $enabled ? '0' : '1', 'group' => 'general', 'type' => 'text'],
                    );

                    Notification::make()
                        ->title($enabled ? 'Другу зміну сховано із сайту' : 'Другу зміну показано на сайті')
                        ->success()->send();
                }),
            Actions\CreateAction::make(),
        ];
    }

    public function getSubheading(): ?string
    {
        return BellPeriod::secondShiftEnabled()
            ? 'Друга зміна зараз показується на сайті.'
            : 'Друга зміна зараз прихована — на сайті видно лише пари першої зміни.';
    }
}
