@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">
        {{ isset($data->exists) ? 'Edit Boarder' : 'Add Boarder' }}
    </h1>
</div>
<div class="container">
    <div class="card shadow">
        <div class="card-body">
            <!-- Nav Tabs -->
            @include('admin.boarder.add-boarder.tab')
            {{-- Success Message --}}
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

            <!-- Tab Content -->
            <div class="tab-content" id="employeeTabContent">

                <div class="tab-pane fade show active"
                    id="official"
                    role="tabpanel">
                    <form action="{{ route('admin.boarder.profile.photo.update',$data->boarder_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="accordion" id="employeeAccordion">
                            {{-- Personal Information --}}
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#personalInfo"
                                            aria-expanded="true">
                                        Photograph
                                    </button>
                                </h2>

                                <div id="personalInfo"
                                    class="accordion-collapse collapse show"
                                    data-bs-parent="#employeeAccordion">
                                    <div class="accordion-body">
                                        <div class="row g-3">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4">
                                                <div class="d-flex flex-column align-items-center text-center">

                                                    <label class="mb-2">Image (300px X 300px)</label>

                                                    {{-- Show Current Image --}}
                                                    @if($data->boarder_image)
                                                        <img
                                                        id="blah"
                                                        src="{{ asset('storage/assets/admin/files/boarder/' . $data->boarder_image) }}"
                                                        class="profile-image mb-3"
                                                        alt="Employee Image">
                                                    @else
                                                        <img border="1"
                                                            id="blah"
                                                            src="{{ asset('assets/images/company/no_image.jpg') }}"
                                                            class="profile-image mb-3">
                                                    @endif

                                                    <input type="file"
                                                        class="form-control w-auto"
                                                        name="boarder_image"
                                                        id="aboutImage"
                                                        onchange="imageShow(this, this.id);"
                                                        >

                                                    <input type="hidden" name="old_image" value="{{ $data->boarder_image }}">
                                                    @error('boarder_image')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-success profile_photo save_button">
                                Update Photo
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function(){
        $('.dateInput').datepicker({
            format: 'yyyy-mm-dd',  // format compatible with Laravel date column
            autoclose: true,       // close picker after selecting a date
            todayHighlight: true,  // highlight today
            clearBtn: true,        // optional clear button
            orientation: 'bottom'  // show below the input
        });
    });
</script>
@endpush
