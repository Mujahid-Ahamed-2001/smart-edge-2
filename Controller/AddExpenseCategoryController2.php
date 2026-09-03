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
            $expense_category = isset($_POST['expense_category']) ? $_POST["expense_category"] : "";
            $is_default = isset($_POST['is_default']) ? 1 : 0;
            if(empty($expense_type))
            {
                $response = [
                    "status" => 0,
                    "message" => "Expense Category is required."
                ];
            }
            else if(empty($expense_category))
            {
                $response = [
                    "status" => 0,
                    "message" => "Expense Category is required."
                ];
            }
            else
            {
                
                $sql = "INSERT INTO expensecategory (expense_ctg, expense_ETID, status, is_default, created_by, created_date, modified_by, modified_date) VALUES ('$expense_category', '$expense_type', '$status', '$is_default', '$user_id', NOW(), '$user_id', NOW())";
                $insert = $dbObj->executeTransaction($sql);
                if($insert)
                {
                    $response['success'] = "Expense Category created successfully.";
                    $response = [
                        "status" => 1,
                        "message" => "Expense Category created successfully."
                    ];
                }
                else
                {
                    $response = [
                        "status" => 0,
                        "message" => "Error: Unable to create Expense Category."
                    ];
                }
            }
        }
        else if(!empty($condition) && $condition=="edit" && isset($_GET['ECID']))
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
            $expense_category = $_POST['expense_category'] ?? '';
            $expense_type = $_POST['expense_type'] ?? '';
            $status = isset($_POST['status']) ? 1 : 0;
            $is_default = isset($_POST['is_default']) ? 1 : 0;
            if(empty($expense_category))
            {
                $response = [
                    "status" => 0,
                    "message" => "Expense Category is required."
                ];
            }
            else if(empty($expense_type))
            {
                $response = [
                    "status" => 0,
                    "message" => "Expense Type is required."
                ];
            }
            else
            {
                $sql = "UPDATE expensecategory SET expense_ctg='$expense_category', expense_ETID='$expense_type', status='$status', is_default='$is_default', modified_by='$user_id', modified_date=NOW() WHERE ECID='$ECID'";
                $update = $dbObj->executeTransaction($sql);
                if($update)
                {
                    $response = [
                        "status" => 1,
                        "message" => "Expense Category updated successfully."
                    ];
                }
                else
                {
                    $response = [
                        "status" => 0,
                        "message" => "Error: Unable to update Expense Category."
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
        else if(!empty($condition) && $condition=="fetch" && isset($_GET['ECID']))
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
            $sql = "SELECT * FROM expensecategory WHERE ECID='$ECID' AND is_deleted=0";
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
                    "message" => "Error: Expense Category not found."
                ];
            }
        }
        else if(!empty($condition) && $condition=="delete" && isset($_GET['ECID']))
        {
            $ECID = $_GET['ECID'];
            $sql = "UPDATE expensecategory SET is_deleted='1', modified_by='$user_id', modified_date=NOW() WHERE ECID='$ECID'";
            $update = $dbObj->executeTransaction($sql);
            if($update)
            {
                $response = [
                    "status" => 1,
                    "message" => "Expense Category deleted successfully."
                ];
            }
            else
            {
                $response = [
                    "status" => 0,
                    "message" => "Error: Unable to delete Expense Category."
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