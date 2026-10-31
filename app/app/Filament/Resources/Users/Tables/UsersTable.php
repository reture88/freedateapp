<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\User\Enum\AccountStatus;
use App\User\RequestHandler\AdminModerateUser\AdminModerateUserCommand;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('role')
                    ->searchable(),
                TextColumn::make('is_admin')
                    ->formatStateUsing(static fn (bool $state): string => $state ? 'Admin' : 'Customer'),
                TextColumn::make('account_status')
                    ->label('Account status')
                    ->badge()
                    ->formatStateUsing(static fn (AccountStatus $state): string => $state->label())
                    ->sortable(),
                TextColumn::make('deletion_requested_at')
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
                SelectFilter::make('role')
                    ->options([
                        'customer' => 'Customer',
                        'admin' => 'Admin',
                    ]),
                SelectFilter::make('account_status')
                    ->label('Account status')
                    ->options(array_reduce(
                        AccountStatus::cases(),
                        static function (array $options, AccountStatus $status): array {
                            $options[$status->value] = $status->label();

                            return $options;
                        },
                        [],
                    )),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('suspend')
                    ->requiresConfirmation()
                    ->visible(static fn (User $record): bool => $record->hasActiveAccount() && !$record->canAccessAdminPanel())
                    ->action(static fn (User $record): mixed => self::dispatchUserModerationAction($record, 'suspend')),
                Action::make('ban')
                    ->requiresConfirmation()
                    ->visible(static fn (User $record): bool => AccountStatus::BANNED !== $record->accountStatus() && !$record->canAccessAdminPanel())
                    ->action(static fn (User $record): mixed => self::dispatchUserModerationAction($record, 'ban')),
                Action::make('deactivate')
                    ->requiresConfirmation()
                    ->visible(static fn (User $record): bool => AccountStatus::DEACTIVATED !== $record->accountStatus() && !$record->canAccessAdminPanel())
                    ->action(static fn (User $record): mixed => self::dispatchUserModerationAction($record, 'deactivate')),
                Action::make('restore')
                    ->requiresConfirmation()
                    ->visible(static fn (User $record): bool => !$record->hasActiveAccount())
                    ->action(static fn (User $record): mixed => self::dispatchUserModerationAction($record, 'restore')),
            ])
            ->toolbarActions([]);
    }

    private static function dispatchUserModerationAction(User $target, string $action): mixed
    {
        $actor = auth()->user();

        if (!$actor instanceof User) {
            return null;
        }

        return app(CommandBusInterface::class)->dispatchPayload(AdminModerateUserCommand::class, [
            'actor_user_id' => $actor->getKey(),
            'target_user_id' => $target->getKey(),
            'action' => $action,
        ], new SystemContext((string) $actor->getKey(), request()->ip()));
    }
}
