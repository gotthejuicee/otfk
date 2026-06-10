<?php

namespace App\Filament\Resources\BellPeriodResource\Pages;

use App\Filament\Resources\BellPeriodResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBellPeriods extends ListRecords
{
    protected static string $resource = BellPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
