<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(){
        //$users = User::select(['id','username','full_name','email','contact_no','user_group','is_active'])->get();
        return view('admin.users.userList');
    }

    public function getUsers(Request $request)
    {
        if ($request->ajax()) {

            $users = User::with('userGroup')->select([
                'id',
                'username',
                'full_name',
                'contact_no',
                'email',
                'user_group',
                'is_active'
            ]);

            return DataTables::of($users)

                ->addIndexColumn()

                 ->addColumn('user_group', function ($user) {
                    return $user->userGroup->group_name ?? '-';
                })
                ->addColumn('status', function ($user) {
                    return $user->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })

                ->addColumn('action', function ($users) {
                    $editUrl   = route('admin.users.edit', $users->id);
                    $activeInactiveUrl = route('admin.modules.destroy', $users->id);
                    return '
                        <div class="dropdown">
                            <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown">
                                Action
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="' . $editUrl . '" class="d-block ps-3 mb-2">
                                        <span class="ui-button-text">&nbsp;Update</span>
                                    </a>
                                </li>
                            </ul>
                        </div>';

                })

                ->rawColumns(['status', 'action'])
                ->make(true);

                // <a href="'.route('admin.users.edit', $user->id).'"
                //            class="btn btn-sm btn-primary">
                //            Edit
                //         </a>
        }
    }

    public function edit(Request $request, $id){
        $users = User::findOrFail($id);
        $userGroups = UserGroup::get();
        return view('admin.users.create_update', compact('users','userGroups'));
    }

    public function update(Request $request, $id){
        $users = User::findOrFail($id);
        $users->update($request->all());
        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User group update successfuly.');
    }
}
