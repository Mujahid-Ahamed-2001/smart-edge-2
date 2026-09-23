<?php
include "../Includes/includes.php";
$user_id = $_SESSION['user_id'];
$shop_id = $_SESSION['shop_id'];
if(!empty($user_id))
{
    if(isset($_GET['condition']))
    {
        $dbObj   = new DBTransactions();
        $shopObj = new Shop();
        $response = [];
        $condition = $_GET['condition'];
        if(!empty($condition) && $condition=="new")
        {
            $status = isset($_POST['status']) ? 1 : 0;
            $is_default = isset($_POST['is_default']) ? 1 : 0;
            $expense = $_POST['expense'] ?? '';
            $expense_cat = $_POST['expense_cat'] ?? '';
            $expense_amount = $_POST['expense_amount'] ?? '';
            $payment = $_POST['payment'] ?? '';
            if(empty($expense) || empty($expense_cat) || empty($expense_amount) || empty($payment))
            {
                $response = [
                    "status" => 0,
                    "message" => "Please fill all the required fields."
                ];
            }
            else
            {
                // INSERT INTO `expenses`(`EPID`, `EffectiveDate`, `ExpenseAmount`, `ExpenseReason`, `status`, `is_deleted`, `is_default`, `expensecategory_id`, `user_USID`, `shop_SHID`, `counter_id`, `created_date`, `created_by`, `modified_by`, `modified_date`) VALUES ('[value-1]','[value-2]','[value-3]','[value-4]','[value-5]','[value-6]','[value-7]','[value-8]','[value-9]','[value-10]','[value-11]','[value-12]','[value-13]','[value-14]','[value-15]')
                $CashCounter_CCID=0;
                if($shopObj->hascounter($shop_id)==1)
                {
                    $current_date = date("Y-m-d");
                    $sql = "SELECT CCID FROM cashcounter WHERE user_USID='$user_id' AND shop_SHID='$shop_id' AND CounterStat = 1 AND CounterDate = '$current_date' ORDER BY CCID DESC LIMIT 1;";
                    $counterData = $dbObj->getData($sql);
                    if(count($counterData)==0)
                    {
                        $CashCounter_CCID = 0;
                    }
                    else
                    {
                        $CashCounter_CCID = $counterData[0]['CCID'];
                    }
                    
                }
                else
                {
                    
                }
                $table ="expenses";
                $data = [
                    "EffectiveDate"=> date("Y-m-d"),
                    "ExpenseAmount"=> $expense_amount,
                    "ExpenseReason"=> $expense,
                    "status"=> $status,
                    "is_deleted"=> 0,
                    "is_default"=> $is_default,
                    "expensecategory_id"=> $expense_cat,
                    "user_USID"=> $user_id,
                    "shop_SHID"=> $shop_id,
                    "counter_id"=> $CashCounter_CCID,
                    "created_date"=> date("Y-m-d"),
                    "created_by"=> $user_id,
                    "modified_by"=> $user_id,
                    "modified_date"=> date("Y-m-d"),

                ];
                $insert = $dbObj->insertAndGetId($table, $data);
                if($insert)
                {
                    // INSERT INTO `expensetransactions`(`ETID`, `expTransactionAmount`, `expTransactionStat`, `expense_id`, `paymethod_id`) VALUES ('[value-1]','[value-2]','[value-3]','[value-4]','[value-5]')
                    $table2 ="expensetransactions";
                    $data2 = [
                        "expTransactionAmount" => $expense_amount,
                        "expTransactionStat" => 1,
                        "expense_id" => $insert,
                        "paymethod_id" => $payment,
                    ];
                    $insert2 = $dbObj->insertAndGetId($table2, $data2);
                    if($insert2)
                    {
                        $response['success'] = "Expense created successfully.";
                        $response = [
                            "status" => 1,
                            "message" => "Expense created successfully."
                        ];    
                    }
                    else
                    {
                        $response = [
                            "status" => 0,
                            "message" => "Error: Unable to create Expense transactions."
                        ];
                    }
                }
                else
                {
                    $response = [
                        "status" => 0,
                        "message" => "Error: Unable to create Expense."
                    ];
                }
            }
        }
        else if(!empty($condition) && $condition=="edit" && isset($_GET['EPID']))
        {
            $EPID = $_GET['EPID'];
            if(empty($EPID)) {
                $response = [
                    "status" => 0,
                    "message" => "Error: Expense ID is missing."
                ];
                echo json_encode($response);
                exit;
            }
            $status = isset($_POST['status']) ? 1 : 0;
            $is_default = isset($_POST['is_default']) ? 1 : 0;
            $expense = $_POST['expense'] ?? '';
            $expense_cat = $_POST['expense_cat'] ?? '';
            $expense_amount = $_POST['expense_amount'] ?? '';
            $payment = $_POST['payment'] ?? '';
            if(empty($expense) || empty($expense_cat) || empty($expense_amount) || empty($payment))
            {
                $response = [
                    "status" => 0,
                    "message" => "Please fill all the required fields."
                ];
            }
            else
            {
                $sql = "UPDATE expenses SET ExpenseAmount='$expense_amount', ExpenseReason='$expense', status='$status', is_default='$is_default', expensecategory_id='$expense_cat', modified_by='$user_id', modified_date=NOW() WHERE EPID='$EPID'";
                $update = $dbObj->executeTransaction($sql);
                $delete = "DELETE FROM expensetransactions WHERE `expensetransactions`.`expense_id` = '$EPID' ";
                $delete = $dbObj->executeTransaction($delete);
                $table2 ="expensetransactions";
                $data2 = [
                    "expTransactionAmount" => $expense_amount,
                    "expTransactionStat" => 1,
                    "expense_id" => $EPID,
                    "paymethod_id" => $payment,
                ];
                $insert2 = $dbObj->insertAndGetId($table2, $data2);
                if($update)
                {
                    if($insert2)
                    {
                        $response = [
                            "status" => 1,
                            "message" => "Expense updated successfully."
                        ];    
                    }
                    else
                    {
                        $response = [
                            "status" => 0,
                            "message" => "Error: Unable to update Expense."
                        ];
                    }
                }
                else
                {
                    $response = [
                        "status" => 0,
                        "message" => "Error: Unable to update Expense."
                    ];
                }
            }
        }
        else if(!empty($condition) && $condition=="update_status" && isset($_GET['ECID']))
        {
            $ECID = $_GET['ECID'];
            if(empty($ECID)) {
                $response = [
                    "status" => 0,
                    "message" => "Error: Expense Category ID is missing."
                ];
                echo json_encode($response);
                exit;
            }
            $sql = "UPDATE expensecategory SET status=!status, modified_by='$user_id', modified_date=NOW() WHERE ECID='$ECID'";
            $update = $dbObj->executeTransaction($sql);
            if($update)
            {
                $sql2 ="SELECT status FROM expensecategory WHERE ECID='$ECID'";
                $data = $dbObj->getData($sql2);
                $status = $data[0]['status'] ?? 0;
                $response = [
                    "status" => 1,
                    "message" => "Status updated successfully.",
                    "new_status" => $status
                ];
            }
            else
            {
                $response = [
                    "status" => 0,
                    "message" => "Error: Unable to update Status."
                ];
            }
        }
        else if(!empty($condition) && $condition=="update_default" && isset($_GET['ECID']))
        {
            $ECID = $_GET['ECID'];
            if(empty($ECID)) {
                $response = [
                    "status" => 0,
                    "message" => "Error: Expense Category ID is missing."
                ];
                echo json_encode($response);
                exit;
            }
            $sql = "UPDATE expensecategory SET is_default=!is_default, modified_by='$user_id', modified_date=NOW() WHERE ECID='$ECID'";
            $update = $dbObj->executeTransaction($sql);
            if($update)
            {
                $sql2 ="SELECT is_default FROM expensecategory WHERE ECID='$ECID'";
                $data = $dbObj->getData($sql2);
                $is_default = $data[0]['is_default'] ?? 0;
                $response = [
                    "status" => 1,
                    "message" => "Default status updated successfully.",
                    "new_is_default" => $is_default
                ];
            }
            else
            {
                $response = [
                    "status" => 0,
                    "message" => "Error: Unable to update Default Status."
                ];
            }
        }
        else if(!empty($condition) && $condition=="fetch_type" )
        {
            $sql = "SELECT * FROM expensetype WHERE is_deleted=0 AND status=1";
            $data = $dbObj->getData($sql);
            if(!empty($data))
            {
                $response = [
                    "status" => 1,
                    "data" => $data
                ];
            }
            else
            {
                $response = [
                    "status" => 0,
                    "message" => "Error: Expense Types not found."
                ];
            }
        }
        else if(!empty($condition) && $condition=="fetch" && isset($_GET['EPID']))
        {
            $EPID = $_GET['EPID'];
            if(empty($EPID)) {
                $response = [
                    "status" => 0,
                    "message" => "Error: Expense ID is missing."
                ];
                echo json_encode($response);
                exit;
            }
            $sql = "SELECT * FROM expenses e
            INNER JOIN expensetransactions et ON et.expense_id=e.EPID
            WHERE e.EPID='$EPID' AND is_deleted=0";
            $data = $dbObj->getData($sql);
            if(!empty($data))
            {
                $response = [
                    "status" => 1,
                    "data" => $data[0]
                ];
            }
            else
            {
                $response = [
                    "status" => 0,
                    "message" => "Error: Expense not found."
                ];
            }
        }
        else if(!empty($condition) && $condition == "delete" && isset($_GET['EPID']))
        {
            $EPID = (int)$_GET['EPID'];
            if ($EPID <= 0) {
                $response = [
                    "status" => 0,
                    "message" => "Invalid Expense ID."
                ];
                echo json_encode($response);
                exit;
            }

            /*
            |--------------------------------------------------------------------------
            | Check Expense
            |--------------------------------------------------------------------------
            */

            $sql = "SELECT EPID, is_default
                FROM expenses
                WHERE EPID = '$EPID'
                AND shop_SHID = '$shop_id'
                AND is_deleted = 0
                LIMIT 1
            ";

            $expenseData = $dbObj->getData($sql);

            if (empty($expenseData)) {

                $response = [
                    "status" => 0,
                    "message" => "Expense not found."
                ];

                echo json_encode($response);
                exit;
            }


            /*
            |--------------------------------------------------------------------------
            | Optional: Protect Default Expense
            |--------------------------------------------------------------------------
            */

            if (
                isset($expenseData[0]['is_default']) &&
                (int)$expenseData[0]['is_default'] === 1
            ) {

                $response = [
                    "status" => 0,
                    "message" => "Default expenses cannot be deleted."
                ];

                echo json_encode($response);
                exit;
            }


            /*
            |--------------------------------------------------------------------------
            | Soft Delete Expense
            |--------------------------------------------------------------------------
            */

            $sql = "UPDATE expenses
                SET
                    is_deleted = 1,
                    status = 0,
                    modified_by = '$user_id',
                    modified_date = NOW()
                WHERE EPID = '$EPID'
                AND shop_SHID = '$shop_id'
            ";

            $update = $dbObj->executeTransaction($sql);


            if ($update) {

                /*
                |--------------------------------------------------------------------------
                | Disable Related Expense Transactions
                |--------------------------------------------------------------------------
                */

                $sqlTransaction = "UPDATE expensetransactions
                    SET expTransactionStat = 0
                    WHERE expense_id = '$EPID'
                ";

                $dbObj->executeTransaction($sqlTransaction);


                $response = [
                    "status" => 1,
                    "message" => "Expense deleted successfully."
                ];

            } else {

                $response = [
                    "status" => 0,
                    "message" => "Unable to delete expense."
                ];
            }
        }
        else
        {
            $response = [
                "status" => 0,
                "message" => "Invalid condition."
            ];
        }
        echo json_encode($response);
    }
    else
    {
        $response = [
            "status" => 0,
            "message" => "Error: Condition parameter is missing."
        ];
        echo json_encode($response);
    }   
}

?>