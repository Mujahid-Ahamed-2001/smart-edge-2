<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();
$sql = "SELECT 
            s.*,
            c.CTID FROM `subcategories` s
LEFT JOIN categories c ON c.CTID=s.categories_CTID";
if (isset($_POST['SCID'])) {
    $SCID = (int)$_POST['SCID'];
    $sql .= " WHERE s.SCID = $SCID ";
}

$dbData = $dbObj->getData(sql: $sql);
$response[] = $dbData[0];
// echo json_encode(["response"=> $response, "SQL"=> $sql]);
echo json_encode($response);