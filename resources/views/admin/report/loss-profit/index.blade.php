@extends('layouts.app')

@section('content')
<div class="header">
    <h1 class="page-title">Loss Profit Report</h1>
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">Home</a> / </li>
        <li><a href="#">Report</a> / </li>
        <li class="active"><a href="/admin/loss-profit-report">Loss Profit Report</a></li>
    </ul>
</div>
<div class="main-content">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-default"> 
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group form-float" >
                            <label class="form-label"> From Date</label>
                            <input type="text" class="form-control dateInput" name="fromDate" id="fromDate" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group form-float" >
                            <label class="form-label"> To Date</label>
                            <input type="text" class="form-control dateInput" name="toDate" id="toDate" autocomplete="off">
                        </div>
                    </div>		
                </div>
                <form target="_blank" action="{{ route('admin.loss.profit.report.details') }}" method="POST" id="formId">
                    @csrf
                    <input type="hidden" name="fromDate" id="fromDateHidden">
                    <input type="hidden" name="toDate" id="toDateHidden">
                </form>
                <div class="text-left">
                    <button class="btn btn-primary save_button mt-3" onclick="submitForm()">Show Report</button>
                </div> 
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    function submitForm() {
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();

        if (fromDate === "" || toDate === "") {
            sweetAlert('From Date and To Date is required...!');
            return false;
        }

        var toDateCheck = new Date(toDate);
        var fromDateCheck = new Date(fromDate);
        if (toDateCheck < fromDateCheck) {
            sweetAlert('To Date should be greater than or equal of From date...!');
            return false;
        }

        $('#fromDateHidden').val(fromDate);
        $('#toDateHidden').val(toDate);

        $("#formId").submit();
    }
</script>
@endpush