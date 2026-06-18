@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">
        {{ isset($data->exists) ? 'Edit Employee' : 'Add Employee' }}
    </h1>
</div>
<div class="container">
  <div class="card shadow">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul style="margin-bottom:0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
      <!-- Nav Tabs -->
       @include('admin.employee.nav-tab')
      {{-- Success Message --}} @if(session('success')) <div class="alert alert-success">
        {{ session('success') }}
      </div> @endif
      <!-- Tab Content -->
      <div class="tab-content" id="employeeTabContent">
        <div class="tab-pane fade show active" id="official" role="tabpanel">
          <form action="{{ route('admin.employee.education.update',$data->id) }}" method="POST">
             @csrf 
             <div class="panel-group">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12">
                      <h4 class="panel-title">
                        <i class="fa fa-book"></i> Educational Information
                      </h4>
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12 text-right">
                      <h4 class="panel-title">
                        <i class="fa fa-user"></i> <?php /*echo $employeeId*/ ?>
                      </h4>
                    </div>
                  </div>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-hover custom-table" id="eduQualificationTable" style="width: 100%">
                        <tr class="bg-info">
                            <th style="width:136px">Level Of Education</th>
                            <th style="width:125px">Exam/Degree</th>
                            <th style="width:65px">Group</th>
                            <th>Institute Name</th>
                            <th style="width:100px">Board</th>
                            <th style="width:100px">Result</th>
                            <th style="width:60px">CGPA/Marks</th>
                            <th style="width:60px">Scale</th>
                            <th style="width:65px">Passing Year</th>
                            <th style="width:40px;font-size: 10px;padding: 0px">Duration (years)</th>
                            <th></th>
                        </tr>

                        @php $serial = 0; @endphp

                        @if($empEduDetails->isEmpty())
                            <tr id="noDataTd">
                                <th colspan="11" class="text-center">No Data Found</th>
                            </tr>
                        @endif

                        @foreach($empEduDetails as $empEduDetail)
                            <tr id="eduQualificationRow{{ $serial }}">

                                {{-- Level of Education --}}
                                <td>
                                    <select class="form-control" id="levelofEducation{{ $serial }}"
                                            name="levelOfEducation{{ $serial }}"
                                            onchange="setExamDegree(this.value, '{{ $serial }}')">
                                        <option value="{{ $empEduDetail->level_of_education }}">
                                            {{ $empEduDetail->education_level }}
                                        </option>
                                        <option value="">-- Select --</option>
                                        @foreach($levelOfEducations as $levelOfEducation)
                                            <option value="{{ $levelOfEducation->element_code }}">
                                                {{ $levelOfEducation->element }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                {{-- Exam Degree --}}
                                <td id="examDegreeTd<?php echo $serial ?>">
                                    <?php
                                    if ($empEduDetail->level_of_education == 'psc_5_pass' || $empEduDetail->level_of_education == 'jsc_jdc_8_pass' || $empEduDetail->level_of_education == 'secondary' || $empEduDetail->level_of_education == 'higher_secondary') {
                                        ?>
                                        <select class="form-control" id="examDegree<?php echo $serial ?>" name="examDegree<?php echo $serial ?>">
                                            <option value="<?php echo $empEduDetail->exam_degree ?>"><?php echo $empEduDetail->exam_title ?></option>
                                            <option value="">-- Select --</option>
                                        </select>
                                        <?php
                                    } else {
                                        ?>
                                        <input type="text" name="examDegree<?php echo $serial ?>" value="<?php echo $empEduDetail->exam_degree ?>" class="form-control">
                                        <?php
                                    }
                                    ?>

                                </td>

                                {{-- Major Group --}}
                                <td id="majorGroupTd{{ $serial }}">
                                    @if(in_array($empEduDetail->level_of_education, ['secondary','higher_secondary']))
                                        <input type="text" name="majorGroup{{ $serial }}"
                                            value="{{ $empEduDetail->major_group }}"
                                            placeholder="eg.Science" class="form-control">
                                    @else
                                        <input type="text" name="majorGroup{{ $serial }}" class="form-control" readonly>
                                    @endif
                                </td>

                                {{-- Institute --}}
                                <td>
                                    <input type="text" name="instituteName{{ $serial }}"
                                        value="{{ $empEduDetail->institute_name }}" class="form-control">
                                </td>

                                {{-- Board --}}
                                <td>
                                    <select class="form-control" name="educationBoard{{ $serial }}">
                                        <option value="{{ $empEduDetail->education_board }}">
                                            {{ $empEduDetail->education_board_name }}
                                        </option>
                                        <option value="">-- Select --</option>
                                        @foreach($educationBoards as $educationBoard)
                                            <option value="{{ $educationBoard->element_code }}">
                                                {{ $educationBoard->element }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                {{-- Result --}}
                                <td>
                                    <select class="form-control" name="qualificationResult{{ $serial }}"
                                            onchange="setCgpaMarksTextBox(this.value, '{{ $serial }}')">
                                        <option value="{{ $empEduDetail->qualification_result }}">
                                            {{ $empEduDetail->quali_result_name }}
                                        </option>
                                        <option value="">-- Select --</option>
                                        @foreach($qualificationResults as $qualificationResult)
                                            <option value="{{ $qualificationResult->element_code }}">
                                                {{ $qualificationResult->element }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                {{-- CGPA --}}
                                <td id="cgpaMarksTd{{ $serial }}">
                                    <input type="text" name="cgpaMarks{{ $serial }}" class="form-control"
                                        value="{{ $empEduDetail->cgpa_marks }}">
                                </td>

                                {{-- Scale --}}
                                <td id="scaleTd{{ $serial }}">
                                    <input type="text" name="scale{{ $serial }}" class="form-control"
                                        value="{{ $empEduDetail->scale }}">
                                </td>

                                {{-- Passing Year --}}
                                <td>
                                    <select class="form-control" name="passingYear{{ $serial }}">
                                        <option value="{{ $empEduDetail->passing_year }}">{{ $empEduDetail->passing_year }}</option>
                                        <option value="">--------</option>
                                        @for($i = 1962; $i <= date('Y'); $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </td>

                                {{-- Duration --}}
                                <td>
                                    <input type="text" name="duration{{ $serial }}" class="form-control"
                                        value="{{ $empEduDetail->duration }}">
                                </td>

                                {{-- Remove --}}
                                <td class="text-center">
                                    <input type="checkbox" id="removeCheckBox{{ $serial }}">
                                    <input type="hidden" id="hiddenEducationRow{{ $serial }}"
                                        name="hiddenEducationRow{{ $serial }}"
                                        value="{{ $empEduDetail->id }}">
                                </td>
                            </tr>

                            @php $serial++; @endphp
                        @endforeach
                    </table>

                    <input type="hidden" id="eduQualificationCount" name="eduQualificationCount" value="{{ $serial }}">
                    <input type="hidden" id="deleteEduRow" name="deleteEduRow" value="">

                    <input type='button' value='Add' id='addEduQualiButton' onclick="addEduQualificationRow()"
                        class="btn btn-sm btn-primary">
                    <input type='button' value='Remove' id='removeEduQualiButton' onclick="removeEduQualificationRow()"
                        class="btn btn-sm btn-danger">

                    <div class="checkbox text-end">
                        <input type="submit" class="btn btn-success btn-sm save_button" id="updateEmployeeSubmit" value="Update">
                    </div>
                </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
    $(document).ready(function(){
        $('.dateInput').datepicker({
            format: 'yyyy-mm-dd',  // format compatible with Laravel date column
            autoclose: true,       // close picker after selecting a date
            todayHighlight: true,  // highlight today
            clearBtn: true,        // optional clear button
            orientation: 'bottom'  // show below the input
        });
    });
    
    var examTitleObj = jQuery.parseJSON(JSON.stringify(<?php echo $examTitles ?>));
    var counter = {{ $serial }};

    function addEduQualificationRow() {
        $("#noDataTd").remove();

        var newRow = $(document.createElement('tr')).attr("id", 'eduQualificationRow' + counter);

        var levelOptions = `@foreach ($levelOfEducations as $levelOfEducation)
            <option value="{{ $levelOfEducation->element_code }}">{{ $levelOfEducation->element }}</option>
        @endforeach`;

        var boardOptions = `@foreach ($educationBoards as $educationBoard)
            <option value="{{ $educationBoard->element_code }}">{{ $educationBoard->element }}</option>
        @endforeach`;

        var resultOptions = `@foreach ($qualificationResults as $qualificationResult)
            <option value="{{ $qualificationResult->element_code }}">{{ $qualificationResult->element }}</option>
        @endforeach`;

        var yearOptions = '';
        for (let i = 1962; i <= 2026; i++) {
            yearOptions += `<option value="${i}">${i}</option>`;
        }

        newRow.html(`
            <td>
                <select class="form-control" name="levelOfEducation${counter}" id="levelOfEducation${counter}" onchange="setExamDegree(this.value, ${counter})">
                    <option value="">-- Select --</option>
                    ${levelOptions}
                </select>
            </td>
            <td id="examDegreeTd${counter}">
                <select class="form-control" name="examDegree${counter}">
                </select>
            </td>
            <td id="majorGroupTd${counter}">
                <input type="text" name="majorGroup${counter}" class="form-control" readonly>
            </td>
            <td>
                <input type="text" name="instituteName${counter}" class="form-control">
            </td>
            <td>
                <select class="form-control" name="educationBoard${counter}">
                    <option value="">-- Select --</option>
                    ${boardOptions}
                </select>
            </td>
            <td>
                <select class="form-control" name="qualificationResult${counter}" onchange="setCgpaMarksTextBox(this.value, ${counter})">
                    <option value="">-- Select --</option>
                    ${resultOptions}
                </select>
            </td>
            <td id="cgpaMarksTd${counter}">
                <input type="text" name="cgpaMarks${counter}" class="form-control" readonly>
            </td>
            <td id="scaleTd${counter}">
                <input type="text" name="scale${counter}" class="form-control" readonly>
            </td>
            <td>
                <select class="form-control" name="passingYear${counter}">
                    <option value="">--------</option>
                    ${yearOptions}
                </select>
            </td>
            <td>
                <input type="text" name="duration${counter}" class="form-control">
            </td>
            <td class="text-center">
                <input type="checkbox" id="removeCheckBox${counter}">
                <input type="hidden" name="hiddenEducationRow${counter}" id="hiddenEducationRow${counter}" value="0">
            </td>
        `);

        newRow.appendTo("#eduQualificationTable");
        $('#eduQualificationCount').val(counter + 1);
        counter++;
    }

    function removeEduQualificationRow() {
        var idArr = new Array();
        for (var i = 1; i < counter; i++) {
            if ($('#removeCheckBox' + i).is(':checked')) {
                idArr.push($('#hiddenEducationRow' + i).val());
                $("#eduQualificationRow" + i).remove();
            }
        }
        if ($('#deleteEduRow').val() !== "") {
            idArr.push($('#deleteEduRow').val());
        }
        $('#deleteEduRow').val(idArr.join());

        console.log(idArr);
    }

    function setExamDegree(levelOfEducation, rowCount) {

        if (levelOfEducation === 'psc_5_pass' || levelOfEducation === 'jsc_jdc_8_pass' || levelOfEducation === 'secondary' || levelOfEducation === 'higher_secondary') {
            var optionStr = "<option value=''>-- Select --</option>";
            for (var i = 0; i < examTitleObj.examTitleData.length; i++) {
                if (examTitleObj.examTitleData[i].depend_on_element === levelOfEducation) {
                    optionStr += "<option value='" + examTitleObj.examTitleData[i].element_code + "'>" + examTitleObj.examTitleData[i].element + "</option>";
                }
            }
            $('#examDegreeTd' + rowCount).html('<select class="form-control" name="examDegree' + rowCount + '">' + optionStr + '</select>');
        } else if (levelOfEducation === "") {
            $('#examDegreeTd' + rowCount).html('<input type="text" name="examDegree' + rowCount + '" class="form-control" readonly>');
        } else {
            $('#examDegreeTd' + rowCount).html('<input type="text" name="examDegree' + rowCount + '" class="form-control">');
        }

        if (levelOfEducation === 'secondary' || levelOfEducation === 'higher_secondary') {
            $('#majorGroupTd' + rowCount).html('<input type="text" name="majorGroup' + rowCount + '" placeholder="eg.Science" class="form-control" >');
        } else {
            $('#majorGroupTd' + rowCount).html('<input type="text" name="majorGroup' + rowCount + '"  class="form-control" readonly>');
        }

    }

    function setCgpaMarksTextBox(qualificationResult, rowCount) {
        $('#scaleTd' + rowCount).html('<input type="text" name="scale' + rowCount + '" class="form-control">');
        if (qualificationResult === 'first_division_class' || qualificationResult === 'second_division_class' || qualificationResult === 'third_division_class') {
            $('#cgpaMarksTd' + rowCount).html('<input type="text" placeholder="Marks" name="cgpaMarks' + rowCount + '" class="form-control">');
        } else if (qualificationResult === 'grade') {
            $('#cgpaMarksTd' + rowCount).html('<input type="text" placeholder="CGPA" name="cgpaMarks' + rowCount + '" class="form-control">');
        } else if (qualificationResult === "") {
            $('#cgpaMarksTd' + rowCount).html('<input type="text" name="cgpaMarks' + rowCount + '" class="form-control" readonly>');
            $('#scaleTd' + rowCount).html('<input type="text" name="scale' + rowCount + '" class="form-control" readonly>');
        } else {
            $('#cgpaMarksTd' + rowCount).html('<input type="text"name="cgpaMarks' + rowCount + '" class="form-control">');
        }
    }
</script>
@endpush
