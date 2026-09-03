<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$PDID = isset($_POST["PDID"]) ? $_POST["PDID"] : 0;
$dbObj = new DBTransactions();
$sql = "UPDATE products SET ProductStat = !ProductStat WHERE PDID='$PDID'";
// $update = $dbObj->executeTransaction($sql);
$response=[];
if($dbObj->executeTransaction($sql))
{
    $sql = "SELECT ProductStat FROM products WHERE PDID='$PDID'";
    $ProductStat = $dbObj->getData($sql);
    $ProductStat = $ProductStat[0]["ProductStat"];
    $response = [
        "status"=>"success",
        "message"=>"Product status updated",
        "ProductStat"=> $ProductStat
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