<?php

namespace App\Filament\Resources\Reports\Tables;

use App\Models\Report;
use App\Models\User;
use App\Safety\RequestHandler\AdminModerateReport\AdminModerateReportCommand;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use Filament\Actions\Action;
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
                Action::make('resolve')
                    ->requiresConfirmation()
                    ->visible(static fn (Report $record): bool => 'open' === $record->status)
                    ->action(static fn (Report $record): mixed => self::dispatchModerationAction($record, 'resolve', 'resolved_from_filament')),
                Action::make('hide_profile')
                    ->label('Hide profile')
                    ->requiresConfirmation()
                    ->visible(static fn (Report $record): bool => 'open' === $record->status && 'profile' === $record->target_type)
                    ->action(static fn (Report $record): mixed => self::dispatchModerationAction($record, 'hide_profile', 'profile_hidden_from_filament')),
            ])
            ->toolbarActions([]);
    }

    private static function dispatchModerationAction(Report $report, string $action, string $resolution): mixed
    {
        $user = auth()->user();

        if (!$user instanceof User) {
            return null;
        }

        return app(CommandBusInterface::class)->dispatchPayload(AdminModerateReportCommand::class, [
            'actor_user_id' => $user->getKey(),
            'report_id' => $report->getKey(),
            'action' => $action,
            'resolution' => $resolution,
        ], new SystemContext((string) $user->getKey(), request()->ip()));
    }
}
