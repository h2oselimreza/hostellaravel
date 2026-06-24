@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">
        {{ isset($data->exists) ? 'Edit Boarder' : 'Add Boarder' }}
    </h1>
</div>

@php
    $path = request()->path(); // returns 'admin/employee-office-info'
    $lastPart = collect(explode('/', $path))->last();
@endphp

<div class="container">
    <div class="card shadow">
        <div class="card-body">
            <!-- Nav Tabs -->
            @include('admin.boarder.add-boarder.tab')
            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Tab Content -->
            <div class="tab-content" id="employeeTabContent">

                <div class="tab-pane fade show active"
                    id="personal"
                    role="tabpanel">
                    @php
                        $isEdit = isset($data);
                    @endphp
                    <form action="{{ $isEdit 
                        ? route('admin.boarder-enrollment.new-boarder.personal.info.update', $data->boarder_id) 
                        : route('admin.boarder-enrollment.new-boarder.personal.info.store') }}"
                        method="POST">

                    @csrf

                    @if($isEdit)
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
                                        Personal Information
                                    </button>
                                </h2>

                                <div id="personalInfo"
                                    class="accordion-collapse collapse show"
                                    data-bs-parent="#employeeAccordion">

                                    <div class="accordion-body">

                                        <div class="row g-3">
                                            {{-- Full Name --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Full Name</label>
                                                <input type="text"
                                                    name="boarder_name"
                                                    value="{{ old('boarder_name', $data->boarder_name ?? '') }}"
                                                    placeholder="Enter full name"
                                                    class="form-control @error('boarder_name') is-invalid @enderror">

                                                @error('boarder_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- National ID --}}
                                            <div class="col-md-6">
                                                <label class="form-label">National ID</label>
                                                <input type="text"
                                                    name="national_id"
                                                    value="{{ old('national_id', $data->national_id ?? '') }}"
                                                    placeholder="Enter national ID number"
                                                    class="form-control @error('national_id') is-invalid @enderror">

                                                @error('national_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Gender --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Gender</label>
                                                <select name="gender"
                                                    class="form-select @error('gender') is-invalid @enderror">
                                                    <option value="">Select Gender</option>
                                                    <option value="male" {{ old('gender')=='male'?'selected':'' }}>Male</option>
                                                    <option value="female" {{ old('gender')=='female'?'selected':'' }}>Female</option>

                                                    <option value="male"
                                                        {{ old('gender', $data->gender ?? '') == 'male'?'selected':'' }}>
                                                        Male
                                                    </option>
                                                    <option value="female"
                                                        {{ old('gender', $data->gender ?? '')=='female'?'selected':'' }}>
                                                        Female
                                                    </option>
                                                </select>
                                                @error('gender')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Religion --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Religion</label>
                                                <select name="religion"
                                                    class="form-select @error('religion') is-invalid @enderror">
                                                    <option value="">Select Religion</option>
                                                    @foreach(['Islam','Hindu','Christian','Buddhist'] as $religion)
                                                        <option value="{{ $religion }}"
                                                            {{ old('religion', $data->religion ?? '')==$religion?'selected':'' }}>
                                                            {{ $religion }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @error('religion')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Nationality --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Nationality</label>
                                                <select name="nationality"
                                                        class="form-select @error('nationality') is-invalid @enderror">
                                                    <option value="">-- Select Nationality --</option>
                                                    <option value="Bangladeshi"
                                                        {{ old('nationality', $data->nationality ?? '')=='Bangladeshi'?'selected':'' }}>
                                                        Bangladeshi
                                                    </option>
                                                    <option value="Other"
                                                        {{ old('nationality', $data->nationality ?? '')=='Other'?'selected':'' }}>
                                                        Other
                                                    </option>
                                                </select>
                                                @error('nationality')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Date of Birth --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Date of Birth</label>
                                                <input type="text"
                                                    name="dob"
                                                    value="{{ old('dob', $data->dob ?? '') }}"
                                                    placeholder="yyyy-mm-dd"
                                                    class="form-control dateInput @error('dob') is-invalid @enderror">

                                                @error('dob')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Blood Group --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Blood Group</label>
                                                <select name="blood_group"
                                                        class="form-select @error('blood_group') is-invalid @enderror">
                                                    <option value="">-- Select Group --</option>
                                                    @foreach(['O+','O-','A+','A-','B+','B-','AB+','AB-'] as $bg)
                                                        <option value="{{ $bg }}"
                                                            {{ old('blood_group', $data->blood_group ?? '')==$bg?'selected':'' }}>
                                                            {{ $bg }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @error('blood_group')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Marital Status --}}
                                            <div class="col-md-3">
                                                <label class="form-label">Marital Status</label>
                                                <select name="marital_status"
                                                        class="form-select @error('marital_status') is-invalid @enderror">
                                                    <option value="">-- Select Status --</option>
                                                    <option value="Single"
                                                        {{ old('marital_status', $data->marital_status ?? '')=='Single'?'selected':'' }}>
                                                        Single
                                                    </option>
                                                    <option value="Married"
                                                        {{ old('marital_status', $data->marital_status ?? '')=='Married'?'selected':'' }}>
                                                        Married
                                                    </option>
                                                </select>

                                                @error('marital_status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Anniversary --}}
                                            <div class="col-md-3" id="anniversaryDateDiv">
                                                <label class="form-label">Anniversary Date</label>
                                                <input type="text"
                                                    name="anniversary"
                                                    value="{{ old('anniversary', $data->anniversary ?? '') }}"
                                                    placeholder="yyyy-mm-dd"
                                                    class="form-control dateInput @error('anniversary') is-invalid @enderror">

                                                @error('anniversary')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Passport No --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Passport No</label>
                                                <input type="text"
                                                    name="passport_no"
                                                    value="{{ old('passport_no', $data->passport_no ?? '') }}"
                                                    placeholder="Enter passport number"
                                                    class="form-control @error('passport_no') is-invalid @enderror">

                                                @error('passport_no')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Passport Expiry Date --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Passport Expiry Date</label>
                                                <input type="text"
                                                    name="passport_expiry_date"
                                                    value="{{ old('passport_expiry_date', $data->passport_expiry_date ?? '') }}"
                                                    placeholder="yyyy-mm-dd"
                                                    class="form-control dateInput @error('passport_expiry_date') is-invalid @enderror">

                                                @error('passport_expiry_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Driving License No --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Driving License No</label>
                                                <input type="text"
                                                    name="driving_license_no"
                                                    value="{{ old('driving_license_no', $data->driving_license_no ?? '') }}"
                                                    placeholder="Enter driving license number"
                                                    class="form-control @error('driving_license_no') is-invalid @enderror">

                                                @error('driving_license_no')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Driving License Expiry Date --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Driving License Expiry Date</label>
                                                <input type="text"
                                                    name="driving_license_expiry_date"
                                                    value="{{ old('driving_license_expiry_date', $data->driving_license_expiry_date ?? '') }}"
                                                    placeholder="yyyy-mm-dd"
                                                    class="form-control dateInput @error('driving_license_expiry_date') is-invalid @enderror">

                                                @error('driving_license_expiry_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Personal Contact --}}
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#contactInfo">
                                        Personal Contact Information
                                    </button>
                                </h2>

                                <div id="contactInfo"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#employeeAccordion">

                                    <div class="accordion-body">

                                        <div class="row g-3">

                                            {{-- Primary Mobile --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Primary Mobile</label>
                                                <input type="text"
                                                    name="primary_mobile"
                                                    value="{{ old('primary_mobile', $data->primary_mobile ?? '') }}"
                                                    placeholder="Enter primary mobile number"
                                                    class="form-control @error('primary_mobile') is-invalid @enderror"
                                                    id="primaryMobile"
                                                    onchange="checkMobileNumber(this.value, 'primaryMobile')">

                                                @error('primary_mobile')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Secondary Mobile --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Secondary Mobile</label>
                                                <input type="text"
                                                    name="secendary_mobile"
                                                    value="{{ old('secendary_mobile', $data->secendary_mobile ?? '') }}"
                                                    placeholder="Enter secondary mobile number"
                                                    class="form-control @error('secendary_mobile') is-invalid @enderror"
                                                    id="secendaryMobile"
                                                    onchange="checkMobileNumber(this.value, 'secendaryMobile')">

                                                @error('secendary_mobile')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Land Phone --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Land Phone</label>
                                                <input type="text"
                                                    name="boarder_tnt_phone"
                                                    value="{{ old('boarder_tnt_phone', $data->boarder_tnt_phone ?? '') }}"
                                                    placeholder="Enter land phone number"
                                                    class="form-control @error('boarder_tnt_phone') is-invalid @enderror">

                                                @error('boarder_tnt_phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Email --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="email"
                                                    name="email"
                                                    value="{{ old('email', $data->email ?? '') }}"
                                                    placeholder="Enter email address"
                                                    class="form-control @error('email') is-invalid @enderror">

                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Present Address --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Present Address</label>
                                                <input type="text"
                                                    name="present_address"
                                                    value="{{ old('present_address', $data->present_address ?? '') }}"
                                                    placeholder="Enter present address"
                                                    class="form-control @error('present_address') is-invalid @enderror">

                                                @error('present_address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Permanent Address --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Permanent Address</label>
                                                <input type="text"
                                                    name="boarder_permanent_address"
                                                    value="{{ old('boarder_permanent_address', $data->boarder_permanent_address ?? '') }}"
                                                    placeholder="Enter permanent address"
                                                    class="form-control @error('boarder_permanent_address') is-invalid @enderror">

                                                @error('boarder_permanent_address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>


                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#parentInfo">
                                        Parent Information
                                    </button>
                                </h2>

                                <div id="parentInfo"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#employeeAccordion">

                                    <div class="accordion-body">

                                        <div class="row g-3">
                                            {{-- Father's Name --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Father's Name</label>
                                                <input type="text"
                                                    name="father_name"
                                                    value="{{ old('father_name', $data->father_name ?? '') }}"
                                                    placeholder="Enter father's full name"
                                                    class="form-control @error('father_name') is-invalid @enderror">

                                                @error('father_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Father's Occupation --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Father's Occupation</label>
                                                <select name="father_occupation"
                                                        class="form-select @error('father_occupation') is-invalid @enderror">
                                                    <option value="">-- Select Father's Occupation --</option>

                                                    @foreach([
                                                        'Banker','Business','Doctor','Engineer',
                                                        'Government Service','Private Service',
                                                        'Service Job','Journalist','Singer Artist',
                                                        'Teacher','Consultancy','Physician',
                                                        'Architect','Lawyer','N_A'
                                                    ] as $occupation)

                                                        <option value="{{ $occupation }}"
                                                            {{ old('father_occupation', $data->father_occupation ?? '') == $occupation ? 'selected' : '' }}>
                                                            {{ $occupation }}
                                                        </option>

                                                    @endforeach
                                                </select>

                                                @error('father_occupation')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Father's Office Address --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Father's Office Address</label>
                                                <input type="text"
                                                    name="father_office_address"
                                                    value="{{ old('father_office_address', $data->father_office_address ?? '') }}"
                                                    placeholder="Enter father's office address"
                                                    class="form-control @error('father_office_address') is-invalid @enderror">

                                                @error('father_office_address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Father's Contact --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Father's Contact</label>
                                                <input type="text"
                                                    name="father_contact"
                                                    value="{{ old('father_contact', $data->father_contact ?? '') }}"
                                                    placeholder="Enter father's mobile number"
                                                    class="form-control @error('father_contact') is-invalid @enderror">

                                                @error('father_contact')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Mother's Name --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Mother's Name</label>
                                                <input type="text"
                                                    name="mother_name"
                                                    value="{{ old('mother_name', $data->mother_name ?? '') }}"
                                                    placeholder="Enter mother's full name"
                                                    class="form-control @error('mother_name') is-invalid @enderror">

                                                @error('mother_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Mother's Occupation --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Mother's Occupation</label>
                                                <select name="mother_occupation"
                                                        class="form-select @error('mother_occupation') is-invalid @enderror">
                                                    <option value="">-- Select Mother's Occupation --</option>

                                                    @foreach([
                                                        'Banker','Business','Doctor','Engineer',
                                                        'Government Service','Private Service',
                                                        'Service Job','Journalist','Singer Artist',
                                                        'Teacher','Consultancy','Physician',
                                                        'Architect','Lawyer','Housewife','N_A'
                                                    ] as $occupation)

                                                        <option value="{{ $occupation }}"
                                                            {{ old('mother_occupation', $data->mother_occupation ?? '') == $occupation ? 'selected' : '' }}>
                                                            {{ $occupation }}
                                                        </option>

                                                    @endforeach
                                                </select>

                                                @error('mother_occupation')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Mother's Office Address --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Mother's Office Address</label>
                                                <input type="text"
                                                    name="mother_office_address"
                                                    value="{{ old('mother_office_address', $data->mother_office_address ?? '') }}"
                                                    placeholder="Enter mother's office address"
                                                    class="form-control @error('mother_office_address') is-invalid @enderror">

                                                @error('mother_office_address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Mother's Contact --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Mother's Contact</label>
                                                <input type="text"
                                                    name="mother_contact"
                                                    value="{{ old('mother_contact', $data->mother_contact ?? '') }}"
                                                    placeholder="Enter mother's mobile number"
                                                    class="form-control @error('mother_contact') is-invalid @enderror">

                                                @error('mother_contact')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#guardianInfo">
                                        Guardian Information
                                    </button>
                                </h2>

                                <div id="guardianInfo"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#employeeAccordion">

                                    <div class="accordion-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Guardian's Name</label>
                                                <input type="text"
                                                    name="guardian_name"
                                                    value="{{ old('guardian_name', $data->guardian_name ?? '') }}"
                                                    placeholder="Enter guardian's full name"
                                                    class="form-control @error('guardian_name') is-invalid @enderror">

                                                @error('guardian_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">Guardian's Relation</label>
                                                <input type="text"
                                                    name="guardian_relation"
                                                    value="{{ old('guardian_relation', $data->guardian_relation ?? '') }}"
                                                    placeholder="Enter relationship with guardian"
                                                    class="form-control @error('guardian_relation') is-invalid @enderror">

                                                @error('guardian_relation')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <label class="form-label">Guardian's House Address</label>
                                                <input type="text"
                                                    name="guardian_house_address"
                                                    value="{{ old('guardian_house_address', $data->guardian_house_address ?? '') }}"
                                                    placeholder="Enter guardian's house address"
                                                    class="form-control @error('guardian_house_address') is-invalid @enderror">

                                                @error('guardian_house_address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <label class="form-label">Guardian's Contact No</label>
                                                <input type="text"
                                                    name="guardian_contact"
                                                    id="guardian_contact"
                                                    value="{{ old('guardian_contact', $data->guardian_contact ?? '') }}"
                                                    placeholder="Enter guardian's mobile number"
                                                    class="form-control @error('guardian_contact') is-invalid @enderror"
                                                    onchange="checkMobileNumber(this.value, 'guardian_contact')">

                                                @error('guardian_contact')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item" id="spouseInformationDiv">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#spouseInfo">
                                        Spouse Information
                                    </button>
                                </h2>

                                <div id="spouseInfo"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#employeeAccordion">

                                    <div class="accordion-body">

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Spouse Name</label>
                                                <input type="text"
                                                    name="spouse_name"
                                                    value="{{ old('spouse_name', $data->spouse_name ?? '') }}"
                                                    placeholder="Enter spouse full name"
                                                    class="form-control @error('spouse_name') is-invalid @enderror">

                                                @error('spouse_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <label class="form-label">Spouse Occupation</label>
                                                <select class="form-control @error('spouse_occupation') is-invalid @enderror"
                                                        name="spouse_occupation">
                                                    <option value="">-- Select Spouse Occupation --</option>

                                                    @foreach([
                                                        'Banker','Business','Doctor','Engineer',
                                                        'Government Service','Private Service',
                                                        'Service Job','Journalist','Singer Artist',
                                                        'Teacher','Consultancy','Physician',
                                                        'Architect','Lawyer','Housewife','N_A'
                                                    ] as $occupation)

                                                    <option value="{{ $occupation }}"
                                                        {{ old('spouse_occupation', $data->spouse_occupation ?? '') == $occupation ? 'selected' : '' }}>
                                                        {{ $occupation }}
                                                    </option>

                                                    @endforeach
                                                </select>

                                                @error('spouse_occupation')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <label class="form-label">Spouse Office Address</label>
                                                <input type="text"
                                                    name="spouse_office_address"
                                                    value="{{ old('spouse_name', $data->spouse_office_address ?? '') }}"
                                                    placeholder="Enter spouse office address"
                                                    class="form-control @error('spouse_office_address') is-invalid @enderror">

                                                @error('spouse_office_address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <label class="form-label">Spouse Contact No</label>
                                                <input type="text"
                                                    name="spouse_contact"
                                                    id="spouse_contact"
                                                    value="{{ old('spouse_name', $data->spouse_contact ?? '') }}"
                                                    placeholder="Enter spouse mobile number"
                                                    class="form-control @error('spouse_contact') is-invalid @enderror"
                                                    onchange="checkMobileNumber(this.value, 'spouse_contact')">

                                                @error('spouse_contact')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#emergencyInfo">
                                        Emergency Contact Information
                                    </button>
                                </h2>

                                <div id="emergencyInfo"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#employeeAccordion">

                                    <div class="accordion-body">
                                        <div class="row g-3">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <label class="form-label">Emergency Contact Name</label>
                                                    <input type="text"
                                                        class="form-control @error('emer_contact_name') is-invalid @enderror"
                                                        name="emer_contact_name"
                                                        value="{{ old('emer_contact_name', $data->emer_contact_name ?? '') }}"
                                                        placeholder="Enter emergency contact name">

                                                    @error('emer_contact_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>	
                                            </div>

                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <label class="form-label">Emergency Contact Mobile No</label>
                                                    <input type="text"
                                                        class="form-control @error('emer_conatct_mobile') is-invalid @enderror"
                                                        name="emer_conatct_mobile"
                                                        id="emer_conatct_mobile"
                                                        value="{{ old('emer_conatct_mobile', $data->emer_conatct_mobile ?? '') }}"
                                                        placeholder="Enter emergency mobile number"
                                                        onchange="checkMobileNumber(this.value, 'emer_conatct_mobile')">

                                                    @error('emer_conatct_mobile')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>	
                                            </div>

                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <label class="form-label">Relationship</label>
                                                    <input type="text"
                                                        class="form-control @error('emer_contact_relation') is-invalid @enderror"
                                                        name="emer_contact_relation"
                                                        value="{{ old('emer_contact_relation', $data->emer_contact_relation ?? '') }}"
                                                        placeholder="Enter relationship with emergency contact">

                                                    @error('emer_contact_relation')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>	
                                            </div>

                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <label class="form-label">Address</label>
                                                    <input type="text"
                                                        class="form-control @error('emer_contact_address') is-invalid @enderror"
                                                        name="emer_contact_address"
                                                        value="{{ old('emer_contact_address', $data->emer_contact_address ?? '') }}"
                                                        placeholder="Enter emergency contact address">

                                                    @error('emer_contact_address')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>	
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <input class="hidden" name="seatCode" id="seatCode" value="{{ request()->route('seatCode') }}">
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-success save_button">
                                {{ $isEdit ? 'Update Boarder' : 'Save Boarder' }}
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
