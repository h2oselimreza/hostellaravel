<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\ModuleGroup;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
Use App\Http\Requests\ModuleRequest;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("admin.modules.index");
    }

    public function getModulesData(Request $request)
    {
        if ($request->ajax()) {

            $modules = Module::select([
                'id',
                'panel_type',
                'module_group',
                'modules_name',
                'module_url',
                'module_order',
            ]);

            return DataTables::of($modules)

                ->addIndexColumn()

                ->addColumn('action', function ($modules) {
                    $editUrl   = route('admin.modules.edit', $modules->id);
                    $deleteUrl = route('admin.modules.destroy', $modules->id);
                return '
                    <div class="dropdown">
                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown">
                            Action
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="'.$editUrl.'" 
                                class="d-block ps-3 mb-2">
                                    <span class="ui-button-text">&nbsp;Update</span>
                                </a>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li>
                                <a onclick="deleteRecord(\''.$deleteUrl.'\')" href="javascript:void(0)" 
                                class="ps-3">
                                    <span class="ui-button-text">&nbsp;Delete</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    ';
                })

                ->rawColumns(['status', 'action'])
                ->make(true);

        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.modules.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ModuleRequest $request)
    {
        Module::create($request->validated());

        return redirect()
        ->route('admin.modules.index')
        ->with('success', 'Module created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        return view('admin.modules.create', compact('module'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ModuleRequest $request, Module $module)
    {
        $module->update($request->validated());

        return redirect()
            ->route('admin.modules.index')
            ->with('success', 'Module updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
        $module->delete();

        return redirect()
            ->route('admin.modules.index')
            ->with('success', 'Module deleted successfully.');
    }

    public function selectModuleData($panel){
        return ModuleGroup::where('panel_type', $panel)
            ->select('module_group_name','module_group_code')
            ->distinct()
            ->get();
    }
}
