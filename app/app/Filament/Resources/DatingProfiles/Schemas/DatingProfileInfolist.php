<?php

namespace App\Filament\Resources\DatingProfiles\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DatingProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('display_name'),
                TextEntry::make('birthdate')
                    ->date(),
                TextEntry::make('gender'),
                TextEntry::make('orientation'),
                TextEntry::make('city'),
                TextEntry::make('country_code'),
                TextEntry::make('approximate_latitude')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('approximate_longitude')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('relationship_intent'),
                TextEntry::make('bio')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('visibility_status'),
                TextEntry::make('moderation_status'),
                IconEntry::make('ai_wingman_enabled')
                    ->boolean(),
                TextEntry::make('completed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
