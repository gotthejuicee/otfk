<?php

namespace App\Filament\Resources\FeedbackMessageResource\Pages;

use App\Filament\Resources\FeedbackMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFeedbackMessage extends CreateRecord
{
    protected static string $resource = FeedbackMessageResource::class;
}
