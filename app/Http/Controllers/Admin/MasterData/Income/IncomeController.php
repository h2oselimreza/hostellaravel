<?php

namespace App\Http\Controllers\Admin\MasterData\Income;

use App\Http\Controllers\Controller;
use App\Models\Admin\ItemCategory;
use App\Models\Admin\ItemHead;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index(){
        $categoryCount = ItemCategory::count();
        $headCount = ItemHead::count();
        return view('admin.master-data.income-head.index',compact('categoryCount','headCount'));
    }
}
