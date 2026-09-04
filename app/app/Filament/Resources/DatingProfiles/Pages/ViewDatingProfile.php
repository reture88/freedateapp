<?php

namespace App\Filament\Resources\DatingProfiles\Pages;

use App\Filament\Resources\DatingProfiles\DatingProfileResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDatingProfile extends ViewRecord
{
    protected static string $resource = DatingProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
