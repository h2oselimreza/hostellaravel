@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Expense Category</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/master-data/expense/cost-category') }}">Home</a></li>
        <li><a href="#">/ Master Data</a></li>
        <li><a href="/admin/master-data/expense/cost-category/create">/ Expense Category</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="card from_card">
        <div class="card-header">   
            {{ isset($data->exists) ? 'Update' : 'Create' }}
        </div>

        <div class="card-body" x-data="vehicleForm()">
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
            <form 
                action="{{ isset($data->exists) ? route('admin.module.master-data.expense-category.update', $data->category_code) : route('admin.module.master-data.expense-category.store') }}"
                method="POST"
                @submit.prevent="submitForm($event)" 
            >
                @csrf
                @if(isset($data->exists)) @method('PUT') @endif

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Parent Category : </label>
                        <select class="form-select" name="parent_category" 
                            x-model="formData.parent_category"
                            @change="validateField('parent_category')"
                            :class="errors.parent_category ? 'is-invalid' : ''">
                            <option value="1">--- Parent ---</option>
                            @if ($categories)
                                @foreach ($categories as $value)
                                    <option value="{{ $value->category_code }}">{{ $value->category_name }}</option>
                                @endforeach
                            @endif
                        </select>

                        <template x-if="errors.parent_category">
                            <div class="text-danger small" x-text="errors.parent_category"></div>
                        </template>

                        @error('parent_category')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Expense Category Name : <span>*</span></label>
                        <input type="text" name="category_name" class="form-control"
                            x-model="formData.category_name"
                            @blur="validateField('category_name')"
                            placeholder="Expense category name"
                            :class="errors.category_name ? 'is-invalid' : ''">
                        
                        <template x-if="errors.category_name">
                            <div class="text-danger small" x-text="errors.category_name"></div>
                        </template>
                        
                        @error('category_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="ps-0 card-footer bg-white d-flex gap-2">
                    <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                        <span x-show="!isSubmitting">{{ isset($data->exists) ? 'Update' : 'Save' }}</span>
                        <span x-show="isSubmitting">Processing...</span>
                    </button> 

                    <a href="{{ url('admin/master-data/cost-category') }}" class="btn btn-outline-secondary">
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
            category_name: "{{ old('category_name', $data->category_name ?? '') }}",
            parent_category: "{{ old('parent_category', $data->parent_category ?? '') }}",
        },
        errors: {},
        isSubmitting: false,

        validateField(field) {
            if (field === 'category_name') {
                this.errors.category_name = this.formData.category_name.trim() === '' ? 'Category name is required.' : '';
            }
        },

        submitForm(event) {
            // Validate all fields before submission
            this.validateField('category_name');

            // Check if there are any errors
            if (!this.errors.category_name) {
                this.isSubmitting = true;
                event.target.submit(); // Standard Laravel form submit
            }
        }
    }
}
</script>
@endsection