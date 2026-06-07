<?php

namespace App\Filament\Resources\QuickLinkResource\Pages;

use App\Filament\Resources\QuickLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuickLinks extends ListRecords
{
    protected static string $resource = QuickLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
