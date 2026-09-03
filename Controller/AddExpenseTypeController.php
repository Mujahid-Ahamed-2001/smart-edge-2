<?php
include "../Includes/includes.php";
$user_id = $_SESSION['user_id'];
if(!empty($user_id))
{
    if(isset($_GET['condition']))
    {
        $dbObj   = new DBTransactions();
        $response = [];
        $condition = $_GET['condition'];
        if(!empty($condition) && $condition=="new")
        {
            $expense_type = $_POST['expense_type'] ?? '';
            $status = isset($_POST['status']) ? 1 : 0;
            $is_default = isset($_POST['is_default']) ? 1 : 0;
            if(empty($expense_type))
            {
                $response = [
                    "status" => 0,
                    "message" => "Expense Type is required."
                ];
            }
            else
            {
                
                $sql = "INSERT INTO expensetype (expense_type, status, is_default, created_by, created_date, modified_by, modified_date) VALUES ('$expense_type', '$status', '$is_default', '$user_id', NOW(), '$user_id', NOW())";
                $insert = $dbObj->executeTransaction($sql);
                if($insert)
                {
                    $response['success'] = "Expense Type created successfully.";
                    $response = [
                        "status" => 1,
                        "message" => "Expense Type created successfully."
                    ];
                }
                else
                {
                    $response = [
                        "status" => 0,
                        "message" => "Error: Unable to create Expense Type."
                    ];
                }
            }
        }
        else if(!empty($condition) && $condition=="edit" && isset($_GET['ETID']))
        {
            $ETID = $_GET['ETID'];
            if(empty($ETID)) {
                $response = [
                    "status" => 0,
                    "message" => "Error: Expense Type ID is missing."
                ];
                echo json_encode($response);
                exit;
            }
            $expense_type = $_POST['expense_type'] ?? '';
            $status = isset($_POST['status']) ? 1 : 0;
            $is_default = isset($_POST['is_default']) ? 1 : 0;
            if(empty($expense_type))
            {
                $response = [
                    "status" => 0,
                    "message" => "Expense Type is required."
                ];
            }
            else
            {
                $sql = "UPDATE expensetype SET expense_type='$expense_type', status='$status', is_default='$is_default', modified_by='$user_id', modified_date=NOW() WHERE ETID='$ETID'";
                $update = $dbObj->executeTransaction($sql);
                if($update)
                {
                    $response = [
                        "status" => 1,
                        "message" => "Expense Type updated successfully."
                    ];
                }
                else
                {
                    $response = [
                        "status" => 0,
                        "message" => "Error: Unable to update Expense Type."
                    ];
                }
            }
        }
        else if(!empty($condition) && $condition=="update_status" && isset($_GET['ETID']))
        {
            $ETID = $_GET['ETID'];
            if(empty($ETID)) {
                $response = [
                    "status" => 0,
                    "message" => "Error: Expense Type ID is missing."
                ];
                echo json_encode($response);
                exit;
            }
            $sql = "UPDATE expensetype SET status=!status, modified_by='$user_id', modified_date=NOW() WHERE ETID='$ETID'";
            $update = $dbObj->executeTransaction($sql);
            if($update)
            {
                $sql2 ="SELECT status FROM expensetype WHERE ETID='$ETID'";
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
        else if(!empty($condition) && $condition=="update_default" && isset($_GET['ETID']))
        {
            $ETID = $_GET['ETID'];
            if(empty($ETID)) {
                $response = [
                    "status" => 0,
                    "message" => "Error: Expense Type ID is missing."
                ];
                echo json_encode($response);
                exit;
            }
            $sql = "UPDATE expensetype SET is_default=!is_default, modified_by='$user_id', modified_date=NOW() WHERE ETID='$ETID'";
            $update = $dbObj->executeTransaction($sql);
            if($update)
            {
                $sql2 ="SELECT is_default FROM expensetype WHERE ETID='$ETID'";
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
        else if(!empty($condition) && $condition=="fetch" && isset($_GET['ETID']))
        {
            $ETID = $_GET['ETID'];
            if(empty($ETID)) {
                $response = [
                    "status" => 0,
                    "message" => "Error: Expense Type ID is missing."
                ];
                echo json_encode($response);
                exit;
            }
            $sql = "SELECT * FROM expensetype WHERE ETID='$ETID' AND is_deleted=0";
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
                    "message" => "Error: Expense Type not found."
                ];
            }
        }
        else if(!empty($condition) && $condition=="delete" && isset($_GET['ETID']))
        {
            $ETID = $_GET['ETID'];
            $sql = "UPDATE expensetype SET is_deleted='1', modified_by='$user_id', modified_date=NOW() WHERE ETID='$ETID'";
            $update = $dbObj->executeTransaction($sql);
            if($update)
            {
                $response = [
                    "status" => 1,
                    "message" => "Expense Type deleted successfully."
                ];
            }
            else
            {
                $response = [
                    "status" => 0,
                    "message" => "Error: Unable to delete Expense Type."
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

    // Insert
    else if (isset($_POST['btn_save_type'])) {
        $expense_type = $_POST['expense_type'];

        $ExpenseTypeObj = new AddExpenseTypeModels();
        if ($ExpenseTypeObj->setTypeModels($expense_type)) {
            echo "Type Added Successfully";
        } else {
            echo "Error: Unable to add the type";
        }
    }

    // Delete
    else if (isset($_POST['delete_type'])) {
        $ETID = $_POST['ETID'];

        if (empty($ETID)) {
            die("Error: Type ID is missing.");
        }

        $TypeObj = new AddExpenseTypeModels();
        $check=$TypeObj->checktypes($ETID);
        $count=count($check);
        if($count==0)
        {
            $result = $TypeObj->deletetypes($ETID);
            if ($result) {
                echo "Type Deleted Successfully";
            } else {
                echo "Error: Couldn't delete the type";
            }
        }
        else
        {
            echo "Type Cannot Be Deleted As There Are Categories Assigned";
        }
        
        exit();
    }    
}

?>