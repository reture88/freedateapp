<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Connection extends Model
{
    protected $fillable = [
        'profile_one_id',
        'profile_two_id',
        'profile_one_decision',
        'profile_two_decision',
        'status',
        'matched_at',
        'unmatched_at',
        'blocked_at',
    ];

    protected $casts = [
        'matched_at' => 'datetime',
        'unmatched_at' => 'datetime',
        'blocked_at' => 'datetime',
    ];

    /**
     * @param array<string, mixed> $attributes
     */
    public static function createBetween(DatingProfile $first, DatingProfile $second, array $attributes = []): self
    {
        $profileIds = [(int) $first->getKey(), (int) $second->getKey()];
        sort($profileIds);

        return self::query()->create(array_merge($attributes, [
            'profile_one_id' => $profileIds[0],
            'profile_two_id' => $profileIds[1],
        ]));
    }

    /**
     * @return BelongsTo<DatingProfile, $this>
     */
    public function profileOne(): BelongsTo
    {
        return $this->belongsTo(DatingProfile::class, 'profile_one_id');
    }

    /**
     * @return BelongsTo<DatingProfile, $this>
     */
    public function profileTwo(): BelongsTo
    {
        return $this->belongsTo(DatingProfile::class, 'profile_two_id');
    }

    /**
     * @return HasOne<Conversation, $this>
     */
    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class);
    }
}
