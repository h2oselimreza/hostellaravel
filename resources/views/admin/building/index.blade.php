@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Building</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li><a href="#">/ Master Data</a></li>
        <li><a href="{{ url('admin/building') }}">/ Building</a></li>
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

            {{-- @include('admin.master-data.call-center.tab') --}}

            <div class="panel panel-default"> 
                <div class="add-button">
                    <a href="{{ route('admin.building.create') }}">Add Building</a>
                </div>
                <div class="table-responsive">

                    <table class="table table-bordered table-hover custom-table" id="datatable">
                        <thead>
                            <tr class="bg-primary">
                                <th class="text-center">SL</th>
                                <th class="text-center">Building Code</th>
                                <th class="text-start">Building Title</th>
                                <th class="text-center">Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($data as $value)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $value->building_code }}</td>
                                    <td class="text-center">{{ $value->title }}</td>
                                    <td class="text-center">{{ ($value->is_active) ? 'Active':'Inactive' }}</td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown">
                                                Action
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ $value ? route('admin.building.edit', $value->building_code) : '#' }}" 
                                                    class="d-block ps-3">
                                                        <span class="ui-button-text">Update</span>
                                                    </a>                                    
                                                </li>
                                                <li class="mt-2">
                                                    <form action="#" method="POST">
                                                        @csrf
                                                        <button type="submit" class="d-block ps-3 active_button">
                                                            <span>
                                                                {{ $value->is_active ? 'Inactive' : 'Active' }}
                                                            </span>
                                                        </button>
                                                    </form>
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
</script>
@endpush