<?php

namespace App\Http\Controllers\Admin\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Admin\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(){
        $data = Vendor::where('is_active',1)->get();
        return view('admin.master-data.vendor.index',compact('data'));
    }
}
