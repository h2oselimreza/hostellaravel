@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Add Room</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li><a href="#">/ Master Data</a> / </li>
        <li><a href="{{ url('admin/master-data/room') }}">Room</a></li>
        @if (isset($data->exists))
            <li><a href="{{ route('admin.room.edit',$data->room_code) }}">/ Edit Room</a></li>
        @else
            <li><a href="{{ route('admin.room.create') }}">/ Create Room</a></li>
        @endif
    </ul>
</div>

<div class="main-content">
    
    @include('admin.room.tab')

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

        <div class="card-body" x-data="vehicleForm()" x-init="init()">
            <form id="addRoomForm"
                action="{{ isset($data->exists) ? route('admin.room.update', $data->room_code) : route('admin.room.store') }}"
                method="POST"
                @submit.prevent="submitForm($event)">

                @csrf
                @if(isset($data->exists))
                    @method('PUT')
                @endif

                <div class="card-body bg-color">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Room Title : <span>*</span></label>

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
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description :</label>
                            <textarea class="form-control" rows="5" name="description" id="address" x-model="formData.description"></textarea>
                            @error('description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Building : <span>*</span>
                            </label>

                            <select class="form-select form-control"
                                name="buildingCode"
                                id="buildingCode"
                                x-model="formData.buildingCode"
                                @blur="validateField('buildingCode')"
                                :class="errors.buildingCode ? 'is-invalid' : ''"
                                @change="loadFloors()">

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

                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Floor : <span>*</span>
                            </label>

                            {{-- <select class="form-select form-control"
                                name="floorCode"
                                id="floorCode"
                                x-model="formData.floorCode"
                                @blur="validateField('floorCode')"
                                :class="errors.floorCode ? 'is-invalid' : ''">

                                <option value="">Select Floor</option>

                                @foreach ($buildings as $building)
                                    <option value="{{ $building->building_code }}">
                                        {{ $building->title }}
                                    </option>
                                @endforeach
                            </select> --}}

                            <select class="form-select form-control"
                                name="floorCode"
                                id="floorCode"
                                x-model="formData.floorCode"
                                @blur="validateField('floorCode')"
                                :class="errors.floorCode ? 'is-invalid' : ''">

                                <option value="">Select Floor</option>

                                <template x-for="floor in floors" :key="floor.floor_code">
                                    <option :value="floor.floor_code" x-text="floor.title"></option>
                                </template>

                            </select>

                            <template x-if="errors.floorCode">
                                <div class="text-danger small" x-text="errors.floorCode"></div>
                            </template>

                            @error('floorCode')
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

                    <a href="{{ route('admin.room.index') }}"
                        class="btn btn-outline-secondary">
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
            title: @js(old('title', $data->title ?? '')),
            description: @js(old('description', $data->description ?? '')),
            buildingCode: @js(old('buildingCode', $data->floorInfo->buildingInfo->building_code ?? '')),
            floorCode: @js(old('floorCode', $data->floor ?? ''))
        },

        floors: [],
        errors: {},
        isSubmitting: false,

        init() {
            if (this.formData.buildingCode) {
                this.loadFloors(true);
            }
        },

        validateField(field) {

            if (field === 'title') {
                this.errors.title =
                    this.formData.title.trim() === ''
                        ? 'Room title is required.'
                        : '';
            }

            if (field === 'buildingCode') {
                this.errors.buildingCode =
                    this.formData.buildingCode === ''
                        ? 'Building is required.'
                        : '';
            }

            if (field === 'floorCode') {
                this.errors.floorCode =
                    this.formData.floorCode === ''
                        ? 'Floor is required.'
                        : '';
            }
        },
        loadFloors(isEdit = false) {

            let selectedFloor = this.formData.floorCode;

            this.formData.floorCode = '';
            this.floors = [];

            if (!this.formData.buildingCode) {
                return;
            }

            $.ajax({
                type: 'GET',
                url: "{{ route('admin.get.floors') }}",
                data: {
                    buildingCode: this.formData.buildingCode
                },
                success: (res) => {

                    this.floors = res;

                    // 🔥 CRITICAL FIX: wait DOM render then set value
                    this.$nextTick(() => {

                        if (isEdit && selectedFloor) {
                            this.formData.floorCode = selectedFloor;
                        }
                    });
                }
            });
        },
        submitForm(event) {

            this.validateField('title');
            this.validateField('buildingCode');
            this.validateField('floorCode');

            if (
                this.errors.title ||
                this.errors.buildingCode ||
                this.errors.floorCode
            ) {
                return;
            }

            this.isSubmitting = true;

            submitAddRoom(this);
        }
    };
}

    function submitAddRoom(alpineObj) {

        $.ajax({
            type: 'POST',

            // Room duplicate check route
            url: "{{ route('admin.room.check-duplicate-room') }}",

            data: {
                _token: "{{ csrf_token() }}",
                title: $('#title').val(),
                buildingCode: $('#buildingCode').val(),
                floorCode: $('#floorCode').val(),
                roomCode: "{{ $data->room_code ?? '' }}"
            },

            success: function(response) {

                if (response.status == 1) {

                    document.getElementById('addRoomForm').submit();

                } else {

                    alpineObj.isSubmitting = false;

                    sweetAlert('Room title is duplicate...!');
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