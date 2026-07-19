<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfilePhoto extends Model
{
    protected $fillable = [
        'dating_profile_id',
        'path',
        'sort_order',
        'is_primary',
        'moderation_status',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
    ];

    /**
     * @return BelongsTo<DatingProfile, $this>
     */
    public function datingProfile(): BelongsTo
    {
        return $this->belongsTo(DatingProfile::class);
    }

    public function publicUrl(): string
    {
        if (Str::startsWith($this->path, ['http://', 'https://'])) {
            return $this->path;
        }

        return Storage::disk('public')->url($this->path);
    }
}
