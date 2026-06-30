<?php

namespace App\Models\Admin\MasterData;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class CostHead extends BaseModel
{
    protected $table = 'cost_heads';

    protected $fillable = [
        'cost_category',
        'cost_head',
        'unit_name',
        'unit_price',
        'cost_head_code',
        'cost_head_dis_code',
        'is_active',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];

      /**
     * Get the parent category.
     * The 'parent_category' column points to the 'category_code' of another row.
     */
    public function category()
    {
        return $this->belongsTo(CostCategory::class, 'cost_category', 'category_code');
    }
}
