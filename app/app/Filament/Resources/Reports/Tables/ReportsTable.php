<?php

namespace App\Filament\Resources\Reports\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reporter_user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('target_type')
                    ->searchable(),
                TextColumn::make('target_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('category')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('reviewer_user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('resolution')
                    ->searchable(),
                TextColumn::make('resolved_at')
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
                SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'resolved' => 'Resolved',
                        'dismissed' => 'Dismissed',
                    ]),
                SelectFilter::make('target_type')
                    ->options([
                        'profile' => 'Profile',
                        'message' => 'Message',
                        'assistant_message' => 'Assistant message',
                        'user' => 'User',
                    ]),
                SelectFilter::make('category')
                    ->options([
                        'profile_concern' => 'Profile concern',
                        'message_concern' => 'Message concern',
                        'safety' => 'Safety',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
