@extends('layouts.app')

@section('content')

<div class="header">
    <h1 class="page-title">Add Invoice</h1>
    <ul class="breadcrumb">
        <li><a href="/admin/Home"> Home /</a></li>
        <li><a href="#"> Invoice /</a></li>
        <li><a href="/admin/invoice-payment"> Invoice List /</a></li>
        <li class="active"><a href="/admin/invoice/create"> Add Invoice</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-default"> 
                <div id="errorDiv" class="alert alert-danger hidden">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
                <form action="{{ route('admin.invoice.store') }}" method="POST" enctype="multipart/form-data" id="invoiceForm">
                    @csrf
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label">Invoice Title</label><span class="text-danger">*</span><small class="hidden custom-text-danger" id="invoiceTitleReq-error"> Invoice Title is required</small>
                                    <input type="text" class="form-control" name="invoiceTitle" id="invoiceTitle">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label">Invoice Date</label><span class="text-danger">*</span><small class="hidden custom-text-danger" id="invoiceDateReq-error"> Invoice Date is required</small>
                                    <input type="text" class="form-control dateInput" name="invoiceDate" id="invoiceDate" value="<?php echo date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label">Due Date</label><span class="text-danger">*</span><small class="hidden custom-text-danger" id="invoiceDueDateReq-error"> Due Date is required</small>
                                    <input type="text" class="form-control dateInput" name="invoiceDueDate" id="invoiceDueDate" value="<?php echo date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-6" id="boarderDiv">
                                <div class="form-group" >
                                    <label class="form-label">Boarder</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="boarderNameHidden" id="boarderNameHidden" onclick="showBoarderModal()" readonly>
                                    <input type="hidden" name="boarderIdHidden" id="boarderIdHidden">
                                    <input type="hidden" name="boarderPrimaryMobileHidden" id="boarderPrimaryMobileHidden">
                                    <input type="hidden" name="boarderAddressHidden" id="boarderAddressHidden">
                                    <input type="hidden" name="boarderEmailHidden" id="boarderEmailHidden">
                                </div>	
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-6">
                                <div class="form-group" >
                                    <label class="form-label">Is Guest?</label><br>
                                    <input type="checkbox" name="isGuest" id="isGuest" value="0" onchange="toggleGuestDiv()">
                                </div>	
                            </div>
                        </div>
                        <div class="row" id="guestDiv" style="display: none">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label">Guest Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="guestName" id="guestName">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label">Guest Mobile Number</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="guestMobile" id="guestMobile" onchange="checkMobileNumber(this.value, this.id)">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label">Guest Email</label><span class="text-danger">*</span>
                                    <input type="email" class="form-control" name="guestEmail" id="guestEmail" onchange="checkEmail(this.value, this.id)" >
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label">Guest Address</label>
                                    <input type="text" class="form-control" name="guestAddress" id="guestAddress">
                                </div>
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div id="itemTableDiv">

                                </div>
                                <input type="hidden" id="takenItemSerial">
                                <button type="button" class="btn btn-info save_button" onclick="showItemTable()">Add Item</button>
                            </div>
                        </div>
                        <div class="row m-t-10">
                            <div class="col-md-6 col-sm-6 col-xs-12 ">
                                <div class="form-group">
                                    <input type="file" class="form-control" name="invoiceFile[]" id='invoiceFile' onchange='checkFile(this, this.id);' multiple/>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="text-right">
                                    <b>Total Invoice:</b> <span id="totalAmount">0.00</span> BDT
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <button type="button" class="btn btn-primary save_button my-3" onclick="addNewInvoice()">Save</button>
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

                                            <tr>
                                                <td>{{ $count }}</td>

                                                <td class="td-left">
                                                    {{ $itemHead->category_name }}
                                                </td>

                                                <td class="td-left">
                                                    {{ $itemHead->item_head }}
                                                </td>

                                                <td class="td-center">
                                                    {{ $itemHead->unit_name }}
                                                </td>

                                                <td class="td-right">
                                                    {{ $itemHead->unit_price }}
                                                </td>

                                                <td class="td-center">
                                                    <button
                                                        type="button"
                                                        class="btn btn-primary btn-xs btn-circle-puchase"
                                                        onclick="addItemHead({{ $count }})">
                                                        <i class="fa fa-arrow-down"></i>
                                                    </button>
                                                </td>
                                            </tr>

                                            <input
                                                type="hidden"
                                                id="itemCategoryCodeHidden{{ $count }}"
                                                value="{{ $itemHead->parent_category_str }}">

                                            <input
                                                type="hidden"
                                                id="itemCategoryNameHidden{{ $count }}"
                                                value="{{ $itemHead->category_name }}">

                                            <input
                                                type="hidden"
                                                id="itemHeadCodeHidden{{ $count }}"
                                                value="{{ $itemHead->item_head_code }}">

                                            <input
                                                type="hidden"
                                                id="itemHeadNameHidden{{ $count }}"
                                                value="{{ $itemHead->item_head }}">

                                            <input
                                                type="hidden"
                                                id="itemUnitNameHidden{{ $count }}"
                                                value="{{ $itemHead->unit_name }}">

                                            <input
                                                type="hidden"
                                                id="itemUnitPriceHidden{{ $count }}"
                                                value="{{ $itemHead->unit_price == '0.00' ? '' : $itemHead->unit_price }}">

                                            @php $count++; @endphp

                                        @endforeach
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button"
                                        class="btn btn-link waves-effect"
                                        id="itemModalCloseBtn"
                                        data-bs-dismiss="modal">
                                    CLOSE
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- --------------- boarder list modal -------------------- -->
                <button type="button" class="hidden" data-toggle="modal" data-target="#boarderModal" id="boarderShowBtn"></button>
                <div class="modal fade" id="boarderModal" tabindex="-1" role="dialog" aria-labelledby="boarderModalLabel">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-between align-items-center">
                                <h4 class="modal-title mb-0" id="boarderModalLabel">
                                    Boarder List
                                </h4>
                                <button type="button" class="btn-close"  data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-bordered table-hover custom-table dataTable">
                                    <thead>
                                        <tr class="bg-primary">
                                            <th>SL</th>
                                            <th>Boarder ID</th>
                                            <th>Boarder Name</th>
                                            <th>Contact No</th>
                                            <th>Building</th>
                                            <th>Floor</th>
                                            <th>Room</th>
                                            <th>Seat</th>
                                            <th>Seat Type</th>
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
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($boarders as $index => $boarder)
                                            <tr>
                                                <td class="td-center">{{ $loop->iteration }}</td>
                                                <td class="td-center">{{ $boarder->boarder_id }}</td>
                                                <td>{{ $boarder->boarder_name }}</td>
                                                <td>{{ $boarder->primary_mobile }}</td>
                                                <td>{{ $boarder->building_title }}</td>
                                                <td>{{ $boarder->floor_title }}</td>
                                                <td>{{ $boarder->room_title }}</td>
                                                <td>{{ $boarder->seat_title }}</td>
                                                <td class="td-center">{{ $boarder->seat_type_title }}</td>
                                                <td class="td-center">
                                                    <button type="button"
                                                            class="btn btn-primary btn-xs btn-circle-puchase"
                                                            onclick="setBoarder({{ $loop->iteration }})">
                                                        <i class="fa fa-arrow-down"></i>
                                                    </button>
                                                </td>

                                                <input type="hidden"
                                                    id="boarderIdModalHidden{{ $loop->iteration }}"
                                                    value="{{ $boarder->boarder_id }}">

                                                <input type="hidden"
                                                    id="boarderNameModalHidden{{ $loop->iteration }}"
                                                    value="{{ $boarder->boarder_name }}">

                                                <input type="hidden"
                                                    id="boarderPrimaryMobileModalHidden{{ $loop->iteration }}"
                                                    value="{{ $boarder->primary_mobile }}">

                                                <input type="hidden"
                                                    id="boarderAddressModalHidden{{ $loop->iteration }}"
                                                    value="{{ $boarder->boarder_permanent_address }}">

                                                <input type="hidden"
                                                    id="boarderEmailModalHidden{{ $loop->iteration }}"
                                                    value="{{ $boarder->email }}">
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary save_button" id="modalCloseBtn" data-bs-dismiss="modal">Close</button>
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
    var b = 2;
    function showItemTable() {
        var takenItemCount = $("#takenItemCount").val();
        if (typeof takenItemCount === "undefined") {
            var itemTableStr = '<tr id="itemTakenTr1">\n\
                                            <td class="td-left pointer" id="itemHeadTd1" onclick="showItemHeadModal(1)">\n\
                                                <small class="text-muted"><i>Show Head</i></small>\n\
                                            </td>\n\
                                            <input type="hidden" name="itemCategoryCode1" id="itemCategoryCode1">\n\
                                            <input type="hidden" name="itemCategoryName1" id="itemCategoryName1">\n\
                                            <input type="hidden" name="itemHeadCode1" id="itemHeadCode1">\n\
                                            <input type="hidden" name="itemHeadName1" id="itemHeadName1">\n\
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
                                            <td class="td-center"><i class="fa fa-remove pointer text-danger" onclick="removeItem(1)"></i></td>\n\
                                        </tr>';
            var newRow = $(document.createElement('div')).attr("id", 'itemTableDiv');
            var itemTableDiv = '<table class="table table-bordered custom-table m-t-10" id="itemTable">\n\
                                    <tr class="bg-info">\n\
                                        <td width="15%"><b>Item Head</b></td>\n\
                                        <td width="10%"><b>Quantity</b></td>\n\
                                        <td width="10%"><b>Unit Name</b></td>\n\
                                        <td width="10%"><b>Price Per Unit</b></td>\n\
                                        <td width="10%"><b>Adjust<small> (+/-)</small></b></td>\n\
                                        <td width="10%"><b>Amount (BDT)</b></td>\n\
                                        <td width="25%"><b>Remarks</b></td>\n\
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
                                            <input type="hidden" name="itemCategoryCode' + takenItemCount + '" id="itemCategoryCode' + takenItemCount + '">\n\
                                            <input type="hidden" name="itemCategoryName' + takenItemCount + '" id="itemCategoryName' + takenItemCount + '">\n\
                                            <input type="hidden" name="itemHeadCode' + takenItemCount + '" id="itemHeadCode' + takenItemCount + '">\n\
                                            <input type="hidden" name="itemHeadName' + takenItemCount + '" id="itemHeadName' + takenItemCount + '">\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' + takenItemCount + ')" onchange="calculateGrandTotal(' + takenItemCount + ')" name="quantity' + takenItemCount + '" id="quantity' + takenItemCount + '">\n\
                                                </td>\n\
                                            <td><input type="text" class="form-control custom-form-control1"  name="unitName' + takenItemCount + '" id="unitName' + takenItemCount + '" readonly>\n\
                                                </td>\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' + takenItemCount + ')" onchange="calculateGrandTotal(' + takenItemCount + ')" name="unitPrice' + takenItemCount + '" id="unitPrice' + takenItemCount + '">\n\
                                                </td>\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' + takenItemCount + ')" onchange="calculateGrandTotal(' + takenItemCount + ')" name="adjust' + takenItemCount + '" id="adjust' + takenItemCount + '">\n\
                                                 </td>\n\
                                            <td><input type="text" class="form-control custom-form-control" onkeyup="calculateGrandTotal(' + takenItemCount + ')" onchange="calculateGrandTotal(' + takenItemCount + ')" name="amount' + takenItemCount + '" id="amount' + takenItemCount + '" readonly>\n\
                                                </td>\n\
                                            <td><input type="text" class="form-control custom-form-control1" max="200" name="remarks' + takenItemCount + '" id="remarks' + takenItemCount + '" >\n\
                                                </td>\n\
                                            <td class="td-center"><i class="fa fa-remove pointer text-danger" onclick="removeItem(' + takenItemCount + ')"></i></td>';
            newRow.after().html(itemTableRowStr);
            newRow.appendTo("#itemTable");
            $("#takenItemCount").val(takenItemCount);
        }
    }

    function removeItem(takenItemCount) {
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
        var adjustInput = $('#adjust' + takenItemCount).val();
        if (!$.isNumeric(quantity)) {
            quantity = 0;
            $('#quantity' + takenItemCount).val('');
        }

        if (!$.isNumeric(unitPrice)) {
            unitPrice = 0;
            $('#unitPrice' + takenItemCount).val('');
        }

        var adjust = parseFloat(adjustInput);
        if (!(adjustInput === '-' || adjustInput === '+')) {
            if (!$.isNumeric(adjustInput)) {
                $('#adjust' + takenItemCount).val('');
                adjust = 0;
            }
        } else {
            adjust = 0;
        }

        var amount = (parseFloat(quantity) * parseFloat(unitPrice)) + parseFloat(adjust);
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

    function checkFile() {
        var fp = $("#invoiceFile");
        var lg = fp[0].files.length; // get length
        var items = fp[0].files;
        var fileSize = 0;
        var fileExtension = ['jpeg', 'jpg', 'png', 'txt', 'doc', 'docx', 'pdf'];
        if (lg > 0) {
            for (var i = 0; i < lg; i++) {
                fileSize = fileSize + items[i].size;
                if ($.inArray(items[i].name.split('.').pop().toLowerCase(), fileExtension) === -1) {
                    sweetAlert("Only 'jpeg','jpg','png','txt','doc','docx','pdf' formats are allowed...!");
                    $('#invoiceFile').val('');
                    return false;
                }
            }
            if (fileSize > 2097152) {
                sweetAlert('File size must not be more than 2 MB...!');
                $('#invoiceFile').val('');
            }
        }
    }

    function showItemHeadModal(takenItemCount) {
        $('#takenItemSerial').val(takenItemCount);

        const modal = new bootstrap.Modal(document.getElementById('itemModal'));
        modal.show();
    }

    function addNewInvoice() {
        var takenItemCount = $('#takenItemCount').val();
        var itemFlag = 0;
        for (var j = 1; j <= takenItemCount; j++) {
            var amount = $('#amount' + j).val();
            if (typeof amount !== 'undefined') {
                itemFlag = 1;
                var itemCategoryCode = $('#itemCategoryCode' + j).val();
                var itemHeadCode = $('#itemHeadCode' + j).val();
                var quantity = $('#quantity' + j).val();
                var unitName = $('#unitName' + j).val();
                var unitPrice = $('#unitPrice' + j).val();
                if (amount === "" || itemCategoryCode === "" || itemHeadCode === "" || quantity === "" || unitName === "" || unitPrice === "") {
                    if ($('#remarks' + j).val().length > 200) {
                        sweetAlert('Remarks max length is 200 characters...!');
                        return false;
                    }

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

        if (itemFlag === 0) {
            sweetAlert('Please select at least one item...!');
            return false;
        }

        if ($.trim($('#invoiceTitle').val()) === "" || $.trim($('#invoiceDate').val()) === "" || $.trim($('#invoiceDueDate').val()) === "") {
            sweetAlert('Invoice Title, Invoice Date and Invoice Due Date is required...!');
            return false;
        }

        var isGuest = parseInt($('#isGuest').val());
        if (isGuest === 0) {
            var boarderId = $('#boarderIdHidden').val();
            if (boarderId === '') {
                sweetAlert('Boarder is required...!');
                return false;
            }
        } else if (isGuest === 1) {
            var guestName = $('#guestName').val();
            var guestMobile = $('#guestMobile').val();
            var guestEmail = $('#guestEmail').val();

            if (guestName === '' || guestMobile === '' || guestEmail === '') {
                sweetAlert('Guest Name, Email is required...!');
                return false;
            }
        }
        $('#invoiceForm').submit();
    }

    function toggleGuestDiv() {
        var isGuest = parseInt($('#isGuest').val());
        if (isGuest === 0) {
            $('#boarderIdHidden').val('');
            $('#boarderNameHidden').val('');
            $('#boarderPrimaryMobileHidden').val('');
            $("#guestDiv").show("fast");
            $('#isGuest').val('1');
            $('#boarderDiv').hide("fast");
        } else if (isGuest === 1) {
            $('#guestName').val('');
            $('#guestMobile').val('');
            $("#guestDiv").hide("fast");
            $('#isGuest').val('0');
            $('#boarderDiv').show("fast");
        }
    }

    function showBoarderModal() {
        const modal = new bootstrap.Modal(document.getElementById('boarderModal'));
        modal.show();
    }

    function setBoarder(count) {
        const modal = bootstrap.Modal.getInstance(document.getElementById('boarderModal'));
        modal.hide();
        $('#boarderNameHidden').val($('#boarderNameModalHidden' + count).val());
        $('#boarderIdHidden').val($('#boarderIdModalHidden' + count).val());
        $('#boarderPrimaryMobileHidden').val($('#boarderPrimaryMobileModalHidden' + count).val());
        $('#boarderAddressHidden').val($('#boarderAddressModalHidden' + count).val());
        $('#boarderEmailHidden').val($('#boarderEmailModalHidden' + count).val());
    }
</script>
@endpush
