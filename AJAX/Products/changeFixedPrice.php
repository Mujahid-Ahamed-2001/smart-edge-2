<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$PDID = isset($_POST["PDID"]) ? $_POST["PDID"] : 0;
$dbObj = new DBTransactions();
$sql = "UPDATE products SET is_fixedPrice = !is_fixedPrice WHERE PDID='$PDID'";
// $update = $dbObj->executeTransaction($sql);
$response=[];
if($dbObj->executeTransaction($sql))
{
    $sql = "SELECT is_fixedPrice FROM products WHERE PDID='$PDID'";
    $is_fixedPrice = $dbObj->getData($sql);
    $is_fixedPrice = $is_fixedPrice[0]["is_fixedPrice"];
    $response = [
        "status"=>"success",
        "message"=>"Product fixed price updated",
        "is_fixedPrice"=> $is_fixedPrice
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