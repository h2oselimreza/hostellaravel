<?php

namespace App\Http\Controllers\Admin\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MasterData\StoreVendorRequest;
use App\Models\Admin\Vendor;
use App\Models\MetaData\Division;
use App\Repositories\MasterData\AreaRepository;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class VendorController extends Controller
{
    public function index(){
        $data = Vendor::where('is_active',1)->get();
        return view('admin.master-data.vendor.index',compact('data'));
    }

    public function create(AreaRepository $areaRepository){
        $divisions = $areaRepository->getDivision();
        return view('admin.master-data.vendor.create-edit',compact('divisions'));
    }

    public function store(StoreVendorRequest $request, TokenService $tokenService)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $prefix = config('constants.VENDOR_CODE');
            $vendorCode = $prefix . $tokenService->getTokenByCode($prefix);

            $vendorData = array_merge($validated, [
                'vendor_code' => $vendorCode,
                'status' => 1,
                'postal_code' => isset($validated['postal_code']) ? $validated['postal_code'] : 0,
                'created_by' => Auth::user()->user_id,
                'updated_by' => Auth::user()->user_id,
            ]);

            $vendor = Vendor::create($vendorData);

            DB::commit();

            return redirect()
                ->route('admin.master.data.vendor.edit', $vendor->vendor_code)
                ->with('success', 'Vendor created successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
            // 4. Log the error internally so you can debug later, don't leak raw SQL errors to users
            Log::error('Vendor Creation Failed: ' . $e->getMessage(), ['context' => $validated]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the vendor. Please try again.');
        }
    }

    public function edit($vendorCode, AreaRepository $areaRepository){
        $divisions = $areaRepository->getDivision();
        $data = Vendor::where('vendor_code', $vendorCode)->first();
        return view('admin.master-data.vendor.create-edit',compact('data','divisions'));
    }

    public function update($vendorCode, StoreVendorRequest $request)
    {
        $vendor = Vendor::where('vendor_code', $vendorCode)->firstOrFail();

        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $vendorData = array_merge($validated, [
                'updated_by' => Auth::id(),
            ]);

            $vendor->update($vendorData);

            DB::commit();

            return redirect()
                ->route('admin.master.data.vendor.edit', $vendor->vendor_code)
                ->with('success', 'Vendor updated successfully.');

        } catch (Exception $e) {
            DB::rollBack();

            Log::error("Vendor Update Failed for Code [{$vendorCode}]: " . $e->getMessage(), [
                'context' => $validated
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the vendor. Please try again.');
        }
    }

    public function getDistricts($division_id, AreaRepository $areaRepository){
        $districts = $areaRepository->getDistrict($division_id);
        return $districts;
    }

    public function getUpazilas($district_id, AreaRepository $areaRepository){
        $upazilas = $areaRepository->getUpozilla($district_id);
        return $upazilas;
    }
}
