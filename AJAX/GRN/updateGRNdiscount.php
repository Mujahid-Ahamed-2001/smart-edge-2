<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
$dbObj = new DBTransactions();
$GHID = isset($_POST["GHID"]) && !empty($_POST["GHID"]) ? $_POST["GHID"] : "";
$PurchDiscType = isset($_POST["PurchDiscType"]) && !empty($_POST["PurchDiscType"]) ? $_POST["PurchDiscType"] : 2;
$PurchDisc = isset($_POST["PurchDisc"]) && !empty($_POST["PurchDisc"]) ? $_POST["PurchDisc"] : "0.00";
$TotalDisc = isset($_POST["TotalDisc"]) && !empty($_POST["TotalDisc"]) ? $_POST["TotalDisc"] : "0.00";
$ItemCount = isset($_POST["ItemCount"]) && !empty($_POST["ItemCount"]) ? $_POST["ItemCount"] : "0";
$TotalPurchasePrice = isset($_POST["TotalPurchasePrice"]) && !empty($_POST["TotalPurchasePrice"]) ? $_POST["TotalPurchasePrice"] : "0.00";
$TotalSellPrice = isset($_POST["TotalSellPrice"]) && !empty($_POST["TotalSellPrice"]) ? $_POST["TotalSellPrice"] : "0.00";
$response = [];

if(!empty($GHID))
{
    $sql="UPDATE grnheader SET `ItemCount`='$ItemCount',`TotalPurchasePrice`='$TotalPurchasePrice',`TotalSellPrice`='$TotalSellPrice', PurchDiscType='$PurchDiscType', PurchDisc='$PurchDisc', TotalDisc='$TotalDisc' WHERE GHID = '$GHID'";
    if($dbObj->executeTransaction($sql))
    {
        // $response = ["success" =>"Deleted successfully"];
    }
    else
    {
        $response = ["error" =>"Oops! Something went wrong"];
    }
}
else
{
    $response = ["error" =>"Invalid GHID"];
}

echo json_encode($response);