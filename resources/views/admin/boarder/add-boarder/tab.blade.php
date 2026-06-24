@php
    $segments = request()->segments();
    $lastSegment = request()->segment(count($segments));
    $secondLastSegment = request()->segment(count($segments) - 1);
    $thirdLastSegment = request()->segment(count($segments) - 2);
@endphp

<ul class="nav nav-tabs mb-4" id="employeeTab" role="tablist">
    @if(isset($data->exists))
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ isset($data->exists) ? '' : 'nav_item' }}
                        <?= $thirdLastSegment == 'personal-info' ? 'active' : ''?>" href="{{ isset($data) ? route('admin.boarder-enrollment.new-boarder.personal.info.edit', $data->boarder_id) : '#' }}" id="personal-tab" role="tab"> Personal </a>
        </li>
    @else
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ isset($data->exists) ? '' : 'nav_item' }}
                        <?= $thirdLastSegment == 'create' ? 'active' : ''?>" href="{{ route('admin.boarder-enrollment.new-boarder.personal.info.create',[request()->route('roomCode'), request()->route('seatCode')]) }}" id="personal-tab" role="tab"> Personal </a>
        </li>
    @endif
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ isset($data->exists) ? '' : 'nav_item' }}
                    <?= $secondLastSegment == 'employee-office-info' ? 'active' : ''?>" href="{{ isset($data->exists) ? route('admin.employee.office.edit', $data->id) : '#' }}"> Official </a>
    </li>
    <li class="nav-item" role="presentation">
    <a class="nav-link {{ isset($data->exists) ? '' : 'nav_item' }}
                    <?= $secondLastSegment == 'boarder-education-info' ? 'active' : ''?>" href="{{ isset($data->exists) ? route('admin.boarder.education.edit', $data->boarder_id) : '#' }}"> Education </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ isset($data->exists) ? '' : 'nav_item' }}
                    <?= $secondLastSegment == 'working-experience-info' ? 'active' : ''?>" href="{{ isset($data->exists) ? route('admin.boarder.working.experience.edit', $data->boarder_id) : '#' }}" id="official-tab" role="tab"> Working Experience </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ isset($data->exists) ? '' : 'nav_item' }}
            <?= $secondLastSegment == 'profile-photo-info' ? 'active' : ''?>" href="{{ isset($data->exists) ? route('admin.boarder.profile.photo.edit', $data->boarder_id) : '#' }}"> Photograph </a>
    </li>
</ul>