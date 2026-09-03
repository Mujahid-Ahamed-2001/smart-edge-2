<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$shop_id = $_SESSION['shop_id'];
$dbObj = new DBTransactions();
$whereClause = "WHERE 1=1";
if(isset($_GET['CTID']) && !empty($_GET['CTID']))
{
    $CTID = $_GET['CTID'];
    $whereClause .= " AND c.CTID = '$CTID'";
    
}
$sql = "SELECT c.*, COALESCE(SUM(ct.CreditAmount), 0) - COALESCE(SUM(ct.DebitAmount), 0) AS Balance, c.created_date FROM customers c LEFT JOIN creditcustomer ct ON ct.Customers_CTID = c.CTID $whereClause GROUP BY c.CTID ORDER BY c.CTID ASC;";

$dbObj = new DBTransactions();
$itemData = $dbObj->getData($sql);

echo json_encode($itemData);