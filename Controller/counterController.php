<?php 
include "../Includes/includes.php";

$counterObj = new Counter();
$dbObj = new DBTransactions();
$user_id = $_SESSION['user_id'];
$shop_id = $_SESSION['shop_id'];

if(isset($_GET["condition"]))
{
    $condition=$_GET["condition"];
    $result = [];
    if($condition=="startCounter")
    {
        $startAmount=isset($_POST['startAmount']) && !empty($_POST['startAmount']) ? $_POST['startAmount'] : 0;
        if($startAmount == "" || $startAmount == null)
        {
            $result = [
                "status" => 0,
                "message" => "Please enter a valid start amount."
            ];
            echo json_encode($result);
            exit;
        }
        else
        {
            date_default_timezone_set("Asia/Colombo");
            $counter_date = date("Y-m-d h:i:s");
            $end_amount = $startAmount;
            $counter_stat = 1;
            $update = $counterObj->setCounter($counter_date, $startAmount, $end_amount, $counter_date, $counter_date, $counter_stat, $user_id, $shop_id);
            if($update == true)
            {
                $result = [
                    "status" => 1,
                    "message" => "Counter started successfully."
                ];
            }
            else
            {
                $result = [
                    "status" => 0,
                    "message" => "Failed to start counter. Please try again."
                ];
            }
            
        }//valid start amount
    }
    else if($condition=="fetch")
    {
        $CCID = isset($_GET['CCID']) ? $_GET['CCID'] : 0;
        if($CCID == 0)
        {
            $result = [
                "status" => 0,
                "message" => "Invalid Cashcounter ID."
            ];
            echo json_encode($result);
            exit;
        }
        else
        {
            $sql = "SELECT * FROM cashcounter WHERE CCID = ?;";
            $data = $dbObj->getMultipleData($sql, [$CCID]);
            if(!empty($data))
            {
                $CounterDate = $data[0]['CounterDate'];
                $StartBalance = $data[0]['StartBalance'];
                $sql_revenue ="SELECT IFNULL(SUM(t.TransferAmount), 0) AS TotalCashAmount
                            FROM transactions t
                            INNER JOIN invoiceheader h
                                ON h.IHID = t.InvoiceHeader_IHID
                            WHERE h.CashCounter_CCID = '$CCID'
                            AND h.InvStat = 1
                            AND t.paymethod_PMID = 1
                            AND t.TransactionStat = 1
                            AND EXISTS (
                                SELECT 1
                                FROM invoicedetails d
                                WHERE d.InvoiceHeader_IHID = h.IHID
                            );";
                $totalCashAmount_data = $dbObj->getData($sql_revenue);
                $TotalCashAmount = isset($totalCashAmount_data[0]['TotalCashAmount']) ? $totalCashAmount_data[0]['TotalCashAmount'] : 0;
                $totalCashAmount = floatval($TotalCashAmount);
                $sql_expense = "SELECT IFNULL(SUM(et.expTransactionAmount), 0) AS TotalExpenses
                                FROM expensetransactions et
                                INNER JOIN expenses e
                                    ON e.EPID = et.expense_id
                                WHERE e.EffectiveDate = '$CounterDate'
                                AND e.counter_id = '$CCID'
                                AND e.status = 1
                                AND e.is_deleted = 0
                                AND et.paymethod_id = 1
                                AND et.expTransactionStat = 1;";
                $totalExpenses_data = $dbObj->getData($sql_expense);
                $TotalExpenses = isset($totalExpenses_data[0]['TotalExpenses']) ? $totalExpenses_data[0]['TotalExpenses'] : 0;
                $totalExpenses = floatval($TotalExpenses);
                $systemBalance = $StartBalance + $totalCashAmount - $totalExpenses;
                $result = [
                    "status" => 1,
                    "data" => array_merge($data[0], ['TotalCashAmount' => $totalCashAmount, 'TotalCashExpenses' => $totalExpenses, 'ActualBalance' => $systemBalance])
                ];
            }
            else
            {
                $result = [
                    "status" => 0,
                    "message" => "Cashcounter not found."
                ];
            }
        }//valid CCID
    }//fetch
    else if($condition=="closeCounter")
    {
        $CCID = isset($_GET['counterid']) ? $_GET['counterid'] : 0;
        $endAmount = isset($_POST['endAmount']) ? $_POST['endAmount'] : 0;
        if($CCID == 0 || $endAmount == "" || $endAmount == null)
        {
            $result = [
                "status" => 0,
                "message" => "Invalid Cashcounter ID or End Amount."
            ];
            echo json_encode($result);
            exit;
        }
        else
        {
            date_default_timezone_set("Asia/Colombo");
            $counter_date = date("Y-m-d h:i:s");
            $counter_stat = 0;
            $update = $counterObj->closeCounter($endAmount, $counter_date, $counter_stat, $CCID);
            if($update == true)
            {
                $result = [
                    "status" => 1,
                    "message" => "Counter closed successfully."
                ];
            }
            else
            {
                $result = [
                    "status" => 0,
                    "message" => "Failed to close counter. Please try again."
                ];
            }
        }//valid CCID and end amount
    }//closeCounter
    echo json_encode($result);
    exit;
}
if(isset($_POST['btn_start_new_counter']))
{
    //check active counters
    $sql = "SELECT * FROM cashcounter WHERE user_USID=".$user_id." AND shop_SHID=".$shop_id." AND CounterStat = 1;";
    
    $dbData = $dbObj->getData($sql);

    if(!empty($dbData))
    {
        foreach($dbData as $row)
        {
            $counter_id = $row['CCID'];

            $counterObj->closeAllUserCounters($counter_id);
        }//foreach
    }//has cash counters

    //CCID, CounterDate, StartBalance, EndBalance, CounterStartTime, CounterEndTime, CounterStat, user_USID, shop_SHID
    //get current date time
    date_default_timezone_set("Asia/Colombo");
    $counter_date = date("Y-m-d h:i:s");

    $start_amount = $_POST['start_amount'];
    $end_amount = $start_amount;

    $counter_stat = 1;

    $user_id = $_SESSION['user_id'];
    $shop_id = $_SESSION['shop_id'];

    $update = $counterObj->setCounter($counter_date, $start_amount, $end_amount, $counter_date, $counter_date, $counter_stat, $user_id, $shop_id);

    if ($update==true) 
    {
        $_SESSION['status']=1;
        header("Location:../Public/gui-pos.php");
    }
    else
    {
        $_SESSION['status']=4;

        header("Location:../Public/home.php");
    }
    
    echo "Counter created Successfully, Goto sale... ";
}//create counter

if (isset($_POST['btn_start_counter'])) {

    //get values for denomination
    $rs_5000 = empty($_POST['rs_5000']) ? 0 : $_POST['rs_5000'];
    $rs_1000 = empty($_POST['rs_1000']) ? 0 : $_POST['rs_1000'];
    $rs_500 = empty($_POST['rs_500']) ? 0 : $_POST['rs_500'];
    $rs_100 = empty($_POST['rs_100']) ? 0 : $_POST['rs_100'];
    $rs_50 = empty($_POST['rs_50']) ? 0 : $_POST['rs_50'];
    $rs_20 = empty($_POST['rs_20']) ? 0 : $_POST['rs_20'];
    $rs_10 = empty($_POST['rs_10']) ? 0 : $_POST['rs_10'];
    $rs_5 = empty($_POST['rs_5']) ? 0 : $_POST['rs_5'];
    $rs_2 = empty($_POST['rs_2']) ? 0 : $_POST['rs_2'];
    $rs_1 = empty($_POST['rs_1']) ? 0 : $_POST['rs_1'];

    $dbObj = new DBTransactions();
    //get next counter id
    $sql = "SELECT max(CCID) as max_counter_id FROM `cashcounter`;";
    $countData = $dbObj->getData($sql);

    $max_counter_id = floatval($countData[0]['max_counter_id']);
    $next_counter_id = $max_counter_id + 1;

    date_default_timezone_set("Asia/Colombo");
    $counter_date = date("Y-m-d h:i:s");
    $counter_type = "counter_start";

    //add denomination
    $counterObj = new Counter();

    $counterObj->setDenomination($counter_date, $counter_type, $rs_5000, $rs_1000, $rs_500, $rs_100, $rs_50, $rs_20, $rs_10, $rs_5, $rs_2, $rs_1,$shop_id, $user_id, $next_counter_id);    

    $start_amount = $_POST['start_amount'];
    $actual_amount = $start_amount;

    $counter_stat = 1;

    $update = $counterObj->setCounter($counter_date, $start_amount, $actual_amount, $counter_date, $counter_date, $counter_stat, $user_id, $shop_id);

    //check invoice type
    $shopObj = new Shop();

    if($shopObj->hasRetailShop($shop_id))
    {
        header("Location: ../Public/gui-pos.php");
    }//has retail invoice
    else
    {
        header("Location: ../Public/wholesale-invoice.php");
    }//has wholesale

    // if($update == true)
    // {
    //     $_SESSION['status']=1;
    //     header("Location:../Public/home.php");
    // }
    // else
    // {
    //     echo "Counter created Successfully, Goto sale... ";
    // }    
}//start counter


if(isset($_POST['btn_end_counter']))
{
    //get values for denomination
    $rs_5000_c = empty($_POST['rs_5000_c']) ? 0 : $_POST['rs_5000_c'];
    $rs_1000_c = empty($_POST['rs_1000_c']) ? 0 : $_POST['rs_1000_c'];
    $rs_500_c = empty($_POST['rs_500_c']) ? 0 : $_POST['rs_500_c'];
    $rs_100_c = empty($_POST['rs_100_c']) ? 0 : $_POST['rs_100_c'];
    $rs_50_c = empty($_POST['rs_50_c']) ? 0 : $_POST['rs_50_c'];
    $rs_20_c = empty($_POST['rs_20_c']) ? 0 : $_POST['rs_20_c'];
    $rs_10_c = empty($_POST['rs_10_c']) ? 0 : $_POST['rs_10_c'];
    $rs_5_c = empty($_POST['rs_5_c']) ? 0 : $_POST['rs_5_c'];
    $rs_2_c = empty($_POST['rs_2_c']) ? 0 : $_POST['rs_2_c'];
    $rs_1_c = empty($_POST['rs_1_c']) ? 0 : $_POST['rs_1_c'];

    $end_amount = $_POST['end_amount'];

    //get current date time
    date_default_timezone_set("Asia/Colombo");
    $counter_date = date("Y-m-d h:i:s");
    $counter_type = "counter_close";

    $counterData = $counterObj->getOneCounterByUser($user_id);
    $counter_id = $counterData[0]['CCID'];

    //add denomination
    $counterObj = new Counter();

    $counterObj->setDenomination($counter_date, $counter_type, $rs_5000_c, $rs_1000_c, $rs_500_c, $rs_100_c, $rs_50_c, $rs_20_c, $rs_10_c, $rs_5_c, $rs_2_c, $rs_1_c,$shop_id, $user_id, $counter_id);

    $counterObj = new Counter();
    $state=0;
    $update=$counterObj->closeCounter($end_amount, $counter_date, $state, $counter_id);

    header("Location: ../Receipts/counter_close.php?counter_id=" . $counter_id);

    // if ($update==true) 
    // {
    //     $_SESSION['status']=2;
    //     // echo"done";
    //     header("Location:../Public/home.php");
    // }
    // else
    // {
    //     $_SESSION['status']=3;
    //     // echo"no";
    //     header("Location:../Public/home.php");
    // }
}//close counter