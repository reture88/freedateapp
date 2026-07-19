<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
