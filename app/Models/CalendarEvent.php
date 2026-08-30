<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    protected $fillable = [
        'user_id',
        'team_type',
        'event_date',
        'content_title',
        'aipe_pillar',
        'content_objective',
        'format',
        'remarks',
        'boosting_budget',
        'drive_link',
        'shoot_date',
        'color_concern',
        'platform',
        'product',
        'post_no',
        'product_focus',
    ];

    protected $attributes = [
        'boosting_budget' => '0',
    ];

    protected $casts = [
        'event_date' => 'date',
        'shoot_date' => 'date',
    ];

    public function setBoostingBudgetAttribute($value)
    {
        $this->attributes['boosting_budget'] = (is_null($value) || trim((string)$value) === '') ? '0' : trim((string)$value);
    }

    public function getBoostingBudgetAttribute($value)
    {
        return (is_null($value) || trim((string)$value) === '') ? '0' : $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
