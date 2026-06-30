@php
    $subModulesArr = get_sub_modules('admin/employees');
@endphp

@if(empty($subModulesArr))
    <script>
        window.location.href = "{{ route('admin.dashboard') }}";
    </script>
@endif

<ul class="nav nav-tabs mb-4" id="employeeTab" role="tablist">

    {{-- Personal --}}
    @if(in_array('personal', $subModulesArr))
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ !isset($data->exists) ? 'nav_item' : '' }} {{ request()->routeIs('admin.employee.module.create', 'admin.employee.module.edit') ? 'active' : '' }}"
               href="{{ isset($data->exists) ? route('admin.employee.module.edit', $data->id) : route('admin.employee.module.create') }}"
               id="personal-tab">
                Personal
            </a>
        </li>
    @endif

    {{-- Official --}}
    @if(in_array('official', $subModulesArr))
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ !isset($data->exists) ? 'nav_item' : '' }} {{ request()->routeIs('admin.employee.office.edit') ? 'active' : '' }}"
               href="{{ isset($data->exists) ? route('admin.employee.office.edit', $data->id) : '#' }}">
                Official
            </a>
        </li>
    @endif

    {{-- Education --}}
    @if(in_array('education', $subModulesArr))
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ !isset($data->exists) ? 'nav_item' : '' }} {{ request()->routeIs('admin.employee.education.edit') ? 'active' : '' }}"
               href="{{ isset($data->exists) ? route('admin.employee.education.edit', $data->id) : '#' }}">
                Education
            </a>
        </li>
    @endif

    {{-- Working Experience --}}
    @if(in_array('workingExp', $subModulesArr))
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ !isset($data->exists) ? 'nav_item' : '' }} {{ request()->routeIs('admin.working.experience.edit') ? 'active' : '' }}"
               href="{{ isset($data->exists) ? route('admin.working.experience.edit', $data->id) : '#' }}">
                Working Experience
            </a>
        </li>
    @endif

    {{-- Photograph --}}
    @if(in_array('photograph', $subModulesArr))
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ !isset($data->exists) ? 'nav_item' : '' }} {{ request()->routeIs('admin.profile.photo.edit') ? 'active' : '' }}"
               href="{{ isset($data->exists) ? route('admin.profile.photo.edit', $data->id) : '#' }}">
                Photograph
            </a>
        </li>
    @endif

</ul>