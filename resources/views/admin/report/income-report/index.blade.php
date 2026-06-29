@extends('layouts.app')

@section('content')

<div class="header">
    <h1 class="page-title">Income Report</h1>
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">Home</a> / </li>
        <li><a href="#">Report</a> / </li>
        <li class="active"><a href="/admin/income-report"> Income Report</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-default"> 
                <div class="row mb-4">
                    <div class="col-md-6 col-sm-6 col-xs-12 mb-2">
                        <div class="form-group form-float" >
                            <label class="form-label"> From Date</label>
                            <input type="text" class="form-control dateInput" name="fromDate" id="fromDate" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12 mb-3">
                        <div class="form-group form-float" >
                            <label class="form-label"> To Date</label>
                            <input type="text" class="form-control dateInput" name="toDate" id="toDate" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12 mb-3">
                        <div class="form-group form-float" >
                            <label class="form-label"> Status</label>
                            <select class="form-control" id="status">
                                <option value="">All</option>
                                <option value="{{ config('constants.UNPAID') }}">Unpaid</option>
                                <option value="{{ config('constants.PAID') }}">Paid</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12 align-items-center">
                        <div class="form-group form-float">
                            <div class="row my-auto">
                                <div class="col-md-6 col-sm-6 col-xs-12" style="margin-top: 25px">
                                    <input type="radio" name="dateTypeCheckBox" value="invoiceDate" checked>                      
                                    <label class="form-label"> Invoice Date</label><br>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12" style="margin-top: 25px">
                                    <input type="radio" name="dateTypeCheckBox" value="dueDate">
                                    <label class="form-label"> Due Date</label><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-center mb-2">
                    <b style="font-size: 14px">Item Head</b>
                </div>
                <table class="table table-bordered table-hover custom-table dataTable" id="datatable">
                    <thead>
                        <tr class="bg-primary">
                            <th>SL</th>
                            <th>Item Category</th>
                            <th>Item Head</th>
                            <th class="no-sort" style="width:50px">
                                <input type="checkbox" id="selectAllHead" class="filled-in chk-col-blue" onClick="selectAllHead(this)" />
                                <!--<label for="selectAllHead" class="form-label m-l-20 m-b--10"></label>-->
                            </th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $count = 1;
                        $itemCategory = '';
                        foreach ($itemHeads as $itemHead) {
                            if ($itemCategory == '') {
                                $bgColor = '#efebe8';
                            } else if ($itemCategory != $itemHead->item_category) {
                                if ($bgColor == '#f7f7f7') {
                                    $bgColor = '#efebe8';
                                } else {
                                    $bgColor = '#f7f7f7';
                                }
                            }
                            $itemCategory = $itemHead->item_category;
                            echo "<tr style='background-color:$bgColor'>";

                            echo "<td class='td-center'>$count</td>";
                            echo "<td class='td-left'>$itemHead->category_name</td>";
                            echo "<td class='td-left'>$itemHead->item_head</td>";
                            ?>
                        <td class='td-center'>
                            <input type="checkbox" id='headCheck<?php echo $count ?>' value='<?php echo $itemHead->item_head_code ?>' name='headCheck[]' onclick="setHeadCheckBox(this.value, this.id)" class="filled-in chk-col-blue"/>
                            <!--<label for="headCheck<?php echo $count ?>" class="form-label" style="margin-bottom: -12px"></label>-->
                        </td>
                        <?php
                        $count++;
                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
                <hr>
                <div class="text-center mb-3">
                    <b style="font-size: 14px">Boarder</b>
                </div>
                <table class="table table-bordered table-hover custom-table dataTable m-t-20" id="datatable2">
                    <thead>
                        <tr class="bg-primary">
                            <th>SL</th>
                            <th>Boarder Name</th>
                            <th>Boarder Code</th>
                            <th>Address</th>
                            <th>Mobile</th>
                            <th class="no-sort" style="width:50px">
                                <input type="checkbox" id="selectAllBoarder" class="filled-in chk-col-blue" onClick="selectAllBoarder(this)" />
                                <!--<label for="selectAllBoarder" class="form-label m-l-20 m-b--10"></label>-->
                            </th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $count = 1;
                        foreach ($boarders as $boarder) {
                            echo "<tr>";
                            echo "<td class='td-center'>$count</td>";
                            echo "<td class='td-left'>$boarder[boarder_name]</td>";
                            echo "<td class='td-center'>$boarder[boarder_id]</td>";
                            echo "<td class='td-left'>$boarder[present_address]</td>";
                            echo "<td class='td-center'>$boarder[primary_mobile]</td>";
                            ?>
                        <td class="td-center">
                            <input type="checkbox" id='boarderCheck<?php echo $count ?>' value='<?php echo $boarder['boarder_id'] ?>' name='boarderCheck[]' onclick="setBoarderCheckBox(this.value, this.id)" class="filled-in chk-col-blue"/>
                            <!--<label for="boarderCheck<?php echo $count ?>" class="form-label" style="margin-bottom: -12px"></label>-->
                        </td>
                        <?php
                        $count++;
                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
                <form target="_blank" action="{{ route('admin.income.report.details') }}" method="POST" id="formId">
                    @csrf
                    <input type="hidden" name="itemHeadStr" id="itemHeadStr">
                    <input type="hidden" name="boarderStr" id="boarderStr">
                    <input type="hidden" name="fromDate" id="fromDateHidden">
                    <input type="hidden" name="toDate" id="toDateHidden">
                    <input type="hidden" name="status" id="statusHidden">
                    <input type="hidden" name="dateType" id="dateTypeHidden">
                </form>
                <div class="text-left">
                    <button class="btn btn-primary save_button mt-3" onclick="submitForm()">Show Report</button>
                </div> 
            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<script>
$(document).ready(function () {

    $('#datatable').DataTable({
        pageLength: 10,
        ordering: true,
        searching: true,
        columnDefs: [
            {
                defaultContent: "-",
                targets: "_all"
            }
        ],
        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;

                // ❌ Skip Action column (last column index = 7)
                if (column.index() === 3) return;

                var select = $('<select class="form-control" style="width:100%"><option value="">All</option></select>')
                    .appendTo($(column.footer()).empty())
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());

                        column
                            .search(val ? '^' + val + '$' : '', true, false)
                            .draw();
                    });

                column.data().unique().sort().each(function (d) {

                    // ✅ Convert HTML → plain text
                    var text = $('<div>').html(d).text().trim();

                    if (text) {
                        select.append('<option value="' + text + '">' + text + '</option>');
                    }
                });
            });
        }
    });

     $('#datatable2').DataTable({
        pageLength: 10,
        ordering: true,
        searching: true,
        columnDefs: [
            {
                defaultContent: "-",
                targets: "_all"
            }
        ],
        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;

                // ❌ Skip Action column (last column index = 7)
                if (column.index() === 5) return;

                var select = $('<select class="form-control" style="width:100%"><option value="">All</option></select>')
                    .appendTo($(column.footer()).empty())
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());

                        column
                            .search(val ? '^' + val + '$' : '', true, false)
                            .draw();
                    });

                column.data().unique().sort().each(function (d) {

                    // ✅ Convert HTML → plain text
                    var text = $('<div>').html(d).text().trim();

                    if (text) {
                        select.append('<option value="' + text + '">' + text + '</option>');
                    }
                });
            });
        }
    });

});
</script>
<script>
    var itemHeadArr = new Array();
    function selectAllHead(source) {
        checkboxes = document.getElementsByName('headCheck[]');
        var itemCheckBoxIdArr = new Array();
        for (var i in checkboxes) {
            checkboxes[i].checked = source.checked;
            if (typeof (checkboxes[i].id) !== 'undefined') {
                itemCheckBoxIdArr.push(checkboxes[i].id);
            }
        }
        for (var i = 0; i < itemCheckBoxIdArr.length; i++) {
            if ($("#" + itemCheckBoxIdArr[i]).is(':checked')) {
                var itemtoRemove = $("#" + itemCheckBoxIdArr[i]).val();
                itemHeadArr = jQuery.grep(itemHeadArr, function (value) {
                    return value !== itemtoRemove;
                });
                itemHeadArr.push($("#" + itemCheckBoxIdArr[i]).val());
            } else {
                var itemtoRemove = $("#" + itemCheckBoxIdArr[i]).val();
                itemHeadArr = jQuery.grep(itemHeadArr, function (value) {
                    return value !== itemtoRemove;
                });
            }
        }
    }

    function setHeadCheckBox(itemId, checkBoxId) {
        if ($("#" + checkBoxId).is(':checked')) {
            var itemtoRemove = itemId;
            itemHeadArr = jQuery.grep(itemHeadArr, function (value) {
                return value !== itemtoRemove;
            });
            itemHeadArr.push(itemId);
        } else {
            var itemtoRemove = itemId;
            itemHeadArr = jQuery.grep(itemHeadArr, function (value) {
                return value !== itemtoRemove;
            });
        }
    }

    var boarderArr = new Array();
    function selectAllBoarder(source) {
        checkboxes = document.getElementsByName('boarderCheck[]');
        var boarderCheckBoxIdArr = new Array();
        for (var i in checkboxes) {
            checkboxes[i].checked = source.checked;
            if (typeof (checkboxes[i].id) !== 'undefined') {
                boarderCheckBoxIdArr.push(checkboxes[i].id);
            }
        }
        for (var i = 0; i < boarderCheckBoxIdArr.length; i++) {
            if ($("#" + boarderCheckBoxIdArr[i]).is(':checked')) {
                var itemtoRemove = $("#" + boarderCheckBoxIdArr[i]).val();
                boarderArr = jQuery.grep(boarderArr, function (value) {
                    return value !== itemtoRemove;
                });
                boarderArr.push($("#" + boarderCheckBoxIdArr[i]).val());
            } else {
                var itemtoRemove = $("#" + boarderCheckBoxIdArr[i]).val();
                boarderArr = jQuery.grep(boarderArr, function (value) {
                    return value !== itemtoRemove;
                });
            }
        }
    }

    function setBoarderCheckBox(boarderCode, checkBoxId) {
        if ($("#" + checkBoxId).is(':checked')) {
            var itemtoRemove = boarderCode;
            boarderArr = jQuery.grep(boarderArr, function (value) {
                return value !== itemtoRemove;
            });
            boarderArr.push(boarderCode);
        } else {
            var itemtoRemove = boarderCode;
            boarderArr = jQuery.grep(boarderArr, function (value) {
                return value !== itemtoRemove;
            });
        }
    }

    function submitForm() {
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var status = $('#status').val();
//        var dateType = $('#dateTypeCheckBox').val();
        var dateType = $("input[name='dateTypeCheckBox']:checked").val();
        
//        console.log(status);
//        return false;
        
        if (fromDate === "" || toDate === "") {
            sweetAlert('From Date and To Date is required...!');
            return false;
        }

        var toDateCheck = new Date(toDate);
        var fromDateCheck = new Date(fromDate);
        if (toDateCheck < fromDateCheck) {
            sweetAlert('To Date should be greater than or equal of From date...!');
            return false;
        }

        var itemHeadStr = itemHeadArr.join();
        var boarderStr = boarderArr.join();

        if (itemHeadArr.length === 0 && boarderArr.length === 0) {
            sweetAlert('Please select at least one income head or one boarder...!');
            return false;
        }

        $('#itemHeadStr').val(itemHeadStr);
        $('#boarderStr').val(boarderStr);
        $('#fromDateHidden').val(fromDate);
        $('#toDateHidden').val(toDate);
        $('#statusHidden').val(status);
        $('#dateTypeHidden').val(dateType);

        $("#formId").submit();
    }
</script>
@endpush