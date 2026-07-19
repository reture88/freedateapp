<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfileTagCategory extends Model
{
    protected $fillable = [
        'slug',
        'label',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * @return HasMany<ProfileTag, $this>
     */
    public function tags(): HasMany
    {
        return $this->hasMany(ProfileTag::class)->orderBy('sort_order')->orderBy('label');
    }
}
