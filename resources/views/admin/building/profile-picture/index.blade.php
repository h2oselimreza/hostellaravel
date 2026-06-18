@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Update Information</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li><a href="#">/ Master Data</a> / </li>
        <li><a href="{{ url('admin/building') }}">Building</a></li>
        <li><a href="{{ route('admin.building.update',[$data->building_code]) }}">/ Update Building</a></li>
    </ul>
</div>

<div class="main-content">
    
    @include('admin.building.tab')

    <div class="card from_card">
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

        <div class="card-header">   
            {{ isset($data->exists) ? 'Update' : ' Basic Information' }}
        </div>

        <div class="card-body" x-data="vehicleForm()">
            <form 
                action="{{ isset($data->exists) ? route('admin.building.profile-picture.update', $data->building_code) : route('admin.building.store') }}"
                method="POST" enctype="multipart/form-data"
            >
                @csrf
                @if(isset($data->exists)) @method('PUT') @endif
                <div class="row g-3">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <div class="d-flex flex-column align-items-center text-center">

                            <label class="mb-2">Image (300px X 300px)</label>

                            {{-- Show Current Image --}}
                            @if($data->building_image)
                                <img
                                    src="{{ asset('storage/assets/admin/images/building/' . $data->building_image) }}"
                                    class="profile-image"
                                    alt="Building Image">
                            @else
                                <img
                                    src="{{ asset('assets/images/company/no_image.jpg') }}"
                                    class="profile-image"
                                    alt="No Image">
                            @endif

                            <input type="file"
                                class="form-control w-auto"
                                name="building_image"
                                id="aboutImage"
                                onchange="imageShow(this, this.id);"
                                >

                            <input type="hidden" name="old_image" value="{{ $data->building_image ?? "" }}">
                            @error('building_image')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4"></div>
                </div>
                <div class="ps-0 card-footer bg-white d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        Update
                    </button> 
                </div>
            </form>
        </div>
    </div>
</div>

@endsection