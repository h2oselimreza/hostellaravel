@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Income Category</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/master-data/income/cost-head') }}">Home</a></li>
        <li><a href="#">/ Master Data</a></li>
        <li><a href="/admin/master-data/income/cost-head/create">/ Income Category</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="card from_card">
        <div class="card-header">   
            {{ isset($data->exists) ? 'Update' : 'Create' }}
        </div>

        <div class="card-body" x-data="vehicleForm()">
            <form 
                action="{{ isset($data->exists) ? route('admin.module.master-data.income-head.update', $data->item_head_code) : route('admin.module.master-data.income-head.store') }}"
                method="POST"
                @submit.prevent="submitForm($event)" 
            >
                @csrf
                @if(isset($data->exists)) @method('PUT') @endif

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Parent Category : <span>*</span></label>
                        <select class="form-select" name="item_category" 
                            x-model="formData.item_category"
                            @change="validateField('item_category')"
                            :class="errors.item_category ? 'is-invalid' : ''">
                            <option value="1">--- Parent ---</option>
                            @if ($categories)
                                @foreach ($categories as $value)
                                    <option value="{{ $value->category_code }}">{{ $value->category_name }}</option>
                                @endforeach
                            @endif
                        </select>

                        <template x-if="errors.item_category">
                            <div class="text-danger small" x-text="errors.item_category"></div>
                        </template>

                        @error('item_category')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Income Head : <span>*</span></label>
                        <input type="text" name="item_head" class="form-control"
                            x-model="formData.item_head"
                            @blur="validateField('item_head')"
                            placeholder="Income Head"
                            :class="errors.item_head ? 'is-invalid' : ''">
                        
                        <template x-if="errors.item_head">
                            <div class="text-danger small" x-text="errors.item_head"></div>
                        </template>
                        
                        @error('item_head')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit Name : <span>*</span></label>
                        <input type="text" name="unit_name" class="form-control"
                            x-model="formData.unit_name"
                            @blur="validateField('unit_name')"
                            placeholder="Unit Name "
                            :class="errors.unit_name ? 'is-invalid' : ''">
                        
                        <template x-if="errors.unit_name">
                            <div class="text-danger small" x-text="errors.unit_name"></div>
                        </template>
                        
                        @error('unit_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit Price : </label>
                        <input type="text" name="unit_price" class="form-control"
                            x-model="formData.unit_price"
                            @blur="validateField('unit_price')"
                            placeholder="Unit Price"
                            :class="errors.unit_price ? 'is-invalid' : ''">
                        
                        <template x-if="errors.unit_price">
                            <div class="text-danger small" x-text="errors.unit_price"></div>
                        </template>
                        
                        @error('unit_price')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="ps-0 card-footer bg-white d-flex gap-2">
                    <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                        <span x-show="!isSubmitting">{{ isset($data->exists) ? 'Update' : 'Save' }}</span>
                        <span x-show="isSubmitting">Processing...</span>
                    </button> 

                    <a href="{{ url('admin/master-data/cost-head') }}" class="btn btn-outline-secondary">
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
            item_category: "{{ old('item_category', $data->item_category ?? '') }}",
            item_head: "{{ old('item_head', $data->item_head ?? '') }}",
            unit_name: "{{ old('unit_name', $data->unit_name ?? '') }}",
            unit_price: "{{ old('unit_price', $data->unit_price ?? '') }}"
        },
        errors: {},
        isSubmitting: false,

        validateField(field) {
            if (field === 'item_category') {
                this.errors.item_category = this.formData.item_category.trim() === '' ? 'Category name is required.' : '';
            }
            if (field === 'item_head') {
                this.errors.item_head = this.formData.item_head.trim() === '' ? 'Income head is required.' : '';
            }
            if (field === 'unit_name') {
                this.errors.unit_name = this.formData.unit_name.trim() === '' ? 'Unit name is required.' : '';
            }
        },

        submitForm(event) {
            // Validate all fields before submission
            this.validateField('item_category');
            this.validateField('item_head');
            this.validateField('unit_name');

            // Check if there are any errors
            if (!this.errors.item_category && !this.errors.item_head && !this.errors.unit_name) {
                this.isSubmitting = true;
                event.target.submit(); // Standard Laravel form submit
            }
        }
    }
}
</script>
@endsection