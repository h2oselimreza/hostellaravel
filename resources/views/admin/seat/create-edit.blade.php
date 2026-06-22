@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Add Seat</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li><a href="#">/ Master Data</a> / </li>
        <li><a href="{{ url('admin/master-data/seat') }}">Seat</a></li>
        @if (isset($data->exists))
            <li><a href="{{ route('admin.seat.edit',$data->seat_code) }}">/ Edit Seat</a></li>
        @else
            <li><a href="{{ route('admin.seat.create') }}">/ Add Seat</a></li>
        @endif
    </ul>
</div>

<div class="main-content">
    
    @include('admin.seat.tab')

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
            <form id="addSeatForm"
                action="{{ isset($data->exists) ? route('admin.seat.update', $data->seat_code) : route('admin.seat.store') }}"
                method="POST"
                @submit.prevent="submitForm($event)">

                @csrf
                @if(isset($data->exists))
                    @method('PUT')
                @endif

                <div class="card-body bg-color">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Seat Title : <span>*</span></label>

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

                            <select class="form-select form-control"
                                name="floorCode"
                                id="floorCode"
                                x-model="formData.floorCode"
                                @blur="validateField('floorCode')"
                                :class="errors.floorCode ? 'is-invalid' : ''"
                                @change="loadRooms()">

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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Room : <span>*</span>
                            </label>

                            <select class="form-select form-control"
                                name="roomCode"
                                id="roomCode"
                                x-model="formData.roomCode"
                                @blur="validateField('roomCode')"
                                :class="errors.roomCode ? 'is-invalid' : ''"
                                >

                                <option value="">Select Room</option>

                                <template x-for="room in rooms" :key="room.room_code">
                                    <option :value="room.room_code" x-text="room.title"></option>
                                </template>
                            </select>

                            <template x-if="errors.roomCode">
                                <div class="text-danger small" x-text="errors.roomCode"></div>
                            </template>

                            @error('roomCode')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Seat Type : <span>*</span>
                            </label>

                            <select class="form-select form-control"
                                name="seatTypeCode"
                                id="seatTypeCode"
                                x-model="formData.seatTypeCode"
                                @blur="validateField('seatTypeCode')"
                                :class="errors.seatTypeCode ? 'is-invalid' : ''">

                                <option value="">Select Seat Type</option>

                                @foreach ($seatTypes as $seatType)
                                    <option value="{{ $seatType->seat_type_code }}">
                                        {{ $seatType->title }}
                                    </option>
                                @endforeach

                            </select>

                            <template x-if="errors.seatTypeCode">
                                <div class="text-danger small" x-text="errors.seatTypeCode"></div>
                            </template>

                            @error('seatTypeCode')
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

                    <a href="{{ route('admin.seat.index') }}"
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
            buildingCode: @js(old('buildingCode', $data->roomInfo?->floorInfo?->buildingInfo?->building_code ?? '')),
            floorCode: @js(old('floorCode', $data->roomInfo?->floorInfo?->floor_code ?? '')),
            roomCode: @js(old('roomCode', $data->roomInfo?->room_code ?? '')),
            seatTypeCode: @js(old('seatTypeCode', $data->seat_type ?? '')),
        },

        floors: [],
        rooms: [],
        errors: {},
        isSubmitting: false,

        async init() {
            if (this.formData.buildingCode) {
                await this.loadFloors(true);
            }
        },
        

        validateField(field) {

            if (field === 'title') {
                this.errors.title =
                    this.formData.title.trim() === ''
                        ? 'Seat title is required.'
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

            if (field === 'roomCode') {
                this.errors.roomCode =
                    this.formData.roomCode === ''
                        ? 'Room is required.'
                        : '';
            }

            if (field === 'seatTypeCode') {
                this.errors.seatTypeCode =
                    this.formData.seatTypeCode === ''
                        ? 'Seat type code is required.'
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

                            this.loadRooms(true);
                        }
                    });
                }
            });
        },

        loadRooms(isEdit = false) {

            let selectedRoom = this.formData.roomCode;

            this.formData.roomCode = '';
            this.rooms = [];

            if (!this.formData.floorCode) {
                return;
            }

            $.ajax({
                type: 'GET',
                url: "{{ route('admin.get.rooms') }}",
                data: {
                    floorCode: this.formData.floorCode
                },
                success: (res) => {
                    console.log(res);
                    this.rooms = res;

                    // 🔥 CRITICAL FIX: wait DOM render then set value
                    this.$nextTick(() => {

                        if (isEdit && selectedRoom) {
                            this.formData.roomCode = selectedRoom;
                        }
                    });
                }
            });
        },
        submitForm(event) {

            this.validateField('title');
            this.validateField('buildingCode');
            this.validateField('floorCode');
            this.validateField('roomCode');
            this.validateField('seatTypeCode');

            if (
                this.errors.title ||
                this.errors.buildingCode ||
                this.errors.floorCode ||
                this.errors.roomCode ||
                this.errors.seatTypeCode
            ) {
                return;
            }

            this.isSubmitting = true;

            submitAddSeat(this);
        }
    };
}

    function submitAddSeat(alpineObj) {

        $.ajax({
            type: 'POST',

            // Seat duplicate check route
            url: "{{ route('admin.seat.check.duplicate.seat') }}",

            data: {
                _token: "{{ csrf_token() }}",
                title: $('#title').val(),
                buildingCode: $('#buildingCode').val(),
                floorCode: $('#floorCode').val(),
                roomCode: $('#roomCode').val(),
                seatCode: "{{ $data->seat_code ?? '' }}"
            },

            success: function(response) {

                if (response.status == 1) {

                    document.getElementById('addSeatForm').submit();

                } else {

                    alpineObj.isSubmitting = false;

                    sweetAlert('Seat title is duplicate...!');
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