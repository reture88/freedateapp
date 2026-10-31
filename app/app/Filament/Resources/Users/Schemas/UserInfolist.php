<?php

namespace App\Filament\Resources\Users\Schemas;

use App\User\Enum\AccountStatus;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('role'),
                IconEntry::make('is_admin')
                    ->boolean(),
                TextEntry::make('account_status')
                    ->label('Account status')
                    ->badge()
                    ->formatStateUsing(static fn (AccountStatus $state): string => $state->label()),
                TextEntry::make('deletion_requested_at')
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
