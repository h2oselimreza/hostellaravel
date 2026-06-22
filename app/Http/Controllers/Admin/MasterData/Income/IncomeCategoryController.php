<?php

namespace App\Http\Controllers\Admin\MasterData\Income;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MasterData\CostCategoryRequest;
use App\Models\Admin\ItemCategory;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncomeCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = ItemCategory::with('parent')
        ->orderBy('parent_category_str', 'asc')
        ->get();
        return view('admin.master-data.income-head.income-category.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->getIncomeCategory();
        return view('admin.master-data.expense-head.cost-category.create-edit',compact('categories'));
    }

    public function getIncomeCategory($isActiveFlag = 1) 
    {
        return DB::table('item_categories')
            ->when($isActiveFlag == 1, fn($q) => $q->where('is_active', 1))
            ->when($isActiveFlag == 2, fn($q) => $q->where('is_active', 0))
            ->orderBy('parent_category_str', 'asc')
            ->get()
            ->toArray();
    }

    public function store(CostCategoryRequest $request, TokenService $tokenService)
    {
        $data = $request->validated();

        $exists = ItemCategory::where('category_name', $data['category_name'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', __('Category name already exists!'));
        }

        DB::beginTransaction();
        try {

            $categoryCode = config('constants.ITEM_CTG_CODE'). $tokenService->getTokenByCode(config('constants.ITEM_CTG_CODE'));
            
            // 2. Hierarchy String Logic
            $parentCategoryStr = $categoryCode; 
            if ($data['parent_category'] != 1) {
                $parent = ItemCategory::where('category_code', $data['parent_category'])->first();
                if ($parent) {
                    $parentCategoryStr = "{$parent->parent_category_str} / {$categoryCode}";
                }
            }

            // 3. Create Record
            ItemCategory::create([
                'parent_category'     => $data['parent_category'],
                'parent_category_str' => $parentCategoryStr,
                'category_name'       => $data['category_name'],
                'category_code'       => $categoryCode,
                'is_active'           => 1,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.module.master-data.income-category.index')
                ->with('success', __('Income category created successfully!'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Income Category Creation Failed: " . $e->getMessage());

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
        $data = ItemCategory::where('category_code', $category_code)->first();
        $categories = $this->getIncomeCategory();
        return view('admin.master-data.income-head.income-category.create-edit',compact('categories','data'));
    }

   public function update(CostCategoryRequest $request, $category_code)
    {
        $itemCategory = ItemCategory::where('category_code',$category_code)->first();
        $data = $request->validated();

        $exists = ItemCategory::where('category_name', $data['category_name'])
            ->where('id', '!=', $itemCategory->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', __('Category name already exists!'));
        }

        DB::beginTransaction();
        try {

            $categoryCode = $itemCategory->category_code;
            $parentCategoryStr = $categoryCode; 

            if ($data['parent_category'] != 1) {
                $parent = ItemCategory::where('category_code', $data['parent_category'])->first();
                if ($parent) {
                    $parentCategoryStr = "{$parent->parent_category_str} / {$categoryCode}";
                }
            }

            // 3. Update Record
            $itemCategory->update([
                'parent_category'     => $data['parent_category'],
                'parent_category_str' => $parentCategoryStr,
                'category_name'       => $data['category_name'],
            ]);

            DB::commit();

            return redirect()
                ->route('admin.module.master-data.income-category.index')
                ->with('success', __('Income category updated successfully!'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Income Category Update Failed: " . $e->getMessage());

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
        $data = ItemCategory::where('category_code', $code)->firstOrFail();
        $data->update(['is_active' => !$data->is_active]);

        return back()->with('success', 'Status updated successfully!');
    }
}
