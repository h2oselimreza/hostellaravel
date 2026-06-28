@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Expense List</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li><a href="#">/ Expense</a></li>
        <li><a href="{{ url('admin/expense') }}">/ Expense List</a></li>
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
                <div class="add-button">
                    <a href="{{ route('admin.expense.create') }}">Add Expense</a>
                </div>
                <div class="table-responsive">

                    <table class="table table-bordered table-hover custom-table" id="datatable">
                        <thead>
                            <tr class="bg-primary">
                                <th class="text-center">SL</th>
                                <th class="text-center">Expense ID</th>
                                <th class="text-center">Expense Title</th>
                                <th class="text-center">Expense Date</th>
                                <th class="text-start">Total Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $value)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $value->expense_no }}</td>
                                    <td class="text-center">{{ $value->expense_title }}</td>
                                    <td class="text-center">{{ $value->expense_date }}</td>
                                    <td class="text-center">{{ $value->total_amount }}</td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown">
                                                Action
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ $value ? route('admin.expense.edit', $value->expense_no) : '#' }}" 
                                                    class="d-block ps-3">
                                                        <span class="ui-button-text">Update</span>
                                                    </a>                                    
                                                </li>
                                                <li class="mt-2">
                                                    <a href="#"  onclick="removeExpense('{{$value->expense_no}}')"
                                                    class="d-block ps-3">
                                                        <span class="ui-button-text">Remove</span>
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

function removeExpense(expenseNo) {

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
                url: `/admin/expense/${expenseNo}`,
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {

                    hideLoader();

                    if (response.status == 1) {

                        Swal.fire({
                            title: "Removed Successfully",
                            text: response.message,
                            icon: "success",
                            confirmButtonColor: "#228B22"
                        }).then(() => {
                            window.location.href = "/admin/expense";
                        });

                    } else {
                        Swal.fire("Oops!", response.message, "warning");
                    }
                },
                error: function (xhr) {

                    hideLoader();

                    let message = "We couldn't connect to the server.";

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire("Error", message, "error");
                }
            });

        }

    });
}
</script>
@endpush