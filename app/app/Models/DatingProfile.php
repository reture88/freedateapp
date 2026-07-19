<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DatingProfile extends Model
{
    protected $fillable = [
        'user_id',
        'display_name',
        'birthdate',
        'gender',
        'orientation',
        'city',
        'country_code',
        'approximate_latitude',
        'approximate_longitude',
        'relationship_intent',
        'bio',
        'visibility_status',
        'moderation_status',
        'ai_wingman_enabled',
        'completed_at',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'approximate_latitude' => 'float',
        'approximate_longitude' => 'float',
        'ai_wingman_enabled' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<ProfilePhoto, $this>
     */
    public function photos(): HasMany
    {
        return $this->hasMany(ProfilePhoto::class);
    }

    /**
     * @return HasOne<ProfilePreference, $this>
     */
    public function preference(): HasOne
    {
        return $this->hasOne(ProfilePreference::class);
    }

    /**
     * @return BelongsToMany<ProfileTag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ProfileTag::class, 'dating_profile_tags')
            ->withPivot(['visibility', 'sort_order'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    /**
     * @return HasMany<DiscoveryDecision, $this>
     */
    public function discoveryDecisionsMade(): HasMany
    {
        return $this->hasMany(DiscoveryDecision::class, 'viewer_profile_id');
    }

    /**
     * @return HasMany<DiscoveryDecision, $this>
     */
    public function discoveryDecisionsReceived(): HasMany
    {
        return $this->hasMany(DiscoveryDecision::class, 'target_profile_id');
    }
}
