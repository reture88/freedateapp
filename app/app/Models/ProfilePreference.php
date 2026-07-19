<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilePreference extends Model
{
    protected $fillable = [
        'dating_profile_id',
        'preferred_age_min',
        'preferred_age_max',
        'preferred_distance_km',
        'preferred_genders',
        'preferred_orientations',
        'relationship_intents',
        'exclude_ai_wingman_enabled',
        'dealbreakers',
        'soft_preferences',
    ];

    protected $casts = [
        'preferred_age_min' => 'integer',
        'preferred_age_max' => 'integer',
        'preferred_distance_km' => 'integer',
        'preferred_genders' => 'array',
        'preferred_orientations' => 'array',
        'relationship_intents' => 'array',
        'exclude_ai_wingman_enabled' => 'boolean',
        'dealbreakers' => 'array',
        'soft_preferences' => 'array',
    ];

    /**
     * @return BelongsTo<DatingProfile, $this>
     */
    public function datingProfile(): BelongsTo
    {
        return $this->belongsTo(DatingProfile::class);
    }
}
