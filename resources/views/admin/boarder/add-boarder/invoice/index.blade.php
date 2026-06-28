@extends('layouts.app')

@section('content')

<div class="header">
    <h1 class="page-title">Update Information</h1>
    <ul class="breadcrumb">
        <li><a href="/admin/Home">Home</a></li>
        <li><a href="#">Boarder Enrollment</a></li>
        <li><a href="/admin/BoarderEnrollment/boarderList"> Boarder List</a></li>
        <li><a href="#"> Update Boarder</a></li>
    </ul>
</div>

<div class="container">
    <div class="card shadow">
        <div class="card-body">
            <!-- Nav Tabs -->
            @include('admin.boarder.add-boarder.tab')
            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <!-- Tab Content -->
            <div class="accordion" id="employeeAccordion">

                {{-- Boarder Invoice Template --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#personalInfo"
                                aria-expanded="true">
                            Boarder Invoice Template
                        </button>
                    </h2>

                    <div id="personalInfo"
                        class="accordion-collapse collapse show"
                        data-bs-parent="#employeeAccordion">

                        <div class="accordion-body">
                            <form action="{{ route('admin.boarder.invoice.updateBoarderInvoiceInfo') }}" method="post" id="officialInformationForm">
                                @csrf
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <div id="itemTableDiv">
                                                <?php
                                                $itemStr = "";
                                                $i = 1;
                                                $totalAmount = 0;
                                                foreach ($templateDetails as $templateDetail) {
                                                    
                                                    $itemStr .= '<tr id="itemTakenTr' . $i . '">
                                                    <td class="td-left pointer" id="itemHeadTd' . $i . '" onclick="showItemHeadModal(' . $i . ')">
                                                        ' . $templateDetail->item_head_name . ' 
                                                    </td>
                                                    <input type="hidden" name="itemHeadCode' . $i . '" id="itemHeadCode' . $i . '" value="' . $templateDetail->item_head . '">
                                                    <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' . $i . ')" onchange="calculateGrandTotal(' . $i . ')" name="quantity' . $i . '" id="quantity' . $i . '" value="' . $templateDetail->quantity . '">
                                                    </td>
                                                    <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' . $i . ')" onchange="calculateGrandTotal(' . $i . ')" name="unitPrice' . $i . '" id="unitPrice' . $i . '" value="' . $templateDetail->unit_price . '">
                                                    </td>
                                                    <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' . $i . ')" onchange="calculateGrandTotal(' . $i . ')" id="amount' . $i . '" value="' . number_format($templateDetail->quantity * $templateDetail->unit_price, 2) . '" readonly>
                                                    </td>
                                                    <td class="td-center"><i class="fa fa-remove pointer text-danger" onclick="removeItem(' . $i . ')"></i></td>
                                                    <input type="hidden" id="detailTableId' . $i . '" name="detailTableId' . $i . '" value="' . $templateDetail->id . '">
                                                </tr>';
                                                    $i++;
                                                    $totalAmount += $templateDetail->quantity * $templateDetail->unit_price;
                                                }
                                                if ($itemStr) {
                                                    ?>
                                                    <table class="table table-bordered custom-table" id="itemTable">
                                                        <tr class="bg-info">
                                                            <td width="15%"><b>Item Head</b></td>
                                                            <td width="10%"><b>Quantity</b></td>
                                                            <td width="10%"><b>Price Per Unit</b></td>
                                                            <td width="10%"><b>Amount (BDT)</b></td>
                                                            <td width="10%" class="td-center"><b>Action</b></td>
                                                        </tr>
                                                        <?php echo $itemStr ?>
                                                        <input type="hidden" id="takenItemCount" name="takenItemCount" value="<?php echo $i ?>">
                                                    </table>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <input type="hidden" id="takenItemSerial">
                                            <button type="button" class="btn btn-info save_button" onclick="showItemTable()">Add Item</button>
                                            <input type="hidden" id="takenItemSerial">
                                            <input type="hidden" id="deleteHeadStr" name="deleteHeadStr">
                                        </div>
                                    </div>
                                    <input type="hidden" id="boarderId" name="boarderId" value="<?php echo $boarderId ?>">
                                    <div class="row m-t-10">
                                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                                            <div class="form-group">
                                                <!--<input type="file" class="form-control" name="invoiceFile[]" id='invoiceFile' onchange='checkFile(this, this.id);' multiple/>-->
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="text-right">
                                                <b>Total Invoice:</b> <span id="totalAmount"><?php echo number_format($totalAmount, 2) ?></span> BDT
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <input type="button" class="btn btn-primary save_button" onclick="submitForm()" value="Save">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Personal Contact --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#contactInfo">
                            Admission Fee
                        </button>
                    </h2>

                    <div id="contactInfo"
                        class="accordion-collapse collapse"
                        data-bs-parent="#employeeAccordion">

                        <div class="accordion-body">
                            <div class="panel-group">
                                <form action="{{ route('admin.boarder.invoice.updateBoarderAdmissionFee') }}" method="post" id="admissionFeeForm">
                                    @csrf
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label class="form-label">Invoice Date</label><span class="text-danger">*</span>
                                                    <input type="text" class="form-control dateInput" name="invoiceDate" id="invoiceDate" value="<?php echo $admissionFeeInfo[0]->invoice_date ?? '' ?>" <?php echo $disableFlag ?? '' ?>>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label class="form-label">Due Date</label><span class="text-danger">*</span>
                                                    <input type="text" class="form-control dateInput" name="invoiceDueDate" id="invoiceDueDate" value="<?php echo $admissionFeeInfo[0]->invoice_due_date ?? '' ?>" <?php echo $disableFlag ?>>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label class="form-label">Boarder Enrollment Fee </label><span class="text-danger">*</span>
                                                    <input type="text" class="form-control" name="unitPrice" id="unitPrice" value="<?php
                                                    if ($admissionFeeInfo[0]->invoice_amount ?? null != 0) {
                                                        echo $admissionFeeInfo[0]->invoice_amount;
                                                    } else {
                                                        echo $itemHeadInfo[0]->unit_price ?? '';
                                                    }
                                                    ?>" <?php echo $disableFlag ?>>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($disableFlag == '') { ?>
                                            <button type="button" class="btn btn-primary save_button my-3" onclick="submitAdmissionFee()">Save</button>
                                            <?php
                                        }
                                        if (!$admissionFeeInfo) {
                                            ?><div class = "text-danger font-bold m-t-20">
                                                *** You have not generated admission fee for this boarder
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <input type="hidden" name="invoiceNo" value="<?php echo $admissionFeeInfo[0]->invoice_no ?? '' ?>">
                                    <input type="hidden" name="boarderId" value="<?php echo $boarderId ?>">
                                    <input type="hidden" name="boarderName" value="<?php echo $boarderDetails[0]->boarder_name ?? '' ?>">
                                    <input type="hidden" name="boarderPrimaryMobile" value="<?php echo $boarderDetails[0]->primary_mobile ?>">
                                    <input type="hidden" name="boarderAddress" value="<?php echo $boarderDetails[0]->boarder_permanent_address ?? '' ?>">
                                    <input type="hidden" name="boarderEmail" value="<?php echo $boarderDetails[0]->email ?? '' ?>">
                                    <input type="hidden" name="unitName" value="<?php echo $itemHeadInfo[0]->unit_name ?? '' ?>">
                                    <input type="hidden" name="itemHead" value="<?php echo $itemHeadInfo[0]->item_head_code ?? '' ?>">
                                    <input type="hidden" name="itemHeadName" value="<?php echo $itemHeadInfo[0]->item_head ?? '' ?>">
                                    <input type="hidden" name="itemCategory" value="<?php echo $itemHeadInfo[0]->item_category ?? '' ?>">
                                    <input type="hidden" name="itemCategoryName" value="<?php echo $itemHeadInfo[0]->category_name ?? '' ?>">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- --------------- invoice head modal -------------------- -->
<button type="button" class="btn btn-default hidden" data-toggle="modal" data-target="#itemModal" id="itemModalBtn"></button>
<div class="modal fade" id="itemModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="largeModalLabel">Item Head List</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover jq-option-datatable custom-table dataTable">
                        <thead>
                            <tr class="bg-primary">
                                <th>SL</th>
                                <th>Item Category</th>
                                <th>Item Head</th>
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
                            @php $count = 1; @endphp

                            @foreach ($itemHeads as $itemHead)

                                @if ($itemHead->item_head_code == config('constants.ADMISSION_FEE_HEAD_CODE'))
                                    @continue
                                @endif

                                <tr>
                                    <td class="td-center">{{ $count }}</td>

                                    <td class="td-left">{{ $itemHead->category_name }}</td>

                                    <td class="td-left">{{ $itemHead->item_head }}</td>

                                    <td class="td-center">{{ $itemHead->unit_name }}</td>

                                    <td class="td-right">{{ $itemHead->unit_price }}</td>

                                    <td class="td-center">
                                        <button
                                            type="button"
                                            class="btn btn-primary btn-xs btn-circle-puchase"
                                            onclick="addItemHead({{ $count }})">
                                            <i class="fa fa-arrow-down"></i>
                                        </button>
                                    </td>
                                </tr>

                                <input type="hidden"
                                    id="itemCategoryCodeHidden{{ $count }}"
                                    value="{{ $itemHead->parent_category_str }}">

                                <input type="hidden"
                                    id="itemCategoryNameHidden{{ $count }}"
                                    value="{{ $itemHead->category_name }}">

                                <input type="hidden"
                                    id="itemHeadCodeHidden{{ $count }}"
                                    value="{{ $itemHead->item_head_code }}">

                                <input type="hidden"
                                    id="itemHeadNameHidden{{ $count }}"
                                    value="{{ $itemHead->item_head }}">

                                <input type="hidden"
                                    id="itemUnitNameHidden{{ $count }}"
                                    value="{{ $itemHead->unit_name }}">

                                <input type="hidden"
                                    id="itemUnitPriceHidden{{ $count }}"
                                    value="{{ $itemHead->unit_price == '0.00' ? '' : $itemHead->unit_price }}">

                                @php $count++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" id="itemModalCloseBtn" data-bs-dismiss="modal"> CLOSE </button> 
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
</script>
<script>
    var a = 1;
    var b = 1;
    function showItemTable() {
        var takenItemCount = $("#takenItemCount").val();
        if (typeof takenItemCount === "undefined") {
            var itemTableStr = '<tr id="itemTakenTr1">\n\
                                            <td class="td-left pointer" id="itemHeadTd1" onclick="showItemHeadModal(1)">\n\
                                                <small class="text-muted"><i>Show Head</i></small>\n\
                                            </td>\n\
                                            <input type="hidden" name="itemHeadCode1" id="itemHeadCode1">\n\
                                            <input type="hidden" name="itemHeadName1" id="itemHeadName1">\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(1)" onchange="calculateGrandTotal(1)" name="quantity1" id="quantity1" >\n\
                                            </td>\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(1)" onchange="calculateGrandTotal(1)" name="unitPrice1" id="unitPrice1" >\n\
                                            </td>\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(1)" onchange="calculateGrandTotal(1)" id="amount1" readonly>\n\
                                                </td>\n\
                                            <td class="td-center"><i class="fa fa-remove pointer text-danger" onclick="removeItem(1)"></i></td>\n\
                                            <input type="hidden" name="detailTableId1" value="0">\n\
                                        </tr>';
            var newRow = $(document.createElement('div')).attr("id", 'itemTableDiv');
            var itemTableDiv = '<table class="table table-bordered custom-table m-t-10" id="itemTable">\n\
                                    <tr class="bg-info">\n\
                                        <td width="15%"><b>Item Head</b></td>\n\
                                        <td width="10%"><b>Quantity</b></td>\n\
                                        <td width="10%"><b>Price Per Unit</b></td>\n\
                                        <td width="10%"><b>Amount (BDT)</b></td>\n\
                                        <td width="10%" class="td-center"><b>Action</b></td>\n\
                                    </tr>\n\
                                    ' + itemTableStr + '\n\
                                    <input type="hidden" id="takenItemCount" name="takenItemCount" value="1">\n\
                                </table>';
            newRow.after().html(itemTableDiv);
            newRow.appendTo("#itemTableDiv");
        } else {
            takenItemCount++;
            var newRow = $(document.createElement('tr')).attr("id", 'itemTakenTr' + takenItemCount);
            var itemTableRowStr = '<td class="td-left pointer" id="itemHeadTd' + takenItemCount + '" onclick="showItemHeadModal(' + takenItemCount + ')">\n\
                                            <small class="text-muted"><i>Show Head</i></small>\n\
                                           </td>\n\
                                            <input type="hidden" name="itemHeadCode' + takenItemCount + '" id="itemHeadCode' + takenItemCount + '">\n\
                                            <input type="hidden" name="itemHeadName' + takenItemCount + '" id="itemHeadName' + takenItemCount + '">\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' + takenItemCount + ')" onchange="calculateGrandTotal(' + takenItemCount + ')" name="quantity' + takenItemCount + '" id="quantity' + takenItemCount + '">\n\
                                                </td>\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' + takenItemCount + ')" onchange="calculateGrandTotal(' + takenItemCount + ')" name="unitPrice' + takenItemCount + '" id="unitPrice' + takenItemCount + '">\n\
                                                </td>\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' + takenItemCount + ')" onchange="calculateGrandTotal(' + takenItemCount + ')" id="amount' + takenItemCount + '" readonly>\n\
                                                </td>\n\
                                            <td class="td-center"><i class="fa fa-remove pointer text-danger" onclick="removeItem(' + takenItemCount + ')"></i></td>\n\
                                            <input type="hidden" name="detailTableId' + takenItemCount + '" value="0">';
            newRow.after().html(itemTableRowStr);
            newRow.appendTo("#itemTable");
            $("#takenItemCount").val(takenItemCount);
        }
    }

    function removeItem(takenItemCount) {
        var idArr = new Array();
        idArr.push($('#detailTableId' + takenItemCount).val());
        if ($('#deleteHeadStr').val() !== "") {
            idArr.push($('#deleteHeadStr').val());
        }
        $('#deleteHeadStr').val(idArr.join());
        $('#itemTakenTr' + takenItemCount).remove();
        var tableRowCount = $("#itemTable tr").length;
        if (tableRowCount === 1) {
            $("#itemTable").remove();
        }
        grandTotal();
    }

    function calculateGrandTotal(takenItemCount) {
        var quantity = $('#quantity' + takenItemCount).val();
        var unitPrice = $('#unitPrice' + takenItemCount).val();

        if (!$.isNumeric(quantity)) {
            quantity = 0;
            $('#quantity' + takenItemCount).val('');
        }

        if (!$.isNumeric(unitPrice)) {
            unitPrice = 0;
            $('#unitPrice' + takenItemCount).val('');
        }

        var amount = (parseFloat(quantity) * parseFloat(unitPrice));

        if (!$.isNumeric(amount)) {
            $('#amount' + takenItemCount).val('');
        } else {
            $('#amount' + takenItemCount).val(amount);
        }
        grandTotal();
    }

    function grandTotal() {
        var totalAmount = 0;
        var takenItemCount = $('#takenItemCount').val();
        for (var j = 1; j <= takenItemCount; j++) {
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

    function showItemHeadModal(takenItemCount) {

        $('#takenItemSerial').val(takenItemCount);

        const modal = new bootstrap.Modal(document.getElementById('itemModal'));
        modal.show();
    }

    function addItemHead(itemCount) {
        var takenItemSerial = $('#takenItemSerial').val();
        var itemCategoryCode = $('#itemCategoryCodeHidden' + itemCount).val();
        var itemCategoryName = $('#itemCategoryNameHidden' + itemCount).val();
        var itemHeadCode = $('#itemHeadCodeHidden' + itemCount).val();
        var itemHeadName = $('#itemHeadNameHidden' + itemCount).val();
        var itemUnitName = $('#itemUnitNameHidden' + itemCount).val();
        var itemUnitPrice = $('#itemUnitPriceHidden' + itemCount).val();
        var takenItemCount = $("#takenItemCount").val();
        for (var i = 1; i < takenItemCount; i++) {
            if (typeof ($('#itemHeadCode' + i).val()) !== 'undefined') {
                if ($('#itemHeadCode' + i).val() === itemHeadCode) {
                    sweetAlert("You have already select this head...!");
                    return false;
                }
            }
        }

        $('#itemHeadTd' + takenItemSerial).text(itemHeadName);
        $('#itemCategoryCode' + takenItemSerial).val(itemCategoryCode);
        $('#itemCategoryName' + takenItemSerial).val(itemCategoryName);
        $('#itemHeadCode' + takenItemSerial).val(itemHeadCode);
        $('#itemHeadName' + takenItemSerial).val(itemHeadName);
        $('#unitName' + takenItemSerial).val(itemUnitName);
        $('#unitPrice' + takenItemSerial).val(itemUnitPrice);
        $('#quantity' + takenItemSerial).val('');
        $('#amount' + takenItemSerial).val('');
        $('#adjust' + takenItemSerial).val('');
        $('#itemModalCloseBtn').click();
        grandTotal();
    }

    function submitForm() {
        var takenItemCount = $('#takenItemCount').val();
        for (var j = 1; j <= takenItemCount; j++) {
            var amount = $('#amount' + j).val();
            if (typeof amount !== 'undefined') {
                var itemHeadCode = $('#itemHeadCode' + j).val();
                var quantity = $('#quantity' + j).val();
                var unitName = $('#unitName' + j).val();
                var unitPrice = $('#unitPrice' + j).val();
                if (amount === "" || itemHeadCode === "" || quantity === "" || unitName === "" || unitPrice === "") {
                    sweetAlert('Item Head, Quantity, Unit Name and Price Per Unit are required...!');
                    return false;
                } else {
                    if (parseFloat(quantity) <= 0 || parseFloat(unitPrice) <= 0) {
                        sweetAlert('Quantity and Price Per Unit must be greater than zero...!');
                        return false;
                    }
                }
            }
        }
        $('#officialInformationForm').submit();
    }

    function submitAdmissionFee() {
        if ($.trim($('#invoiceDate').val()) === "" || $.trim($('#invoiceDueDate').val()) === "") {
            sweetAlert('Invoice Date and Invoice Due Date is required...!');
            return false;
        }
        $('#admissionFeeForm').submit();
    }
</script>
@endpush
