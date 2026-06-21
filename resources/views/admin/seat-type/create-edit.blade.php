@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Add Seat Type</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li><a href="#">/ Master Data</a> / </li>
        <li><a href="{{ url('admin/master-data/seat-type') }}">Seat Type</a></li>
        @if (isset($data->exists))
            <li><a href="{{ route('admin.seat.type.edit',$data->seat_type_code) }}">/ Edit Seat Type</a></li>
        @else
            <li><a href="{{ route('admin.seat.type.create') }}">/ Add Seat Type</a></li>
        @endif
    </ul>
</div>

<div class="main-content">

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
            {{ isset($data->exists) ? 'Update' : 'Seat Type' }}
        </div>

        <div class="card-body" x-data="vehicleForm()">
            <form 
                action="{{ isset($data->exists) ? route('admin.seat.type.update', $data->seat_type_code) : route('admin.seat.type.store') }}"
                method="POST"
                id="seatTypeForm"
                @submit.prevent="submitForm($event)" 
            >
                @csrf
                @if(isset($data->exists)) @method('PUT') @endif
                <div class="card-body bg-color">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Seat Type Title : <span>*</span></label>
                            <input type="text" name="title" class="form-control"
                                id='title'
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
                            <label class="form-label">Description :</label>
                            <textarea class="form-control" rows="5" name="description" id="description" x-model="formData.description"></textarea>
                            @error('description')
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

                    <a href="{{ route('admin.seat.type.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    function vehicleForm() {
        return {
            formData: {
                description: "{{ old('description', $data->description ?? '') }}",
                title: "{{ old('title', $data->title ?? '') }}",
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
            },

            submitForm(event) {

                this.validateField('title');

                if (this.errors.title) {
                    return;
                }

                this.isSubmitting = true;

                submitAddRoom(this);
            }
        }
    }

    function submitAddRoom(alpineObj) {

        $.ajax({
            type: 'POST',
            url: "{{ route('admin.seat.type.check.duplicate') }}",

            data: {
                _token: "{{ csrf_token() }}",
                title: $('#title').val(),
                seatTypeCode: "{{ $data->seat_type_code ?? '' }}"
            },

            success: function(response) {

                if (response.status == 1) {

                    document.getElementById('seatTypeForm').submit();

                } else {

                    alpineObj.isSubmitting = false;

                    sweetAlert('Seat Type title is duplicate!');
                }
            },

            error: function() {

                alpineObj.isSubmitting = false;

                sweetAlert('Something went wrong. Please try again.');
            }
        });
    }
</script>
@endpush