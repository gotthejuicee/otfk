<?php

namespace App\Filament\Resources\StatItemResource\Pages;

use App\Filament\Resources\StatItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStatItems extends ListRecords
{
    protected static string $resource = StatItemResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
