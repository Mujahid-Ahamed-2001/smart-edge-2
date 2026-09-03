<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();
$sql = "SELECT * FROM `categories`";
if (isset($_POST['CTID'])) {
    $cat_id = (int)$_POST['CTID'];
    $sql .= " WHERE CTID = $cat_id";
}

$dbData = $dbObj->getData(sql: $sql);
$response[] = $dbData[0];
// echo json_encode(["response"=> $response, "SQL"=> $sql]);
echo json_encode($response);