<?php

namespace App\Filament\Resources\FeedbackMessageResource\Pages;

use App\Filament\Resources\FeedbackMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeedbackMessages extends ListRecords
{
    protected static string $resource = FeedbackMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
