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
            {{ isset($module->exists) ? 'Update Sub Module' : 'Create Sub Module' }}
        </div>

        <div class="card-body">

            <form action="{{ isset($module->exists) 
                        ? route('admin.sub-modules.update', $module->id) 
                        : route('admin.sub-modules.store') }}"
                method="POST">

                @csrf

                @if(isset($module->exists))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Module :
                        </label>

                        <select class="form-select"
                            name="module"
                            id="module"
                            data-selected="{{ $module->module ?? '' }}">

                            <option value="">Select Module</option>
                            @foreach ($modules as $value)
                                <option value="{{ $value->id }}"
                                    {{ old('module', $module->module ?? '') == $value->id ? 'selected' : '' }}>
                                    {{ $value->modules_name }}
                                </option>
                            @endforeach
                        </select>

                        @error('panel_type')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Panel type : <span>*</span>
                        </label>

                        <select class="form-select"
                            name="panel_type"
                            id="panel_type"
                            data-selected="{{ $module->panel_type ?? '' }}">

                            <option value=""
                                {{ old('panel_type', $module->panel_type ?? '') == '' ? 'selected' : '' }}>
                                Select Panel type
                            </option>

                            <option value="admin"
                                {{ old('panel_type', $module->panel_type ?? '') == 'admin' ? 'selected' : '' }}>
                                Admin
                            </option>

                            <option value="client"
                                {{ old('panel_type', $module->panel_type ?? '') == 'client' ? 'selected' : '' }}>
                                Client
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
                            Sub Module Name : <span>*</span>
                        </label>

                        <input type="text"
                            name="sub_module_name"
                            class="form-control"
                            placeholder="Sub Modules name"
                            value="{{ old('sub_module_name', $module->sub_module_name ?? '') }}">

                        @error('sub_module_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Sub Module Code : <span>*</span>
                        </label>

                        <input type="text"
                            name="sub_module_code"
                            class="form-control"
                            placeholder="Sub module code"
                            value="{{ old('sub_module_code', $module->sub_module_code ?? '') }}">

                        @error('sub_module_code')
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