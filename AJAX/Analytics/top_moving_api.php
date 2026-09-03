<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();
$shop_id = $_SESSION["shop_id"] ?? 0;

// Get date range or default to current month
$start = !empty($_GET['start']) ? $_GET['start'] : date('Y-m-01');
$end   = !empty($_GET['end']) ? $_GET['end'] : date('Y-m-t');

$ProductName = [];
$CurrentQty = [];

$sql = "SELECT 
        p.ItemName,
        SUM(d.SellQty) AS TotalSold
    FROM invoicedetails d
    INNER JOIN products p 
        ON d.products_PDID = p.PDID
    INNER JOIN invoiceheader h 
        ON d.InvoiceHeader_IHID = h.IHID
    WHERE 
        h.InvStat = 1
        AND h.shop_SHID = ?
        AND d.ItemType = 1
    GROUP BY p.ItemName
    ORDER BY TotalSold DESC
    LIMIT 15";

$params = [$shop_id];
$bestSellingProducts = $dbObj->getMultipleData($sql, $params);
$i=1;
if (!empty($bestSellingProducts)) {
    foreach ($bestSellingProducts as $row) {
        $ProductName[] = $i.". ".$row['ItemName'];
        $CurrentQty[] = (float)$row['TotalSold'];
        $i++;
    }
}

echo json_encode([
    "ProductName" => $ProductName,
    "CurrentQty" => $CurrentQty
]);