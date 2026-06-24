@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">
        {{ isset($data->exists) ? 'Edit Boarder' : 'Add Boarder' }}
    </h1>
</div>

<?php
    $day = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31');
    $month = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
?>

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
            @include('admin.boarder.add-boarder.tab')

            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif


            <div class="tab-content" id="workingExperianceTabContent">

                <div class="tab-pane fade show active"
                    id="workingExperiance"
                    role="tabpanel">
                    <form action="{{ route('admin.boarder.working.experience.update',$data->boarder_id) }}" method="POST">
                        @csrf

                        <div class="accordion" id="employeeAccordion">

                            {{-- Personal Information --}}
                            <div class="accordion-item active">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#personalInfo"
                                            aria-expanded="true">
                                        Working Experience
                                    </button>
                                </h2>

                                <div id="personalInfo"
                                    class="accordion-collapse collapse show"
                                    data-bs-parent="#employeeAccordion">

                                    <div class="accordion-body">

                                        <div class="row g-3">
                                            <div class="panel-body" id="empWorkingDetailDiv">

                                            @if(empty($workingDetails) || count($workingDetails) === 0)
                                                <div id="noDataDiv" class="well well-sm text-center">
                                                    <b>No Data Found</b>
                                                </div>
                                            @endif

                                            @php $serial = 1; @endphp

                                            @foreach($workingDetails as $employeeWorkingDetail)

                                            <div class="panel-body" id="workingExpDiv{{ $serial }}">
                                                
                                                <div class="row">
                                                    <div class="col-md-12 text-center">
                                                        <h3 class="experience_title">
                                                            <b>
                                                                Working Experience 
                                                                <span id="workingExpNo{{ $serial }}">{{ $serial }}</span>
                                                            </b>
                                                        </h3>
                                                        <hr>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Institution/Organization Name</label>
                                                            <small class="custom-text-danger" id="institutionNameError{{ $serial }}"></small>

                                                            <input type="text"
                                                                class="form-control"
                                                                id="institutionName{{ $serial }}"
                                                                name="institutionName{{ $serial }}"
                                                                value="{{ $employeeWorkingDetail->institution_name }}"
                                                                required>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Institution/Organization Type</label>
                                                            <input type="text"
                                                                class="form-control"
                                                                name="institutionType{{ $serial }}"
                                                                value="{{ $employeeWorkingDetail->institution_type }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Address</label>
                                                            <input type="text"
                                                                class="form-control"
                                                                name="address{{ $serial }}"
                                                                value="{{ $employeeWorkingDetail->address }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Designation</label>
                                                            <input type="text"
                                                                class="form-control"
                                                                name="designation{{ $serial }}"
                                                                value="{{ $employeeWorkingDetail->designation }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                @php
                                                    $fromDateArr = explode('-', $employeeWorkingDetail->from_date);

                                                    if ($employeeWorkingDetail->is_continued == 0) {
                                                        $continueBlock = "none";
                                                        $toDateBlock = "block";
                                                        $checkBoxValue = 0;
                                                    } else {
                                                        $continueBlock = "block";
                                                        $toDateBlock = "none";
                                                        $checkBoxValue = 1;
                                                    }

                                                    $toDateArr = $employeeWorkingDetail->to_date
                                                        ? explode('-', $employeeWorkingDetail->to_date)
                                                        : ['', '', ''];
                                                @endphp

                                                <div class="row">
                                                    <div class="col-md-6">

                                                        <label class="form-label">Department</label>
                                                        <input type="text"
                                                            class="form-control"
                                                            name="department{{ $serial }}"
                                                            value="{{ $employeeWorkingDetail->department }}">

                                                        <div class="mt-3 mb-2">
                                                            <b>Working Period</b>
                                                        </div>

                                                        <div class="row mt-2">
                                                            <div class="col-md-6">
                                                                <label class="form-label">From Date</label>
                                                                <small class="custom-text-danger" id="fromDateError{{ $serial }}"></small>

                                                                <select name="fYear{{ $serial }}"
                                                                        id="fYear{{ $serial }}"
                                                                        onchange="setDay('{{ $serial }}',1)"
                                                                        class="form-select" required>

                                                                    @if(!empty($fromDateArr[0]))
                                                                        <option value="{{ $fromDateArr[0] }}">{{ $fromDateArr[0] }}</option>
                                                                    @endif

                                                                    <option value="">--yyyy--</option>
                                                                    @for($i=1962;$i<2026;$i++)
                                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                                    @endfor
                                                                </select>

                                                                <select name="fMonth{{ $serial }}"
                                                                        id="fMonth{{ $serial }}"
                                                                        onchange="setDay('{{ $serial }}',1)"
                                                                        class="form-select mt-1" required>

                                                                    @if(!empty($fromDateArr[1]))
                                                                        <option value="{{ $fromDateArr[1] }}">{{ $fromDateArr[1] }}</option>
                                                                    @endif

                                                                    <option value="">--mm--</option>
                                                                    @foreach($month as $m)
                                                                        <option value="{{ $m }}">{{ $m }}</option>
                                                                    @endforeach
                                                                </select>

                                                                <select name="fDay{{ $serial }}"
                                                                        id="fDay{{ $serial }}"
                                                                        class="form-select mt-1" required>

                                                                    @if(!empty($fromDateArr[2]))
                                                                        <option value="{{ $fromDateArr[2] }}">{{ $fromDateArr[2] }}</option>
                                                                    @endif
                                                                </select>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label">To Date</label>
                                                                <small class="custom-text-danger" id="toDateError{{ $serial }}"></small>

                                                                <div id="toDateDiv{{ $serial }}" style="display:{{ $toDateBlock }}">

                                                                    <select name="tYear{{ $serial }}"
                                                                            id="tYear{{ $serial }}"
                                                                            onchange="setDay('{{ $serial }}',2)"
                                                                            class="form-select">

                                                                        @if(!empty($toDateArr[0]))
                                                                            <option value="{{ $toDateArr[0] }}">{{ $toDateArr[0] }}</option>
                                                                        @endif

                                                                        <option value="">--yyyy--</option>
                                                                        @for($i=1962;$i<2026;$i++)
                                                                            <option value="{{ $i }}">{{ $i }}</option>
                                                                        @endfor
                                                                    </select>

                                                                    <select name="tMonth{{ $serial }}"
                                                                            id="tMonth{{ $serial }}"
                                                                            onchange="setDay('{{ $serial }}',2)"
                                                                            class="form-select mt-1">

                                                                        @if(!empty($toDateArr[1]))
                                                                            <option value="{{ $toDateArr[1] }}">{{ $toDateArr[1] }}</option>
                                                                        @endif

                                                                        <option value="">--mm--</option>
                                                                        @foreach($month as $m)
                                                                            <option value="{{ $m }}">{{ $m }}</option>
                                                                        @endforeach
                                                                    </select>

                                                                    <select name="tDay{{ $serial }}"
                                                                            id="tDay{{ $serial }}"
                                                                            class="form-select mt-1">

                                                                        @if(!empty($toDateArr[2]))
                                                                            <option value="{{ $toDateArr[2] }}">{{ $toDateArr[2] }}</option>
                                                                        @endif
                                                                        <option value="">--dd--</option>
                                                                    </select>
                                                                </div>

                                                                <div id="toContinueDiv{{ $serial }}" style="display:{{ $continueBlock }}">
                                                                    <input type="text"
                                                                        value="Continuing"
                                                                        name="toDate{{ $serial }}"
                                                                        id="toDate{{ $serial }}"
                                                                        class="form-control"
                                                                        readonly>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <small class="custom-text-danger" id="fromToErrorBlock{{ $serial }}"></small>

                                                        <div class="form-check mt-2">
                                                            <input type="checkbox"
                                                                class="form-check-input"
                                                                name="currentWorkCheckbox{{ $serial }}"
                                                                id="currentWorkCheckbox{{ $serial }}"
                                                                value="{{ $checkBoxValue }}"
                                                                onclick="continueWorkingCheck('{{ $serial }}')"
                                                                {{ $checkBoxValue ? 'checked' : '' }}>

                                                            <label class="form-check-label">
                                                                Currently Working
                                                            </label>
                                                        </div>

                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Responsibilities</label>
                                                        <textarea class="form-control"
                                                                rows="5"
                                                                name="responsibilites{{ $serial }}">{{ $employeeWorkingDetail->responsibilites }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="mt-3">
                                                    <input type="button"
                                                        value="Remove"
                                                        onclick="removeWorkingExpDiv('{{ $serial }}')"
                                                        class="btn btn-sm btn-danger remove_button">
                                                </div>

                                                <input type="hidden"
                                                    id="hiddenWorkingDiv{{ $serial }}"
                                                    name="hiddenWorkingDiv{{ $serial }}"
                                                    value="{{ $employeeWorkingDetail->id }}">
                                            </div>

                                            <div id="breakDiv{{ $serial }}"><br></div>

                                            @php $serial++; @endphp
                                            @endforeach
                                            <input type="hidden" id="deleteWorkingRow" name="deleteWorkingRow">
                                        </div>
                                        <div class="col-md-3 mt-1">
                                            <input type="button"
                                                value="Add More Working Experience"
                                                onclick="addEduQualificationDiv()"
                                                class="btn btn-sm btn-primary add_button">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="checkbox">
                            <input type="hidden"
                                name="employeeId"
                                id="employeeId"
                                value="{{ $data->boarder_id ?? '' }}">

                            <input type="hidden"
                                name="workingExpCount"
                                id="workingExpCount"
                                value="{{ isset($employeeWorkingDetails) ? count($employeeWorkingDetails) : 0 }}">

                            <input type="submit"
                                class="btn btn-success btn-sm save_button"
                                id="updateEmployeeSubmit"
                                value="Update">

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
    var counter = {{ count($workingDetails ?? []) }};
    counter++;

    function addEduQualificationDiv() {

        $("#noDataDiv").remove();

        var tDt = "2";
        var fDt = "1";

        var newRow = $(document.createElement('div'))
                        .attr("id", 'workingExpDiv' + counter);

        newRow.after().html('<div class="panel-body">\
            <div class="row">\
                <div class="col-md-12 text-center">\
                    <h3 class="experience_title"><b>Working Experience <span id="workingExpNo' + counter + '">' + counter + '</span></b></h3><hr>\
                </div>\
            </div>\
            <div class="row">\
                <div class="col-md-6">\
                    <div class="form-group">\
                        <label class="form-label"> Institution/Organization Name </label>\
                        <small class="text-danger" id="institutionNameError' + counter + '"></small>\
                        <input type="text" placeholder="Institution/Organization Name" class="form-control" name="institutionName' + counter + '" id="institutionName' + counter + '" required>\
                    </div>\
                </div>\
                <div class="col-md-6">\
                    <div class="form-group">\
                        <label class="form-label"> Institution/Organization Type </label>\
                        <input type="text" placeholder="Institution/Organization Type" class="form-control" name="institutionType' + counter + '">\
                    </div>\
                </div>\
            </div>\
            <div class="row">\
                <div class="col-md-6">\
                    <div class="form-group">\
                        <label class="form-label"> Address </label>\
                        <input type="text" placeholder="Address" class="form-control" name="address' + counter + '">\
                    </div>\
                </div>\
                <div class="col-md-6">\
                    <div class="form-group">\
                        <label class="form-label"> Designation </label>\
                        <input type="text" placeholder="Designation" class="form-control" name="designation' + counter + '">\
                    </div>\
                </div>\
            </div>\
            <div class="row">\
                <div class="col-md-6">\
                    <div class="form-group">\
                        <label class="form-label"> Department </label>\
                        <input type="text" class="form-control" name="department' + counter + '">\
                    </div>\
                    <div class="row">\
                        <div class="col-md-12 mt-3 mb-2">\
                            <b>Working Period</b>\
                        </div>\
                        <div class="col-md-6">\
                            <div class="form-group ">\
                                <label class="form-label"> From Date </label> <small class="text-danger" id="fromDateError' + counter + '"></small><br>\
                                <select required class="form-control" name="fYear' + counter + '" id="fYear' + counter + '" onchange="setDay(' + counter + ',' + fDt + ')">\
                                    <option value="">--yyyy--</option>\
                                    @for ($i = 1962; $i <= date('Y'); $i++)\
                                        <option value="{{ $i }}">{{ $i }}</option>\
                                    @endfor\
                                </select>\
                                <select required class="form-control mt-1" name="fMonth' + counter + '" id="fMonth' + counter + '" onchange="setDay(' + counter + ',' + fDt + ')">\
                                    <option value="">-- mm --</option>\
                                    @foreach ($month as $m)\
                                        <option value="{{ $m }}">{{ $m }}</option>\
                                    @endforeach\
                                </select>\
                                <select required class="form-control mt-1" name="fDay' + counter + '" id="fDay' + counter + '">\
                                    <option value="">-- dd --</option>\
                                </select>\
                                <small class="text-danger" id="fromToErrorBlock' + counter + '"></small>\
                                <div class="mt-3">\
                                <input type="checkbox" name="currentWorkCheckbox' + counter + '" id="currentWorkCheckbox' + counter + '" onclick="continueWorkingCheck(' + counter + ')">\
                                <label class="form-label"> Currently Working</label>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-6">\
                            <div class="form-group">\
                                <label class="form-label"> To Date </label> <small class="text-danger" id="toDateError' + counter + '"></small>\
                                <div id="toDateDiv' + counter + '">\
                                    <select class="form-control" name="tYear' + counter + '" id="tYear' + counter + '" onchange="setDay(' + counter + ',' + tDt + ')">\
                                        <option value="">--yyyy--</option>\
                                        @for ($i = 1962; $i <= date('Y'); $i++)\
                                            <option value="{{ $i }}">{{ $i }}</option>\
                                        @endfor\
                                    </select>\
                                    <select class="form-control mt-1" name="tMonth' + counter + '" id="tMonth' + counter + '" onchange="setDay(' + counter + ',' + tDt + ')">\
                                        <option value="">-- mm --</option>\
                                        @foreach ($month as $m)\
                                            <option value="{{ $m }}">{{ $m }}</option>\
                                        @endforeach\
                                    </select>\
                                    <select class="form-control mt-1" name="tDay' + counter + '" id="tDay' + counter + '">\
                                        <option value="">-- dd --</option>\
                                    </select>\
                                </div>\
                                <div id="toContinueDiv' + counter + '" style="display:none">\
                                    <input type="text" value="Continuing" name="toDate' + counter + '" id="toDate' + counter + '" class="form-control" readonly>\
                                </div>\
                            </div>\
                        </div>\
                    </div>\
                </div>\
                <div class="col-md-6">\
                    <div class="form-group">\
                        <label class="form-label"> Responsibilities </label>\
                        <textarea class="form-control" rows="5" style="resize:none" name="responsibilites' + counter + '"></textarea>\
                    </div>\
                </div>\
            </div>\
            <div class="row mt-2">\
                <div class="col-md-6">\
                    <input type="button" value="Remove" onclick="removeWorkingExpDiv(' + counter + ')" class="btn btn-sm btn-danger remove_button">\
                </div>\
            </div>\
        </div><div id="breakDiv' + counter + '"><br></div>');

        newRow.appendTo("#empWorkingDetailDiv");

        $('#workingExpCount').val(counter);

        counter++;

        var i;
        var expNoDiv = 1;

        for (i = 1; i <= counter; i++) {
            if ($("#workingExpNo" + i).length != 0) {
                document.getElementById('workingExpNo' + i).innerHTML = expNoDiv;
                expNoDiv++;
            }
        }
    }

    function continueWorkingCheck(serial) {
        if ($('#currentWorkCheckbox' + serial).is(':checked')) {
            //console.log('checked');
            $("#currentWorkCheckbox" + serial).val('1');
            $("#toContinueDiv" + serial).show();
            $("#toDateDiv" + serial).hide();
        } else {
            //console.log('not checked');
            $("#currentWorkCheckbox" + serial).val('0');
            $("#toContinueDiv" + serial).hide();
            $("#toDateDiv" + serial).show();
        }
    }

    function updateEmployee() {

        var msgArray = [];

        var organizationNameStr = "<strong><li>Institution/Organization Name is required</li></strong>";
        var fromDateStr         = "<strong><li>From Date is required</li></strong>";
        var toDateStr           = "<strong><li>To Date is required</li></strong>";
        var toDateGreaterStr    = "<strong><li>To Date should be greater than From Date</li></strong>";

        for (var i = 1; i < counter; i++) {

            var fromToFlag = 1;

            /* ===============================
            Institution Name Validation
            ================================*/
            if ($.trim($("#institutionName" + i).val()) === "") {

                $("#institutionNameError" + i)
                    .html(" Institution/Organization Name is required");

                if ($.inArray(organizationNameStr, msgArray) === -1) {
                    msgArray.push(organizationNameStr);
                }

            } else {
                $("#institutionNameError" + i).html("");
            }


            /* ===============================
            From Date Validation
            ================================*/
            if (
                $("#fDay" + i).val() === "" ||
                $("#fMonth" + i).val() === "" ||
                $("#fYear" + i).val() === ""
            ) {

                $("#fromDateError" + i).html(" From Date is required");
                $("#fromToErrorBlock" + i).html("");
                fromToFlag = 0;

                if ($.inArray(fromDateStr, msgArray) === -1) {
                    msgArray.push(fromDateStr);
                }

            } else {
                $("#fromDateError" + i).html("");
            }


            /* ===============================
            To Date Validation
            ================================*/
            if (!$("#currentWorkCheckbox" + i).is(":checked")) {

                if (
                    $("#tDay" + i).val() === "" ||
                    $("#tMonth" + i).val() === "" ||
                    $("#tYear" + i).val() === ""
                ) {

                    $("#toDateError" + i).html(" To Date is required");
                    $("#fromToErrorBlock" + i).html("");
                    fromToFlag = 0;

                    if ($.inArray(toDateStr, msgArray) === -1) {
                        msgArray.push(toDateStr);
                    }

                } else {
                    $("#toDateError" + i).html("");
                }


                /* ===============================
                Date Comparison
                ================================*/
                if (fromToFlag === 1) {

                    var toDate = new Date(
                        $("#tYear" + i).val() + "-" +
                        $("#tMonth" + i).val() + "-" +
                        $("#tDay" + i).val()
                    );

                    var fromDate = new Date(
                        $("#fYear" + i).val() + "-" +
                        $("#fMonth" + i).val() + "-" +
                        $("#fDay" + i).val()
                    );

                    if (toDate < fromDate) {

                        $("#fromToErrorBlock" + i)
                            .html(" To Date should be greater than from date");

                        if ($.inArray(toDateGreaterStr, msgArray) === -1) {
                            msgArray.push(toDateGreaterStr);
                        }

                    } else {
                        $("#fromToErrorBlock" + i).html("");
                    }

                } else {
                    $("#fromToErrorBlock" + i).html("");
                }

            } else {
                $("#toDateError" + i).html("");
            }
        }


        /* ===============================
        Final Error Handling
        ================================*/
        if (msgArray.length > 0) {

            $("#errorBlockDiv")
                .show()
                .html(msgArray.join(""));

            var etop = $("#contentDiv").offset().top;

            $("html, body").animate({
                scrollTop: etop
            }, 1000);

            return false;

        } else {
            $("#errorBlockDiv").hide();
        }

        $("#updateEmployeeSubmit").click();
    }


    function removeWorkingExpDiv(serial) {

        var idArr = [];

        /* ===============================
        Collect Deleted Row ID
        ================================*/
        var hiddenId = $('#hiddenWorkingDiv' + serial).val();

        if (hiddenId !== undefined && hiddenId !== "") {
            idArr.push(hiddenId);
        }

        var existingDeleted = $('#deleteWorkingRow').val();

        if (existingDeleted !== "") {
            idArr.push(existingDeleted);
        }

        /* ===============================
        Remove Working Experience Block
        ================================*/
        $('#workingExpDiv' + serial).remove();
        $('#breakDiv' + serial).remove();

        /* ===============================
        Store Deleted IDs
        ================================*/
        $('#deleteWorkingRow').val(idArr.join(','));

        /* ===============================
        Reorder Experience Serial Number
        ================================*/
        var expNoDiv = 1;

        for (var i = 1; i <= counter; i++) {

            if ($("#workingExpNo" + i).length !== 0) {

                $("#workingExpNo" + i).html(expNoDiv);
                expNoDiv++;
            }
        }
    }

    function setDay(serial, fromToFlag) {

        /* ===============================
        Determine From / To Prefix
        ================================*/
        if (fromToFlag === 1) {
            fromToFlag = 'f';
        } else if (fromToFlag === 2) {
            fromToFlag = 't';
        }

        var $yearSelect  = $("#" + fromToFlag + "Year" + serial);
        var $monthSelect = $("#" + fromToFlag + "Month" + serial);
        var $daySelect   = $("#" + fromToFlag + "Day" + serial);

        var year = $yearSelect.val();

        /* ===============================
        Reset Day Dropdown
        ================================*/
        $daySelect.empty().append('<option value="">--dd--</option>');

        if (year !== "") {

            var dayArr = [
                '01','02','03','04','05','06','07','08','09','10',
                '11','12','13','14','15','16','17','18','19','20',
                '21','22','23','24','25','26','27','28','29','30','31'
            ];

            var m = parseInt($monthSelect.val(), 10);
            var y = parseInt(year, 10);

            var validDateArr = [];

            for (var i = 0; i <= dayArr.length; i++) {

                var d = parseInt(dayArr[i], 10);
                var date = new Date(y, m - 1, d);

                if (
                    date.getFullYear() == y &&
                    date.getMonth() + 1 == m &&
                    date.getDate() == d
                ) {
                    validDateArr.push(
                        '<option value="' + dayArr[i] + '">' + dayArr[i] + '</option>'
                    );
                } else {
                    break;
                }
            }

            $daySelect.append(validDateArr.join(''));
        }
    }
</script>
@endpush
