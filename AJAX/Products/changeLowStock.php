<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$PDID = isset($_POST["PDID"]) ? $_POST["PDID"] : 0;
$dbObj = new DBTransactions();
$sql = "UPDATE products SET is_lowStock = !is_lowStock WHERE PDID='$PDID'";
// $update = $dbObj->executeTransaction($sql);
$response=[];
if($dbObj->executeTransaction($sql))
{
    $sql = "SELECT is_lowStock FROM products WHERE PDID='$PDID'";
    $is_lowStock = $dbObj->getData($sql);
    $is_lowStock = $is_lowStock[0]["is_lowStock"];
    $response = [
        "status"=>"success",
        "message"=>"Product Low Stock Status Updated",
        "is_lowStock"=> $is_lowStock
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