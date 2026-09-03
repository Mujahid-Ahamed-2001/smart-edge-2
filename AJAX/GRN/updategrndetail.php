<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
$dbObj = new DBTransactions();

$GDID = isset($_POST["GDID"]) && !empty($_POST["GDID"]) ? $_POST["GDID"] : 0;
$qty = isset($_POST["qty"]) && !empty($_POST["qty"]) ? $_POST["qty"] : 0;
$purchasePrice = isset($_POST["purchasePrice"]) && !empty($_POST["purchasePrice"]) ? $_POST["purchasePrice"] : 0;
$SellingPrice = isset($_POST["SellingPrice"]) && !empty($_POST["SellingPrice"]) ? $_POST["SellingPrice"] : 0;
$mfgDate = isset($_POST["mfgDate"]) && !empty($_POST["mfgDate"]) ? $_POST["mfgDate"] : "";
$expDate = isset($_POST["expDate"]) && !empty($_POST["expDate"]) ? $_POST["expDate"] : "";
$response = [];
if(!empty($GDID))
{
    $TotalPurchasePrice = $purchasePrice * $qty;
    $TotalSellPrice = $SellingPrice * $qty;
    $sql="UPDATE `grndetails` SET `CurrentQty`='$qty',`UnitPurchasePrice`='$purchasePrice',`UnitLabelPrice`='$SellingPrice',`UnitSellPrice`='$SellingPrice',`TotalPurchasePrice`='$TotalPurchasePrice',`TotalSellPrice`='$TotalSellPrice',`MnfDate`='$mfgDate',`ExpDate`='$expDate' WHERE GDID='$GDID'";
    if($dbObj->executeTransaction($sql))
    {
        $response = ["success" =>"GRN detail updated successfully"];
    }
    else
    {
        $response = ["error" =>"Oops!, Something went wrong"];
    }
}
else
{
    $response = ["error" =>"Invalid GDID"];
}

echo json_encode($response);