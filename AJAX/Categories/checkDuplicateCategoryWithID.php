<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$shop_id = $_SESSION['shop_id'];
$cat_name = $_POST['cat_name']; // FIXED
$CTID = $_POST['CTID']; // FIXED

$dbData = new DBTransactions;

$sql = "SELECT COUNT(*) AS CategoryCount FROM `categories` WHERE CategoryName = '$cat_name' AND CTID != $CTID";

$cat_count = 0;
$result = $dbData->getData($sql);
if (!empty($result)) {
    $cat_count = $result[0]['CategoryCount'];
}

echo json_encode([
    "catCount" => $cat_count
]);