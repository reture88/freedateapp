<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model
{
    protected $fillable = [
        'blocker_user_id',
        'blocked_user_id',
        'reason_category',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function blocker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocker_user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function blocked(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_user_id');
    }
}
