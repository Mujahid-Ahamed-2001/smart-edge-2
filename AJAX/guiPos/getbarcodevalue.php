<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$shop_id = $_SESSION['shop_id'];
$dbObj = new DBTransactions();
$barcodevalue=$_GET['barcodevalue'];
if($barcodevalue!="")
{
    $sql="SELECT * FROM products WHERE Barcode='$barcodevalue' AND ProductStat=1";
    $dbObj = new DBTransactions();
    $itemData = $dbObj->getData($sql);
    echo json_encode($itemData);
}

