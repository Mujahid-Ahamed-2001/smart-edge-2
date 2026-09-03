<?php
session_start(); 
include "../../Includes/config.php";
include "../../Model/transfer_class.php";
include "../../Model/DB_Class.php";
include "../../Model/shop_class.php";

$shop_id = $_SESSION['shop_id'];
$shopObj = new Shop();
$dbObj = new DBTransactions();

$product_id = $_GET['product_id'];
$batch_id = $_GET['batch_id'];

$sql = "SELECT * FROM pricehistory
INNER JOIN inventory ON inventory.INID = pricehistory.Inventory_INID
WHERE ProductID = ".$product_id." AND pricehistory.BatchID= '".$batch_id."' AND inventory.shop_SHID = ".$shop_id.";";

$batchData = $dbObj->getData($sql);
$inventory_id = $batchData[0]['INID'];

//current date
date_default_timezone_set("Asia/Colombo");
$current_date = date("Y-m-d");

$transfer_qty = $_GET['transfer_qty'];
$received_qty = $transfer_qty; //assume transfered qty has received
$purchase_price = $_GET['purchase_price'];
$selling_price = $_GET['selling_price'];
$mnf_date = empty($_GET['mnf_date']) ? $current_date : $_GET['mnf_date'];
$exp_date = empty($_GET['exp_date']) ? $current_date : $_GET['exp_date'];
$total_amount = $_GET['total_amount'];

$variation_id = empty($_GET['variation_id']) ? 0 : $_GET['variation_id'];
$rack_id = 1;
$transfer_stat = 0;
$header_id = $_GET['header_id'];

//TransferQty, UnitPurchasePrice, UnitSellingPrice, MnfDate, ExpDate, TransferTotalAmount, products_PDID, VariationID, RackID, TransferStat, TransferHeader_THID

$tranObj = new Transfer();
if($tranObj->checktransferID($inventory_id,$header_id)==true)
{
    $message=1;
    $tranObj->setTransferDetail($transfer_qty, $received_qty, $purchase_price, $selling_price, $mnf_date, $exp_date, $total_amount, $inventory_id, $product_id, $variation_id, $rack_id, $transfer_stat, $header_id, $batch_id);
    $return=1;
}
else
{
    $message=0;
}
echo $message;
