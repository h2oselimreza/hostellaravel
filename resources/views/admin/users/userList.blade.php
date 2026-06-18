@extends('layouts.app')
@section('content')

<script>
    function superAdminInactive() {
        sweetAlert('Can not possible. Super Admin is always active');
        return false;
    }
    function inactiveGroupCheck() {
        sweetAlert('If you want to active this user, you have to active the user group first');
        return false;
    }
</script>

<div class="header">
    <h1 class="page-title">Users</h1>
    <ul class="breadcrumb">
        <li><a href="<?php /*echo base_url() */ ?>admin/Home"> Home</a></li>
        <li><a href="#"> Users</a></li>
        <li><a href="#"> User</a></li>
    </ul>
</div>
<div class="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ session('success') }}
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
                                <th>SL</th>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Contact No</th>
                                <th>Email</th>
                                <th>User Group</th>
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
    ajax: "{{ route('users.data.index') }}",
    columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false,className: 'text-center'},
        {data: 'username', name: 'username'},
        {data: 'full_name', name: 'full_name'},
        {data: 'contact_no', name: 'contact_no'},
        {data: 'email', name: 'email'},
        {data: 'user_group', name: 'user_group', className: 'text-center'},
        {data: 'status', name: 'status', orderable:false, searchable:false, className: 'text-center'},
        {data: 'action', name: 'action', orderable:false, searchable:false, className: 'text-center'},
    ],

    // ✅ moved here (DO NOT create second DataTable)
    initComplete: function () {
        this.api().columns().every(function () {
            var column = this;

            // ❌ Skip Action column (last column index = 7)
            if (column.index() === 7) return;

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

    $('button.inactive-user').click(function () {
        var userId = $(this).attr("data-user-id");
        inactiveUser(userId);
    });

    $('button.active-user').click(function () {
        var userId = $(this).attr("data-user-id");
        activeUser(userId);
    });

    function inactiveUser(userId) {
        swal({
            title: "Are you sure?",
            text: "Do you sure that you want to inactive this user?",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes, Inactive it...!",
            confirmButtonColor: "#ec6c62"
        }, function () {
            $.ajax({
                url: "<?php /*echo base_url()*/ ?>Users/inActiveUser/" + userId,
                type: "DELETE"
            })
                    .done(function (data) {
                        swal({
                            title: "Successfully Inactive",
                            text: "This User is inactive now",
                            type: "success",
                            closeOnConfirm: false,
                            confirmButtonText: "Okey",
                            confirmButtonColor: "#A5DC86"
                        }, function () {
                            window.location.href = "<?php /*echo base_url()*/ ?>Users/userListShow";
                        });
                    })
                    .error(function (data) {
                        swal("Oops", "We couldn't connect to the server!", "error");
                    });
        });
    }
    function activeUser(userId) {
        swal({
            title: "Are you sure?",
            text: "Do you sure that you want to active this user?",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes, active it...!",
            confirmButtonColor: "#ec6c62"
        }, function () {
            $.ajax({
                url: "<?php /*echo base_url()*/ ?>Users/activeUser/" + userId,
                type: "DELETE"
            })
                    .done(function (data) {
                        swal({
                            title: "Successfully Active",
                            text: "This User is active now",
                            type: "success",
                            closeOnConfirm: false,
                            confirmButtonText: "Okey",
                            confirmButtonColor: "#A5DC86"
                        }, function () {
                            window.location.href = "<?php /*echo base_url()*/ ?>Users/userListShow";
                        });
                    })
                    .error(function (data) {
                        swal("Oops", "We couldn't connect to the server!", "error");
                    });
        });
    }
</script>
@endpush
