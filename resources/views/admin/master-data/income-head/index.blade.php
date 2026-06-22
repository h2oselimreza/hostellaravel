@extends('layouts.app')
@section('content')

<div class="header dashboard_from">
    <h1 class="page-title">Income</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }} ">Home</a></li>
        <li><a href="#">/ Master Data</a></li>
        <li><a href="{{ url('admin/master-data/income') }}">/ Income</a></li>
    </ul>
</div>
<div class="main-content">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            
            @include('admin.master-data.income-head.tab')

            <div class="panel panel-default"> 
                <div class="row justify-content-center text-center">

                    <!-- category -->
                    <div class="col-md-2 col-sm-4 col-xs-12">
                        <div class="circle-tile">
                            <a href="#">
                                <div class="circle-tile-heading dark-blue">
                                    <i class="fa fa-bars fa-fw fa-2x"></i>
                                </div>
                            </a>
                            <div class="circle-tile-content dark-blue">
                                <div class="circle-tile-description text-faded">
                                    Income Category
                                </div>
                                <div class="circle-tile-number text-faded">
                                    {{ $categoryCount }}
                                    <span id="sparklineA"></span>
                                </div>
                                <a href="#" class="circle-tile-footer" onclick="areaRoute('master-data/income/income-category')">
                                    View <i class="fa fa-chevron-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- product -->
                    <div class="col-md-2 col-sm-4 col-xs-12">
                        <div class="circle-tile">
                            <a href="#">
                                <div class="circle-tile-heading dark-blue">
                                    <i class="fa fa-list fa-fw fa-2x"></i>
                                </div>
                            </a>
                            <div class="circle-tile-content dark-blue">
                                <div class="circle-tile-description text-faded">
                                    Income Head
                                </div>
                                <div class="circle-tile-number text-faded">
                                    {{ $headCount }}
                                    <span id="sparklineA"></span>
                                </div>
                                <a href="#" class="circle-tile-footer" onclick="areaRoute('master-data/income/income-head')">
                                    View <i class="fa fa-chevron-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    $(document).ready(function () {

        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
        });

    });
@endpush
