<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EventNotificationDelivery extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_ids',
        'target_date',
        'days_ahead',
        'status',
        'attempts',
        'last_error',
        'processing_started_at',
        'sent_at',
    ];

    protected $casts = [
        'user_ids' => 'array',
        'target_date' => 'date',
        'days_ahead' => 'integer',
        'attempts' => 'integer',
        'processing_started_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SENT);
    }
}
