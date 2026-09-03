<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$quote_id = isset($_POST["quote_id"]) ? $_POST["quote_id"] : "";
$status = isset($_POST["status"]) ? $_POST["status"] : "";
$response = [];
$dbObj = new DBTransactions();
if(!empty($quote_id) && !empty($status))
{
    $sql ="UPDATE quotations SET status='$status' WHERE id='$quote_id'";
    $update = $dbObj->executeTransaction($sql);
    if($update)
    {
        $response =[
            "status" => "success",
            "message" =>"Quotation Status Updated Successfully"
        ];
    }
    else
    {
        $response =[
            "status" => "error",
            "message" =>"Oops! Something went wrong."
        ];
    }
}
else
{
    $response =[
        "status" => "error",
        "message" =>"Missing information"
    ];
}
echo json_encode($response);