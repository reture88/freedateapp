<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    public const TYPE_SHIPPING = 'shipping';
    public const TYPE_BILLING = 'billing';

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'country',
        'postal_code',
        'city',
        'street',
        'phone',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return list<string>
     */
    public static function allowedTypes(): array
    {
        return [self::TYPE_SHIPPING, self::TYPE_BILLING];
    }
}
