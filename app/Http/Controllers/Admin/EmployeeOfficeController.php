<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeOfficeRequest;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeOfficeController extends Controller
{
    public function edit($id){
        $data = Employee::findOrFail($id);
        $commonTableElementArr = array('type' => 'emp_designation');
        $designations = $this->getCommonTableElement($commonTableElementArr);
        //dd($designations);
        return view("admin.employee_office.office",compact("data","designations"));
    }

    public function update(EmployeeOfficeRequest $request, Employee $employee)
    {
        DB::transaction(function () use ($request, $employee) {

            // Update employee office info first
            $employee->update($request->validated());

            $systemUserCheck = $request->system_user; // 0 or 1

            if ($systemUserCheck) {

                // Get employee personal info
                $employeeDetails = $employee; // assuming data is already in $employee

                // Check if user already exists
                $user = User::where('user_id', $employee->employee_id)->first();

                if ($user) {
                    // If exists → activate
                    $user->update([
                        'is_active'   => 1,
                        'updated_by'  => auth()->user()->user_id,
                        'updated_dt_tm' => now(),
                    ]);
                } else {
                    try {
                            User::create([
                                'user_id'        => $employee->employee_id,
                                'user_group'     => 1, //super admin group
                                'username'       => $employee->employee_id,
                                'password'       => Hash::make('123456789'), // 🔐 secure
                                'email'          => $employeeDetails->email,
                                'user_type_code' => config('constants.USER_TYPE_ADMIN_EMP'), // adjust if constant defined
                                'panel_type'     => config('constants.P_ADMIN'),
                                'full_name'      => $employeeDetails->employee_name,
                                'contact_no'     => $employeeDetails->primary_mobile,
                                'is_active'      => 1,
                                'created_by'     => Auth::user()->user_id,
                                'created_dt_tm'  => Carbon::now(),
                                'updated_by'     => Auth::user()->user_id,
                                'updated_dt_tm'  => Carbon::now(),
                                'created_type'   => config('constants.USER_TYPE_ADMIN_EMP'),
                                'updated_type'   => config('constants.USER_TYPE_ADMIN_EMP')
                            ]);

                        } catch (\Exception $e) {
                            return redirect()
                                ->route('admin.employee.office.edit', $employee->id)
                                ->with('error', $e->getMessage());
                        }                    
                }

            } else {

                // If unchecked → deactivate user
                $user = User::where('user_id', $employee->employee_id)->first();

                if ($user) {
                    $user->update([
                        'is_active'   => 0,
                        'updated_by'  => auth()->user()->user_id,
                        'updated_dt_tm' => now(),
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.employee.office.edit', $employee->id)
            ->with('success', 'Employee office information updated successfully.');
    }

    function getCommonTableElement($commonTableElementArr)
    {
        $query = DB::table('common_table');

        if (isset($commonTableElementArr['type'])) {
            $query->where('type', $commonTableElementArr['type']);
        }

        if (isset($commonTableElementArr['depend_on_element'])) {
            $query->where('depend_on_element', $commonTableElementArr['depend_on_element']);
        }

        return $query->orderBy('element_order', 'ASC')
                    ->orderBy('element', 'ASC')
                    ->get();
    }

}
