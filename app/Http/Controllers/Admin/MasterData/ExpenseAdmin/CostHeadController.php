<?php

namespace App\Http\Controllers\Admin\MasterData\ExpenseAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MasterData\CostHeadRequest;
use App\Models\Admin\MasterData\CostHead;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CostHeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = CostHead::with('category')->get();
        return view('admin.master-data.expense-head.cost-head.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->getCostCategory();
        return view('admin.master-data.expense-head.cost-head.create-edit',compact('categories'));
    }

    public function getCostCategory($isActiveFlag = 1) 
    {
        return \DB::table('cost_categories')
            ->when($isActiveFlag == 1, fn($q) => $q->where('is_active', 1))
            ->when($isActiveFlag == 2, fn($q) => $q->where('is_active', 0))
            // ->when($this->customerType == 'indv_customer', 
            //     fn($q) => $q->where('company', '999999'),
            //     fn($q) => $q->where('company', $this->companyCode)
            // )
            ->orderBy('parent_category_str', 'asc')
            ->get()
            ->toArray(); // Converts to array to match your old return type
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CostHeadRequest $request, TokenService $tokenService)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $prefix = "CSTHD-";
            $codeHeadCode = $prefix . $tokenService->getTokenByCode($prefix);
        
            CostHead::create([
                'company'             => auth()->user()->company_code ?? '999999',
                'cost_category'     => $data['cost_category'],
                'cost_head'       => $data['cost_head'],
                'cost_head_code'       => $codeHeadCode,
                'cost_head_dis_code' => $codeHeadCode,
                'is_active'           => 1,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.module.master-data.expense-head.index')
                ->with('success', __('Cost category created successfully!'));

        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            Log::error("Creation Failed: " . $e->getMessage());

            return back()->withInput()->with('error', 'Something went wrong!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $cost_head_code)
    {
        $data = CostHead::where('cost_head_code', $cost_head_code)->first();
        $categories = $this->getCostCategory();
        return view('admin.master-data.expense-head.cost-head.create-edit',compact('categories','data'));
    }

    /**
     * Update the specified resource in storage.
     */
    /**
 * Update the specified cost head in storage.
 */
    public function update(CostHeadRequest $request, $cost_head_code)
    {
        $cost_head = CostHead::where('cost_head_code',$cost_head_code)->first();
        // 1. Get validated data from your Request class
        $data = $request->validated();

        DB::beginTransaction();
        try {

            $cost_head->update([
                'company'             => auth()->user()->company_code ?? '999999',
                'cost_category'     => $data['cost_category'],
                'cost_head'       => $data['cost_head'],
                'is_active'           => 1,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.module.master-data.expense-head.index')
                ->with('success', __('Cost head updated successfully!'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Cost Head Update Failed", [
                'id'    => $cost_head->id,
                'error' => $e->getMessage()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong during the update.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function toggle($code)
    {
        $data = CostHead::where('cost_head_code', $code)->firstOrFail();
        $data->update(['is_active' => !$data->is_active]);

        return back()->with('success', 'Status updated successfully!');
    }
}
