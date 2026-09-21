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
        'financial_budget',
        'drive_link',
        'shoot_date',
        'color_concern',
        'platform',
        'product',
        'post_no',
        'product_focus',
        'status',
        'feedback',
        'sort_order',
        'source_key',
        'source_sheet',
    ];

    protected $attributes = [
        'boosting_budget' => '0',
        'financial_budget' => '0',
        'status' => 'not_done',
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

    public function setFinancialBudgetAttribute($value)
    {
        $this->attributes['financial_budget'] = (is_null($value) || trim((string)$value) === '') ? '0' : trim((string)$value);
    }

    public function getFinancialBudgetAttribute($value)
    {
        return (is_null($value) || trim((string)$value) === '') ? '0' : $value;
    }

    protected static function booted(): void
    {
        static::creating(function (CalendarEvent $event) {
            if ($event->status === null || $event->status === '') {
                $event->status = 'not_done';
            }

            if (! array_key_exists('sort_order', $event->getAttributes()) || $event->sort_order === null) {
                $event->sort_order = (int) static::where('status', $event->status)->max('sort_order') + 1;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function boardColumn(): string
    {
        return match ($this->status) {
            'done' => 'done',
            'in_progress' => 'in_progress',
            default => 'not_done',
        };
    }

    public function displayTitle(): string
    {
        return $this->content_title
            ?: ($this->post_no ? 'Post #'.$this->post_no : 'Untitled Event');
    }

    public function teamLabel(): string
    {
        return match ($this->team_type) {
            'product_team' => 'Product',
            'digital_team' => 'Digital',
            'brand_team' => 'Brand',
            'service_team' => 'Service',
            'global_team' => 'Global',
            default => ucwords(str_replace('_', ' ', (string) $this->team_type)),
        };
    }

    public function teamBadgeClasses(): string
    {
        return match ($this->team_type) {
            'product_team' => 'bg-amber-50 text-amber-700 border-amber-200',
            'digital_team' => 'bg-purple-50 text-purple-700 border-purple-200',
            'brand_team' => 'bg-sky-50 text-sky-700 border-sky-200',
            'service_team' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'global_team' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }

    /**
     * Numeric budget used for sorting. Parses values like "$400.00", "15,000",
     * or "15000 each Total 200,000" and returns the largest amount found.
     */
    public function budgetAmount(?string $field = 'total'): float
    {
        return match ($field) {
            'financial' => self::parseMoney($this->financial_budget),
            'boosting' => self::parseMoney($this->boosting_budget),
            default => self::parseMoney($this->financial_budget) + self::parseMoney($this->boosting_budget),
        };
    }

    public static function parseMoney($value): float
    {
        if ($value === null) {
            return 0.0;
        }

        $raw = trim((string) $value);
        if ($raw === '' || $raw === '0') {
            return 0.0;
        }

        if (!preg_match_all('/\d[\d,]*(?:\.\d+)?/', $raw, $matches)) {
            return 0.0;
        }

        $amounts = array_map(
            fn ($n) => (float) str_replace(',', '', $n),
            $matches[0]
        );

        return $amounts ? max($amounts) : 0.0;
    }
}
