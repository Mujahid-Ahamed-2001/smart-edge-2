<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$catID = isset($_POST['catID']) ? trim($_POST['catID']) : '';
$sql = "SELECT COUNT(*) AS subcatCount FROM subcategories WHERE SubCatName = ? AND categories_CTID = ?";
if(isset($_POST["SCID"]) && !empty($_POST["SCID"]))
{
    $SCID = $_POST["SCID"];
    $sql .=" AND SCID!='$SCID'";
}
$params = [$name, $catID];
$result = $dbObj->getMultipleData($sql, $params);
$response = ['subcatCount' => $result[0]['subcatCount']];  
echo json_encode($response);