<?php

namespace App\Http\Controllers\Admin\MasterData\Income;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MasterData\CostHeadRequest;
use App\Models\Admin\ItemHead;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class IncomeHeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = ItemHead::with('category')->get();
        return view('admin.master-data.income-head.income-head.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->getIncomeCategory();
        return view('admin.master-data.income-head.income-head.create-edit',compact('categories'));
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, TokenService $tokenService)
    {
        $request->validate([
            'item_category' => ['required', 'string', 'max:50'],
            'item_head' => [
                'required',
                'string',
                'max:200',
                'unique:item_heads,item_head',
            ],
            'unit_name' => ['required', 'string', 'max:50'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
        ], [
            'item_category.required' => 'Cost category is required.',
            'item_head.required' => 'Cost head is required.',
            'item_head.unique' => 'This item head already exists.',
            'unit_name.required' => 'Unit name is required.',
        ]);

        DB::beginTransaction();
        try {
            $codeHeadCode = config('constants.ITEM_HEAD_CODE') . $tokenService->getTokenByCode(config('constants.ITEM_HEAD_CODE'));
        
            ItemHead::create([
                'item_category'     => $request->item_category,
                'item_head'       => $request->item_head,
                'item_head_code'       => $codeHeadCode,
                'item_head_dis_code' => $codeHeadCode,
                'is_active'           => 1,
                'unit_name' => $request->unit_name,
                'unit_price' => $request->unit_price,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.module.master-data.income-head.index')
                ->with('success', __('Income category head created successfully!'));

        } catch (\Exception $e) {
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
        $data = ItemHead::where('item_head_code', $cost_head_code)->first();
        $categories = $this->getIncomeCategory();
        return view('admin.master-data.income-head.income-head.create-edit',compact('categories','data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $itemHeadCode)
    {
        $request->validate([
            'item_category' => ['required', 'string', 'max:50'],
            'item_head' => [
                'required',
                'string',
                'max:200',
                Rule::unique('item_heads', 'item_head')
                    ->ignore($itemHeadCode, 'item_head_code'),
            ],
            'unit_name' => ['required', 'string', 'max:50'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
        ], [
            'item_category.required' => 'Cost category is required.',
            'item_head.required' => 'Cost head is required.',
            'item_head.unique' => 'This item head already exists.',
            'unit_name.required' => 'Unit name is required.',
        ]);

        DB::beginTransaction();

        try {

            $itemHead = ItemHead::where('item_head_code', $itemHeadCode)->firstOrFail();

            $itemHead->update([
                'item_category' => $request->item_category,
                'item_head'     => $request->item_head,
                'unit_name'     => $request->unit_name,
                'unit_price'    => $request->unit_price,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.module.master-data.income-head.index')
                ->with('success', __('Income head updated successfully!'));

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Update Failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Something went wrong!');
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
        $data = ItemHead::where('item_head_code', $code)->firstOrFail();
        $data->update(['is_active' => !$data->is_active]);

        return back()->with('success', 'Status updated successfully!');
    }
}
