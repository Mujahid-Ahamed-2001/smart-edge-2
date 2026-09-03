<?php 
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();

$barcode = !empty($_POST["barcode"]) ? $_POST["barcode"] : "";

$sql= "SELECT COUNT(*) AS ProCount FROM `products` WHERE Barcode='$barcode' ";
if(!empty($_POST["PDID"]))
{
    $PDID = $_POST["PDID"];
    $sql .=" AND PDID !='$PDID'";
}
$data = $dbObj->getData($sql);
$response = ['ProCount' => $data[0]['ProCount'],"SQL"=>$sql];  

echo json_encode($response);

?>