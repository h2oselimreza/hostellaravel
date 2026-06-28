@extends('layouts.app')

@section('content')
<style>
    p{
        font-size: 14px;
    }
    .table-td-info{
        margin-bottom: 2px;
    }
</style>
<div class="header">
    <h1 class="page-title">Make Payment</h1>
    <ul class="breadcrumb">
        <li><a href="/admin/Home"> Home</a></li>
        <li><a href="#"> Invoice</a></li>
        <li><a href="/admin/Invoice/invoicePaymentList"> Invoice Payment</a></li>
        <li class="active"><a href="/admin/Invoice/invoicePaymentShow?invoiceNo=<?php echo $invoiceNo ?>"> Make Payment</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-default"> 
                <div id="errorDiv" class="alert alert-danger hidden">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <?php
                    foreach ($invoiceSummary as $invoiceSummary) {
                        $invoiceTitle = $invoiceSummary['invoice_title'];
                        $invoiceDate = $invoiceSummary['invoice_date'];
                        $invoiceDueDate = $invoiceSummary['invoice_due_date'];
                        $invoiceNo = $invoiceSummary['invoice_no'];
                        $updateDtTm = $invoiceSummary['updated_dt_tm'];
                        $isGuest = $invoiceSummary['is_guest'];
                        $boarderPrimaryMobile = $invoiceSummary['boarder_primary_mobile'];
                        $boarderName = $invoiceSummary['boarder_name'];
                        $boarderCode = $invoiceSummary['boarder'];

                        if (!$boarderCode) {
                            $guestName = $invoiceSummary['guest_name'];
                            $guestMobile = $invoiceSummary['guest_mobile'];
                        }
                        $discountAmount = $invoiceSummary['discount'];
                        $discountType = $invoiceSummary['discount_type'];
                        $paidAmount = $invoiceSummary['paid_amount'];
                        $totalAmount = $invoiceSummary['total_amount'];
                    }
                    ?>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="form-label mb-0"> Invoice Title </label><span class="text-danger">*</span><small class="hidden custom-text-danger" id="invoiceTitleReq-error"> Invoice Title is required</small>
                                <p><?php echo $invoiceTitle ?></p>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="form-label mb-0">Invoice Date</label><small class="hidden custom-text-danger" id="invoiceDateReq-error"> Invoice Date is required</small>
                                <p><?php echo get_date_format1($invoiceDate) ?></p>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="form-label mb-0">Due Date</label><small class="hidden custom-text-danger" id="invoiceDueDateReq-error"> Due Date is required</small>
                                <p><?php echo get_date_format1($invoiceDueDate) ?></p>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-6" id="boarderDiv" <?php if ($isGuest == 1) { ?> style="display: none" <?php } ?>>
                            <div class="form-group mb-0">
                                <label class="form-label">Boarder</label>
                                <p><?php echo $boarderName ?></p>
                            </div>	
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group mb-0">
                                <label class="form-label">Is Guest?</label><br>
                                <p><?php echo $isGuest ? 'Yes' : 'No'; ?></p>
                            </div>	
                        </div>
                    </div>
                    <div class="row" id="guestDiv" <?php if ($isGuest == 0) { ?> style="display: none" <?php } ?>>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="form-label mb-0">Guest Name</label><span class="text-danger">*</span>
                                <p><?php echo $guestName ?></p>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="form-label mb-0">Guest Vendor Mobile Number</label>
                                <p><?php echo $guestMobile ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <?php
                            $itemStr = "";
                            $i = 1;
                            $invoiceAmount = 0;
                            foreach ($invoiceDetails as $invoiceDetail) {

                                $itemStr .= '
                                    <tr id="itemTakenTr'.$i.'">
                                        <td class="td-left pointer" id="itemHeadTd'.$i.'" onclick="showItemHeadModal('.$i.')">
                                            '.$invoiceDetail->head_name.'
                                        </td>

                                        <input type="hidden" name="itemCategoryCode'.$i.'" id="itemCategoryCode'.$i.'" value="'.$invoiceDetail->item_category.'">
                                        <input type="hidden" name="itemCategoryName'.$i.'" id="itemCategoryName'.$i.'" value="'.$invoiceDetail->category_name.'">
                                        <input type="hidden" name="itemHeadCode'.$i.'" id="itemHeadCode'.$i.'" value="'.$invoiceDetail->item_head.'">
                                        <input type="hidden" name="itemHeadName'.$i.'" id="itemHeadName'.$i.'" value="'.$invoiceDetail->head_name.'">

                                        <td>'.$invoiceDetail->quantity.'</td>
                                        <td>'.$invoiceDetail->unit_name.'</td>
                                        <td>'.$invoiceDetail->unit_price.'</td>
                                        <td>'.$invoiceDetail->adjust.'</td>
                                        <td>'.$invoiceDetail->amount.'</td>
                                        <td>'.$invoiceDetail->remarks.'</td>
                                    </tr>';

                                $invoiceAmount += $invoiceDetail->amount;
                                $i++;
                            }
                            if ($itemStr) {
                                ?>
                                <div id="itemTableDiv">
                                    <table class="table table-bordered custom-table" id="itemTable">
                                        <tr class="bg-info">
                                            <td width="15%"><b>Item Head</b></td>
                                            <td width="10%"><b>Quantity</b></td>
                                            <td width="10%"><b>Unit Name</b></td>
                                            <td width="10%"><b>Price Per Unit</b></td>
                                            <td width="10%"><b>Adjust<small> (+/-)</small></b></td>
                                            <td width="10%"><b>Amount (BDT)</b></td>
                                            <td width="25%"><b>Remarks</b></td>
                                            <!--<td width="10%" class="td-center"><b>Action</b></td>-->
                                        </tr>
                                        <?php echo $itemStr ?>
                                        <input type="hidden" id="takenItemCount" name="takenItemCount" value="<?php echo $i ?>">
                                    </table>
                                </div>
                                <?php
                            }
                            ?>
                            <input type="hidden" id="takenItemSerial">
                            <!--<button type="button" class="btn btn-info" onclick="showItemTable()">Add Item</button>-->
                            <input type="hidden" id="takenItemSerial">
                            <input type="hidden" name="invoiceNo" value="<?php echo $invoiceNo ?>">
                            <input type="hidden" name="updateDtTm" value="<?php echo $updateDtTm ?>">

                            <input type="hidden" id="deleteHeadStr" name="deleteHeadStr">
                            <input type="hidden" id="deleteFileStr" name="deleteFileStr">
                        </div>
                    </div>
                    <div class="row m-t-10">
                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                            <div class="form-group">
                                <!--<input type="file" class="form-control" name="invoiceFile[]" id='invoiceFile' onchange='checkFile(this, this.id);' multiple/>-->
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12 mb-2">
                            <div class="text-right">
                                <b>Total Invoice:</b> <span id="invoiceAmount"><?php echo number_format($invoiceAmount, 2) ?></span> BDT
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover custom-table dataTable">
                                    <thead>
                                        <tr class="bg-primary">
                                            <th>SL</th>
                                            <th>File Name</th>
                                            <th>Show</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($invoiceFiles as $invoiceFile)
                                            <tr id="fileTr{{ $loop->iteration }}">
                                                <td class="td-center">{{ $loop->iteration }}</td>

                                                <td class="td-left">
                                                    {{ $invoiceFile->original_name }}
                                                </td>
                                                <td class="td-center">
                                                    <a href="{{ asset('storage/assets/admin/files/invoice/' . $invoiceFile->file_name) }}">
                                                        Show
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>	
                    </div>
                </div>
                <hr>
                <form action="{{ route('admin.invoice.invoicePayment') }}" method="POST" enctype="multipart/form-data" id="invoicePayment">
                    @csrf
                    <div class="row">
                        <br>
                        <div class="col-md-12">
                            <table class="" border="0" cellpadding="0" cellspacing="0" align="left" width="37%">
                                <tr class="table-td-info">
                                    <td width="20%" align="left" class="content-table-td"><b>Total Invoice Amount </b></td>
                                    <td width="2%" align="center">:</td>
                                    <td width="15%" align="right" class="content-table-td" id="totalInvoiceAmount"><?php echo number_format($invoiceAmount, 2) ?> BDT</td>
                                </tr>
                                <tr class="table-td-info">
                                    <td class="content-table-td" align="left" ><b> Discount Type</b></td>
                                    <td class="content-table-td" align="center">:</td>
                                    <td class="content-table-td" align="right">
                                        <select class="form-control custom-form-control" name="discountType" id="discountType" onchange="calculateDiscount()">
                                            <option value="1" <?php if($discountType == 1) echo 'selected';?>>Flat</option>
                                            <option value="2" <?php if($discountType == 2) echo 'selected';?>>Percent (%)</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="table-td-info">
                                    <td class="content-table-td bottom-border" align="left" ><b> Discount</b></td>
                                    <td class="content-table-td bottom-border" align="center">:</td>
                                    <td class="content-table-td bottom-border" align="right">
                                        <input class="form-control custom-form-control" id="discount" name="discount" type="text" onkeyup="calculateDiscount()" onchange="calculateDiscount()" value="<?php echo $discountAmount ?>">
                                    </td>
                                </tr>
                                <tr class="table-td-info">
                                    <td align="left" class="content-table-td"><b>Total Amount </b></td>
                                    <td align="center">:</td>
                                    <td align="right" class="content-table-td" id="totalAmount"><?php echo $totalAmount ?> BDT</td>
                                </tr>
                                <tr class="table-td-info">
                                    <td class="content-table-td" align="left" ><b> Paid Amount</b></td>
                                    <td class="content-table-td" align="center">:</td>
                                    <td class="content-table-td" align="right">
                                        <input class="form-control custom-form-control" id="paidAmount" name="paidAmount" type="text" value="<?php echo $paidAmount != 0.00 ? $paidAmount : $totalAmount ?>" onkeyup="calculateDiscount()" onchange="calculateDiscount()" readonly>
                                    </td>
                                </tr>
                                <input type="hidden" id="invoiceAmountHidden" value="<?php echo $invoiceAmount ?>">
                                <input type="hidden" id="totalAmountHidden" value="<?php echo $totalAmount ?>">
                                <input type="hidden" name="invoiceNo" value="<?php echo $invoiceNo ?>">
                            </table>
                        </div>
                    </div>
                </form>
                <button type="button" class="btn btn-primary save_button mt-3" onclick="invoicePayment()">Save</button>
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
                if (column.index() === 9) return;

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
    function calculateDiscount() {
        var discount = $('#discount').val();
        var discountType = $('#discountType').val();
        var totalAmount = $('#totalAmountHidden').val();
        if (!$.isNumeric(discount)) {
            $('#discount').val("0.00");
        } else {
            discount = parseFloat(discount);
            if (discount < 0 || totalAmount < discount) {
                $('#discount').val("0.00");
            }
        }
        showBillSummary();
    }

    function showBillSummary() {
        var invoiceAmountHidden = parseFloat($('#invoiceAmountHidden').val());
        var discount = $('#discount').val();
        var discountType = parseInt($('#discountType').val());
        if (discount !== "") {
            discount = parseFloat(discount);
        }
        var totalAmount = 0;

        if (discountType == 1) {
            totalAmount = invoiceAmountHidden - discount;
        } else if (discountType == 2) {
            totalAmount = invoiceAmountHidden - ((invoiceAmountHidden * discount) / 100);
        }
        $('#totalAmount').text((totalAmount).toFixed(2) + ' BDT');
        $('#totalAmountHidden').val(totalAmount);
        $('#paidAmount').val(totalAmount);
    }

    function invoicePayment() {
        var discount = $('#discount').val();
        var discountType = $('#discountType').val();
        var paidAmount = $('#paidAmount').val();
        var totalAmount = parseFloat($('#totalAmountHidden').val());

        if (discount === '' || discountType === '' || paidAmount === '') {
            sweetAlert('Discount type, discount amount, paid amount is required ...!');
            return false;
        }
        
        paidAmount = parseFloat(paidAmount);

        if (paidAmount > totalAmount) {
            sweetAlert('Paid amount can not exceed total amount...!');
            return false;
        }

        if (paidAmount < 0) {
            sweetAlert('You must make a valid payment...!');
            return false;
        }

        $('#invoicePayment').submit();
    }
</script>
@endpush