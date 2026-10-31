<?php

declare(strict_types=1);

namespace App\Models;

use App\User\Enum\AccountStatus;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'role',
        'is_admin',
        'is_active',
        'account_status',
        'deletion_requested_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'account_status' => AccountStatus::class,
        'deletion_requested_at' => 'datetime',
    ];

    public function canAccessAdminPanel(): bool
    {
        $role = $this->getAttribute('role');

        return $this->hasActiveAccount() && (true === $this->getAttribute('is_admin') || 'admin' === $role);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return 'admin' === $panel->getId() && $this->canAccessAdminPanel();
    }

    public function accountStatus(): AccountStatus
    {
        $status = $this->getAttribute('account_status');

        if ($status instanceof AccountStatus) {
            return $status;
        }

        if (is_string($status) && '' !== $status) {
            return AccountStatus::from($status);
        }

        if (null !== $this->getAttribute('deletion_requested_at')) {
            return AccountStatus::DELETION_REQUESTED;
        }

        return true === $this->getAttribute('is_active') ? AccountStatus::ACTIVE : AccountStatus::DEACTIVATED;
    }

    public function hasActiveAccount(): bool
    {
        return $this->accountStatus()->canUseProduct();
    }

    /**
     * @return HasMany<UserAddress, $this>
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    /**
     * @return HasOne<DatingProfile, $this>
     */
    public function datingProfile(): HasOne
    {
        return $this->hasOne(DatingProfile::class);
    }
}
