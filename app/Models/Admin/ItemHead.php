<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class ItemHead extends BaseModel
{
    protected $table = 'item_heads';

    protected $fillable = [
        'item_category',
        'item_head',
        'unit_name',
        'unit_price',
        'item_head_code',
        'item_head_dis_code',
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
        return $this->belongsTo(ItemCategory::class, 'item_category', 'category_code');
    }
}
