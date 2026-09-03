<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$PDID = isset($_POST["PDID"]) ? (int)$_POST["PDID"] : 0;
$dbObj = new DBTransactions();
$sql = "DELETE FROM products WHERE PDID='$PDID'";
// $update = $dbObj->executeTransaction($sql);
$response=[];
if($dbObj->executeTransaction($sql))
{
    $response = [
        "status"=>"success",
        "message"=>"Product deleted successfully"
    ];
}
else
{
    $response =[
        "status"=>"error",
        "message"=>"Oops! Something went wrong."
    ];
}
echo json_encode($response);