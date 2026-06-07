<?php

namespace App\Filament\Resources\FeedbackMessageResource\Pages;

use App\Filament\Resources\FeedbackMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeedbackMessage extends EditRecord
{
    protected static string $resource = FeedbackMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
