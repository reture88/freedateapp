<?php

namespace App\Filament\Resources\MetricEvents\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MetricEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_name')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('occurred_at')
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
                SelectFilter::make('event_name')
                    ->options([
                        'registration_completed' => 'Registration completed',
                        'profile_completed' => 'Profile completed',
                        'discovery_decision' => 'Discovery decision',
                        'match_created' => 'Match created',
                        'first_message_sent' => 'First message sent',
                        'report_submitted' => 'Report submitted',
                        'user_blocked' => 'User blocked',
                        'connection_unmatched' => 'Connection unmatched',
                        'assistant_starter_posted' => 'Assistant starter posted',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
