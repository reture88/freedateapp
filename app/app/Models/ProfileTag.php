<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProfileTag extends Model
{
    protected $fillable = [
        'profile_tag_category_id',
        'slug',
        'label',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * @return BelongsTo<ProfileTagCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProfileTagCategory::class, 'profile_tag_category_id');
    }

    /**
     * @return BelongsToMany<DatingProfile, $this>
     */
    public function datingProfiles(): BelongsToMany
    {
        return $this->belongsToMany(DatingProfile::class, 'dating_profile_tags')
            ->withPivot(['visibility', 'sort_order'])
            ->withTimestamps();
    }
}
