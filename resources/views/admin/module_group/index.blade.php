@extends('layouts.app')
@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Module Group</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}"> Home</a></li>
        <li><a href="#">/ User</a></li>
        <li><a href="{{ url('admin/module-group') }}">/ Module Group</a></li>
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
        <a href="{{ route('admin.module-group.create') }}">Add Module Group</a>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-default"> 
                <div class="table-responsive">
                     <table class="table table-bordered table-hover custom-table" id="datatable">
                        <thead>
                            <tr class="bg-primary">
                                <th>Penal Type</th>
                                <th>Module group name</th>
                                <th>Module group order</th>
                                <th>Module group code</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                        <tr>
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
        ajax: "{{ route('module-group.data.index') }}",
        columns: [
            {data: 'panel_type', name: 'panel_type', orderable:false, searchable:false},
            {data: 'module_group_name', name: 'module_group_name', orderable:true, searchable:true},
            {data: 'module_group_code', name: 'module_group_code', orderable:true, searchable:true},
            {data: 'module_group_order', name: 'module_group_order', orderable:true, searchable:true},
            {data: 'action', name: 'action', orderable:false, searchable:false, className: 'text-center'},
        ],

        // ✅ moved here (DO NOT create second DataTable)
        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;

                // ❌ Skip Action column (last column index = 7)
                if (column.index() === 4) return;

                var select = $('<select class="form-control" style="width:100%"><option value="">Select All</option></select>')
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

    // $('button.inactive-user').click(function () {
    //     var userId = $(this).attr("data-user-id");
    //     inactiveUser(userId);
    // });

    // $('button.active-user').click(function () {
    //     var userId = $(this).attr("data-user-id");
    //     activeUser(userId);
    // });

    // function inactiveUser(userId) {
    //     swal({
    //         title: "Are you sure?",
    //         text: "Do you sure that you want to inactive this user?",
    //         type: "warning",
    //         showCancelButton: true,
    //         closeOnConfirm: false,
    //         confirmButtonText: "Yes, Inactive it...!",
    //         confirmButtonColor: "#ec6c62"
    //     }, function () {
    //         $.ajax({
    //             url: "<?php /*echo base_url()*/ ?>Users/inActiveUser/" + userId,
    //             type: "DELETE"
    //         })
    //                 .done(function (data) {
    //                     swal({
    //                         title: "Successfully Inactive",
    //                         text: "This User is inactive now",
    //                         type: "success",
    //                         closeOnConfirm: false,
    //                         confirmButtonText: "Okey",
    //                         confirmButtonColor: "#A5DC86"
    //                     }, function () {
    //                         window.location.href = "<?php /*echo base_url()*/ ?>Users/userListShow";
    //                     });
    //                 })
    //                 .error(function (data) {
    //                     swal("Oops", "We couldn't connect to the server!", "error");
    //                 });
    //     });
    // }
    // function activeUser(userId) {
    //     swal({
    //         title: "Are you sure?",
    //         text: "Do you sure that you want to active this user?",
    //         type: "warning",
    //         showCancelButton: true,
    //         closeOnConfirm: false,
    //         confirmButtonText: "Yes, active it...!",
    //         confirmButtonColor: "#ec6c62"
    //     }, function () {
    //         $.ajax({
    //             url: "<?php /*echo base_url()*/ ?>Users/activeUser/" + userId,
    //             type: "DELETE"
    //         })
    //                 .done(function (data) {
    //                     swal({
    //                         title: "Successfully Active",
    //                         text: "This User is active now",
    //                         type: "success",
    //                         closeOnConfirm: false,
    //                         confirmButtonText: "Okey",
    //                         confirmButtonColor: "#A5DC86"
    //                     }, function () {
    //                         window.location.href = "<?php /*echo base_url()*/ ?>Users/userListShow";
    //                     });
    //                 })
    //                 .error(function (data) {
    //                     swal("Oops", "We couldn't connect to the server!", "error");
    //                 });
    //     });
    // }
</script>
@endpush
