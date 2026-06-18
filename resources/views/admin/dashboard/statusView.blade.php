<style>
    .bg-green{
        background-color: #287575;
    }
    .bg-blue-grey{
        background-color: #607D8B;
    }
    .custom-card-body{
        padding: 10px 10px 10px 8px;
        color: #fff;
        font-size: 13px;
    }
    .bg-green div, .custom-card-body div{
        margin-bottom: 10px;
    }
    .dashboard-stat-list{
        line-height: 35px;
    }
    .pull-right{
        float: right;
    }
    .custom-scrollber{
        height: 252px;
        overflow-x: scroll;
    }
</style>
<div class="row p-t-10">
    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <div class="custom-card">
            <div class="custom-card-body bg-green custom-scrollber status-card-body">
                <div class="m-b--35 font-bold">Corporate Company</div>
                <ul class="dashboard-stat-list">
                    <?php echo $companyStr ?? null  ?>
                </ul>
            </div>
        </div>
    </div>

    <?php
    //------------ quotation request ---------------//
    $quotationRequests = $statusInformation['quotationRequests'];
    $draftStatus = 0;
    $pendingStatus = 0;
    $processingStatus = 0;
    $submitStatus = 0;
    $approveStatus = 0;
    $rejectStatus = 0;

    foreach ($quotationRequests as $quotationRequest) {

        if ($quotationRequest->status == config('constants.REQ_DRAFT_STATUS')) {
            $draftStatus = $quotationRequest->status_count;

        } elseif ($quotationRequest->status == config('constants.REQ_PENDING_STATUS')) {
            $pendingStatus = $quotationRequest->status_count;

        } elseif ($quotationRequest->status == config('constants.REQ_PROCCESSING_STATUS')) {
            $processingStatus = $quotationRequest->status_count;

        } elseif ($quotationRequest->status == config('constants.REQ_QUOT_SUB_STATUS')) {
            $submitStatus = $quotationRequest->status_count;

        } elseif ($quotationRequest->status == config('constants.REQ_QUOT_APPV_CUS_STATUS')) {
            $approveStatus = $quotationRequest->status_count;

        } elseif ($quotationRequest->status == config('constants.REQ_REJECT_STATUS')) {
            $rejectStatus = $quotationRequest->status_count;
        }
    }
    //-------------------------------------------------------//
    //--------------------- appointment ---------------------//

    $appointmentServices = $statusInformation['appointmentServices'];
    $pendingStatusApp = 0;
    $processingStatusApp = 0;
    $rejectStatusApp = 0;
    $acceptStatusApp = 0;
    $compeleteStatusApp = 0;
    foreach ($appointmentServices as $appointmentService) {
        switch ($appointmentService->status) {

            case config('constants.APPOINTMENT_PENDING'):
                $pendingStatusApp = $appointmentService->status_count;
                break;

            case config('constants.APPOINTMENT_PROCCESSING'):
                $processingStatusApp = $appointmentService->status_count;
                break;

            case config('constants.APPOINTMENT_ACCEPT'):
                $acceptStatusApp = $appointmentService->status_count;
                break;

            case config('constants.APPOINTMENT_COMPLETE'):
                $compeleteStatusApp = $appointmentService->status_count;
                break;

            case config('constants.APPOINTMENT_REJECT'):
                $rejectStatusApp = $appointmentService->status_count;
                break;
        }
    }
    //--------------------------------------------------------------//
    //------------------- home service -----------------------//

    $homeServices = $statusInformation['homeServices'];
    $pendingStatusHome = 0;
    $processingStatusHome = 0;
    $rejectStatusHome = 0;
    $acceptStatusHome = 0;
    $compeleteStatusHome = 0;
    $startStatusHome = 0;
    $cashCollectedStatusHome = 0;

    foreach ($homeServices as $homeService) {
        switch ($homeService->status) {

            case config('constants.APPOINTMENT_PENDING'):
                $pendingStatusHome = $homeService->status_count;
                break;

            case config('constants.APPOINTMENT_PROCCESSING'):
                $processingStatusHome = $homeService->status_count;
                break;

            case config('constants.APPOINTMENT_ACCEPT'):
                $acceptStatusHome = $homeService->status_count;
                break;

            case config('constants.APPOINTMENT_START'):
                $startStatusHome = $homeService->status_count;
                break;

            case config('constants.APPOINTMENT_COMPLETE'):
                $compeleteStatusHome = $homeService->status_count;
                break;

            case config('constants.APPOINTMENT_CASH_COLLECT'):
                $cashCollectedStatusHome = $homeService->status_count;
                break;

            case config('constants.APPOINTMENT_REJECT'):
                $rejectStatusHome = $homeService->status_count;
                break;
        }
    }
    ?>

    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <div class="custom-card">
            <div class="custom-card-body  bg-blue-grey custom-scrollber status-card-body">
                <div class="m-b--35 font-bold">Request For Quotation</div>
                <ul class="dashboard-stat-list">
                    <li>Pending
                        <span class="pull-right">
                            <?php
                            echo $pendingStatus;
                            ?>
                        </span>
                    </li>
                    <li>Processing
                        <span class="pull-right">
                            <?php
                            echo $processingStatus;
                            ?>
                        </span>
                    </li>
                    <li>Submitted
                        <span class="pull-right">
                            <?php
                            echo $submitStatus;
                            ?>
                        </span>
                    </li>
                    <li>Approved
                        <span class="pull-right">
                            <?php
                            echo $approveStatus;
                            ?>
                        </span>
                    </li>
                    <li>Rejected
                        <span class="pull-right">
                            <?php
                            $statusCount = 0;
                            echo $rejectStatus;
                            ?>
                        </span>
                    </li>

                </ul>
            </div>
        </div>
    </div>


    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <div class="custom-card">
            <div class="custom-card-body bg-green custom-scrollber status-card-body">
                <div class="font-bold m-b--35">Appointment</div>
                <ul class="dashboard-stat-list">
                    <li>Pending
                        <span class="pull-right">
                            <?php echo $pendingStatusApp ?>
                        </span>
                    </li>
                    <li>Processing
                        <span class="pull-right">
                            <?php echo $processingStatusApp ?>
                        </span>
                    </li>
                    <li>Accepted
                        <span class="pull-right">
                            <?php echo $acceptStatusApp ?>
                        </span>
                    </li>
                    <li>Completed
                        <span class="pull-right">
                            <?php echo $compeleteStatusApp ?>
                        </span>
                    </li>
                    <li>Rejected
                        <span class="pull-right">
                            <?php echo $rejectStatusApp ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <div class="custom-card">
            <div class="custom-card-body bg-blue-grey custom-scrollber status-card-body">
                <div class="m-b--35 font-bold">Home Service</div>
                <ul class="dashboard-stat-list">
                    <li>Pending
                        <span class="pull-right">
                            <?php echo $pendingStatusHome ?>
                        </span>
                    </li>
                    <li>Processing
                        <span class="pull-right">
                            <?php echo $processingStatusHome ?>
                        </span>
                    </li>
                    <li>Accepted
                        <span class="pull-right">
                            <?php echo $acceptStatusHome ?>
                        </span>
                    </li>
                    <li>Started
                        <span class="pull-right">
                            <?php echo $startStatusHome ?>
                        </span>
                    </li>
                    <li>Completed
                        <span class="pull-right">
                            <?php echo $compeleteStatusHome ?>
                        </span>
                    </li>
                    <li>Paid
                        <span class="pull-right">
                            <?php echo $cashCollectedStatusHome ?>
                        </span>
                    </li>
                    <li>Rejected
                        <span class="pull-right">
                            <?php echo $rejectStatusHome ?>
                        </span>
                    </li>

                </ul>
            </div>
        </div>
    </div>

</div>