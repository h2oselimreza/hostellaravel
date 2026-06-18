@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">
        {{ isset($moduleGroup->exists) ? 'Edit Module Group' : 'Add Module Group' }}
    </h1>
</div>


<div class="main-content">
    <div class="card from_card">
        <div class="card-header">
            {{ isset($moduleGroup->exists) ? 'Update Module Group' : 'Create Module Group' }}
        </div>

        <div class="card-body">

            <form class="form-horizontal"
              action="{{ isset($moduleGroup->exists) 
                    ? route('admin.module-group.update', $moduleGroup->id) 
                    : route('admin.module-group.store') }}"
              method="POST">

                @csrf

                @if(isset($moduleGroup->exists))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Module Panel :
                        </label>

                        <select class="form-select"
                            name="panel_type"
                            id="panel_type"
                            data-selected="{{ $module->panel_type ?? '' }}">

                            <option value="">Select Panel type</option>

                            <option value="admin"
                                {{ old('panel_type', isset($module->panel_type)) == 'admin' ? 'selected' : '' }}>
                                Society Admin
                            </option>

                        </select>

                        @error('panel_type')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Module Group Name :
                        </label>

                        <input type="text"
                            name="module_group_name"
                            class="form-control"
                            placeholder="module group name"
                            value="{{ old('module_group_name', $module->module_group_name ?? '') }}">

                        @error('module_group_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Order -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Module Group Order :
                        </label>

                        <input type="text"
                            name="module_group_order"
                            class="form-control"
                            placeholder="Module group order"
                            value="{{ old('module_group_order', $module->module_group_order ?? '') }}">

                        @error('module_group_order')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="ps-0 card-footer bg-white d-flex gap-2">
                    <button class="btn btn-primary">
                        {{ isset($module->exists) ? 'Update' : 'Save' }}
                    </button> 

                    <a href="{{ route('admin.module-group.index') }}"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection
