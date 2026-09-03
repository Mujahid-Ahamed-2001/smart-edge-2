<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();
$shop_id = $_SESSION["shop_id"] ?? 0;

// Get date range or default to current month
$start = !empty($_GET['start']) ? $_GET['start'] : date('Y-m-01');
$end   = !empty($_GET['end']) ? $_GET['end'] : date('Y-m-t');

$categories = [];
$amounts = [];

$sql = "SELECT 
    p.ItemName,
    SUM(d.SellQty) AS TotalQty
FROM invoicedetails d
INNER JOIN invoiceheader h 
    ON d.InvoiceHeader_IHID = h.IHID
INNER JOIN products p 
    ON d.products_PDID = p.PDID
WHERE 
    h.InvStat = 1
    AND h.shop_SHID = ?
    AND d.ItemType = 1
    AND h.EffectiveDate BETWEEN ? AND ?
GROUP BY d.products_PDID
ORDER BY TotalQty DESC
LIMIT 5";

$params = [$shop_id, $start, $end];
$bestSellingProducts = $dbObj->getMultipleData($sql, $params);

if (!empty($bestSellingProducts)) {
    foreach ($bestSellingProducts as $row) {
        $categories[] = $row['ItemName'];
        $amounts[] = (float)$row['TotalQty'];
    }
}

echo json_encode([
    "categories" => $categories,
    "amounts" => $amounts
]);