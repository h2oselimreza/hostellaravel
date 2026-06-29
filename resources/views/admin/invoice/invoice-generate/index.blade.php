@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Invoice Generate</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li><a href="#">/ Invoice</a></li>
        <li><a href="{{ url('admin/inv-generate') }}">/ Invoice Generate</a></li>
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
        <div class="col-sm-12 col-md-12">

            <div class="panel panel-default"> 
                <div class="table-responsive">

                    <table class="table table-bordered table-hover custom-table" id="datatable">
                        <thead>
                            <tr class="bg-primary">
                                <th class="text-center">SL</th>
                                <th class="text-center">Boarder ID</th>
                                <th class="text-center">Boarder Name</th>
                                <th class="text-center">Building</th>
                                <th class="text-start">Floor</th>
                                <th class="text-start">Room</th>
                                <th class="text-start">Seat Title</th>
                                <th class="text-start">Seat Type</th>
                                <th class="text-start">Last Invoice Date</th>
                                <th class="text-center">Has Template</th>
                                <th class="no-sort" style="width:50px"><input type="checkbox" id="selectall" onClick="selectAll(this)" /></th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $value)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $value->boarder_id }}</td>
                                    <td class="text-center">{{ $value->boarder_name }}</td>
                                    <td class="text-center">{{ $value->building_title }}</td>
                                    <td class="text-center">{{ $value->floor_title }}</td>
                                    <td class="text-center">{{ $value->room_title }}</td>
                                    <td class="text-center">{{ $value->seat_title }}</td>
                                    <td class="text-center">{{ $value->seat_type_title }}</td>
                                    <td class="text-center">{{ $value->last_invoice_date }}</td>
                                    @if($value->has_template == 1)
                                        <td class="td-center">Yes</td>
                                        <td class="td-center">
                                            <input
                                                type="checkbox"
                                                id="boarderCheck{{ $loop->iteration }}"
                                                name="boarderCheck[]"
                                                value="{{ $loop->iteration }}-{{ $value->id }}"
                                                onclick="setCheckBox(this.value, this.id)"
                                            >
                                        </td>
                                    @else
                                        <td class="td-center">No</td>
                                        <td class="td-center"></td>
                                    @endif
                                </tr>
                            @empty
                                {{-- <tr>
                                    <td colspan="3" class="text-center">No Data Found</td>
                                </tr> --}}
                            @endforelse
                        </tbody>

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

                    </table>
                    <form id="formId" action="{{ route('admin.invoice-generate.doGenerate') }}" method="post">
                        @csrf
                        <div class="container">
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <div class="form-group">
                                        <label class="form-label">Invoice Title</label><span class="text-danger">*</span><small class="hidden custom-text-danger" id="invoiceTitleReq-error"> Invoice Title is required</small>
                                        <input type="text" class="form-control" name="invoiceTitle" id="invoiceTitle">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-4 ">
                                    <div class="form-group">
                                        <label class="form-label">Invoice Date</label><span class="text-danger">*</span><small class="hidden custom-text-danger" id="invoiceDateReq-error"> Invoice Date is required</small>
                                        <input type="text" class="form-control dateInput" name="invoiceDate" id="invoiceDate" value="<?php echo date('Y-m-d') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <div class="form-group">
                                        <label class="form-label">Due Date</label><span class="text-danger">*</span><small class="hidden custom-text-danger" id="invoiceDueDateReq-error"> Due Date is required</small>
                                        <input type="text" class="form-control dateInput" name="invoiceDueDate" id="invoiceDueDate" value="<?php echo date('Y-m-d') ?>">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="boarderIdStr" id="boarderIdStr">
                        </div>
                    </form>
                    <div style="margin-left: 12px;">
                        <button class="btn btn-success save_button mt-3" onclick="submitForm()">Generate Invoice</button>
                    </div>
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
    var boarderIdArr = new Array();
    function selectAll(source) {
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
                boarderIdArr = jQuery.grep(boarderIdArr, function (value) {
                    return value !== itemtoRemove;
                });
                boarderIdArr.push($("#" + boarderCheckBoxIdArr[i]).val());
            } else {
                var itemtoRemove = $("#" + boarderCheckBoxIdArr[i]).val();
                boarderIdArr = jQuery.grep(boarderIdArr, function (value) {
                    return value !== itemtoRemove;
                });
            }
        }
        $("#selectedBoarder").html(boarderIdArr.length);
    }

    function setCheckBox(boarderId, checkBoxId) {
        if ($("#" + checkBoxId).is(':checked')) {
            var itemtoRemove = boarderId;
            boarderIdArr = jQuery.grep(boarderIdArr, function (value) {
                return value !== itemtoRemove;
            });
            boarderIdArr.push(boarderId);
        } else {
            var itemtoRemove = boarderId;
            boarderIdArr = jQuery.grep(boarderIdArr, function (value) {
                return value !== itemtoRemove;
            });
        }
        $("#selectedBoarder").html(boarderIdArr.length);
    }

    function submitForm() {
        if ($.trim($('#invoiceTitle').val()) === "" || $.trim($('#invoiceDate').val()) === "" || $.trim($('#invoiceDueDate').val()) === "") {
            sweetAlert('Invoice Title, Invoice Date and Invoice Due Date is required...!');
            return false;
        }
        var boarderIdStr = boarderIdArr.join();
        if (boarderIdStr) {
            $('#boarderIdStr').val(boarderIdStr);
            $("#formId").submit();
        } else {
            sweetAlert('Please select at least one boarder...!');
        }
    }


</script>
@endpush