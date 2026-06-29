<html>
    <head>
        <style type="text/css">
            @media print {
                div.newPageDivClass {
                    page-break-after: always;
                }
            }
            .content-div{
                width: 50%;
                margin: auto; 
                padding: 10px 20px 10px 20px;
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
                    .heading-right{padding-right: 40px;}
                    .heading-right-head{font-weight: bold;font-size: 25px;}
                    .heading-right-body{font-size: 14px;line-height: 14px;}
                    .heading-border-bottom{margin: 10px 10px;border: 1px solid #ababab;}
                    .body-heading1{font-weight: bold;font-size: 19px;}
                    .body-heading2{font-size: 16px;}
                    .r-p-5{padding-right: 5px}
                    .cust-workshop-detail {border-collapse: collapse;outline: 1px solid black;}
                    .cust-workshop-detail td{border: 1px solid black;font-size: 13px;padding-left: 5px;}
                    .quo-status{font-size: 14px}
                    .description{ margin-top: 20px;border-collapse: collapse;outline: 2px solid black;}
                    .description td{font-size: 13px;padding-left: 5px;padding-right: 5px;}
                    .description th{background-color: #eee;font-size: 13px;padding: 5px;text-align: center;}
                    .left-border{border-left: 1px solid black;} 
                    .right-border{ border-right: 1px solid black;} 
                    .top-border{border-top: 1px solid black; } 
                    .bottom-border{border-bottom: 1px solid black;} 
                    .bottom-border-gray{border-bottom: 1px solid #ddd;} 
                    .vehicle-reg{padding:5px}
                    .product-label{padding-top:5px}
                    .double-underline{text-decoration: underline;text-decoration-style:double;padding-bottom: 5px;}
                    .m-t-20{margin-top: 20px;}
                    .note{text-align:justify;font-size: 13px;}
                    .left_signature{float: left;margin-left: 30px;}
                    .right_signature{float: right;margin-right: 30px;}
                    .generated-footer{font-size: 12px;width: 100%;display: inline-block;padding: 0px 0px 0px 0px;}
                    .footer{text-align: center;font-family:Calibri;font-size:12px; margin-top: -5px;}

                </style>

                <div id="" class="content-div">
                    <table border="0" cellpadding="0" cellspacing="0" align="center" width="753">
                        <tr>
                            <td align="left"></td>
                            <td align="right" class="heading-right">
                                <span class="heading-right-head">Hostel</span><br>
                                <span class="heading-right-body">
                                    <?php // echo VROOM_ADDRESS ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                    <div class="heading-border-bottom"></div>
                    <table border="0" cellpadding="0" cellspacing="0" align="center" width="726">
                        <tr>
                            <td align="center" class="body-heading1"><?php // echo get_company_name($company) ?></td>
                        </tr>
                        <tr>
                            <td align="center" class="body-heading2">Statement of Boarder and Head Wise Income</td>
                        </tr>
                        <tr>
                            <td align="center" class="body-heading2">From <?php echo get_date_format1($fromDate) ?> To <?php echo get_date_format1($toDate) ?></td>
                        </tr>
                    </table>

                    <table class="description" cellpadding="0" cellspacing="0" width="726">
                        <tr>
                            <th class="bottom-border right-border" width="150"><b>Item Head</b></th>
                            <th class="bottom-border right-border" width="120"><b>Total Times of Transaction</b></th>
                            <th class="bottom-border right-border" width="100"><b>Quantity</b></th>
                            <th class="bottom-border right-border" width="140"><b>Unit Price(AVG)<br>(BDT)</b></th>
                            <th class="bottom-border right-border" width="110"><b>Income (BDT)</b></th>
                        </tr>
                        <?php
                        $totalIncome = 0;
                        //$vehicle = "";
                        $boarder = "";
                        $incomeCategory = "";
                        $incomeHead = "";
                        $subTotal = 0;
                        $subTotalFlag = 0;
                        $inTotal = 0;
                        $inTotalFlag = 0;
                        $grandTotal = 0;
                        foreach ($incomeDetails as $incomeDetail) {
                            if ($incomeDetail->boarder != $boarder) {
                                $boarderName = $incomeDetail->boarder_name;

                                if ($subTotalFlag) {
                                    echo "<tr>";
                                    echo "<td class='right-border' align='right' colspan='4'><b>Sub Total</b></td>";
                                    echo "<td class='right-border' align='right'><b><u>" . number_format($subTotal, 2) . "</u></b></td>";
                                    echo "</tr>";
                                    $subTotal = 0;
                                    $subTotalFlag = 0;
                                }

                                if ($inTotalFlag) {
                                    echo "<tr>";
                                    echo "<td class='right-border' align='right' colspan='4'><b>In Total</b></td>";
                                    echo "<td class='right-border double-underline' align='right'><b><u>" . number_format($inTotal, 2) . "</u></b></td>";
                                    echo "</tr>";
                                    $inTotal = 0;
                                    $inTotalFlag = 0;
                                }


                                echo "<tr>";
                                echo "<td class='right-border top-border vehicle-reg' align='center' colspan='4'><b>" . $boarderName . "</b></td>";
                                echo "<td class='top-border right-border' align='right'></td>";
                                echo "</tr>";
                                $boarder = $incomeDetail->boarder;
                                $incomeCategory = "";
                                $incomeHead = "";
                            }
                            if ($boarder == $incomeDetail->boarder && $incomeDetail->item_category != $incomeCategory) {
                                if ($subTotalFlag) {
                                    echo "<tr>";
                                    echo "<td class='right-border' align='right' colspan='4'><b>Sub Total</b></td>";
                                    echo "<td class='right-border' align='right'><b><u>" . number_format($subTotal, 2) . "</u></b></td>";
                                    echo "</tr>";
                                    $subTotal = 0;
                                    $subTotalFlag = 0;
                                }
                                echo "<tr>";
                                echo "<td class='right-border' align='left' colspan='4'><b><u>" . $incomeDetail->category_name . "</u></b></td>";
                                echo "<td class='right-border' align='right'></td>";
                                echo "</tr>";
                                $incomeCategory = $incomeDetail->item_category;
                                $incomeHead = "";
                            }

                            if ($boarder == $incomeDetail->boarder && $incomeCategory == $incomeDetail->item_category && $incomeDetail->item_head != $incomeHead) {
                                echo "<tr>";
                                echo "<td class='bottom-border-gray' align='left'>" . $incomeDetail->item_head_name . "</td>";
                                echo "<td class='bottom-border-gray' align='center'>" . $incomeDetail->total_tran . " </td>";
                                echo "<td class='bottom-border-gray' align='center'>" . $incomeDetail->total_quantity . " " . $incomeDetail->unit_name . " </td>";
                                echo "<td class='right-border bottom-border-gray' align='right'>" . number_format($incomeDetail->average_unit_price, 2) . "</td>";
                                echo "<td class='right-border bottom-border-gray' align='right'>" . $incomeDetail->total_income . "</td>";
                                echo "</tr>";
                                $subTotal += $incomeDetail->total_income;
                                $subTotalFlag = 1;
                                $incomeHead = $incomeDetail->item_head;
                                $inTotalFlag = 1;
                                $inTotal += $incomeDetail->total_income;
                                $grandTotal += $incomeDetail->total_income;
                            }
                            $totalIncome += $incomeDetail->total_income;
                        }
                        echo "<tr>";
                        echo "<td class='right-border' align='right' colspan='4'><b>Sub Total</b></td>";
                        echo "<td class='right-border' align='right'><b><u>" . number_format($subTotal, 2) . "</u></b></td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td class='right-border' align='right' colspan='4'><b>In Total</b></td>";
                        echo "<td class='right-border double-underline' align='right'><b><u>" . number_format($inTotal, 2) . "</u></b></td>";
                        echo "</tr>";
                        ?>
                    </table>


                    <table class="description" cellpadding="0" cellspacing="0" width="726">
                        <tr>
                            <th class="bottom-border right-border" width="590"><b>Income Category</b></th>
                            <th class="bottom-border right-border" width="136"><b>Income SubTotal (BDT)</b></th>
                        </tr>
                        <?php
                        foreach ($categoryDetails as $categoryDetail) {
                            echo "<tr>";
                            echo "<td class='bottom-border right-border'>$categoryDetail[category_name]</td>";
                            echo "<td class='bottom-border right-border' align='right'>" . number_format($categoryDetail->total_income, 2) . "</td>";
                            echo "</tr>";
                        }
                        echo "<tr>";
                        echo "<td class='top-border right-border' align='right'><b>Grand Total</b></td>";
                        echo "<td class='top-border right-border' align='right'><b>" . number_format($grandTotal, 2) . "</b></td>";
                        echo "</tr>";
                        ?>
                    </table>

                    <?php
                    $grandTotalInWords = "";
                    if ($grandTotal) {
                        $numberArr = explode('.', number_format($grandTotal, 2, '.', ''));
                        $grandTotalInTaka = numberConvertToWords($numberArr[0]);
                        $grandTotalInFraction = numberConvertToWords($numberArr[1]);
                        $grandTotalInWords = $grandTotalInTaka . " Taka Only";
                        if ($grandTotalInFraction) {
                            $grandTotalInWords .= " And " . $grandTotalInFraction . " Poisa Only";
                        }
                    }
                    ?>
                    <table class="m-t-20" border="0" cellpadding="0" cellspacing="0"  width="726">
                        <tr>
                            <td align="left" class="quo-status r-p-5 p-b-5"><b>In Words: </b> <?php echo $grandTotalInWords; ?></td>
                        </tr>
                    </table>


                    <hr>
                    <div class="generated-footer">
                        <div class="left_signature">
                            Printed By:<b> {{ auth()->user()->full_name }} </b>
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