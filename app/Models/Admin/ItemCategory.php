<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class ItemCategory extends BaseModel
{
    protected $table = 'item_categories';

    protected $fillable = [
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
        return $this->belongsTo(ItemCategory::class, 'parent_category', 'category_code');
    }

    /**
     * Get the sub-categories (children).
     */
    public function children()
    {
        return $this->hasMany(ItemCategory::class, 'parent_category', 'category_code');
    }
}
