<?php

namespace App\Filament\Resources\BellPeriodResource\Pages;

use App\Filament\Resources\BellPeriodResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBellPeriod extends EditRecord
{
    protected static string $resource = BellPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
