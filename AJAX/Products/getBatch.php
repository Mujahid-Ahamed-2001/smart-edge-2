<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/product_class.php";

$product_id = $_GET['product_id'];
$shop_id = $_SESSION['shop_id'];

$varObj = new Product();
$varData = $varObj->getBatchesByProduct($product_id,$shop_id);


foreach($varData as $row)
{
   echo "<option value='".$row['INID']."'>".$row['BatchID']." - ".$row['PurchasePrice']."</option>";
}
//foreach