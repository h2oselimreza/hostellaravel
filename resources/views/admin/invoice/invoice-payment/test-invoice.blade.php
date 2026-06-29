<!DOCTYPE html>
<html>
    <head>
        <title>Invoice</title>
        <link rel="icon" href="{{ asset('assets/images/company/favicon1.png') }}" type="image/x-icon">
        <style>
            .poligon {
                display: inline-block;
                position: relative;
                width: 100%;
                height: 130px;
                background: #0E6655;
                box-sizing: border-box;
                -webkit-clip-path: polygon(0 80%, 0 0, 100% 0, 100% 80%, 50% 100%);
                clip-path: polygon(0 75%, 0 0, 100% 0, 100% 75%, 50% 100%);
            }

        </style>

        <style type="text/css">
            @media print {
                div.newPageDivClass {
                    page-break-after: always;
                }
            }
            .content-div{
                width: 50%;
                margin: auto; 
                padding: 0px 0px 10px 0px;
                /*                border: 1px solid black;*/
            }
            body {
                -webkit-print-color-adjust: exact;
            }
        </style>  

        <script type="text/javascript">
            function printdiv(printpage)
            {
                var headstr = "<html><head><title></title>";
                var style = "<link rel='stylesheet' href='' type='text/css' />";
                var headstr1 = "</head><body>";
                var footstr = "</body>";
                var newstr = document.all.item(printpage).innerHTML;
                var oldstr = document.body.innerHTML;
                document.body.innerHTML = headstr + style + headstr1 + newstr + footstr;
                window.print();
                document.body.innerHTML = oldstr;
                return false;
            }
        </script>
    </head>
    <body style="background-color: #eee;">
        <input name="b_print" type="button" onClick="printdiv('my-div');" value=" Print ">
        <br>

        <div class="full-page" >
            <div id="my-div" class="my-div">
                <style>
                    .content-div{width: 735px;font-family:Calibri;background-color: #FFF;}

                    .heading-right{padding: 10px 15px; color: #ffffff}
                    .heading-right-head{font-weight: bold;font-size: 25px;}
                    .heading-right-body{font-size: 14px;line-height: 14px;}
                    .heading-border-bottom{margin: 3px 28px;border: 1px solid #ababab;}
                    .body-heading{padding: 2px}
                    .body-heading1{font-weight: bold;font-size: 22px;}
                    .body-heading2{font-size: 12px;}
                    .body-heading3{font-size: 12px; font-weight: bold}
                    .body-billing{font-size: 12px;}
                    .body-billing-left{padding-left: 25px}
                    .body-billing-right{padding-right: 25px}
                    .r-p-5{padding-right: 5px}
                    .cust-workshop-detail {border-collapse: collapse;outline: 1px solid black;}
                    .cust-workshop-detail td{border: 1px solid black;font-size: 13px;padding-left: 5px;}
                    .quo-status{font-size: 14px}
                    .description{ margin-top: 20px; margin-left: 28px; border-collapse: collapse;outline: 2px solid black;}
                    .description td{font-size: 13px;padding-left: 5px;padding-right: 5px;}
                    .description th{background-color: #eee;font-size: 13px;padding: 5px;text-align: center;}
                    .employeeInfo td{font-size: 13px;padding-left: 5px;padding-right: 5px;}
                    .left-border{border-left: 1px solid black;} 
                    .right-border{ border-right: 1px solid black;} 
                    .top-border{border-top: 1px solid black; } 
                    .bottom-border{border-bottom: 1px solid black;} 
                    .vehicle-reg{padding:5px}
                    .product-label{padding-top:5px}
                    .double-underline{text-decoration: underline;text-decoration-style:double;padding-bottom: 5px;}
                    .m-t-20{margin-top: 20px;}
                    .note{text-align:justify;font-size: 13px;}
                    .left_signature{float: left;margin-left: 30px;}
                    .right_signature{float: right;margin-right: 30px;}
                    .generated-footer{font-size: 12px;width: 100%;display: inline-block;padding: 0px 0px 0px 0px;}
                    .footer{text-align: center;font-family:Calibri;font-size:12px; margin-top: -5px;}
                    .float-left{float: left}
                    .float-right{float:right}
                </style>
                <div id="" class="content-div">
                    <div class="poligon">
                        <table border="0" cellpadding="0" cellspacing="0" align="center" width="726">
                            <td align="left" class="heading-right"><span class="heading-right-head">Super Hostel</span><br>
                            </td>
                            <td align="right" class="heading-right">
                                <span class="heading-right-body">
                                    KA/67-68, Norda, East Baridhara<br>Dhaka 1212, Bangladesh<br>+88 09678187666<br>info@hostel.com
                                </span>
                            </td>
                        </table>
                    </div>
                    <table border="0" cellpadding="0" cellspacing="0" align="center" width="726">
                        <tr><td align="center" class="body-heading body-heading1">invoice</td></tr>
                        <tr><td align="center" class="body-heading body-heading2"><?php echo $invoiceSummary[0]['invoice_no'] ?></td></tr>
                        <tr><td align="center" class="body-heading body-heading3"><?php echo date('d/m/Y', strtotime($invoiceSummary[0]['invoice_date'])); ?></td></tr>
                    </table>
                    <table border="0" cellpadding="0" cellspacing="0" align="center" width="726">
                        <tr class="body-billing">
                            <td width="60%" align="left" class="body-heading body-billing-left"> Due Date: 
                                <b><?php echo date('d/m/Y', strtotime($invoiceSummary[0]['invoice_due_date'])); ?></b>
                            </td>
                            <td width="40%" align="left" class="body-heading body-billing-right"> 
                                Bill To: 
                            </td>
                        </tr>
                    </table>
                    <div class="heading-border-bottom"></div>
                    <table border="0" cellpadding="0" cellspacing="0" align="center" width="726">
                        <tr class="body-billing">
                            <td width="60%" align="left" class="body-heading body-billing-left">
                            </td>
                            <td width="40%" align="left" class="body-heading body-billing-right"> 
                                <b><?php echo $invoiceSummary[0]['boarder_name'] ?></b><br>
                                <?php echo $invoiceSummary[0]['boarder_primary_mobile'] ?>
                            </td>
                        </tr>
                    </table>
                    <?php
//                    $itemStr = "";
//                    $deductionStr = "";
//                    foreach ($invoiceDetails as $incomeDetail) {
//                        $itemStr .= '<div class="float-left">' . $incomeDetail['head_name'] . '</div> <div class="float-right">' . $incomeDetail['quantity'] . ' BDT</div><br>';
//                    }
//                    foreach ($salarySlipDetails as $salarySlipDetail) {
//                        if ($salarySlipDetail['payroll_details_code'] == DETAILS_CODE_PROVIDENT_FUND) {
//                            $deductionStr .= '<div class="float-left">' . $salarySlipDetail['details_title'] . '</div> <div class="float-right">' . $salarySlipDetail['monthly_amount'] . ' BDT</div><br>';
//                        } else {
//                            $itemStr .= '<div class="float-left">' . $salarySlipDetail['details_title'] . '</div> <div class="float-right">' . $salarySlipDetail['monthly_amount'] . ' BDT</div><br>';
//                        }
//                        //$salarySlipDetail['details_title']
//                    }

                    // $deductionStr .= '<div class="float-left">Tax</div> <div class="float-right">' . $salarySlipSummary[0]['monthly_payable_tax'] . ' BDT</div><br>';
                    ?>

                    <table class="description" cellpadding="10" cellspacing="0" width="92%">
                        <tr>
                            <th class="bottom-border"><b>Items</b></th>
                            <th class="bottom-border"><b>Quantity</b></th>
                            <th class="bottom-border"><b>Unit Price</b></th>
                            <th class="bottom-border"><b>Adjust</b></th>
                            <th class="bottom-border"><b>Total</b></th>
                        </tr>
                        <?php foreach ($invoiceDetails as $invoiceDetail) { ?>
                            <tr>
                                <td class="right-border"><?php echo $invoiceDetail->head_name ?></td>
                                <td class="right-border"><div class="float-right"><?php echo $invoiceDetail->quantity ?></div></td>
                                <td class="right-border"><div class="float-right"><?php echo $invoiceDetail->unit_price . ' per ' . $invoiceDetail->unit_name ?></div></td>
                                <td class="right-border"><div class="float-right"><?php echo $invoiceDetail->adjust?></div></td>
                                <td class="right-border"><div class="float-right"><?php echo $invoiceDetail->amount ?> BDT</div></td>
                            </tr>
                        <?php }
                        ?>
                        <tr>
                            <td class="top-border"></td>
                            <td class="top-border"></td>
                            <td class="top-border"></td>
                            <td class="top-border right-border"></td>
                            <td class="top-border">
                                <b><div class="float-left">Subtotal</div> <div class="float-right"><?php echo number_format($invoiceSummary[0]['invoice_amount'], 2) ?> BDT</div></b>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="right-border"></td>
                            <td class="">
                                <b><div class="float-left">Discount</div> <div class="float-right"><?php echo number_format($invoiceSummary[0]['invoice_amount'] - $invoiceSummary[0]['total_amount'], 2) ?> BDT</div></b>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class=" right-border"></td>
                            <td class="">
                                <b><div class="float-left">Net Pay</div> <div class="float-right"><?php echo number_format($invoiceSummary[0]['total_amount'], 2) ?> BDT</div></b>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <div class="generated-footer">
                        <div class="left_signature">
                            Printed By:<b> {{ auth()->user()->full_name }}</b>
                        </div>
                        <div class="right_signature">
                            Printed Date & Time: <b><?php echo date("F j, Y, g:i a"); ?></b>
                        </div>
                    </div>
                    <div class="footer">
                        <br>Developed By <b>ArrowLink™ Soft</b>
                    </div>
                </div>
                <br>
            </div>  
        </div>  
        <br>
    </body>
</html>