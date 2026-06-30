<style>
    /* Custom 7-column layout for laptops and desktops */
    @media (min-width: 992px) {
        .row-cols-lg-7 > * {
            flex: 0 0 auto;
            width: 14.2857142857%;
        }
    }

    .custom-button-group {
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 13px;
        padding: 10px 5px;
    }

    .vehicle .col {
        margin: 0;
        padding: 0;
    }
</style>

@php
    $buttons = [
        [
            'title' => 'Income Category',
            'icon'  => 'fa-bars',
            'url'   => url('admin/master-data/income/income-category'),
            'active'=> request()->is('admin/master-data/income/income-category'),
            'permission' => 'itemCategory',
        ],
        [
            'title' => 'Income Head',
            'icon'  => 'fa-list',
            'url'   => url('admin/master-data/income/income-head'),
            'active'=> request()->is('admin/master-data/income/income-head'),
            'permission' => 'itemHead',
        ],
    ];
@endphp

@php
    $subModulesArr = get_sub_modules('admin/master-data/income');
@endphp

@if(empty($subModulesArr))
    <script>
        window.location.href = "{{ route('admin.dashboard') }}";
    </script>
@endif

<div class="row text-center border-ccc vehicle" role="group" aria-label="Vehicle Filters">

    @foreach($buttons as $button)
        @if(in_array($button['permission'], $subModulesArr))
            <div class="col col-md-{{ count($subModulesArr) == 1 ? '12' : '6' }}">
                <div class="btn-group d-block" role="group">
                    <a href="{{ $button['url'] }}"
                    class="btn btn-{{ $button['active'] ? 'success' : 'default' }} custom-button-group">
                        <i class="fa {{ $button['icon'] }}"></i>
                        <b>{{ $button['title'] }}</b>
                    </a>
                </div>
            </div>
        @endif
    @endforeach

</div>