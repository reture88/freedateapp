<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscoveryDecision extends Model
{
    protected $fillable = [
        'viewer_profile_id',
        'target_profile_id',
        'decision',
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<DatingProfile, $this>
     */
    public function viewerProfile(): BelongsTo
    {
        return $this->belongsTo(DatingProfile::class, 'viewer_profile_id');
    }

    /**
     * @return BelongsTo<DatingProfile, $this>
     */
    public function targetProfile(): BelongsTo
    {
        return $this->belongsTo(DatingProfile::class, 'target_profile_id');
    }
}
