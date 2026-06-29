@extends('layouts.app')

@section('content')

<div class="header">
    <h1 class="page-title">Expense Report</h1>
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">Home</a> / </li>
        <li><a href="#">Report</a> / </li>
        <li class="active"><a href="/admin/exp-report"> Expense Report</a></li>
    </ul>
</div>
<div class="main-content">
    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <div class="row">
        <div class="col-sm-12 col-md-12t-">
            <div class="panel panel-default"> 
                <div class="row mt-2 mb-5">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group form-float" >
                            <label class="form-label"> From Date</label>
                            <input type="text" class="form-control dateInput" name="fromDate" id="fromDate" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group form-float" >
                            <label class="form-label"> To Date</label>
                            <input type="text" class="form-control dateInput" name="toDate" id="toDate" autocomplete="off">
                        </div>
                    </div>		
                </div>
                
                <div class="text-center mb-3">
                    <b style="font-size: 14px">Expense Head</b>
                </div>
                <table class="table table-bordered table-hover custom-table dataTable" id="datatable">
                    <thead>
                        <tr class="bg-primary">
                            <th>SL</th>
                            <th>Expense Category</th>
                            <th>Expense Head</th>
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
                        $costCategory = '';
                        foreach ($costHeads as $costHead) {
                            if ($costCategory == '') {
                                $bgColor = '#efebe8';
                            } else if ($costCategory != $costHead->cost_category) {
                                if ($bgColor == '#f7f7f7') {
                                    $bgColor = '#efebe8';
                                } else {
                                    $bgColor = '#f7f7f7';
                                }
                            }
                            $costCategory = $costHead->cost_category;
                            echo "<tr style='background-color:$bgColor'>";

                            echo "<td class='td-center'>$count</td>";
                            echo "<td class='td-left'>$costHead->category_name</td>";
                            echo "<td class='td-left'>$costHead->cost_head</td>";
                            ?>
                        <td class='td-center'>
                            <input type="checkbox" id='headCheck<?php echo $count ?>' value='<?php echo $costHead->cost_head_code ?>' name='headCheck[]' onclick="setHeadCheckBox(this.value, this.id)" class="filled-in chk-col-blue"  />
                            <!--<label for="headCheck<?php echo $count ?>" class="form-label" style="margin-bottom: -12px"></label>-->
                        </td>
                        <?php
                        $count++;
                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
                
                <div class="text-center mb-3">
                    <b style="font-size: 14px">Vendor</b>
                </div>
                <table class="table table-bordered table-hover custom-table dataTable" id="datatable2">
                    <thead>
                        <tr class="bg-primary">
                            <th>SL</th>
                            <th>Vendor Title/Name</th>
                            <th>Vendor Code</th>
                            <th>Address</th>
                            <th>Mobile</th>
                            <th class="no-sort" style="width:50px">
                                <input type="checkbox" id="selectAllVendor" class="filled-in chk-col-blue" onClick="selectAllVendor(this)" />
                                <!--<label for="selectAllVendor" class="form-label m-l-20 m-b--10"></label>-->
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
                        foreach ($vendors as $vendor) {
                            echo "<tr>";
                            echo "<td class='td-center'>$count</td>";
                            echo "<td class='td-left'>$vendor[title]</td>";
                            echo "<td class='td-center'>$vendor[vendor_code]</td>";
                            echo "<td class='td-left'>$vendor[address]</td>";
                            echo "<td class='td-center'>$vendor[vendor_mobile]</td>";
                            ?>
                        <td class="td-center">
                            <input type="checkbox" id='vendorCheck<?php echo $count ?>' value='<?php echo $vendor['vendor_code'] ?>' name='vendorCheck[]' onclick="setVendorCheckBox(this.value, this.id)" class="filled-in chk-col-blue"  />
                            <!--<label for="vendorCheck<?php echo $count ?>" class="form-label" style="margin-bottom: -12px"></label>-->
                        </td>
                        <?php
                        $count++;
                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
                <form target="_blank" action="{{ route('admin.expense.report.details') }}" method="POST" id="formId">
                    @csrf
                    <input type="hidden" name="costHeadStr" id="costHeadStr">
                    <input type="hidden" name="vendorStr" id="vendorStr">
                    <input type="hidden" name="fromDate" id="fromDateHidden">
                    <input type="hidden" name="toDate" id="toDateHidden">
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
    var costHeadArr = new Array();
    function selectAllHead(source) {
        checkboxes = document.getElementsByName('headCheck[]');
        var costCheckBoxIdArr = new Array();
        for (var i in checkboxes) {
            checkboxes[i].checked = source.checked;
            if (typeof (checkboxes[i].id) !== 'undefined') {
                costCheckBoxIdArr.push(checkboxes[i].id);
            }
        }
        for (var i = 0; i < costCheckBoxIdArr.length; i++) {
            if ($("#" + costCheckBoxIdArr[i]).is(':checked')) {
                var itemtoRemove = $("#" + costCheckBoxIdArr[i]).val();
                costHeadArr = jQuery.grep(costHeadArr, function (value) {
                    return value !== itemtoRemove;
                });
                costHeadArr.push($("#" + costCheckBoxIdArr[i]).val());
            } else {
                var itemtoRemove = $("#" + costCheckBoxIdArr[i]).val();
                costHeadArr = jQuery.grep(costHeadArr, function (value) {
                    return value !== itemtoRemove;
                });
            }
        }
    }

    function setHeadCheckBox(costId, checkBoxId) {
        if ($("#" + checkBoxId).is(':checked')) {
            var itemtoRemove = costId;
            costHeadArr = jQuery.grep(costHeadArr, function (value) {
                return value !== itemtoRemove;
            });
            costHeadArr.push(costId);
        } else {
            var itemtoRemove = costId;
            costHeadArr = jQuery.grep(costHeadArr, function (value) {
                return value !== itemtoRemove;
            });
        }
    }

    var vendorArr = new Array();
    function selectAllVendor(source) {
        checkboxes = document.getElementsByName('vendorCheck[]');
        var vendorCheckBoxIdArr = new Array();
        for (var i in checkboxes) {
            checkboxes[i].checked = source.checked;
            if (typeof (checkboxes[i].id) !== 'undefined') {
                vendorCheckBoxIdArr.push(checkboxes[i].id);
            }
        }
        for (var i = 0; i < vendorCheckBoxIdArr.length; i++) {
            if ($("#" + vendorCheckBoxIdArr[i]).is(':checked')) {
                var itemtoRemove = $("#" + vendorCheckBoxIdArr[i]).val();
                vendorArr = jQuery.grep(vendorArr, function (value) {
                    return value !== itemtoRemove;
                });
                vendorArr.push($("#" + vendorCheckBoxIdArr[i]).val());
            } else {
                var itemtoRemove = $("#" + vendorCheckBoxIdArr[i]).val();
                vendorArr = jQuery.grep(vendorArr, function (value) {
                    return value !== itemtoRemove;
                });
            }
        }
    }

    function setVendorCheckBox(vendorCode, checkBoxId) {
        if ($("#" + checkBoxId).is(':checked')) {
            var itemtoRemove = vendorCode;
            vendorArr = jQuery.grep(vendorArr, function (value) {
                return value !== itemtoRemove;
            });
            vendorArr.push(vendorCode);
        } else {
            var itemtoRemove = vendorCode;
            vendorArr = jQuery.grep(vendorArr, function (value) {
                return value !== itemtoRemove;
            });
        }
    }

    function submitForm() {
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();

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

        var costHeadStr = costHeadArr.join();
        var vendorStr = vendorArr.join();

        if (costHeadArr.length === 0 && vendorArr.length === 0) {
            sweetAlert('Please select at least one expense head or one vendor...!');
            return false;
        }

        $('#costHeadStr').val(costHeadStr);
        $('#vendorStr').val(vendorStr);
        $('#fromDateHidden').val(fromDate);
        $('#toDateHidden').val(toDate);

        $("#formId").submit();
    }
</script>
@endpush