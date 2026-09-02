<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A row from the content plan workbook's "<Month> Logic" sheet.
 *
 * Allocation rows describe how many posts a product gets and why; note and source
 * rows carry the methodology paragraphs that sit under the table.
 */
class ContentPlanLogic extends Model
{
    protected $fillable = [
        'period',
        'row_type',
        'product',
        'units',
        'share',
        'share_shift',
        'previous_retail',
        'forecast',
        'posts_planned',
        'pillar_split',
        'rationale',
        'sort_order',
    ];

    protected $casts = [
        'posts_planned' => 'integer',
        'sort_order' => 'integer',
    ];

    public function scopeAllocations($query)
    {
        return $query->where('row_type', 'allocation');
    }

    public function scopeNotes($query)
    {
        return $query->whereIn('row_type', ['note', 'source']);
    }
}
