<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$shop_id = $_SESSION['shop_id'];
$dbObj = new DBTransactions();

$CTID = isset($_POST['CTID']) && !empty($_POST['CTID']) ? $_POST['CTID'] : "";
if (empty($CTID)) {
    echo json_encode(['status' => 0, 'message' => 'Customer ID is required.']);
    exit;
}
if($CTID == 1) {
    echo json_encode(['status' => 0, 'message' => 'Cannot update common customer.']);
    exit;
}
$sql = "UPDATE customers SET CustStat = CASE WHEN CustStat = 1 THEN 0 ELSE 1 END WHERE CTID = '$CTID'";

$dbObj = new DBTransactions();
$result = $dbObj->executeTransaction($sql);

if ($result) {
    $sql = "SELECT CustStat FROM customers WHERE CTID = '$CTID'";
    $CustStat = $dbObj->getData($sql);
    echo json_encode(['status' => 1, 'message' => 'Customer status updated successfully.', 'CustStat' => $CustStat[0]['CustStat']]);
    // echo json_encode(['status' => 1, 'message' => 'Customer status updated successfully.', 'CustStat' => $sql]);
} else {
    echo json_encode(['status' => 0, 'message' => 'Failed to update customer status.']);
}