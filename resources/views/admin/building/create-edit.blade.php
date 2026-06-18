@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Add Building</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li><a href="#">/ Master Data</a> / </li>
        <li><a href="{{ url('admin/master-data/building') }}">Building</a></li>
        @if (isset($data->exists))
            <li><a href="{{ route('admin.building.edit',$data->building_code) }}">/ Edit Building</a></li>
        @else
            <li><a href="{{ route('admin.building.create') }}">/ Create Building</a></li>
        @endif
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
                action="{{ isset($data->exists) ? route('admin.building.update', $data->building_code) : route('admin.building.store') }}"
                method="POST"
                @submit.prevent="submitForm($event)" 
            >
                @csrf
                @if(isset($data->exists)) @method('PUT') @endif
                <div class="card-body bg-color">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Title : <span>*</span></label>
                            <input type="text" name="title" class="form-control"
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
                            <label class="form-label">Address :</label>
                            <textarea class="form-control" rows="5" name="address" id="address" x-model="formData.address" value="{{ old('address', $data->address ?? '') }}"></textarea>
                            @error('address')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="ps-0 card-footer bg-white d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                        <span x-show="!isSubmitting">{{ isset($data->exists) ? 'Update' : 'Save' }}</span>
                        <span x-show="isSubmitting">Processing...</span>
                    </button> 

                    <a href="{{ route('admin.building.index') }}" class="btn btn-outline-secondary">
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
            address: "{{ old('address', $data->address ?? '') }}",
            title: "{{ old('title', $data->title ?? '') }}",
        },
        errors: {},
        isSubmitting: false,

        validateField(field) {
            if (field === 'title') {
                this.errors.title = this.formData.title === '' ? 'Title is required.' : '';
            }
        },

        submitForm(event) {
            // Validate all fields before submission
            this.validateField('title');

            // Check if there are any errors
            if (!this.errors.title) {
                this.isSubmitting = true;
                event.target.submit(); // Standard Laravel form submit
            }
        }
    }
}
</script>
@endsection