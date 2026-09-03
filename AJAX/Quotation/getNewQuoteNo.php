<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$shop_id = $_SESSION['shop_id'];
function formatNumber($number, $length = 3) {
    return str_pad($number, $length, '0', STR_PAD_LEFT);
}
function getTodayDateFormatted() {
    return date('dmY');
}
// Generate new quotation number
$dbObj = new DBTransactions(); 
$sql = "SELECT q_no FROM docno WHERE shop_id = '$shop_id' ORDER BY DNID DESC LIMIT 1";
$result = $dbObj->getData($sql);
if(!empty($result))
{
    $last_quote_no = $result[0]['q_no']+1;
    $q_no = formatNumber($last_quote_no);
    $today = getTodayDateFormatted();
    $new_quote_no = 'Q' . $today . '-' . $q_no;
}
else
{
    // If no previous quote, start with QT-000001
    $sql="INSERT INTO docno (shop_id, q_no) VALUES ('$shop_id', 1)";
    $dbObj->executeTransaction($sql);
    $last_quote_no = 1;
    $q_no = formatNumber($last_quote_no);
    $today = getTodayDateFormatted();
    $new_quote_no = 'Q' . $today . '-' . $q_no;
}
echo json_encode(['new_quote_no' => $new_quote_no]);