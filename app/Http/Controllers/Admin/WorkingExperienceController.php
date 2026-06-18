<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmpWorkingExperience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkingExperienceController extends Controller
{
    public function edit($employeeId){
        $data = Employee::findOrFail($employeeId);
        $empWorkingDetails = $this->getEmpWorkingDetails($employeeId);
        return view("admin.employee.working_experience.edit",
        compact("data",'empWorkingDetails')
        );
    }
    
    public function update(Request $request, $employeeId)
    {
        $workingExpCount = (int) $request->workingExpCount;
        for ($i = 0; $i < $workingExpCount; $i++) {
            $request->validate([
                "fYear$i" => 'required',
                "fMonth$i"=> 'required',
                "fDay$i" => 'required',
                "institutionName$i"=> 'required',
            ],[
                "institutionNamey$i.required" => "InstitutionName is required in row ".($i),
                "fYear$i.required" => "Year is required in row ".($i),
                "fMonth$i.required" => "Month is required in row ".($i),
                "fDay$i.required" => "Day is required in row ".($i),
            ]);
       }
//dd($request->all(),$workingExpCount);
        // IDs of rows deleted by user
        $deleteRows = $request->deleteWorkingRow ? explode(',', $request->deleteWorkingRow) : [];

        DB::transaction(function () use ($request, $employeeId, $workingExpCount, $deleteRows) {
        
            // 1️⃣ Delete removed rows
            if (!empty($deleteRows)) {
                EmpWorkingExperience::whereIn('id', $deleteRows)->delete();
            }

            // 2️⃣ Loop through each working experience block
            for ($i = 0; $i <= $workingExpCount; $i++) {
                //dd($request->all(),$workingExpCount,'pp');
                // Skip if no institution name (empty row)
                if (!$request->has("institutionName{$i}") || empty($request->input("institutionName{$i}"))) {
                    continue;
                }

                // Determine From Date
                $fromYear  = $request->input("fYear{$i}") ?? '0000';
                $fromMonth = $request->input("fMonth{$i}") ?? '00';
                $fromDay   = $request->input("fDay{$i}") ?? '00';
                $fromDate  = "{$fromYear}-{$fromMonth}-{$fromDay}";

                // Determine To Date
                $isContinued = $request->input("currentWorkCheckbox{$i}") == '1' ? 1 : 0;

                if ($isContinued) {
                    $toDate = null; // still working
                } else {
                    $toYear  = $request->input("tYear{$i}") ?? '0000';
                    $toMonth = $request->input("tMonth{$i}") ?? '00';
                    $toDay   = $request->input("tDay{$i}") ?? '00';
                    $toDate  = "{$toYear}-{$toMonth}-{$toDay}";
                }
                if ($toDate === '0000-00-00') {
                    $toDate = null;
                }
                // Check if existing row
                if ($request->filled("hiddenWorkingDiv{$i}")) {
                    $workingExp = EmpWorkingExperience::find($request->input("hiddenWorkingDiv{$i}"));
                } else {
                    $workingExp = new EmpWorkingExperience();
                    $workingExp->employee_id = $employeeId;
                }
                // Save/update values
                $workingExp->institution_name  = $request->input("institutionName{$i}");
                $workingExp->institution_type  = $request->input("institutionType{$i}");
                $workingExp->address           = $request->input("address{$i}");
                $workingExp->designation       = $request->input("designation{$i}");
                $workingExp->department        = $request->input("department{$i}");
                $workingExp->from_date         = $fromDate;
                $workingExp->to_date           = $toDate;
                $workingExp->is_continued      = $isContinued;
                $workingExp->responsibilites   = $request->input("responsibilites{$i}");
                
                $workingExp->save();
            }
        });

        return redirect()->back()->with('success', 'Working experience updated successfully!');
    
    }

    private function getEmpWorkingDetails($employeeId = null, $employeeIdArray = [])
    {
        $query = EmpWorkingExperience::query();

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        } elseif (!empty($employeeIdArray)) {
            $query->whereIn('employee_id', $employeeIdArray);
        }

        $query->orderBy('from_date', 'desc');

        return $query->get(); // returns a Collection
    }
}
