<?php

namespace App\Http\Controllers\Admin\Boarder;

use App\Http\Controllers\Controller;
use App\Models\Admin\Boarder;
use App\Models\Admin\BoarderEduQualification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BoarderEducationController extends Controller
{
    public function edit($boarderId){

        $data = Boarder::where('boarder_id', $boarderId)->first();
        $query = DB::table('boarder_edu_qualification as emp')
        ->select(
            'emp.*',
            'education_level_tb.element as education_level',
            'exam_title_tb.element as exam_title',
            'education_board_tb.element as education_board_name',
            'quali_result_tb.element as quali_result_name'
        )
        ->leftJoin('common_table as education_level_tb', 
            'education_level_tb.element_code', '=', 'emp.level_of_education'
        )
        ->leftJoin('common_table as exam_title_tb', 
            'exam_title_tb.element_code', '=', 'emp.exam_degree'
        )
        ->leftJoin('common_table as education_board_tb', 
            'education_board_tb.element_code', '=', 'emp.education_board'
        )
        ->leftJoin('common_table as quali_result_tb', 
            'quali_result_tb.element_code', '=', 'emp.qualification_result'
        );

    if ($boarderId) {
        $query->where('emp.boarder_id', $boarderId);
    } 

    $eduDetails =  $query->get(); // collection
    $commonTableElementArr = array('type' => 'level_of_education');
    $levelOfEducations = $this->getCommonTableElement($commonTableElementArr);

    $commonTableElementArr = array('type' => 'exam_degree_title');
    $examTitles = json_encode(array('examTitleData' => $this->getCommonTableElement($commonTableElementArr)));

    $commonTableElementArr = array('type' => 'education_board');
    $educationBoards = $this->getCommonTableElement($commonTableElementArr);

    $commonTableElementArr = array('type' => 'edu_qualification_result');
    $qualificationResults = $this->getCommonTableElement(commonTableElementArr: $commonTableElementArr);

    return view("admin.boarder.add-boarder.education.index",
        compact("data","eduDetails","levelOfEducations","examTitles","educationBoards","qualificationResults")
    );

    }
    
    public function update(Request $request, $boarderId)
    {
        // Get total number of rows
        $totalRows = $request->input('eduQualificationCount', 0);
       if ($totalRows == 0) {
        return redirect()->back()->with('error','All Field are empty');
       }
        for ($i = 0; $i < $totalRows; $i++) {
            
            $level = $request->input("levelOfEducation{$i}");

            // deleted row
            if (
                !$request->has("hiddenEducationRow{$i}") &&
                empty($level)
            ) {
                continue;
            }

            if (!empty($level)) {

                $request->validate([
                    "levelOfEducation$i" => 'required',
                    "examDegree$i" => 'required',
                    "duration$i" => 'required',
                ],[
                    "levelOfEducation$i.required" => "Level of education is required in row ".($i+1),
                    "examDegree$i.required" => "Exam/Degree is required in row ".($i+1),
                    "duration$i.required" => "Duration is required in row ".($i+1),
                ]);

            }
       }
        // Collect deleted row IDs
        $deleteIds = explode(',', $request->input('deleteEduRow', ''));

        DB::transaction(function() use ( $request, $totalRows, $deleteIds, $boarderId ) {

            $deleteIds = array_filter($deleteIds);
            // Delete rows if any were removed
            if(!empty($deleteIds)){
                BoarderEduQualification::whereIn('id', $deleteIds)->delete();
            }

            // Loop through submitted rows
            for ($i = 0; $i < $totalRows; $i++) {

                // Skip rows without level of education
                $level = $request->input("levelOfEducation$i");
                if(!$level) continue;

                // Check if existing record or new
                $rowId = $request->input("hiddenEducationRow$i");

                $data = [
                    'boarder_id' => $boarderId, // make sure this is in the form
                    'level_of_education' => $level,
                    'exam_degree' => $request->input("examDegree$i"),
                    'institute_name' => $request->input("instituteName$i"),
                    'education_board' => $request->input("educationBoard$i"),
                    'qualification_result' => $request->input("qualificationResult$i"),
                    'cgpa_marks' => $request->input("cgpaMarks$i"),
                    'scale' => $request->input("scale$i"),
                    'passing_year' => $request->input("passingYear$i"),
                    'duration' => $request->input("duration$i"),
                    'major_group' => $request->input("majorGroup$i"),
                    'is_active' => 1,
                    'updated_by' => Auth::user()->name ?? 'system',
                    'updated_dt_tm' => Carbon::now(),
                ];

                if($rowId && $rowId != 0){
                    // Update existing
                    BoarderEduQualification::where('id', $rowId)->update($data);
                } else {
                    // Create new
                    $data['created_by'] = Auth::user()->name ?? 'system';
                    $data['created_dt_tm'] = Carbon::now();
                    BoarderEduQualification::create($data);
                }
            }
        });

        return redirect()->back()->with('success', 'Education details updated successfully.');
    }


    private function getCommonTableElement($commonTableElementArr)
    {
        return DB::table('common_table')
            ->when(isset($commonTableElementArr['type']), function ($q) use ($commonTableElementArr) {
                $q->where('type', $commonTableElementArr['type']);
            })
            ->when(isset($commonTableElementArr['depend_on_element']), function ($q) use ($commonTableElementArr) {
                $q->where('depend_on_element', $commonTableElementArr['depend_on_element']);
            })
            ->get()
            ->toArray();
    }
}
