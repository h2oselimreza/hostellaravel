<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\SubModules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class SubModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("admin.sub_modules.index");
    }

    public function getSubModulesData(Request $request){
        if ($request->ajax()) {

            $employee = SubModules::select([
                'id',
                'sub_module_name',
                'sub_module_code',
                'panel_type',
                'module',
            ]);

            return DataTables::of($employee)

                ->addIndexColumn()

                ->addColumn('action', content: function ($employee) {
                    $editUrl   = route('admin.sub-modules.edit', $employee->id);
                    return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Actions
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">

                                <!-- Edit -->
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="' . $editUrl . '">
                                        <i class="fa fa-edit me-2"></i> Edit
                                    </a>
                                </li>
                            </ul>
                        </div>
                    ';
                })

                ->rawColumns(['action'])
                ->make(true);

        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modules = Module::orderBy('modules_name')->get();
        return view('admin.sub_modules.create-edit',compact('modules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $validated = $request->validate([
            'module'           => 'required|exists:modules,id',
            'panel_type'       => 'required|in:admin,client',
            'sub_module_name'  => 'required|string|max:255',
            'sub_module_code'  => 'required|string|max:255|unique:sub_modules,sub_module_code',
        ]);

        SubModules::create([
            'module'          => $validated['module'],
            'panel_type'      => $validated['panel_type'],
            'sub_module_name' => $validated['sub_module_name'],
            'sub_module_code' => $validated['sub_module_code'],
        ]);

        return redirect()
            ->route('admin.sub-modules.index')
            ->with('success', 'Sub Module created successfully.');
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
    public function edit(string $id)
    {
        $module = SubModules::findOrFail($id);
        $modules = Module::orderBy('modules_name')->get();
        return view('admin.sub_modules.create-edit',compact('modules','module'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $subModule = SubModules::findOrFail($id);

        $validated = $request->validate([
            'module'           => 'required|exists:modules,id',
            'panel_type'       => 'required|in:admin,client',
            'sub_module_name'  => 'required|string|max:255',
            'sub_module_code'  => 'required|string|max:255|unique:sub_modules,sub_module_code,' . $id,
        ]);

        $subModule->update($validated);

        return redirect()
            ->route('admin.sub-modules.index')
            ->with('success', 'Sub Module updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
