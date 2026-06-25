@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Edit Expense</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li><a href="#">/ Expense</a> / </li>
        <li><a href="{{ route('admin.expense.index') }}">Expense List</a></li>
        <li><a href="{{ route('admin.expense.edit',$expenseNo) }}">/ Edit Expense</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-default"> 
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
                <div id="errorDiv" class="alert alert-danger hidden">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <?php
                    //dd($expenseSummary);
                    foreach ($expenseSummary as $expSummary) {
                        $expenseTitle = $expSummary->expense_title;
                        $expenseDate = $expSummary->expense_date;
                        $expenseNo = $expSummary->expense_no;
                        //$totalAmount = $expSummary['total_amount'];
                        $updateDtTm = $expSummary->updated_dt_tm;
                        $vendorTitle = $expSummary->vendor_title;
                        $vendorCode = $expSummary->vendor;

                        if (!$vendorCode) {
                            $vendorTitle = "Guest";
                            $guestName = $expSummary->guest_name;
                            $guestMobile = $expSummary->guest_mobile;
                        }
                    }
                    ?>
                    <form action="{{ route('admin.expense.update',$expenseNo) }}" method="POST"  enctype="multipart/form-data" id="expenseForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label"> Expense Title </label><span class="text-danger">*</span><small class="hidden custom-text-danger" id="expenseTitleReq-error"> Expense Title is required</small>
                                    <input type="text" class="form-control" name="expenseTitle" id="expenseTitle" value="<?php echo $expenseTitle ?>">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label">Expense Date</label><small class="hidden custom-text-danger" id="expenseDateReq-error"> Expense Date is required</small>
                                    <input type="text" class="form-control dateInput" name="expenseDate" id="expenseDate" value="<?php echo $expenseDate ?>">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group" >
                                    <label class="form-label">Vendor</label>
                                    <select class="form-control" name="vendor" id="vendor" onchange="toggleGuestDiv(this.value)">
                                        <option value="<?php echo $vendorCode ?>"><?php echo $vendorTitle ?></option>
                                        <option value="">Guest</option>
                                        <?php
                                        foreach ($vendors as $vendor) {
                                            echo "<option value='$vendor[vendor_code]'>$vendor[title]</option>";
                                        }
                                        ?>
                                    </select>
                                </div>	
                            </div>
                        </div>
                        <div class="row" id="guestDiv" <?php if ($vendorCode) { ?> style="display: none" <?php } ?>>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label">Guest Vendor Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="guestName" id="guestName" value="<?php echo $guestName ?? '' ?>">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label">Guest Vendor Mobile Number</label>
                                    <input type="text" class="form-control" name="guestMobile" id="guestMobile" onchange="checkMobileNumber(this.value, this.id)" value="<?php echo $guestMobile ?? '' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <?php
                                $expenseStr = "";
                                $i = 1;
                                $totalAmount = 0;
                                foreach ($expenseDetails as $expenseDeails) {
                                    $expenseStr .= '<tr id="expenseTakenTr' . $i . '">
                                        <td class="td-left pointer" id="expenseHeadTd' . $i . '" onclick="showExpHeadModal(' . $i . ')">
                                            ' . $expenseDeails->expense_head_name . ' 
                                        </td>
                                        <input type="hidden" name="expenseHeadCode' . $i . '" id="expenseHeadCode' . $i . '" value="' . $expenseDeails->expense_head . '">
                                        <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' . $i . ')" onchange="calculateGrandTotal(' . $i . ')" name="quantity' . $i . '" id="quantity' . $i . '" value="' . $expenseDeails->quantity . '">
                                        </td>
                                        <td><input type="text" class="form-control custom-form-control1"  name="unitName' . $i . '" id="unitName' . $i . '" value="' . $expenseDeails->unit_name . '" readonly>
                                        </td>
                                        <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' . $i . ')" onchange="calculateGrandTotal(' . $i . ')" name="unitPrice' . $i . '" id="unitPrice' . $i . '" value="' . $expenseDeails->unit_price . '">
                                        </td>
                                        <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' . $i . ')" onchange="calculateGrandTotal(' . $i . ')" name="adjust' . $i . '" id="adjust' . $i . '" value="' . $expenseDeails->adjust . '">
                                        </td>
                                        <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' . $i . ')" onchange="calculateGrandTotal(' . $i . ')"  name="amount' . $i . '" id="amount' . $i . '" value="' . $expenseDeails->amount . '" readonly>
                                        </td>
                                        <td><input type="text" class="form-control custom-form-control1" name="remarks' . $i . '" id="remarks' . $i . '" value="' . $expenseDeails->remarks . '">
                                        </td>
                                        <td class="td-center"><i class="fa fa-remove pointer text-danger" onclick="removeExpense(' . $i . ')"></i></td>
                                        <input type="hidden" id="detailTableId' . $i . '" name="detailTableId' . $i . '" value="' . $expenseDeails->id . '">
                                    </tr>';
                                    $i++;
                                    $totalAmount += $expenseDeails->amount;
                                }
                                if ($expenseStr) {
                                    ?>
                                    <div id="expenseTableDiv">
                                        <table class="table table-bordered custom-table" id="expenseTable">
                                            <tr class="bg-info">
                                                <td width="15%"><b>Expense Head</b></td>
                                                <td width="10%"><b>Quantity</b></td>
                                                <td width="10%"><b>Unit Name</b></td>
                                                <td width="10%"><b>Price Per Unit</b></td>
                                                <td width="10%"><b>Adjust<small> (+/-)</small></b></td>
                                                <td width="10%"><b>Amount (BDT)</b></td>
                                                <td width="25%"><b>Remarks</b></td>
                                                <td width="10%" class="td-center"><b>Action</b></td>
                                            </tr>
                                            <?php echo $expenseStr ?>
                                            <input type="hidden" id="takenExpenseCount" name="takenExpenseCount" value="<?php echo $i ?>">
                                        </table>
                                    </div>
                                    <?php
                                }
                                ?>
                                <input type="hidden" id="takenExpenseSerial">
                                <button type="button" class="btn btn-info save_button mb-3" style="color:#fff" onclick="showExpenseTable()">Add Expense</button>
                                <input type="hidden" id="takenExpenseSerial">
                                <input type="hidden" name="expenseNo" value="<?php echo $expenseNo ?>">
                                <input type="hidden" name="updateDtTm" value="<?php echo $updateDtTm ?>">

                                <input type="hidden" id="deleteHeadStr" name="deleteHeadStr">
                                <input type="hidden" id="deleteFileStr" name="deleteFileStr">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 col-sm-6 col-xs-12 ">
                                <div class="form-group">
                                    <input type="file" class="form-control" name="expenseFile[]" id='expenseFile' onchange='checkFile(this, this.id);' multiple/>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="text-right">
                                    <b>Total Expense:</b> <span id="totalAmount"><?php echo number_format($totalAmount, 2) ?></span> BDT
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover custom-table dataTable">
                                    <thead>
                                        <tr class="bg-primary">
                                            <th>SL</th>
                                            <th>File Name</th>
                                            <th>Show</th>
                                            <th>Remove</th>
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
                                        @foreach($expenseFiles as $serial => $expenseFile)
                                            <tr id="fileTr{{ $serial + 1 }}">
                                                <td class="td-center">{{ $serial + 1 }}</td>

                                                <td class="td-left">
                                                    {{ $expenseFile->original_name }}
                                                </td>

                                                <td class="td-center">
                                                    <a
                                                        href="{{ asset('storage/assets/admin/files/expense/' . $expenseFile->file_name) }}"
                                                        target="_blank">
                                                        Show
                                                    </a>
                                                </td>

                                                <td class="td-center">
                                                    <i
                                                        class="fa fa-remove text-danger pointer"
                                                        onclick="deleteFile('{{ $expenseFile->id }}', '{{ $serial + 1 }}')">
                                                    </i>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>	
                    </div>
                    <button type="button" class="btn btn-primary save_button mt-3" onclick="editExpense()">Save</button>
                </div>
                <!-- --------------- expense head modal -------------------- -->
                <button type="button" class="btn btn-default hidden" data-toggle="modal" data-target="#expenseModal" id="expenseModalBtn"></button>
                <div class="modal fade" id="expenseModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="largeModalLabel">Expense Head List</h4>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover jq-option-datatable custom-table dataTable">
                                        <thead>
                                            <tr class="bg-primary">
                                                <th>SL</th>
                                                <th>Expense Category</th>
                                                <th>Expense Head</th>
                                                <th>Unit Name</th>
                                                <th>Unit Price</th>
                                                <th>Select</th>
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
                                            @foreach($costHeads as $index => $costHead)
                                                @php
                                                    $count = $index + 1;
                                                    $unitPriceMaster = ($costHead->unit_price == '0.00' || $costHead->unit_price == 0)
                                                        ? ''
                                                        : $costHead->unit_price;
                                                @endphp

                                                <tr>
                                                    <td>{{ $count }}</td>
                                                    <td class="td-left">{{ $costHead->category_name }}</td>
                                                    <td class="td-left">{{ $costHead->cost_head }}</td>
                                                    <td class="td-center">{{ $costHead->unit_name }}</td>
                                                    <td class="td-right">{{ $costHead->unit_price }}</td>

                                                    <td class="td-center">
                                                        <button
                                                            type="button"
                                                            class="btn btn-primary btn-xs btn-circle-puchase"
                                                            onclick="addExpenseHead({{ $count }})">
                                                            <i class="fa fa-arrow-down"></i>
                                                        </button>
                                                    </td>
                                                </tr>

                                                <input
                                                    type="hidden"
                                                    id="costHeadCodeHidden{{ $count }}"
                                                    value="{{ $costHead->cost_head_code }}">

                                                <input
                                                    type="hidden"
                                                    id="costHeadNameHidden{{ $count }}"
                                                    value="{{ $costHead->cost_head }}">

                                                <input
                                                    type="hidden"
                                                    id="costUnitNameHidden{{ $count }}"
                                                    value="{{ $costHead->unit_name }}">

                                                <input
                                                    type="hidden"
                                                    id="costUnitPriceHidden{{ $count }}"
                                                    value="{{ $unitPriceMaster }}">
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button"
                                        class="btn btn-link waves-effect"
                                        data-bs-dismiss="modal">
                                    CLOSE
                                </button>                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    var a = 1;
    var b = 1;
    function showExpenseTable() {
        var takenExpenseCount = $("#takenExpenseCount").val();
        if (typeof takenExpenseCount === "undefined") {
            var expenseTableStr = '<tr id="expenseTakenTr1">\n\
                                            <td class="td-left pointer" id="expenseHeadTd1" onclick="showExpHeadModal(1)">\n\
                                                <small class="text-muted"><i>Show Head</i></small>\n\
                                            </td>\n\
                                            <input type="hidden" name="expenseHeadCode1" id="expenseHeadCode1">\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(1)" onchange="calculateGrandTotal(1)" name="quantity1" id="quantity1" >\n\
                                            </td>\n\
                                            <td><input type="text" class="form-control custom-form-control1" name="unitName1" id="unitName1" readonly>\n\
                                                </td>\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(1)" onchange="calculateGrandTotal(1)" name="unitPrice1" id="unitPrice1" >\n\
                                            </td>\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(1)" onchange="calculateGrandTotal(1)" name="adjust1" id="adjust1" >\n\
                                                </td>\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(1)" onchange="calculateGrandTotal(1)" name="amount1" id="amount1" readonly>\n\
                                                </td>\n\
                                            <td><input type="text" class="form-control custom-form-control1" max="200" name="remarks1" id="remarks1" >\n\
                                                </td>\n\
                                            <td class="td-center"><i class="fa fa-remove pointer text-danger" onclick="removeExpense(1)"></i></td>\n\
                                            <input type="hidden" name="detailTableId1" value="0">\n\
                                        </tr>';
            var newRow = $(document.createElement('div')).attr("id", 'expenseTableDiv');
            var expenseTableDiv = '<table class="table table-bordered custom-table m-t-10" id="expenseTable">\n\
                                    <tr class="bg-info">\n\
                                        <td width="15%"><b>Expense Head</b></td>\n\
                                        <td width="10%"><b>Quantity</b></td>\n\
                                        <td width="10%"><b>Unit Name</b></td>\n\
                                        <td width="10%"><b>Price Per Unit</b></td>\n\
                                        <td width="10%"><b>Adjust<small> (+/-)</small></b></td>\n\
                                        <td width="10%"><b>Amount (BDT)</b></td>\n\
                                        <td width="25%"><b>Remarks</b></td>\n\
                                        <td width="10%" class="td-center"><b>Action</b></td>\n\
                                    </tr>\n\
                                    ' + expenseTableStr + '\n\
                                    <input type="hidden" id="takenExpenseCount" name="takenExpenseCount" value="1">\n\
                                </table>';
            newRow.after().html(expenseTableDiv);
            newRow.appendTo("#expenseTableDiv");
        } else {
            takenExpenseCount++;
            var newRow = $(document.createElement('tr')).attr("id", 'expenseTakenTr' + takenExpenseCount);
            var expenseTableRowStr = '<td class="td-left pointer" id="expenseHeadTd' + takenExpenseCount + '" onclick="showExpHeadModal(' + takenExpenseCount + ')">\n\
                                            <small class="text-muted"><i>Show Head</i></small>\n\
                                           </td>\n\
                                            <input type="hidden" name="expenseHeadCode' + takenExpenseCount + '" id="expenseHeadCode' + takenExpenseCount + '">\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' + takenExpenseCount + ')" onchange="calculateGrandTotal(' + takenExpenseCount + ')" name="quantity' + takenExpenseCount + '" id="quantity' + takenExpenseCount + '">\n\
                                                </td>\n\
                                            <td><input type="text" class="form-control custom-form-control1"  name="unitName' + takenExpenseCount + '" id="unitName' + takenExpenseCount + '" readonly>\n\
                                                </td>\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' + takenExpenseCount + ')" onchange="calculateGrandTotal(' + takenExpenseCount + ')" name="unitPrice' + takenExpenseCount + '" id="unitPrice' + takenExpenseCount + '">\n\
                                                </td>\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' + takenExpenseCount + ')" onchange="calculateGrandTotal(' + takenExpenseCount + ')" name="adjust' + takenExpenseCount + '" id="adjust' + takenExpenseCount + '">\n\
                                                 </td>\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' + takenExpenseCount + ')" onchange="calculateGrandTotal(' + takenExpenseCount + ')" name="amount' + takenExpenseCount + '" id="amount' + takenExpenseCount + '" readonly>\n\
                                                </td>\n\
                                            <td><input type="text" class="form-control custom-form-control1" max="200" name="remarks' + takenExpenseCount + '" id="remarks' + takenExpenseCount + '" >\n\
                                                </td>\n\
                                            <td class="td-center"><i class="fa fa-remove pointer text-danger" onclick="removeExpense(' + takenExpenseCount + ')"></i></td>\n\
                                            <input type="hidden" name="detailTableId' + takenExpenseCount + '" value="0">';
            newRow.after().html(expenseTableRowStr);
            newRow.appendTo("#expenseTable");
            $("#takenExpenseCount").val(takenExpenseCount);
        }
    }

    function removeExpense(takenExpenseCount) {
        var idArr = new Array();
        idArr.push($('#detailTableId' + takenExpenseCount).val());
        if ($('#deleteHeadStr').val() !== "") {
            idArr.push($('#deleteHeadStr').val());
        }
        $('#deleteHeadStr').val(idArr.join());
        $('#expenseTakenTr' + takenExpenseCount).remove();
        var tableRowCount = $("#expenseTable tr").length;
        if (tableRowCount === 1) {
            $("#expenseTable").remove();
        }
        grandTotal();
    }

    function calculateGrandTotal(takenExpenseCount) {
        var quantity = $('#quantity' + takenExpenseCount).val();
        var unitPrice = $('#unitPrice' + takenExpenseCount).val();
        var adjustInput = $('#adjust' + takenExpenseCount).val();
        if (!$.isNumeric(quantity)) {
            quantity = 0;
            $('#quantity' + takenExpenseCount).val('');
        }

        if (!$.isNumeric(unitPrice)) {
            unitPrice = 0;
            $('#unitPrice' + takenExpenseCount).val('');
        }

        var adjust = parseFloat(adjustInput);
        if (!(adjustInput === '-' || adjustInput === '+')) {
            if (!$.isNumeric(adjustInput)) {
                $('#adjust' + takenExpenseCount).val('');
                adjust = 0;
            }
        } else {
            adjust = 0;
        }

        var amount = (parseFloat(quantity) * parseFloat(unitPrice)) + parseFloat(adjust);
        if (!$.isNumeric(amount)) {
            $('#amount' + takenExpenseCount).val('');
        } else {
            $('#amount' + takenExpenseCount).val(amount);
        }
        grandTotal();
    }

    function grandTotal() {
        var totalAmount = 0;
        var takenExpenseCount = $('#takenExpenseCount').val();
        for (var j = 1; j <= takenExpenseCount; j++) {
            var amount = $('#amount' + j).val();
            if (typeof amount !== 'undefined' && amount !== "") {
                totalAmount += parseFloat(amount);
            }
        }

        totalAmount = totalAmount.toFixed(2);
        if (!$.isNumeric(totalAmount)) {
            totalAmount = '0.00';
        }
        $('#totalAmount').text(totalAmount);
    }

    function addExpenseHead(expenseCount) {

        var takenExpenseSerial = $('#takenExpenseSerial').val();
        var costHeadCode = $('#costHeadCodeHidden' + expenseCount).val();
        var costHeadName = $('#costHeadNameHidden' + expenseCount).val();
        var costUnitName = $('#costUnitNameHidden' + expenseCount).val();
        var costUnitPrice = $('#costUnitPriceHidden' + expenseCount).val();
        var takenExpenseCount = $("#takenExpenseCount").val();
        for (var i = 1; i < takenExpenseCount; i++) {
            if (typeof ($('#expenseHeadCode' + i).val()) !== 'undefined') {
                if ($('#expenseHeadCode' + i).val() === costHeadCode) {
                    sweetAlert("You have already select this head...!");
                    return false;
                }
            }
        }

        $('#expenseHeadTd' + takenExpenseSerial).text(costHeadName);
        $('#expenseHeadCode' + takenExpenseSerial).val(costHeadCode);
        $('#unitName' + takenExpenseSerial).val(costUnitName);
        $('#unitPrice' + takenExpenseSerial).val(costUnitPrice);
        $('#quantity' + takenExpenseSerial).val('');
        $('#amount' + takenExpenseSerial).val('');
        $('#adjust' + takenExpenseSerial).val('');
        const modalEl = document.getElementById('expenseModal');
        const modal = bootstrap.Modal.getInstance(modalEl);

        if (modal) {
            modal.hide();
        }   
        grandTotal();
    }

    function checkFile() {
        var fp = $("#expenseFile");
        var lg = fp[0].files.length; // get length
        var items = fp[0].files;
        var fileSize = 0;
        var fileExtension = ['jpeg', 'jpg', 'png', 'txt', 'doc', 'docx', 'pdf'];
        if (lg > 0) {
            for (var i = 0; i < lg; i++) {
                fileSize = fileSize + items[i].size;
                if ($.inArray(items[i].name.split('.').pop().toLowerCase(), fileExtension) === -1) {
                    sweetAlert("Only 'jpeg','jpg','png','txt','doc','docx','pdf' formats are allowed...!");
                    $('#expenseFile').val('');
                    return false;
                }
            }
            if (fileSize > 2097152) {
                sweetAlert('File size must not be more than 2 MB...!');
                $('#expenseFile').val('');
            }
        }
    }

    function showExpHeadModal(takenExpenseCount) {

        $('#takenExpenseSerial').val(takenExpenseCount);

        let modal = new bootstrap.Modal(
            document.getElementById('expenseModal')
        );

        modal.show();
    }

    function deleteFile(fileId, serial) {
        var idArr = new Array();
        idArr.push(fileId);
        if ($('#deleteFileStr').val() !== "") {
            idArr.push($('#deleteFileStr').val());
        }
        $('#deleteFileStr').val(idArr.join());
        $('#fileTr' + serial).remove();
    }

    function editExpense() {
        var takenExpenseCount = $('#takenExpenseCount').val();
        var expenseFlag = 0;
        for (var j = 1; j <= takenExpenseCount; j++) {
            var amount = $('#amount' + j).val();
            if (typeof amount !== 'undefined') {
                expenseFlag = 1;
                var expenseHeadCode = $('#expenseHeadCode' + j).val();
                var quantity = $('#quantity' + j).val();
                var unitName = $('#unitName' + j).val();
                var unitPrice = $('#unitPrice' + j).val();
                if (amount === "" || expenseHeadCode === "" || quantity === "" || unitName === "" || unitPrice === "") {
                    if ($('#remarks' + j).val().length > 200) {
                        sweetAlert('Remarks max length is 200 characters...!');
                        return false;
                    }

                    sweetAlert('Expense Head, Quantity, Unit Name and Price Per Unit are required...!');
                    return false;
                } else {
                    if (parseFloat(quantity) <= 0 || parseFloat(unitPrice) <= 0) {
                        sweetAlert('Quantity and Price Per Unit must be greater than zero...!');
                        return false;
                    }
                }
            }
        }

        if (expenseFlag === 0) {
            sweetAlert('Please select at least one expense...!');
            return false;
        }

        if ($.trim($('#expenseTitle').val()) === "" || $.trim($('#expenseDate').val()) === "") {
            sweetAlert('Expense Title and Expense Date is required...!');
            return false;
        }

        var vendor = $('#vendor').val();
        if (vendor === '') {
            var guestName = $('#guestName').val();
            if (guestName === '') {
                sweetAlert('Guest Vendor Name is required...!');
                return false;
            }
        }
        $('#expenseForm').submit();
    }

    function toggleGuestDiv(vendor) {
        $('guestName').val('');
        $('guestMobile').val('');
        if (vendor === '') {
            $("#guestDiv").show("fast");
        } else {
            $("#guestDiv").hide("fast");
        }
    }
</script>
@endpush