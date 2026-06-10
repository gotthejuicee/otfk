<?php

namespace App\Filament\Resources\ApplicantRequestResource\Pages;

use App\Filament\Resources\ApplicantRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplicantRequest extends EditRecord
{
    protected static string $resource = ApplicantRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
