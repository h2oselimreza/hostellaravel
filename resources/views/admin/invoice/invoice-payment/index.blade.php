@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Invoice List</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li><a href="#">/ Master Data</a></li>
        <li><a href="{{ url('admin/invoice') }}">/ Invoice List</a></li>
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
                                <th class="text-center">Invoice ID</th>
                                <th class="text-center">Invoice Title</th>
                                <th class="text-center">Invoice Date</th>
                                <th class="text-start">Due Date</th>
                                <th class="text-start">Invoice Amount</th>
                                <th class="text-start">Name</th>
                                <th class="text-start">Is Guest</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Mail Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $value)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $value->invoice_no }}</td>
                                    <td class="text-center">{{ $value->invoice_title }}</td>
                                    <td class="text-center">{{ $value->invoice_date }}</td>
                                    <td class="text-center">{{ $value->invoice_due_date }}</td>
                                    <td class="text-center">{{ $value->invoice_amount }}</td>
                                    <td class="text-center">{{ $value->boarder_name }}</td>
                                    <td class="text-center">{{ ($value->is_guest) ? 'Yes':'No' }}</td>
                                    <td class="text-center">{{ ($value->is_paid) ? 'Paid':'Unpaid' }}</td>
                                    <td class="text-center">{{ ($value->mail_send_status) ? 'Yes':'No' }}</td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown">
                                                Action
                                            </button>
                                            <ul class="dropdown-menu">

                                                @if (!$value->is_paid)
                                                    <li>
                                                        <a href="{{ $value ? route('admin.invoice.payment.show', $value->invoice_no) : '#' }}" 
                                                        class="d-block ps-3">
                                                            <span class="ui-button-text">Make Payment</span>
                                                        </a>                                    
                                                    </li>
                                                @endif

                                                @if (!$value->is_paid && !$value->mail_send_status)
                                                    <li class="mt-2">
                                                        <a href="{{ $value ? route('admin.invoice.send.invoice.mail', $value->invoice_no) : '#' }}"
                                                        class="d-block ps-3">
                                                            <span class="ui-button-text">Send a Email</span>
                                                        </a>                                    
                                                    </li>
                                                 @endif
                                                
                                                <li class="mt-2">
                                                    <a href="{{ $value ? route('admin.invoice.test.invoice', $value->invoice_no) : '#' }}"
                                                    class="d-block ps-3">
                                                        <span class="ui-button-text">Test Invoice</span>
                                                    </a>                                    
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
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
function removeInvoice(invoiceNo) {

    Swal.fire({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ec6c62",
        confirmButtonText: "Yes, remove it!"
    }).then((result) => {

        if (result.isConfirmed) {

            showLoader();

            $.ajax({
                url: "{{ route('admin.invoice.destroy', ':invoiceNo') }}".replace(':invoiceNo', invoiceNo),
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {

                    hideLoader();

                    if (response.status == 1) {

                        Swal.fire({
                            title: "Removed Successfully",
                            text: "This invoice has been removed.",
                            icon: "success"
                        }).then(() => {
                            window.location.href = "{{ route('admin.invoice.index') }}";
                        });

                    } else {

                        Swal.fire("Error", response.message, "error");
                    }
                },
                error: function () {

                    hideLoader();

                    Swal.fire(
                        "Oops!",
                        "We couldn't connect to the server!",
                        "error"
                    );
                }
            });

        }

    });

}
</script>
@endpush