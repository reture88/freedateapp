<?php

namespace App\Filament\Resources\DatingProfiles\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class DatingProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('display_name')
                    ->searchable(),
                TextColumn::make('birthdate')
                    ->date()
                    ->sortable(),
                TextColumn::make('gender')
                    ->searchable(),
                TextColumn::make('orientation')
                    ->searchable(),
                TextColumn::make('city')
                    ->searchable(),
                TextColumn::make('country_code')
                    ->searchable(),
                TextColumn::make('approximate_latitude')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('approximate_longitude')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('relationship_intent')
                    ->searchable(),
                TextColumn::make('visibility_status')
                    ->searchable(),
                TextColumn::make('moderation_status')
                    ->searchable(),
                IconColumn::make('ai_wingman_enabled')
                    ->boolean(),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('visibility_status')
                    ->options([
                        'incomplete' => 'Incomplete',
                        'visible' => 'Visible',
                        'hidden' => 'Hidden',
                        'suspended' => 'Suspended',
                        'banned' => 'Banned',
                    ]),
                SelectFilter::make('moderation_status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                TernaryFilter::make('ai_wingman_enabled'),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
