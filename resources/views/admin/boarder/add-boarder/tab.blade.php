@php
    $segments = request()->segments();
    $lastSegment = request()->segment(count($segments));
    $secondLastSegment = request()->segment(count($segments) - 1);
    $thirdLastSegment = request()->segment(count($segments) - 2);

    $subModulesArr = get_sub_modules('admin/boarder-enrollment/boarder');

    $isEdit = isset($data->exists);

    $activeTab = '';

    if ($thirdLastSegment == 'personal-info' || $thirdLastSegment == 'create') {
        $activeTab = 'personal';
    } elseif ($secondLastSegment == 'invoice-info') {
        $activeTab = 'invoice';
    } elseif ($secondLastSegment == 'boarder-education-info') {
        $activeTab = 'education';
    } elseif ($secondLastSegment == 'working-experience-info') {
        $activeTab = 'workingExp';
    } elseif ($secondLastSegment == 'profile-photo-info') {
        $activeTab = 'photograph';
    } elseif ($secondLastSegment == 'boarder-attachment') {
        $activeTab = 'attachment';
    }
@endphp

@if(empty($subModulesArr))
    <script>
        window.location.href = "{{ route('admin.dashboard') }}";
    </script>
@endif

<ul class="nav nav-tabs mb-4" id="employeeTab" role="tablist">

    {{-- Personal --}}
    @if(in_array('boarderPersonal', $subModulesArr))
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $isEdit ? '' : 'nav_item' }} {{ $activeTab == 'personal' ? 'active' : '' }}"
                href="{{ $isEdit
                        ? route('admin.boarder-enrollment.new-boarder.personal.info.edit', $data->boarder_id)
                        : (request()->route('roomCode') && request()->route('seatCode')
                            ? route('admin.boarder-enrollment.new-boarder.personal.info.create', [
                                'roomCode' => request()->route('roomCode'),
                                'seatCode' => request()->route('seatCode')
                            ])
                            : '#') }}"
                id="personal-tab"
                role="tab">
                Personal
            </a>
        </li>
    @endif

    {{-- Invoice --}}
    @if(in_array('boarderOfficial', $subModulesArr))
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $isEdit ? '' : 'nav_item' }} {{ $activeTab == 'invoice' ? 'active' : '' }}"
                href="{{ $isEdit ? route('admin.boarder.invoice.edit', $data->boarder_id) : '#' }}">
                Invoice
            </a>
        </li>
    @endif

    {{-- Education --}}
    @if(in_array('boarderEducation', $subModulesArr))
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $isEdit ? '' : 'nav_item' }} {{ $activeTab == 'education' ? 'active' : '' }}"
                href="{{ $isEdit ? route('admin.boarder.education.edit', $data->boarder_id) : '#' }}">
                Education
            </a>
        </li>
    @endif

    {{-- Working Experience --}}
    @if(in_array('boarderWorkingExp', $subModulesArr))
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $isEdit ? '' : 'nav_item' }} {{ $activeTab == 'workingExp' ? 'active' : '' }}"
                href="{{ $isEdit ? route('admin.boarder.working.experience.edit', $data->boarder_id) : '#' }}"
                id="official-tab"
                role="tab">
                Working Experience
            </a>
        </li>
    @endif

    {{-- Photograph --}}
    @if(in_array('boarderPhotograph', $subModulesArr))
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $isEdit ? '' : 'nav_item' }} {{ $activeTab == 'photograph' ? 'active' : '' }}"
                href="{{ $isEdit ? route('admin.boarder.profile.photo.edit', $data->boarder_id) : '#' }}">
                Photograph
            </a>
        </li>
    @endif

    {{-- Attachment --}}
    @if(in_array('boarderAttachment', $subModulesArr))
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $isEdit ? '' : 'nav_item' }} {{ $activeTab == 'attachment' ? 'active' : '' }}"
                href="{{ $isEdit ? route('admin.boarder.boarder.attachment.edit', $data->boarder_id) : '#' }}">
                Attachment
            </a>
        </li>
    @endif

</ul>