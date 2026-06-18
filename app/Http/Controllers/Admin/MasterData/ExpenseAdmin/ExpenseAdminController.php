<?php

namespace App\Http\Controllers\Admin\MasterData\ExpenseAdmin;

use App\Http\Controllers\Controller;
use App\Models\Admin\MasterData\CostCategory;
use App\Models\Admin\MasterData\CostHead;
use Illuminate\Http\Request;

class ExpenseAdminController extends Controller
{
    public function index(){
        $costCategoryCount = CostCategory::count();
        $costHeadCount = CostHead::count();
        return view('admin.master-data.expense-head.index',compact('costCategoryCount','costHeadCount'));
    }
}
