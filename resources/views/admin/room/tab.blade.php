<style>
    /* Custom 7-column layout for laptops and desktops */
    @media (min-width: 992px) {
        .row-cols-lg-7 > * {
            flex: 0 0 auto;
            width: 14.2857142857%;
        }
    }

    /* Ensure buttons fill the full width of their small columns */
    .custom-button-group {
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 13px; /* Smaller font helps fit 7 items on one line */
        padding: 10px 5px;
    }
    
    .vehicle .col {
        margin-bottom: 0px; /* Spacing for mobile view */
        margin-top: 0px;
        padding-left: 0px;
        padding-right: 0px;
    }
</style>

@php
   $disabled = request()->routeIs('admin.room.create');

    $tabs = [
        [
            'title' => 'Basic',
            'route' => isset($data) ? route('admin.room.edit', $data->room_code) : route('admin.room.create'),
            'active' => request()->routeIs('admin.room.create', 'admin.room.edit'),
        ],
        [
            'title' => 'Profile Picture',
            'route' => isset($data) ? route('admin.room.profile-picture.edit', $data->room_code) : '#',
            'active' => request()->routeIs('admin.room.profile-picture.*'),
        ],
        [
            'title' => 'Additional Images',
            'route' => isset($data) ? route('admin.room.additional-images.edit', $data->room_code) : '#',
            'active' => request()->routeIs('admin.room.additional-images.*'),
        ],
    ];
@endphp

<div class="row text-center border-ccc vehicle mt-4">
    @foreach ($tabs as $tab)
        <div class="col col-md-4">
            <div class="btn-group d-block">
                <a
                    href="{{ $tab['route'] }}"
                    class="btn btn-{{ $tab['active'] ? 'success' : 'default' }} custom-button-group {{ isset($data->exists) ? '' : '' }} 
                    {{ ($disabled) && $tab['title'] != 'Basic' ? 'disabled' : '' }}">
                    <i class="fa fa-list"></i>
                    <b>{{ $tab['title'] }}</b>
                </a>
            </div>
        </div>
    @endforeach
</div>