<?php 
include "../../Includes/config.php";
include "../../Model/transfer_class.php";

//get current date
date_default_timezone_set("Asia/Colombo");
$current_date = date("Y-m-d");

$inventory_id = $_GET['inventory_id'];
$variation_id = empty($_GET['variation_id']) ? 0 : $_GET['variation_id'];
$transfer_detail_id = $_GET['transfer_detail_id'];
$transfer_qty = $_GET['transfer_qty'];
$receive_qty = $_GET['receive_qty'];
$purchase_price = $_GET['purchase_price'];
$selling_price = $_GET['selling_price'];
$mnf_date = empty($_GET['mnf_date']) ? $current_date : $_GET['mnf_date'];
$exp_date = empty($_GET['exp_date']) ? $current_date : $_GET['exp_date'];
$total_amount = $_GET['total_amount'];
$product_id = $_GET['product_id'];
$batch_id = $_GET['batch_id'];
$rack_id = 1;

//TransferQty, UnitPurchasePrice, UnitSellingPrice, TransferTotalAmount, products_PDID, TransferStat, TransferHeader_THID
$tranObj = new Transfer();
$tranObj->editTransferDetail($transfer_qty, $receive_qty, $purchase_price, $selling_price, $mnf_date, $exp_date, $total_amount, $inventory_id, $product_id, $variation_id, $rack_id, $batch_id, $transfer_detail_id);

echo "transfer detail updated successfully... ";