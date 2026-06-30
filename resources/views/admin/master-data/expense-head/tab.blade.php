<script>
    function areaRoute(flag) {
        var routeFunction;
        if (flag === 'master-data/expense/cost-category') {
            routeFunction = 'master-data/expense/cost-category';
        } else if (flag === 'master-data/expense/cost-head') {
            routeFunction = 'master-data/expense/cost-head';
        }
        
        window.location.href = "/admin/" + routeFunction;
    }
</script>

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
$btnFlag = "";
@endphp

@php
    $subModulesArr = get_sub_modules('admin/master-data/expense');
@endphp

@if(empty($subModulesArr))
    <script>
        window.location.href = "{{ route('admin.dashboard') }}";
    </script>
@endif

<div class="row text-center border-ccc vehicle" role="group" aria-label="Vehicle Filters">

    @if(in_array('costCategory', $subModulesArr))
    <div class="col col-md-{{!in_array('costHead', $subModulesArr) ? '12' : '6'}}">
        <div class="btn-group d-block" role="group">
            <button type="button"
                onclick="areaRoute('master-data/expense/cost-category')"
                class="btn btn-{{ (request()->is('admin/master-data/expense/cost-category')) ? 'success' : 'default' }} custom-button-group">
                <i class="fa fa-bars"></i> <b>Expense Category</b>
            </button>
        </div>
    </div>
    @endif

    @if(in_array('costHead', $subModulesArr))
    <div class="col col-md-{{!in_array('costCategory', $subModulesArr) ? '12' : '6'}}">
        <div class="btn-group d-block" role="group">
            <button type="button"
                onclick="areaRoute('master-data/expense/cost-head')"
                class="btn btn-{{ (request()->is('admin/master-data/expense/cost-head')) ? 'success' : 'default' }} custom-button-group">
                <i class="fa fa-list"></i> <b>Expense Head</b>
            </button>
        </div>
    </div>
    @endif
</div>
