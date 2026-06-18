<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfilePhotoRequest;
use App\Models\Employee;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoController extends Controller
{
    public function edit($id){
        $data = Employee::findOrFail($id);
        return view("admin.employee.profile_photo.index",compact("data"));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'employee_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        
        // Find employee by ID
        $employee = Employee::findOrFail($id);

        if ($request->hasFile('employee_image')) {

            // Delete old image if exists
            if ($employee->employee_image &&
                Storage::disk('public')->exists('assets/admin/images/employee/' . $employee->employee_image)) {

                Storage::disk('public')->delete('assets/admin/images/employee/' . $employee->employee_image);
            }

            // Store new image
            $imageName = time() . '.' . $request->file('employee_image')->extension();

            $request->file('employee_image')
                    ->storeAs('assets/admin/images/employee', $imageName, 'public');

            // Update database
            $employee->update([
                'employee_image' => $imageName
            ]);
        }

        return redirect()
            ->route('admin.profile.photo.edit', $id)
            ->with('success', 'Photo updated successfully');
    }
}
