@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Add Floor</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li><a href="#">/ Master Data</a> / </li>
        <li><a href="{{ url('admin/master-data/floor') }}">Floor</a></li>
        @if (isset($data->exists))
            <li><a href="{{ route('admin.floor.edit',$data->floor_code) }}">/ Edit Floor</a></li>
        @else
            <li><a href="{{ route('admin.floor.create') }}">/ Create Floor</a></li>
        @endif
    </ul>
</div>

<div class="main-content">
    
    @include('admin.floor.tab')

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
            <form id="addFloorForm"
                action="{{ isset($data->exists) ? route('admin.floor.update', $data->floor_code) : route('admin.floor.store') }}"
                method="POST"
                @submit.prevent="submitForm($event)">

                @csrf
                @if(isset($data->exists))
                    @method('PUT')
                @endif

                <div class="card-body bg-color">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Title : <span>*</span></label>

                            <input type="text"
                                name="title"
                                id="title"
                                class="form-control"
                                x-model="formData.title"
                                @blur="validateField('title')"
                                placeholder="Title"
                                :class="errors.title ? 'is-invalid' : ''">

                            <template x-if="errors.title">
                                <div class="text-danger small" x-text="errors.title"></div>
                            </template>

                            @error('title')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">
                                Building :
                            </label>

                            <select class="form-select form-control"
                                name="buildingCode"
                                id="buildingCode"
                                x-model="formData.buildingCode"
                                @blur="validateField('buildingCode')"
                                :class="errors.buildingCode ? 'is-invalid' : ''">

                                <option value="">Select Building</option>

                                @foreach ($buildings as $building)
                                    <option value="{{ $building->building_code }}">
                                        {{ $building->title }}
                                    </option>
                                @endforeach
                            </select>

                            <template x-if="errors.buildingCode">
                                <div class="text-danger small" x-text="errors.buildingCode"></div>
                            </template>

                            @error('buildingCode')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="ps-0 card-footer bg-white d-flex gap-2 mt-3">
                    <button type="submit"
                        class="btn btn-primary"
                        :disabled="isSubmitting">

                        <span x-show="!isSubmitting">
                            {{ isset($data->exists) ? 'Update' : 'Save' }}
                        </span>

                        <span x-show="isSubmitting">
                            Processing...
                        </span>
                    </button>

                    <a href="{{ route('admin.floor.index') }}"
                        class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function vehicleForm() {
    return {

        formData: {
            title: @js(old('title', $data->title ?? '')),
            buildingCode: @js(old('buildingCode', $data->building ?? ''))
        },

        errors: {},
        isSubmitting: false,

        validateField(field) {

            if (field === 'title') {
                this.errors.title =
                    this.formData.title.trim() === ''
                    ? 'Title is required.'
                    : '';
            }

            if (field === 'buildingCode') {
                this.errors.buildingCode =
                    this.formData.buildingCode === ''
                    ? 'Building is required.'
                    : '';
            }
        },

        submitForm(event) {

            this.validateField('title');
            this.validateField('buildingCode');

            if (this.errors.title || this.errors.buildingCode) {
                return;
            }

            this.isSubmitting = true;

            submitAddFloor(this);
        }
    }
}

function submitAddFloor(alpineObj) {

    $.ajax({
        type: 'POST',
        url: "{{ route('admin.floor.check-duplicate-floor') }}",

        data: {
            _token: "{{ csrf_token() }}",
            title: $('#title').val(),
            buildingCode: $('#buildingCode').val(),
            floorCode: "{{ $data->floor_code ?? '' }}"
        },

        success: function(response) {

            if (response.status == 1) {

                document.getElementById('addFloorForm').submit();

            } else {

                alpineObj.isSubmitting = false;
                sweetAlert('Floor title is duplicate...!');
            }
        },

        error: function() {

            alpineObj.isSubmitting = false;
            sweetAlert('Something went wrong. Please try again.');
        }
    });
}
</script>
@endsection