@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">
        {{ isset($users->exists) ? 'Edit User' : 'Add Module' }}
    </h1>
</div>

<div class="main-content">
    <div class="card from_card">
        <div class="card-header">
            {{ isset($users->exists) ? 'Update User' : 'Create Module' }}
        </div>

        <div class="card-body">

            <form action="{{ isset($users->exists) 
                        ? route('admin.users.update', $users->id) 
                        : route('admin.modules.store') }}"
                method="POST">

                @csrf

                @if(isset($users->exists))
                    @method('PUT')
                @endif

                <!-- userGroup -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Module Panel :
                        </label>

                        <select class="form-select"
                            name="user_group"
                            id="user_group"
                            data-selected="{{ $module->user_group ?? '' }}" required>

                            <option value="">Select User Group</option>
                            @if (isset($userGroups))
                                @foreach ( $userGroups as  $userGroup)
                                    <option value="{{ $userGroup->id }}"
                                        {{ old('user_group', $users->user_group ?? '' ) == $userGroup->id ? 'selected' : '' }}>
                                        {{ $userGroup->group_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>

                        @error('user_group')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            User Status :
                        </label>

                        <select class="form-select"
                                name="is_active"
                                id="is_active"
                                required>
                            <option value="">Select User Status</option>
                            <option value="1" {{ old('is_active', $users->is_active ?? '') == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active', $users->is_active ?? '') == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>

                        @error('is_active')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="ps-0 card-footer bg-white d-flex gap-2">
                    <button class="btn btn-primary">
                        {{ isset($users->exists) ? 'Update' : 'Save' }}
                    </button> 

                    <a href="{{ route('admin.users.index') }}"
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