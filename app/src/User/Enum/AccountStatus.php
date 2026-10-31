<?php

declare(strict_types=1);

namespace App\User\Enum;

enum AccountStatus: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case BANNED = 'banned';
    case DEACTIVATED = 'deactivated';
    case DELETION_REQUESTED = 'deletion_requested';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::SUSPENDED => 'Suspended',
            self::BANNED => 'Banned',
            self::DEACTIVATED => 'Deactivated',
            self::DELETION_REQUESTED => 'Deletion requested',
        };
    }

    public function canUseProduct(): bool
    {
        return self::ACTIVE === $this;
    }
}
