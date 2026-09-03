<?php 
include "../Includes/includes.php";
include '../Includes/authcheck.php';

$counter_id = 0;
// $user_id = $_SESSION['user_id'];

$shop_id = $_SESSION['shop_id'];

if(isset($_GET['counter_id']))
{
    $counter_id = $_GET['counter_id'];
}//assign invoice id
else if(empty($_GET['counter_id']) || $_GET['counter_id']=="" || $_GET['counter_id']==0)
{
     header("Location: ../Public/home.php");
}
else
{
    header("Location: ../Public/home.php");
}

$dbObj = new DBTransactions();
$date=date("Y-m-d");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Next Edge Counter Close Report - <?=$date?></title>
    <script src="../JQuery_361.js"></script>
    <style>
        *{
            box-sizing: border-box;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }
        #img_receipt{
            width: 50%;
            margin-left: 25%;
        }
        #p_address{
            text-align: center;
            font-size: 12px;
            margin: 0px 5px;
        }
        #tbl_bill_detail{
            width: 100%;
        }
        .td_bill_detail{
            font-size: 12px;
        }
        .td_data{
            text-align: right;
        }
        /*----  Cart ---- */
        th{
            border: 1px black solid;
            font-size: 15px;
        }
        #tbl_cart{
            width: 100%;
            border-collapse: collapse;
        }
        .td_total{
            text-align: right;
            font-weight: bold;
        }
        .td_row{
            font-size: 15px;
        }

        /*------ Totals ------- */
        #tbl_totals{
            width: 100%;
        }

        /*--------------- Conditions ------------- */
        #p_conditions{
            text-align: center;
            font-size: 11px;
        }

        /*---------------- Footer ------------- */
        #p_footer{
            margin-top: 2px;
            text-align: center;
            font-size: 9px;
        }
    </style>
</head>
<body onload="window.print()">

    <div>
        <?php 
        $sql_revenue ="SELECT IFNULL(SUM(NetAmount),0) AS TotalRevenue
        FROM invoiceheader
        WHERE EffectiveDate = CURDATE() AND InvStat = 1 
        AND (SELECT COUNT(*) AS COUNT FROM invoicedetails d WHERE d.InvoiceHeader_IHID = invoiceheader.IHID) > 0";

        $salesData  = $dbObj->getData($sql_revenue);
        $TotalRevenue = floatval($salesData[0]['TotalRevenue']);
        //get shop details
        $sql = "SELECT * FROM `cashcounter`
        INNER JOIN shop ON shop.SHID = cashcounter.shop_SHID
        INNER JOIN user ON user.USID = cashcounter.user_USID
        WHERE CCID = ".$counter_id.";";

        $headData  = $dbObj->getData($sql);
        $receipt_logo_name = $headData[0]['ReceiptLogo'];
        $address_1 = $headData[0]['AddressLineOne'];
        $address_2 = $headData[0]['AddressLineTwo'];
        $contact = $headData[0]['PhoneNumber'];
        $counter_user_id =  $headData[0]['user_USID'];
        $user_name = $headData[0]['UserName'];

        $counter_start_time = $headData[0]['CounterStartTime'];
        $counter_end_time = $headData[0]['CounterEndTime'];

        $counter_start_balance = floatval($headData[0]['StartBalance']);
        $counter_start_balance = number_format($counter_start_balance, 2, ".", "");

        $counter_close_balance = floatval($headData[0]['EndBalance']);
        $counter_close_balance = number_format($counter_close_balance, 2, ".", "");

        //print date time
        date_default_timezone_set("Asia/Colombo");
        $print_date = date("Y-m-d");
        $print_time = date("H:i:s");

        ?>
        <img src="../Assets/Images/shop_images/<?php echo $receipt_logo_name;?>" alt="shop logo" id="img_receipt">

        <p id="p_address">
            <?php echo $address_1 . ", " . $address_2 . ".";?><br>
            <?php echo "Tel - " . $contact;?>
        </p>
    </div>

    <!-- cash counter summary -->
    <?php 
    $counterObj = new Counter();
    $counterData = $counterObj->getCounterTotalByUser($user_id,$shop_id);

    //getcounter total
    $sql = "";

    $counter_total = floatval($counterData[0]['CounterTotal']);
    ?>

    <p style="font-size: 24px; font-weight: bold; text-align:center;">Counter Summary Report</p>

    <!------------------------------------- Bill Details ------------------------------->
    <div>
        <table id="tbl_bill_detail">

            <tr>
                <td class="td_bill_detail">User: </td>
                <td class="td_bill_detail td_data"><?php echo $user_name?></td>
            </tr>
            <tr>
                <td class="td_bill_detail">Counter Start:</td>
                <td class="td_bill_detail td_data"><?php echo $counter_start_time;?></td>
            </tr>
            <tr>
                <td class="td_bill_detail">Counter Close:</td>
                <td class="td_bill_detail td_data"><?php echo $counter_end_time;?></td>
            </tr>
        </table>
    </div>

    <hr>

    <!---------------------------------------- Cart ---------------------------------->
    <div id="div_cart">
        <table id="tbl_cart">
            <tr style="text-align: center; font-weight: bold;">
                <td colspan="2">Cash Drawer</td>
            </tr>
            <tr>
                <td>Start Balance</td>
                <td class="td_data"><?php echo number_format($counter_start_balance, 2, '.', ',') ?></td>
            </tr>
            <tr>
                <td>Close Balance</td>
                <td class="td_data"><?php echo number_format($counter_close_balance, 2, '.', ',');?></td>
            </tr>
            <tr>
                <td>Total Sales/Revenue</td>
                <td class="td_data"><?php echo number_format($TotalRevenue, 2, '.', ','); ?></td>
            </tr>

            <tr style="text-align: center; font-weight: bold;">
                <td colspan="2">System Transactions</td>
            </tr>

            <?php 
            $sql = "SELECT SUM(transactions.TransferAmount) AS transfer_amount, paymethod.PaymethodName, SUM(invoiceheader.CustBalance) AS CustBalance ,paymethod.PMID FROM `invoiceheader`
            INNER JOIN transactions ON transactions.InvoiceHeader_IHID = invoiceheader.IHID
            INNER JOIN paymethod ON paymethod.PMID = transactions.paymethod_PMID
            WHERE CashCounter_CCID = ".$counter_id." AND InvStat = 1 GROUP BY transactions.paymethod_PMID;";

            $payData = $dbObj->getData($sql);
            $totalCashRefund=0;
            foreach($payData as $row)
            {
                $transferAmount=$row['transfer_amount'];
                if($row["PMID"]==1)
                {
                   $transferAmount=$row['transfer_amount'] - $row['CustBalance']; 
                }
                ?>
                <tr>
                    <td><?php echo $row['PaymethodName'];?></td>
                    <td class="td_data"><?php echo number_format($transferAmount,2,'.');?></td>
                </tr>
                <?php 
            }//foreach transaction
            ?>
            <?php 
            $sql = "SELECT SUM(return_amount) AS return_amount FROM `retrun_invoice_header` WHERE CashCounter_CCID='$counter_id' AND return_type=1 AND return_header_stat=1";

            $ReturnData = $dbObj->getData($sql);
            $totalCashRefund=0;

            foreach($ReturnData as $row)
            {
                $totalCashRefund=$totalCashRefund + $row['return_amount'];
            }//foreach transaction
                ?>
                <tr>
                    <td><?php echo "Total Cash Refund:";?></td>
                    <td class="td_data"><?php echo number_format($totalCashRefund, 2, ".", "");?></td>
                </tr>
                <?php 
            ?>

            <!-- get expenses -->
            <?php 
            $sql = "SELECT IFNULL(SUM(et.expTransactionAmount), 0) AS TotalExpenses
                                FROM expensetransactions et
                                INNER JOIN expenses e
                                    ON e.EPID = et.expense_id
                                WHERE e.EffectiveDate = '$CounterDate'
                                AND e.counter_id = '$counter_id'
                                AND e.status = 1
                                AND e.is_deleted = 0
                                AND et.paymethod_id = 1
                                AND et.expTransactionStat = 1";

            $expData = $dbObj->getData($sql);

            $counter_expenses = floatval($expData[0]['TotalExpenses']);

            if($counter_expenses > 0)
            {
                ?>
                <tr style="text-align: center; font-weight: bold;">
                    <td colspan="2">Counter Expenses</td>
                </tr>
                
                <tr>
                    <td>Total Cash Expenses</td>
                    <td class="td_data"><?php echo $counter_expenses;?></td>
                </tr>

                <?php 
            }//has expenses

            //get counter cash total
            $sql = "SELECT SUM(transactions.TransferAmount - invoiceheader.CustBalance) AS transfer_amount, paymethod.PaymethodName FROM `invoiceheader`
            INNER JOIN transactions ON transactions.InvoiceHeader_IHID = invoiceheader.IHID
            INNER JOIN paymethod ON paymethod.PMID = transactions.paymethod_PMID
            WHERE CashCounter_CCID =".$counter_id." AND paymethod.PMID = 1;";

            $cashData = $dbObj->getData($sql);
            $counter_cash = floatval($cashData[0]['transfer_amount']);
            $final_balance = $counter_cash - $counter_expenses + $counter_start_balance-$totalCashRefund;

            $counter_expenses = number_format($counter_expenses, 2, ".", "");
            $counter_cash = number_format($counter_cash, 2, ".", "");
            $final_balance = number_format($final_balance, 2, ".", "");
            ?>
            <tr style="text-align: center; font-weight: bold;">
                <td colspan="2">Counter Balance</td>
            </tr>
            <tr>
                <td>Cash Total</td>
                <td class="td_data"><?php echo number_format($counter_cash,2,'.');?></td>
            </tr>
            <tr>
                <td><span style="font-weight: bold; display: inline-block; width: 100%;">Final Balance:</span><small style="font-size: 8px;"><strong>(Cash Sales + Start Balance - Expenses - Cash Refund)</strong></small>
                </td>
                <td class="td_data"><?php echo number_format($final_balance,2,'.');?></td>
            </tr>
            <?php 
            if($final_balance!=$counter_close_balance)
            {
                $ban=$final_balance-$counter_close_balance;
                ?>
                <tr>
                    <td><span style="font-weight: bold; display: inline-block; width: 100%;">Cash Varrient: </span><small style="font-size: 8px;"><strong>(Final Balance - Close Balance)</strong></small></td>
                    <td class="td_data"><?php echo number_format($ban, 2, ".", "");?></td>
                </tr>
                <?php
            }
            ?>
        </table>

    </div>

    <hr>

    <!-------------------------- Footer -------------------------->
    <p id="p_footer">
        Powered by : Next Edge IT Solution PVT(LTD)
    </p>

    <input type="hidden" name="" value="<?=$bill_no?>" id="return">
    <!-- <canvas id="barcode"></canvas>
    <script>
        $(document).ready(function() {
            var value = $("#return").val();
            if (value) 
            {
                JsBarcode("#barcode", value, {
                    format: "CODE128",
                    lineColor: "#000",
                    width: 2,
                    height: 50,
                    displayValue: true
                });
            } else {
                alert("Please enter a value for the barcode.");
            };
        });
        setTimeout(function(){
            history.back();
        }, 200);//go back
    </script> -->
</body>
</html>