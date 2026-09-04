<?php

declare(strict_types=1);

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_admin',
        'is_active',
        'deletion_requested_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_active' => 'boolean',
        'deletion_requested_at' => 'datetime',
    ];

    public function canAccessAdminPanel(): bool
    {
        $role = $this->getAttribute('role');

        return true === $this->getAttribute('is_admin') || 'admin' === $role;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return 'admin' === $panel->getId() && $this->canAccessAdminPanel();
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
