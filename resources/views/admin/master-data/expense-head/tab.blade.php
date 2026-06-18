<script>
    function areaRoute(flag) {
        var routeFunction;
        if (flag === 'master-data/cost-category') {
            routeFunction = 'master-data/cost-category';
        } else if (flag === 'master-data/cost-head') {
            routeFunction = 'master-data/cost-head';
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
<div class="row text-center border-ccc vehicle" role="group" aria-label="Vehicle Filters">
    
    <div class="col col-md-6">
        <div class="btn-group d-block" role="group">
            <button type="button"
                onclick="areaRoute('master-data/cost-category')"
                class="btn btn-{{ (request()->is('admin/master-data/cost-category')) ? 'success' : 'default' }} custom-button-group">
                <i class="fa fa-bars"></i> <b>Expense Category</b>
            </button>
        </div>
    </div>

    <div class="col col-md-6">
        <div class="btn-group d-block" role="group">
            <button type="button"
                onclick="areaRoute('master-data/cost-head')"
                class="btn btn-{{ (request()->is('admin/master-data/cost-head')) ? 'success' : 'default' }} custom-button-group">
                <i class="fa fa-list"></i> <b>Expense Head</b>
            </button>
        </div>
    </div>
</div>
