<?php

namespace App\Models\Admin\MasterData;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class CostCategory extends BaseModel
{
    // Explicitly defining the table name
    protected $table = 'cost_categories';

    protected $fillable = [
        'company',
        'parent_category',
        'parent_category_str',
        'category_name',
        'category_code',
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
    public function parent()
    {
        return $this->belongsTo(CostCategory::class, 'parent_category', 'category_code');
    }

    /**
     * Get the sub-categories (children).
     */
    public function children()
    {
        return $this->hasMany(CostCategory::class, 'parent_category', 'category_code');
    }
}
