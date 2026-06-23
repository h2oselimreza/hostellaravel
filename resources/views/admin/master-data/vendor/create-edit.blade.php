@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Add Vendor</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li><a href="#">/ Master Data</a> / </li>
        <li><a href="{{ url('admin/master-data/vendor') }}">Vendor</a></li>
        @if (isset($data->exists))
            <li><a href="{{ route('admin.master.data.vendor.edit',$data->vendor_code) }}">/ Edit Vendor</a></li>
        @else
            <li><a href="{{ route('admin.master.data.vendor.create') }}">/ Add Vendor</a></li>
        @endif
    </ul>
</div>

<div class="main-content">
    
    @include('admin.master-data.vendor.tab')

    <div class="card from_card">
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

        <div class="card-body" x-data="vendorForm()" x-init="init()">
            <form id="addVendorForm"
                action="{{ isset($data->exists) ? route('admin.master.data.vendor.update', $data->vendor_code) : route('admin.master.data.vendor.store') }}"
                method="POST"
                @submit.prevent="submitForm()">

                @csrf
                @if(isset($data->exists))
                    @method('PUT')
                @endif

                <div class="accordion" id="vendorFormAccordion">
                    <!-- SECTION 1: Vendor Information -->
                    <div class="accordion-item mb-3 border-top">
                        <h2 class="accordion-header" id="headingVendorInfo">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVendorInfo" aria-expanded="true" aria-controls="collapseVendorInfo">
                                <i class="bi bi-shop me-2"></i> {{ isset($data->exists) ? 'Update Vendor Information' : 'Vendor Information' }}
                            </button>
                        </h2>
                        <div id="collapseVendorInfo" class="accordion-collapse collapse show" aria-labelledby="headingVendorInfo" data-bs-parent="#vendorFormAccordion">
                            <div class="accordion-body bg-color">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Vendor Title/Name : <span>*</span></label>
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
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Address : </label>
                                        <input type="text"
                                            name="address"
                                            id="address"
                                            class="form-control"
                                            x-model="formData.address"
                                            @blur="validateField('address')"
                                            placeholder="Address"
                                            :class="errors.address ? 'is-invalid' : ''">

                                        <template x-if="errors.address">
                                            <div class="text-danger small" x-text="errors.address"></div>
                                        </template>

                                        @error('address')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email :</label>
                                        <input type="text"
                                            name="vendor_email"
                                            id="vendor_email"
                                            class="form-control"
                                            x-model="formData.vendor_email"
                                            @blur="validateField('vendor_email')"
                                            placeholder="Email"
                                            :class="errors.vendor_email ? 'is-invalid' : ''">

                                        <template x-if="errors.email">
                                            <div class="text-danger small" x-text="errors.vendor_email"></div>
                                        </template>

                                        @error('vendor_email')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Website : </label>
                                        <input type="text"
                                            name="website"
                                            id="website"
                                            class="form-control"
                                            x-model="formData.website"
                                            @blur="validateField('website')"
                                            placeholder="Website"
                                            :class="errors.website ? 'is-invalid' : ''">

                                        <template x-if="errors.website">
                                            <div class="text-danger small" x-text="errors.website"></div>
                                        </template>

                                        @error('website')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mobile : <span>*</span></label>
                                        <input type="text"
                                            name="vendor_mobile"
                                            id="vendor_mobile"
                                            class="form-control"
                                            x-model="formData.vendor_mobile"
                                            @blur="validateField('vendor_mobile')"
                                            placeholder="Vendor mobile"
                                            :class="errors.vendor_mobile ? 'is-invalid' : ''"
                                            onchange="checkMobileNumber(this.value, this.id)">

                                        <template x-if="errors.vendor_mobile">
                                            <div class="text-danger small" x-text="errors.vendor_mobile"></div>
                                        </template>

                                        @error('vendor_mobile')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Land Phone : </label>
                                        <input type="text"
                                            name="vendor_land_phone"
                                            id="vendor_land_phone"
                                            class="form-control"
                                            x-model="formData.vendor_land_phone"
                                            @blur="validateField('vendor_land_phone')"
                                            placeholder="Land Phone"
                                            :class="errors.vendor_land_phone ? 'is-invalid' : ''">

                                        <template x-if="errors.vendor_land_phone">
                                            <div class="text-danger small" x-text="errors.vendor_land_phone"></div>
                                        </template>

                                        @error('vendor_land_phone')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Latitude :</label>
                                        <input type="text"
                                            name="latitude"
                                            id="latitude"
                                            class="form-control"
                                            x-model="formData.latitude"
                                            @blur="validateField('latitude')"
                                            placeholder="Latitude"
                                            :class="errors.latitude ? 'is-invalid' : ''">

                                        <template x-if="errors.latitude">
                                            <div class="text-danger small" x-text="errors.latitude"></div>
                                        </template>

                                        @error('latitude')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Longitude : </label>
                                        <input type="text"
                                            name="longitude"
                                            id="longitude"
                                            class="form-control"
                                            x-model="formData.longitude"
                                            @blur="validateField('longitude')"
                                            placeholder="Longitude"
                                            :class="errors.longitude ? 'is-invalid' : ''">

                                        <template x-if="errors.longitude">
                                            <div class="text-danger small" x-text="errors.longitude"></div>
                                        </template>

                                        @error('longitude')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Division : <span>*</span></label>
                                        <select class="form-select form-control"
                                            name="division"
                                            id="division"
                                            x-model="formData.division"
                                            @blur="validateField('division')"
                                            :class="errors.division ? 'is-invalid' : ''"
                                            @change="loadDistricts()">

                                            <option value="">Select Division</option>
                                            @foreach ($divisions as $division)
                                                <option value="{{ $division->id }}">
                                                    {{ $division->division_en_name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <template x-if="errors.division">
                                            <div class="text-danger small" x-text="errors.division"></div>
                                        </template>

                                        @error('division')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">District : <span>*</span></label>
                                        <select class="form-control"
                                            name="district"
                                            id="district"
                                            x-model="formData.district"
                                            @blur="validateField('district')"
                                            :class="errors.district ? 'is-invalid' : ''"
                                            @change="loadUpazilas()">

                                            <option value="">Select District</option>
                                            <template x-for="district in districts" :key="district.id">
                                                <option :value="district.id" x-text="district.district_en_name"></option>
                                            </template>
                                        </select>

                                        <template x-if="errors.district">
                                            <div class="text-danger small" x-text="errors.district"></div>
                                        </template>

                                        @error('district')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Upozilla : <span>*</span></label>
                                        <select class="form-control"
                                                name="upozilla"
                                                id="upozilla"
                                                x-model="formData.upozilla"
                                                @blur="validateField('upozilla')"
                                                :class="errors.upozilla ? 'is-invalid' : ''">

                                            <option value="">Select Upazila</option>
                                            <template x-for="upazila in upazilas" :key="upazila.id">
                                                <option :value="upazila.id" x-text="upazila.upozilla_en_name"></option>
                                            </template>
                                        </select>

                                        <template x-if="errors.upozilla">
                                            <div class="text-danger small" x-text="errors.upozilla"></div>
                                        </template>

                                        @error('upozilla')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"> Postal Code : </label>
                                        <input type="text"
                                            name="postal_code"
                                            id="postal_code"
                                            class="form-control"
                                            x-model="formData.postal_code"
                                            @blur="validateField('postal_code')"
                                            placeholder="Postal code"
                                            :class="errors.postal_code ? 'is-invalid' : ''">

                                        <template x-if="errors.postal_code">
                                            <div class="text-danger small" x-text="errors.postal_code"></div>
                                        </template>

                                        @error('postal_code')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: Contact Information -->
                    <div class="accordion-item mb-3 shadow-sm border rounded">
                        <h2 class="accordion-header" id="headingContactInfo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseContactInfo" aria-expanded="false" aria-controls="collapseContactInfo">
                                <i class="bi bi-person-lines-fill me-2"></i> {{ isset($data->exists) ? 'Update Contact Information' : 'Contact Information' }}
                            </button>
                        </h2>
                        <div id="collapseContactInfo" class="accordion-collapse collapse" aria-labelledby="headingContactInfo" data-bs-parent="#vendorFormAccordion">
                            <div class="accordion-body bg-color">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Primary Contact Person :</label>
                                        <input type="text"
                                            name="primary_contact_person"
                                            id="primary_contact_person"
                                            class="form-control"
                                            x-model="formData.primary_contact_person"
                                            @blur="validateField('primary_contact_person')"
                                            placeholder="Primary contact person"
                                            :class="errors.primary_contact_person ? 'is-invalid' : ''">

                                        <template x-if="errors.primary_contact_person">
                                            <div class="text-danger small" x-text="errors.primary_contact_person"></div>
                                        </template>

                                        @error('primary_contact_person')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Designation : </label>
                                        <input type="text"
                                            name="primary_contact_designation"
                                            id="primary_contact_designation"
                                            class="form-control"
                                            x-model="formData.primary_contact_designation"
                                            @blur="validateField('primary_contact_designation')"
                                            placeholder="Primary contact designation"
                                            :class="errors.primary_contact_designation ? 'is-invalid' : ''">

                                        <template x-if="errors.primary_contact_designation">
                                            <div class="text-danger small" x-text="errors.primary_contact_designation"></div>
                                        </template>

                                        @error('primary_contact_designation')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mobile : </label>
                                        <input type="text"
                                            name="primary_contact_mobile"
                                            id="primary_contact_mobile"
                                            class="form-control"
                                            x-model="formData.primary_contact_mobile"
                                            @blur="validateField('primary_contact_mobile')"
                                            placeholder="Primary contact mobile"
                                            :class="errors.primary_contact_mobile ? 'is-invalid' : ''"
                                            onchange="checkMobileNumber(this.value, this.id)">

                                        <template x-if="errors.primary_contact_mobile">
                                            <div class="text-danger small" x-text="errors.primary_contact_mobile"></div>
                                        </template>

                                        @error('primary_contact_mobile')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email : </label>
                                        <input type="text"
                                            name="primary_contact_email"
                                            id="primary_contact_email"
                                            class="form-control"
                                            x-model="formData.primary_contact_email"
                                            @blur="validateField('primary_contact_email')"
                                            placeholder="Primary contact email"
                                            :class="errors.primary_contact_email ? 'is-invalid' : ''"
                                            onchange="checkEmail(this.value, this.id)">

                                        <template x-if="errors.primary_contact_email">
                                            <div class="text-danger small" x-text="errors.primary_contact_email"></div>
                                        </template>

                                        @error('primary_contact_email')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Secondary Contact Person :</label>
                                        <input type="text"
                                            name="second_contact_person"
                                            id="second_contact_person"
                                            class="form-control"
                                            x-model="formData.second_contact_person"
                                            @blur="validateField('second_contact_person')"
                                            placeholder="Secondary contact person"
                                            :class="errors.second_contact_person ? 'is-invalid' : ''">

                                        <template x-if="errors.second_contact_person">
                                            <div class="text-danger small" x-text="errors.second_contact_person"></div>
                                        </template>

                                        @error('second_contact_person')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Designation : </label>
                                        <input type="text"
                                            name="second_contact_designation"
                                            id="second_contact_designation"
                                            class="form-control"
                                            x-model="formData.second_contact_designation"
                                            @blur="validateField('second_contact_designation')"
                                            placeholder="Designation"
                                            :class="errors.second_contact_designation ? 'is-invalid' : ''">

                                        <template x-if="errors.second_contact_designation">
                                            <div class="text-danger small" x-text="errors.second_contact_designation"></div>
                                        </template>

                                        @error('second_contact_designation')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mobile :</label>
                                        <input type="text"
                                            name="second_contact_mobile"
                                            id="second_contact_mobile"
                                            class="form-control"
                                            x-model="formData.second_contact_mobile"
                                            @blur="validateField('second_contact_mobile')"
                                            placeholder="Secondary contact mobile"
                                            :class="errors.second_contact_mobile ? 'is-invalid' : ''">

                                        <template x-if="errors.second_contact_mobile">
                                            <div class="text-danger small" x-text="errors.second_contact_mobile"></div>
                                        </template>

                                        @error('second_contact_mobile')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email : </label>
                                        <input type="text"
                                            name="second_contact_email"
                                            id="second_contact_email"
                                            class="form-control"
                                            x-model="formData.second_contact_email"
                                            @blur="validateField('second_contact_email')"
                                            placeholder="Secondary contact email"
                                            :class="errors.second_contact_email ? 'is-invalid' : ''">

                                        <template x-if="errors.second_contact_email">
                                            <div class="text-danger small" x-text="errors.second_contact_email"></div>
                                        </template>

                                        @error('second_contact_email')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
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

                    <a href="{{ route('admin.master.data.vendor.index') }}"
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
function vendorForm() {
    return {
        formData: {
            division: @js(old('division', $data->division ?? '')),
            district: @js(old('district', $data->district ?? '')),
            upozilla: @js(old('upozilla', $data->upozilla ?? '')),
            title: @js(old('title', $data->title ?? '')),
            address: @js(old('address', $data->address ?? '')),
            vendor_email: @js(old('vendor_email', $data->vendor_email ?? '')),
            website: @js(old('website', $data->website ?? '')),
            vendor_mobile: @js(old('vendor_mobile', $data->vendor_mobile ?? '')),
            vendor_land_phone: @js(old('vendor_land_phone', $data->vendor_land_phone ?? '')),
            latitude: @js(old('latitude', $data->latitude ?? '')),
            longitude: @js(old('longitude', $data->longitude ?? '')),
            postal_code: @js(old('postal_code', $data->postal_code ?? '')),
            primary_contact_person: @js(old('primary_contact_person', $data->primary_contact_person ?? '')),
            primary_contact_designation: @js(old('primary_contact_designation', $data->primary_contact_designation ?? '')),
            primary_contact_mobile: @js(old('primary_contact_mobile', $data->primary_contact_mobile ?? '')),
            primary_contact_email: @js(old('primary_contact_email', $data->primary_contact_email ?? '')),
            second_contact_person: @js(old('second_contact_person', $data->second_contact_person ?? '')),
            second_contact_designation: @js(old('second_contact_designation', $data->second_contact_designation ?? '')),
            second_contact_mobile: @js(old('second_contact_mobile', $data->second_contact_mobile ?? '')),
            second_contact_email: @js(old('second_contact_email', $data->second_contact_email ?? ''))
        },

        districts: [],
        upazilas: [],
        errors: {},
        isSubmitting: false,

        init() {
            if (this.formData.division) {
                // Pass true to indicate it is booting with initial values
                this.loadDistricts(true);
            }
        },

        loadDistricts(isEdit = false) {
            let selectedDistrict = this.formData.district;
            let selectedUpazila = this.formData.upozilla;

            if (!isEdit) {
                this.formData.district = '';
                this.formData.upozilla = '';
            }

            this.districts = [];
            this.upazilas = [];

            if (!this.formData.division) {
                return;
            }

            let url = "{{ route('admin.get.districts', ':division_id') }}";
            url = url.replace(':division_id', this.formData.division);

            $.ajax({
                type: 'GET',
                url: url,
                success: (response) => {
                    this.districts = response;
                    this.$nextTick(() => {
                        if (isEdit && selectedDistrict) {
                            this.formData.district = selectedDistrict;
                            // Trigger loading upazilas using the stored district value
                            this.loadUpazilas(true, selectedUpazila);
                        }
                    });
                },
                error: () => {
                    sweetAlert('Failed to load districts');
                }
            });
        },

        loadUpazilas(isEdit = false, selectedUpazila = null) {
            let upazilaId = selectedUpazila ?? this.formData.upozilla;

            if (!isEdit) {
                this.formData.upozilla = '';
            }
            this.upazilas = [];

            if (!this.formData.district) {
                return;
            }

            let url = "{{ route('admin.get.upazilas', ':district_id') }}";
            
            // FIXED: Changing this.formData.division to this.formData.district
            url = url.replace(':district_id', this.formData.district);

            $.ajax({
                type: 'GET',
                url: url,
                success: (response) => {
                    this.upazilas = response;
                    this.$nextTick(() => {
                        if (upazilaId) {
                            this.formData.upozilla = upazilaId;
                        }
                    });
                },
                error: () => {
                    sweetAlert('Failed to load upazilas');
                }
            });
        },

        validateField(field) {
            if (field === 'title') {
                this.errors.title = !this.formData.title || this.formData.title.trim() === '' ? 'Vendor name is required.' : '';
            }
            if (field === 'vendor_mobile') {
                // FIXED: Typo text fix
                this.errors.vendor_mobile = !this.formData.vendor_mobile || this.formData.vendor_mobile.trim() === '' ? 'Mobile number is required.' : '';
            }
            if (field === 'division') {
                this.errors.division = !this.formData.division ? 'Division is required.' : '';
            }
            if (field === 'district') {
                this.errors.district = !this.formData.district ? 'District is required.' : '';
            }
            if (field === 'upozilla') {
                this.errors.upozilla = !this.formData.upozilla ? 'Upazila is required.' : '';
            }
        },

        submitForm() {
            this.validateField('title');
            this.validateField('division');
            this.validateField('district');
            this.validateField('upozilla');

            if (this.errors.title || this.errors.division || this.errors.district || this.errors.upozilla) {
                return;
            }

            this.isSubmitting = true;
            document.getElementById('addVendorForm').submit();
        }
    };
}
</script>
@endpush