<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\ModuleGroup;
use App\Models\SubModules;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserGroupController extends Controller
{
    public function index(){
        //$users = User::select(['id','username','full_name','email','contact_no','user_group','is_active'])->get();
        return view('admin.user_groups.index');
    }

    public function getUserGroups(Request $request)
    {
        if ($request->ajax()) {

            $userGroups = UserGroup::select([
                'id',
                'group_name',
                'is_active'
            ]);

            return DataTables::of($userGroups)

                ->addIndexColumn()

                ->addColumn('is_active', function ($userGroups) {
                    return $userGroups->is_active
                        ? 'Active'
                        : 'Inactive';
                })

                ->addColumn('action', function ($userGroups) {
                    $editUrl   = route('admin.user-groups.edit', $userGroups->id);
                    $activeInactiveUrl   = route('admin.user-groups.status', $userGroups->id);
                    $deleteUrl   = route('admin.user-groups.destroy', $userGroups->id);

                    $statusText = $userGroups->is_active == 1 ? 'Inactive' : 'Active';
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

                                <!-- Active / Inactive -->
                                <li>
                                    <form action="' . $activeInactiveUrl . '" method="POST">
                                        ' . csrf_field() . '
                                        <input type="hidden" name="_method" value="PATCH">
                                        <button type="submit" class="dropdown-item d-flex align-items-center">
                                            <i class="fa fa-toggle-on me-2"></i> ' . $statusText . '
                                        </button>
                                    </form>
                                </li>

                                <!-- Delete -->
                                <li>
                                    <form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this record?\')">
                                        ' . csrf_field() . '
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                                            <i class="fa fa-trash me-2"></i> Delete
                                        </button>
                                    </form>
                                </li>

                            </ul>
                        </div>
                        ';
                })

                ->rawColumns(['status', 'action'])
                ->make(true);
        }
    }

    public function create(Request $request){
        //dd($request->all());
        $panelType = ($request->panelType) ? $request->panelType : NULL;

        if($panelType){
            $modules = Module::where('panel_type', $panelType)->orderBy('module_order', 'asc')->get();

            $moduleGroups = ModuleGroup::where('panel_type', $panelType)->orderBy('module_group_order', 'asc')->get();

            $subModules = SubModules::where('panel_type', $panelType)->get();

            return view('admin.user_groups.create-new',compact('modules','moduleGroups','subModules','panelType'));
        }else{
            return view('admin.user_groups.create-new',compact('panelType'));
        }
        
    }

    public function store(Request $request)
    {

        $request->validate([
            'panelType'     => 'required|string',
            'user_group_name' => 'required|string|max:255',
            'moduleList'    => 'required|array|min:1',
            'moduleList.*'  => 'integer',
            'subModuleList' => 'nullable|array',
            'subModuleList.*' => 'integer',
        ]);

        $result = $this->setModules(
            $request->moduleList,
            $request->user_group_name,
            $request->subModuleList,
            $request->panelType
        );

        if ($result == 1) {
            return redirect()
                ->route('admin.user-groups.index')
                ->with('success', 'User group created successfully.');
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Group name already exists or invalid module selection.');
    }

    private function setModules(array $moduleList, string $userGroupName, ?array $subModuleLists, string $panelType): int
    {
        try {
            DB::beginTransaction();

            $modules = implode(',', $moduleList);
            $subModules = '';

            if (!empty($subModuleLists)) {

                $subModules = implode(',', $subModuleLists);

                $dbModuleArr = SubModules::whereIn('id', $subModuleLists)
                    ->distinct()
                    ->pluck('module')
                    ->toArray();

                foreach ($dbModuleArr as $moduleId) {
                    if (!in_array($moduleId, $moduleList)) {
                        DB::rollBack();
                        return 2;
                    }
                }
            }

            if (UserGroup::where('group_name', $userGroupName)->exists()) {
                DB::rollBack();
                return 2;
            }

            UserGroup::create([
                'group_name'  => $userGroupName,
                'modules'     => $modules,
                'sub_modules' => $subModules,
                'panel_type'  => $panelType, // Don't hardcode 'ddd'
                'created_by'  => Auth::user()->user_id,
                'updated_by'  => Auth::user()->user_id,
            ]);

            DB::commit();

            return 1;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('User group creation failed.', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return 0;
        }
    }

    public function edit($id)
    {
        $userGroup = UserGroup::findOrFail($id);
        $modules = Module::orderBy('module_order', 'asc')->get();
        $moduleGroups = ModuleGroup::orderBy('module_group_order', 'asc')->get();
        $panelType = $userGroup->panel_type;
        $subModules = SubModules::when($panelType, function ($q) use ($panelType) {
            $q->where('panel_type', $panelType);
        })
        ->get();
        $userGroupDetails = UserGroup::where('id', $id)->get();
        $groupId = $id;

        return view('admin.user_groups.create_update', compact(
            'modules',
            'moduleGroups',
            'userGroup',
            'subModules',
            'userGroupDetails',
            'panelType',
            'groupId'
        ));
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        // //dd($request->all());
        //  $request->validate([
        //     'module_group_name' => 'required|string|max:255',
        //     'moduleList'        => 'required|array',
        // ]);

        // $moduleGroup = $id 
        //     ? UserGroup::findOrFail($id)
        //     : new UserGroup();

        // $moduleGroup->group_name = $request->module_group_name;
        // $moduleGroup->modules = implode(',', $request->moduleList);
        // $moduleGroup->is_active = 1;

        // $moduleGroup->save();

        // return redirect()->route('admin.user-groups.index')
        //     ->with('success', $id ? 'User group updated Successfully' : 'User group created Successfully');


            $moduleLists = $request->input('moduleList');
            $subModuleLists = $request->input('subModuleList');
            $moduleGroupId = $request->input('moduleGroupId');

            // new block group
            $userGroupBlockListArr = config('constants.USERGROUP_BLOCKLIST');

            if (
                in_array($moduleGroupId, $userGroupBlockListArr) &&
                $moduleGroupId != Auth::user()->user_group
            ) {
                return redirect()->route('admin.user-groups.index')
                ->with('success', $id ? 'User group updated Successfully' : 'User group created Successfully');
            }
            // ----------------

            if (isset($moduleLists)) {

                $this->editModules($moduleLists, $moduleGroupId, $subModuleLists);

                return redirect()->route('admin.user-groups.edit', $id)
                ->with('success', $id ? 'User group updated Successfully' : 'User group created Successfully');
            }
    }

        public function updateStatus($id)
        {
            $userGroup = UserGroup::findOrFail($id);
            $userGroup->is_active = $userGroup->is_active == 1 ? 0 : 1;
            $userGroup->save();
            return redirect()->back()->with('success', 'Status Updated Successfully');
        }

        public function destroy($id)
        {
            $userGroup = UserGroup::findOrFail($id);
            $userGroup->delete();
            return redirect()->back()->with('success', 'User group deleted Successfully');
        }

        private function editModules($moduleList, $moduleId, $subModuleLists = null)
        {
            $modules = implode(",", $moduleList);

            $subModules = "";

            if (!empty($subModuleLists)) {
                $subModules = implode(",", $subModuleLists);
            }

            UserGroup::where('id', $moduleId)->update([
                'modules' => $modules,
                'sub_modules' => $subModules,
                'updated_by' => Auth::user()->user_id,
                'updated_dt_tm' => Carbon::now(),
            ]);
        }
}
