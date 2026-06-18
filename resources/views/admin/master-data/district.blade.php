@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">District</h1>
    <ul class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li><a href="#">/ Master Data</a></li>
        <li><a href="{{ url('admin/area/districts') }}">/ District</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="row">
        <div class="col-sm-12 col-md-12">

            @include('admin.master-data.areaHeaderMenu')

            <div class="panel panel-default"> 
                <div class="table-responsive">

                    <table class="table table-bordered table-hover custom-table" id="datatable">
                        <thead>
                            <tr class="bg-primary">
                                <th>SL</th>
                                <th>Division Name (En)</th>
                                <th>Division Name (Bn)</th>
                                <th>District Name (En)</th>
                                <th>District Name (Bn)</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($districts as $district)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $district->division_relation->division_en_name }}</td>
                                    <td class="text-center">{{ $district->division_relation->division_bn_name }}</td>
                                    <td class="text-center">{{ $district->district_en_name }}</td>
                                    <td class="text-center">{{ $district->district_bn_name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No Data Found</td>
                                </tr>
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