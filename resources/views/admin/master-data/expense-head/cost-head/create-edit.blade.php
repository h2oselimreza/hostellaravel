@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Expense Category</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/master-data/expense/cost-head') }}">Home</a></li>
        <li><a href="#">/ Master Data</a> / </li>
        <li><a href="/admin/master-data/expense/cost-head">Expense Head</a></li>
        <li><a href="/admin/master-data/expense/cost-head/create">/ Expense Category</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="card from_card">
        <div class="card-header">   
            {{ isset($data->exists) ? 'Update' : 'Create' }}
        </div>

        <div class="card-body" x-data="vehicleForm()">
            <form 
                action="{{ isset($data->exists) ? route('admin.module.master-data.expense-head.update', $data->cost_head_code) : route('admin.module.master-data.expense-head.store') }}"
                method="POST"
                @submit.prevent="submitForm($event)" 
            >
                @csrf
                @if(isset($data->exists)) @method('PUT') @endif

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Parent Category : <span>*</span></label>
                        <select class="form-select" name="cost_category" 
                            x-model="formData.cost_category"
                            @change="validateField('cost_category')"
                            :class="errors.cost_category ? 'is-invalid' : ''">
                            <option value="1">--- Parent ---</option>
                            @if ($categories)
                                @foreach ($categories as $value)
                                    <option value="{{ $value->category_code }}">{{ $value->category_name }}</option>
                                @endforeach
                            @endif
                        </select>

                        <template x-if="errors.cost_category">
                            <div class="text-danger small" x-text="errors.cost_category"></div>
                        </template>

                        @error('cost_category')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Expense Head : <span>*</span></label>
                        <input type="text" name="cost_head" class="form-control"
                            x-model="formData.cost_head"
                            @blur="validateField('cost_head')"
                            placeholder="Expense Head"
                            :class="errors.cost_head ? 'is-invalid' : ''">
                        
                        <template x-if="errors.cost_head">
                            <div class="text-danger small" x-text="errors.cost_head"></div>
                        </template>
                        
                        @error('cost_head')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit Name : <span>*</span></label>
                        <input type="text" name="unit_name" class="form-control"
                            x-model="formData.unit_name"
                            @blur="validateField('unit_name')"
                            placeholder="Unit name"
                            :class="errors.unit_name ? 'is-invalid' : ''">
                        
                        <template x-if="errors.unit_name">
                            <div class="text-danger small" x-text="errors.unit_name"></div>
                        </template>
                        
                        @error('unit_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit price : <span>*</span></label>
                        <input type="text" name="unit_price" class="form-control"
                            x-model="formData.unit_price"
                            @blur="validateField('unit_price')"
                            placeholder="Unit price"
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
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function vehicleForm() {
    return {
        formData: {
            cost_category: "{{ old('cost_category', $data->cost_category ?? '') }}",
            cost_head: "{{ old('cost_head', $data->cost_head ?? '') }}",
            unit_name: "{{ old('unit_name', $data->unit_name ?? '') }}",
            unit_price: "{{ old('unit_price', $data->unit_price ?? '') }}",
        },
        errors: {},
        isSubmitting: false,

        validateField(field) {
            if (field === 'cost_category') {
                this.errors.cost_category = this.formData.cost_category.trim() === '' ? 'Category name is required.' : '';
            }
            if (field === 'cost_head') {
                this.errors.cost_head = this.formData.cost_head.trim() === '' ? 'Expense head is required.' : '';
            }
            if (field === 'unit_name') {
                this.errors.unit_name = this.formData.unit_name.trim() === '' ? 'Unit name is required.' : '';
            }
            if (field === 'unit_price') {
                this.errors.unit_price = this.formData.unit_price.trim() === '' ? 'Unit price is required.' : '';
            }
        },

        submitForm(event) {
            // Validate all fields before submission
            this.validateField('cost_category');
            this.validateField('cost_head');
            this.validateField('unit_name');
            this.validateField('unit_price');

            // Check if there are any errors
            if (!this.errors.cost_category && !this.errors.cost_head && !this.errors.unit_name && !this.errors.unit_price) {
                this.isSubmitting = true;
                event.target.submit(); // Standard Laravel form submit
            }
        }
    }
}
</script>
@endsection