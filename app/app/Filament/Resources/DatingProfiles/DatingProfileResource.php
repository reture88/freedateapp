<?php

namespace App\Filament\Resources\DatingProfiles;

use App\Filament\Resources\DatingProfiles\Pages\ListDatingProfiles;
use App\Filament\Resources\DatingProfiles\Pages\ViewDatingProfile;
use App\Filament\Resources\DatingProfiles\Schemas\DatingProfileInfolist;
use App\Filament\Resources\DatingProfiles\Tables\DatingProfilesTable;
use App\Models\DatingProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DatingProfileResource extends Resource
{
    protected static ?string $model = DatingProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function infolist(Schema $schema): Schema
    {
        return DatingProfileInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DatingProfilesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDatingProfiles::route('/'),
            'view' => ViewDatingProfile::route('/{record}'),
        ];
    }
}
