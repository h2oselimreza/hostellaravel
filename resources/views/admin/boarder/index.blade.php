@extends('layouts.app')
@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Boarder List</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}"> Home</a></li>
        <li><a href="#">/  Boarder Enrollment </a></li>
        <li><a href="{{ url('admin/boarder-enrollment/boarder') }}">/ Boarder List</a></li>
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


    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-default"> 
                <div class="table-responsive">
                    <table class="table table-bordered table-hover custom-table" id="datatable">
                        <thead>
                            <tr class="bg-primary">
                                <th>SL</th>
                                <th>Boarder ID</th>
                                <th>Boarder Name</th>
                                <th>Building</th>
                                <th>Floor</th>
                                <th>Room</th>
                                <th>Seat Title</th>
                                <th>Seat Type</th>
                                <th>Allocated Date Time</th>
                                <th>Has Template</th>
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
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>

                        <tbody>
                            @foreach($boarders as $boarder)
                                <tr>
                                    <td class="td-center">{{ $loop->iteration }}</td>
                                    <td class="td-center">{{ $boarder->boarder_id }}</td>
                                    <td>{{ $boarder->boarder_name }}</td>
                                    <td>{{ $boarder->building_title }}</td>
                                    <td>{{ $boarder->floor_title }}</td>
                                    <td>{{ $boarder->room_title }}</td>
                                    <td>{{ $boarder->seat_title }}</td>
                                    <td class="td-center">{{ $boarder->seat_type_title }}</td>

                                    <td class="td-center">
                                        {{ $boarder->allocated_dt_tm ? get_date_time_format($boarder->allocated_dt_tm) : '' }}
                                    </td>

                                    <td class="td-center">
                                        {{ $boarder->has_template == 1 ? 'Yes' : 'No' }}
                                    </td>

                                    <td class="td-center">
                                        {{ $boarder->is_active == 1 ? 'Active' : 'Inactive' }}
                                    </td>

                                    <td class="td-center">
                                        <div class="dropdown">
                                        <button
                                            class="btn btn-secondary btn-sm dropdown-toggle"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            Action
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item"
                                                href="{{ route('admin.boarder-enrollment.new-boarder.personal.info.edit', $boarder->boarder_id) }}">
                                                    Update
                                                </a>
                                            </li>

                                            <li>
                                                <a class="dropdown-item"
                                                href="#"
                                                onclick="vacant('{{ $boarder->boarder_id }}')">
                                                    Vacant
                                                </a>
                                            </li>

                                            @if($boarder->is_active == 1)
                                                <li>
                                                    <a class="dropdown-item"
                                                    href="#"
                                                    onclick="statusChange('{{ $boarder->boarder_id }}', '2')">
                                                        Inactive
                                                    </a>
                                                </li>
                                            @endif

                                            <li>
                                                <a class="dropdown-item"
                                                href="#"
                                                onclick="showSeatModal('{{ $boarder->boarder_id }}')">
                                                    Transfer
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <input type="hidden" id="transferBoarderId">
                </div>
            </div>
        </div>
    </div>
</div>

<button
    type="button"
    class="btn btn-default d-none"
    data-toggle="modal"
    data-target="#seatModal"
    id="showSeatModalBtn">
</button>

<div class="modal fade" id="seatModal" tabindex="-1" role="dialog" aria-labelledby="largeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="largeModalLabel">
                    Vacant Seat List
                </h4>
            </div>

            <div class="modal-body">
                <div class="table-responsive" style="overflow-x:auto;">
                    <table class="table table-bordered table-hover jq-option-datatable custom-table dataTable">
                        <thead>
                            <tr class="bg-primary">
                                <th>SL</th>
                                <th>Building</th>
                                <th>Floor</th>
                                <th>Room</th>
                                <th>Seat Code</th>
                                <th>Seat Title</th>
                                <th>Seat Type</th>
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

                        <tbody>
                            @php($serial = 1)

                            @foreach($seats as $seat)

                                @if(!empty($seat->boarder))
                                    @continue
                                @endif

                                <tr>
                                    <td class="td-center">
                                        {{ $serial }}
                                    </td>

                                    <td>
                                        {{ $seat->building_title }}
                                    </td>

                                    <td>
                                        {{ $seat->floor_title }}
                                    </td>

                                    <td>
                                        {{ $seat->room_title }}
                                    </td>

                                    <td class="td-center">
                                        {{ $seat->seat_code }}
                                    </td>

                                    <td>
                                        {{ $seat->title }}
                                    </td>

                                    <td class="td-center">
                                        {{ $seat->seat_type_title }}
                                    </td>

                                    <td class="td-center">
                                        <input
                                            type="hidden"
                                            id="seatCodeHidden{{ $serial }}"
                                            value="{{ $seat->seat_code }}">

                                        <button
                                            type="button"
                                            class="btn btn-primary btn-xs btn-circle-puchase"
                                            onclick="transferSeat({{ $serial }})">

                                            <i class="fa fa-arrow-down"></i>
                                        </button>
                                    </td>
                                </tr>

                                @php($serial++)
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-link waves-effect"
                    id="seatModalCloseBtn"
                    data-dismiss="modal">
                    CLOSE
                </button>
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
        serverSide: false,
        pageLength: 25,
        deferRender: true,
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
                if (column.index() === 11) return;

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
    function vacant(boarderId) {

        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, vacant this seat!',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#ec6c62',
            reverseButtons: true
        }).then((result) => {

            if (result.isConfirmed) {

                showLoader();

                $.ajax({
                    url: "{{ route('admin.boarder-enrollment.vacant-seat') }}",
                    type: "DELETE",
                    data: {
                        boarderId: boarderId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {

                        hideLoader();

                        if (response.code == 1) {

                            Swal.fire({
                                title: 'Vacant Successfully',
                                text: 'This seat is vacant now',
                                icon: 'success',
                                confirmButtonColor: '#228B22'
                            }).then(() => {

                                window.location.href =
                                    "{{ route('admin.boarder-enrollment.boarder') }}";
                            });

                        } else if (response.code == 2) {

                            window.location.href =
                                "{{ route('admin.boarder-enrollment.boarder') }}";
                        }
                    },
                    error: function () {

                        hideLoader();

                        Swal.fire(
                            'Oops',
                            "We couldn't connect to the server!",
                            'error'
                        );
                    }
                });
            }
        });
    }

    function statusChange(boarderId, status) {

        let buttonText = '';
        let title = '';
        let text = '';

        if (status === '2') {
            buttonText = 'Yes, inactive this boarder!';
            title = 'Inactive Successfully!';
            text = 'This boarder is inactive now!';
        }

        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ec6c62',
            confirmButtonText: buttonText,
            reverseButtons: true
        }).then((result) => {

            if (result.isConfirmed) {

                showLoader();

                $.ajax({
                    url: "{{ route('admin.boarder-enrollment.status-change') }}",
                    type: "DELETE",
                    data: {
                        boarderId: boarderId,
                        status: status,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {

                        hideLoader();

                        if (response.success) {

                            Swal.fire({
                                title: title,
                                text: text,
                                icon: 'success',
                                confirmButtonColor: '#228B22'
                            }).then(() => {

                                window.location.href =
                                    "{{ route('admin.boarder-enrollment.boarder') }}";
                            });
                        }
                    },
                    error: function() {

                        hideLoader();

                        Swal.fire(
                            'Oops',
                            "We couldn't connect to the server!",
                            'error'
                        );
                    }
                });
            }
        });
    }

    function showSeatModal(boarderId) {
        $('#transferBoarderId').val(boarderId);

        const modal = new bootstrap.Modal(
            document.getElementById('seatModal')
        );

        modal.show();
    }

    function transferSeat(serial) {

    let seatCode = $('#seatCodeHidden' + serial).val();
    let boarderId = $('#transferBoarderId').val();

    $('#seatModalCloseBtn').click();

    Swal.fire({
        title: 'Are you sure?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, transfer to this seat!',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#ec6c62',
        reverseButtons: true
    }).then((result) => {

        if (result.isConfirmed) {

            showLoader();

            $.ajax({
                url: "{{ route('admin.boarder-enrollment.transfer-seat') }}",
                type: "DELETE",
                data: {
                    boarderId: boarderId,
                    seatCode: seatCode,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {

                    hideLoader();
                    console.log(response);
                    if (response.code == 1) {

                        Swal.fire({
                            title: 'Transfer Successfully',
                            text: 'This boarder is transferred now',
                            icon: 'success',
                            confirmButtonColor: '#228B22'
                        }).then(() => {
                            window.location.href =
                                "{{ route('admin.boarder-enrollment.boarder') }}";
                        });

                    } else if (response.code == 2) {

                        Swal.fire({
                            title: 'Transfer Failed',
                            text: 'This seat has already been booked',
                            icon: 'warning',
                            confirmButtonColor: '#f0ad4e'
                        }).then(() => {
                            window.location.href =
                                "{{ route('admin.boarder-enrollment.boarder') }}";
                        });

                    } else if (response.code == 3) {

                        Swal.fire({
                            title: 'Transfer Failed',
                            text: 'Boarder seat allocation not found.',
                            icon: 'warning',
                            confirmButtonColor: '#f0ad4e'
                        }).then(() => {
                            window.location.href =
                                "{{ route('admin.boarder-enrollment.boarder') }}";
                        });
                    }
                },
                error: function (xhr) {

                    hideLoader();

                    Swal.fire(
                        'Oops',
                        "We couldn't connect to the server!",
                        'error'
                    );
                }
            });
        }
    });
}
</script>
@endpush
