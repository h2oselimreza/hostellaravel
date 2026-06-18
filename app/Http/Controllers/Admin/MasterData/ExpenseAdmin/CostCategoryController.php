<?php

namespace App\Http\Controllers\Admin\MasterData\ExpenseAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MasterData\CostCategoryRequest;
use App\Models\Admin\MasterData\CostCategory;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class CostCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = CostCategory::with('parent')
        ->orderBy('parent_category_str', 'asc')
        ->get();
        return view('admin.master-data.expense-head.cost-category.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->getCostCategory();
        return view('admin.master-data.expense-head.cost-category.create-edit',compact('categories'));
    }

    public function getCostCategory($isActiveFlag = 1) 
    {
        return DB::table('cost_categories')
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

    public function store(CostCategoryRequest $request, TokenService $tokenService)
    {
        $data = $request->validated();

        // 1. Manual Duplicate Check (as per your request)
        // We check if category_name already exists for this company
        $exists = CostCategory::where('category_name', $data['category_name'])
            //->where('company', auth()->user()->company_code ?? '999999')
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', __('Category name already exists!'));
        }

        DB::beginTransaction();
        try {
            $prefix = "CSTCATG-";
            $categoryCode = $prefix . $tokenService->getTokenByCode($prefix);
            
            // 2. Hierarchy String Logic
            $parentCategoryStr = $categoryCode; 
            if ($data['parent_category'] != 1) {
                $parent = CostCategory::where('category_code', $data['parent_category'])->first();
                if ($parent) {
                    $parentCategoryStr = "{$parent->parent_category_str} / {$categoryCode}";
                }
            }

            // 3. Create Record
            CostCategory::create([
                'company'             => auth()->user()->company_code ?? '999999',
                'parent_category'     => $data['parent_category'],
                'parent_category_str' => $parentCategoryStr,
                'category_name'       => $data['category_name'],
                'category_code'       => $categoryCode,
                'is_active'           => 1,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.module.master-data.expense-category.index')
                ->with('success', __('Cost category created successfully!'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Cost Category Creation Failed: " . $e->getMessage());

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
    public function edit(string $category_code)
    {
        $data = CostCategory::where('category_code', $category_code)->first();
        $categories = $this->getCostCategory();
        return view('admin.master-data.expense-head.cost-category.create-edit',compact('categories','data'));
    }

   public function update(CostCategoryRequest $request, $category_code)
    {
        $cost_category = CostCategory::where('category_code',$category_code)->first();
        $data = $request->validated();

        $exists = CostCategory::where('category_name', $data['category_name'])
            ->where('id', '!=', $cost_category->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', __('Category name already exists!'));
        }

        DB::beginTransaction();
        try {

            $categoryCode = $cost_category->category_code;
            $parentCategoryStr = $categoryCode; 

            if ($data['parent_category'] != 1) {
                $parent = CostCategory::where('category_code', $data['parent_category'])->first();
                if ($parent) {
                    $parentCategoryStr = "{$parent->parent_category_str} / {$categoryCode}";
                }
            }

            // 3. Update Record
            $cost_category->update([
                'parent_category'     => $data['parent_category'],
                'parent_category_str' => $parentCategoryStr,
                'category_name'       => $data['category_name'],
            ]);

            DB::commit();

            return redirect()
                ->route('admin.module.master-data.expense-category.index')
                ->with('success', __('Cost category updated successfully!'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Cost Category Update Failed: " . $e->getMessage());

            return back()->withInput()->with('error', 'Something went wrong during update!');
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
        $data = CostCategory::where('category_code', $code)->firstOrFail();
        $data->update(['is_active' => !$data->is_active]);

        return back()->with('success', 'Status updated successfully!');
    }
}
