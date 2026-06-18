<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModuleGroup;
use App\Models\SubModules;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ModuleGroupController extends Controller
{
    public function index(){
        return view('admin.module_group.index');
    }

    public function getModuleGroupData(Request $request)
    {
        if ($request->ajax()) {

            $userGroups = ModuleGroup::select([
                'id',
                'panel_type',
                'module_group_name',
                'module_group_code',
                'module_group_order',
            ]);

            return DataTables::of($userGroups)

                ->addIndexColumn()

                ->addColumn('action', function ($userGroups) {
                    $viewUrl   = route('admin.module-group.show', $userGroups->id);
                    $editUrl   = route('admin.module-group.edit', $userGroups->id);
                    $deleteUrl = route('admin.module-group.destroy', $userGroups->id);
                    return '
                        <a href="'.$editUrl.'" 
                        class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary action_button_modify">
                            <span class="ui-button-text">&nbsp;Edit</span>
                        </a>

                        <a onclick="deleteRecord(\''.$deleteUrl.'\')" 
                        href="javascript:void(0)" 
                        class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary action_button_modify">
                            <span class="ui-button-text">&nbsp;Delete</span>
                        </a>
                    ';
                })

                ->rawColumns(['status', 'action'])
                ->make(true);

        }
    }

    public function create(Request $request){
        return view('admin.module_group.create');
    }
    public function store(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'panel_type' => 'required',
            'module_group_name' => 'required|max:250',
            'module_group_order' => 'required|integer'
        ]);

        ModuleGroup::create($request->all());

        return redirect()->route('admin.module-group.index')
            ->with('success','Module Group Created Successfully!');
    }

    public function show(ModuleGroup $moduleGroup){}

    public function edit($id){
        $module = ModuleGroup::findOrFail($id);
        $subModule = SubModules::where('module',$id)->get();
        return view('admin.module_group.create', compact('module'));
            
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'panel_type' => 'required',
            'module_group_name' => 'required|max:250',
            'module_group_order' => 'required|integer'
        ]);

        try {

            $moduleGroup = ModuleGroup::findOrFail($id);

            $moduleGroup->update($validated);

            return redirect()
                ->route('admin.module-group.index')
                ->with('success', 'Module Group updated successfully.');

        } catch (\Exception $e) {

            return back()
                ->with('error', 'Something went wrong!')
                ->withInput();
        }
    }


    public function destroy($id)
    {
        try {

            $moduleGroup = ModuleGroup::findOrFail($id);

            $moduleGroup->delete();

            return redirect()
                ->route('admin.module-group.index')
                ->with('success', 'Module Group deleted successfully.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return redirect()
                ->route('admin.module-group.index')
                ->with('error', 'Record not found.');

        } catch (\Exception $e) {

            return redirect()
                ->route('admin.module-group.index')
                ->with('error', 'Something went wrong. Unable to delete.');
        }
    }

}
