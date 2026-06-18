@extends('layouts.app')

@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">
        {{ isset($userGroup) && $userGroup->exists ? 'Edit Module Group' : 'Add Module Group' }}
    </h1>
</div>

<div class="main-content">
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
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-default">

                @php
                    // $groupName = '';
                    // $groupId = '';
                    // $moduleList = '';
                    // $subModuleList = '';
                    // $panelType = '';
                    // $subModules  = '';
                    if (isset($userGroupDetails) && $userGroupDetails != null ){
                        foreach ($userGroupDetails as $userGroupDetail) {
                            $groupName = $userGroupDetail['group_name'];
                            $groupId = $userGroupDetail['id'];
                            $moduleList = $userGroupDetail['modules'];
                            $subModuleList = $userGroupDetail['sub_modules'];
                        }
                    }

                    $userModule = explode(",", $moduleList);
                    $userSubModule = [];

                    if (!empty($subModuleList)) {
                        $userSubModule = explode(",", $subModuleList);
                    }
                @endphp

                <form action="{{ isset($userGroup) && $userGroup->exists 
                        ? route('admin.user-groups.update', $userGroup->id) 
                        : route('admin.user-groups.store') }}"
                method="POST" id="editUserGroupFrom">

                @csrf

                @if(isset($userGroup) && $userGroup->exists)
                    @method('PUT')
                @endif

                    <div class="row mb-3">
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <label class="form-label">Module Group Name</label>
                                <input type="text" class="form-control" value="{{ $groupName }}" disabled>
                                <input type="hidden" name="moduleGroupId" value="{{ $groupId }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <label class="form-label">Panel Type</label>
                                <input type="text" class="form-control" value="{{ ucfirst($panelType) }}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-xs-12">

                            <table class="table table-bordered custom-table">
                                <thead>
                                    <tr class="bg-primary">
                                        <th>SL</th>
                                        <th>Type</th>
                                        <th>Module Group</th>
                                        <th>Module</th>
                                        <th>Module Select</th>
                                        <th>Sub Module</th>
                                    </tr>
                                </thead>

                                @php
                                    $serial = 1;
                                    $moduleSerial = 1;
                                @endphp

                                @foreach ($moduleGroups as $moduleGroup)

                                    @php
                                        $rowspanValue = 0;
                                        $loopFlag = 1;
                                        $moduleGroupId = $moduleGroup['module_group_code'];
                                        $flag = 0;

                                        foreach ($modules as $module) {
                                            if ($moduleGroupId == $module['module_group']) {
                                                $rowspanValue++;
                                            }
                                        }
                                    @endphp

                                    @foreach ($modules as $module)

                                        @if ($moduleGroupId == $module['module_group'])

                                            @if ($loopFlag == 1)
                                                <tr>
                                                    <td class="td-center" rowspan="{{ $rowspanValue }}">{{ $serial }}</td>
                                                    <td class="td-center" rowspan="{{ $rowspanValue }}">{{ ucfirst($moduleGroup['panel_type']) }}</td>
                                                    <td rowspan="{{ $rowspanValue }}">{{ $moduleGroup['module_group_name'] }}</td>
                                                @php $loopFlag++; @endphp
                                            @endif

                                            <td>{{ $module['modules_name'] }}</td>

                                            <td class="td-center">
                                                <input type="checkbox"
                                                       onclick="moduleCheck({{ $moduleSerial }})"
                                                       id="moduleCheckBox{{ $moduleSerial }}"
                                                       name="moduleList[]"
                                                       value="{{ $module['id'] }}"
                                                       @if(in_array($module['id'], $userModule)) checked @endif>
                                            </td>

                                            <td style="padding-left:10px">

                                                @php $submoduleSerial = 1; @endphp

                                                @if ($subModules != '')
                                                    
                                                @foreach ($subModules as $subModule)

                                                    @if ($subModule['module'] == $module['id'])

                                                        @php
                                                            $subModuleCheckFlag = in_array($subModule['id'], $userSubModule) ? 'checked' : '';
                                                        @endphp

                                                        <input type="checkbox"
                                                               onclick="subModuleCheck({{ $moduleSerial }})"
                                                               id="subModuleCheckBox{{ $moduleSerial }}{{ $submoduleSerial }}"
                                                               name="subModuleList[]"
                                                               value="{{ $subModule['id'] }}"
                                                               {{ $subModuleCheckFlag }}>
                                                        <span class="p-l-10">{{ $subModule['sub_module_name'] }}</span><br>

                                                        @php $submoduleSerial++; @endphp

                                                    @endif

                                                @endforeach
                                                @endif

                                                <input type="hidden" id="subModuleCount{{ $moduleSerial }}" value="{{ $submoduleSerial }}">
                                            </td>

                                        </tr>

                                        @php
                                            $moduleSerial++;
                                            $flag = 1;
                                        @endphp

                                        @endif

                                    @endforeach

                                    @php $serial++; @endphp

                                @endforeach

                            </table>

                            <input type="hidden" name="moduleCount" id="moduleCount" value="{{ $moduleSerial }}">

                        </div>
                    </div>

                </form>

                <button type="submit" class="btn btn-primary save_button" onclick="editUserGroup()">
                    Update Info
                </button>

            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    $(document).ready(function () {
        // You can add JS here if needed
    });
</script>
<script>
    function subModuleCheck(moduleSerial) {
        var subModuleCount = $('#subModuleCount' + moduleSerial).val();
        var flag = 0;
        for (var i = 1; i < subModuleCount; i++) {
            if ($("#subModuleCheckBox" + moduleSerial + i).is(':checked')) {
                flag = 1;
            }
        }
        if (flag === 1) {
            $('#moduleCheckBox' + moduleSerial).prop('checked', true);
        } else {
            $('#moduleCheckBox' + moduleSerial).prop('checked', false);
        }
    }

    function moduleCheck(moduleSerial) {
        var subModuleCount = $('#subModuleCount' + moduleSerial).val();
        for (var i = 1; i < subModuleCount; i++) {
            $('#subModuleCheckBox' + moduleSerial + i).prop('checked', false);
        }
    }

    function editUserGroup() {
        
        var moduleFlag = 0;
        var moduleCount = $('#moduleCount').val();
        for (var i = 1; i < moduleCount; i++) {
            if ($("#moduleCheckBox" + i).is(':checked')) {
                moduleFlag = 1;
                var subModuleFlag = 0;
                var subModuleCount = $('#subModuleCount' + i).val();
                if (subModuleCount === '1') {
                    subModuleFlag = 1;
                }
                for (var j = 1; j < subModuleCount; j++) {
                    if ($("#subModuleCheckBox" + i + j).is(':checked')) {
                        subModuleFlag = 1;
                    }
                }
                if (subModuleFlag === 0) {
                    sweetAlert('Please select at least one sub module of your selected module...!');
                    return false;
                }
            }
        }

        if (moduleFlag === 0) {
            sweetAlert('Please select at least one module...!');
            return false;
        }

        $('#editUserGroupFrom').submit();
    }
</script>
@endpush
