<?php

namespace App\Filament\Resources\QuickLinkResource\Pages;

use App\Filament\Resources\QuickLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuickLink extends EditRecord
{
    protected static string $resource = QuickLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
