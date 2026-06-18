@extends('layouts.app')
@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Modules</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}"> Home</a></li>
        <li><a href="#">/ User</a></li>
        <li><a href="{{ url('admin/modules') }}">/ Modules</a></li>
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
        <a href="{{ route('admin.modules.create') }}">Add Module Group</a>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-default"> 
                <div class="table-responsive">
                     <table class="table table-bordered table-hover custom-table" id="datatable">
                        <thead>
                            <tr class="bg-primary">
                                <th>SL</th>
                                <th>Penal Type</th>
                                <th>Module Group</th>
                                <th>Module Name</th>
                                <th>Module Url</th>
                                <th>Order</th>
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
        ajax: "{{ route('modules.data.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false},
            {data: 'panel_type', name: 'panel_type', orderable:true, searchable:true},
            {data: 'module_group', name: 'module_group', orderable:true, searchable:true},
            {data: 'modules_name', name: 'modules_name', orderable:true, searchable:true},
            {data: 'module_url', name: 'module_url', orderable:true, searchable:true},
            {data: 'module_order', name: 'module_order', orderable:true, searchable:true},
            {data: 'action', name: 'action', orderable:false, searchable:false, className: 'text-center'},
        ],

        // ✅ moved here (DO NOT create second DataTable)
        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;

                // ❌ Skip Action column (last column index = 7)
                if (column.index() === 6) return;

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
