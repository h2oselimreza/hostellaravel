@extends('layouts.app')
@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Employees</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}"> Home</a></li>
        <li><a href="#">/  Corporate Customer </a></li>
        <li><a href="{{ url('admin/employees') }}">/ Employee</a></li>
    </ul>
</div>
<div class="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="add-button">
        <a href="{{ route('admin.employee.module.create') }}">Add employee</a>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-default"> 
                <div class="table-responsive">
                     <table class="table table-bordered table-hover custom-table" id="datatable">
                        <thead>
                            <tr class="bg-primary">
                                <th>SL</th>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Designation</th>
                                <th>Contact No</th>
                                <th>User Group</th>
                                <th>First Time Reset</th>
                                <th>Status</th>
                                <th>Action</th>
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
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.employee.data.index') }}",
        columns: [
            {data: 'id', name: 'id', orderable:true, searchable:true, className: 'text-center'},
            {data: 'employee_id', name: 'employee_id', orderable:true, searchable:true, className: 'text-center'},
            {data: 'employee_name', name: 'employee_name', orderable:true, searchable:true, className: 'text-center'},
            {data: 'designation', name: 'designation', orderable:false, searchable:false, className: 'text-center'},
            {data: 'primary_mobile', name: 'primary_mobile', orderable:false, searchable:false, className: 'text-center'},
            {data: 'group_name', name: 'group_name', orderable:false, searchable:false, className: 'text-center'},
            {data: 'is_reset', name: 'is_reset', orderable:false, searchable:false, className: 'text-center'},
            {data: 'status', name: 'status', orderable:false, searchable:false, className: 'text-center'},
            {data: 'action', name: 'action', orderable:false, searchable:false, className: 'text-center'},
        ],

        // ✅ moved here (DO NOT create second DataTable)
        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;

                // ❌ Skip Action column (last column index = 7)
                if (column.index() === 8) return;

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

function deleteRecord(url)
    {
        if(confirm('Are you sure you want to delete this record?'))
        {
            let form = document.getElementById('delete-form');
            form.action = url;
            form.submit();
        }
    }
    
</script>
@endpush
