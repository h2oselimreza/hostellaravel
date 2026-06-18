@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">
        {{ isset($module->exists) ? 'Edit Module' : 'Add Module' }}
    </h1>
</div>

<div class="main-content">
    <div class="card from_card">
        <div class="card-header">
            {{ isset($module->exists) ? 'Update Module' : 'Create Module' }}
        </div>

        <div class="card-body">

            <form action="{{ isset($module->exists) 
                        ? route('admin.modules.update', $module->id) 
                        : route('admin.modules.store') }}"
                method="POST">

                @csrf

                @if(isset($module->exists))
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

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Module group :
                        </label>

                        <select class="form-select"
                            name="module_group"
                            id="module_group"
                            data-selected="{{ $module->module_group ?? '' }}">
                            <option value="">Select module group</option>
                        </select>

                        @error('module_group')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Module Name :
                        </label>

                        <input type="text"
                            name="modules_name"
                            class="form-control"
                            placeholder="Modules name"
                            value="{{ old('modules_name', $module->modules_name ?? '') }}">

                        @error('modules_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Module URL :
                        </label>

                        <input type="text"
                            name="module_url"
                            class="form-control"
                            placeholder="Modules url"
                            value="{{ old('module_url', $module->module_url ?? '') }}">

                        @error('module_url')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Order -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Module Order :
                        </label>

                        <input type="text"
                            name="module_order"
                            class="form-control"
                            placeholder="Modules order"
                            value="{{ old('module_order', $module->module_order ?? '') }}">

                        @error('module_order')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="ps-0 card-footer bg-white d-flex gap-2">
                    <button class="btn btn-primary">
                        {{ isset($module->exists) ? 'Update' : 'Save' }}
                    </button> 

                    <a href="{{ route('admin.modules.index') }}"
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
    $(document).ready(function () {

    // change event
    $('#panel_type').on('change', function () {
        let panelType = $(this).val();
        module_group_record(panelType);
    });

    function module_group_record(panelType, selectedGroup = null) {

        let groupSelect = $('#module_group');

        groupSelect.html('<option value="">Loading...</option>');

        if (!panelType) {
            groupSelect.html('<option value="">Select module group</option>');
            return;
        }

        $.ajax({
            url: `/admin/module-groups/${panelType}`,
            type: 'GET',
            success: function (data) {

                groupSelect.html('<option value="">Select module group</option>');

                $.each(data, function (index, item) {
                    let selected = selectedGroup === item.module_group_code ? 'selected' : '';
                    groupSelect.append(
                        `<option value="${item.module_group_code}" ${selected}>
                            ${item.module_group_name}
                        </option>`
                    );
                });
            },
            error: function (xhr) {
                console.error(xhr);
                groupSelect.html('<option value="">Failed to load</option>');
            }
        });
    }

    /* ===============================
       EDIT MODE SUPPORT
       =============================== */

    // these values should come from backend in edit page
    let editPanelType = $('#panel_type').data('selected'); 
    let editModuleGroup = $('#module_group').data('selected');

    if (editPanelType) {
        $('#panel_type').val(editPanelType);
        module_group_record(editPanelType, editModuleGroup);
    }

});
</script>
@endpush