<script>
    function areaRoute(flag) {
        var routeFunction;
        if (flag === 'division') {
            routeFunction = 'area/divisions';
        } else if (flag === 'district') {
            routeFunction = 'area/districts';
        } else if (flag === 'upazila') {
            routeFunction = 'area/upazila';
        } 
        window.location.href = "/admin/" + routeFunction;
    }
</script>
@php
 $btnFlag = '';
@endphp
<div class="row text-center border-ccc" role="group" aria-label="...">
    <div class="col-md-4">
        <div class="btn-group" role="group">
            <button type="button"
                onclick="areaRoute('division')"
                class="btn btn-{{ (request()->is('admin/area/divisions')) ? 'success' : 'default' }} custom-button-group">
                <i class="fa fa-bars"></i> <b>Division</b>
            </button>
        </div>
    </div>

    <div class="col-md-4">
        <div class="btn-group" role="group">
            <button type="button"
                onclick="areaRoute('district')"
                class="btn btn-{{ (request()->is('admin/area/districts')) ? 'success' : 'default' }} custom-button-group">
                <i class="fa fa-list"></i> <b>District</b>
            </button>
        </div>
    </div>

    <div class="col-md-4">
        <div class="btn-group" role="group">
            <button type="button"
                onclick="areaRoute('upazila')"
                class="btn btn-{{ (request()->is('admin/area/upazila')) ? 'success' : 'default' }} custom-button-group">
                <i class="fa fa-list-alt"></i> <b>Upazila</b>
            </button>
        </div>
    </div>
</div>
<!-- end for web device -->

<!-- for xs devices -->
<!-- <div class="btn-group btn-group-vertical visible-xs" role="group" aria-label="...">
    <div class="btn-group" role="group">
        <button type="button"
            onclick="areaRoute('division')"
            class="btn btn-{{ ($btnFlag == 'division') ? 'success' : 'default' }} custom-button-group">
            <i class="fa fa-bars"></i> <b>Division</b>
        </button>
    </div>

    <div class="btn-group" role="group">
        <button type="button"
            onclick="areaRoute('district')"
            class="btn btn-{{ ($btnFlag == 'district') ? 'success' : 'default' }} custom-button-group">
            <i class="fa fa-list"></i> <b>District</b>
        </button>
    </div>

    <div class="btn-group" role="group">
        <button type="button"
            onclick="areaRoute('upozilla')"
            class="btn btn-{{ ($btnFlag == 'upozilla') ? 'success' : 'default' }} custom-button-group">
            <i class="fa fa-list-alt"></i> <b>Upozilla</b>
        </button>
    </div>
</div> -->