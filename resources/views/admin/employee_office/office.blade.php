@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">
        {{ isset($data->exists) ? 'Edit Employee' : 'Add Employee' }}
    </h1>
</div>
<div class="container">
    <div class="card shadow">
        <div class="card-body">
            <!-- Nav Tabs -->
            @include('admin.employee.nav-tab')
            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Tab Content -->
            <div class="tab-content" id="employeeTabContent">

                <div class="tab-pane fade show active"
                    id="official"
                    role="tabpanel">
                    <form action="{{ route('admin.employee.office.update',$data->id) }}" method="POST">
                        @csrf
                        @if(isset($data))
                            @method('PUT')
                        @endif

                        <div class="accordion" id="employeeAccordion">

                            {{-- Personal Information --}}
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#personalInfo"
                                            aria-expanded="true">
                                        Official Information
                                    </button>
                                </h2>

                                <div id="personalInfo"
                                    class="accordion-collapse collapse show"
                                    data-bs-parent="#employeeAccordion">

                                    <div class="accordion-body">

                                        <div class="row g-3">

                                            <div class="col-md-12">
                                                <div class="form-check custom-check">
                                                    <!-- Hidden field ensures '0' is sent if checkbox is unchecked -->
                                                    <input type="hidden" name="system_user" value="0">

                                                    <input class="form-check-input"
                                                        type="checkbox"
                                                        id="system_user"
                                                        name="system_user"
                                                        value="1"
                                                        {{ old('system_user', $data->system_user ?? 0) ? 'checked' : '' }}>

                                                    <label class="form-check-label ms-2 form-label mt-1" for="system_user">
                                                        Enable System User
                                                    </label>
                                                </div>

                                                @error('system_user')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            {{-- Designation --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Designation</label>
                                                <select name="designation"
                                                    class="form-control @error('designation') is-invalid @enderror">
                                                    <option value="">-- Select Designation--</option>
                                                        @foreach($designations as $value)
                                                            <option value="{{ $value->element_code }}" 
                                                                {{ old('designation', $data->designation ?? '') == $value->element_code ? 'selected' : '' }}>
                                                                {{ $value->element }}
                                                            </option>
                                                        @endforeach
                                                </select>

                                                @error('designation')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            {{-- First Joining date --}}
                                            <div class="col-md-6">
                                                <label class="form-label">First Joining Date</label>
                                                <input type="text"
                                                    name="first_joining_date"
                                                     value="{{ old('first_joining_date', $data->first_joining_date ?? '') }}"
                                                    placeholder="yyyy-mm-dd"
                                                    class="form-control dateInput @error('first_joining_date') is-invalid @enderror">

                                                @error('first_joining_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-success save_button">
                            Update Office Info
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
