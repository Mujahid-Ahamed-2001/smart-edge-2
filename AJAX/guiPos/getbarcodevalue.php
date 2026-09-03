<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$shop_id = $_SESSION['shop_id'];
$dbObj = new DBTransactions();
$sql = "SELECT * FROM shop
INNER JOIN company ON company.CMID = shop.Company_CMID
WHERE SHID = ".$shop_id.";";
$shopData = $dbObj->getData($sql);
$multi_category = $shopData[0]['is_multicategory'];
$com_id=$shopData[0]['CMID'];
$barcodevalue=$_GET['barcodevalue'];
if($barcodevalue!="")
{
    $sql="SELECT * FROM products WHERE Barcode='$barcodevalue' AND shop_SHID='$shop_id'";
    if($multi_category==1)
    {
        $sql="SELECT * FROM products p
        INNER JOIN shop s ON s.SHID=p.shop_SHID
        WHERE p.Barcode='$barcodevalue' AND s.Company_CMID='$com_id'";
    }
    $dbObj = new DBTransactions();
    $itemData = $dbObj->getData($sql);
    echo json_encode($itemData);
}

