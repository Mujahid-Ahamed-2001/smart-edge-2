<?php
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();
$shop_id = $_SESSION["shop_id"] ?? 0;

$start = !empty($_GET['start']) ? $_GET['start'] : date('Y-m-01');
$end   = !empty($_GET['end']) ? $_GET['end'] : date('Y-m-t');

$categories = [];
$amounts = [];

$sql = "SELECT 
            sc.SubCatName,
            SUM(d.SoldAmount) AS TotalSales
        FROM invoicedetails d
        INNER JOIN invoiceheader h 
            ON d.InvoiceHeader_IHID = h.IHID
        INNER JOIN products p 
            ON d.products_PDID = p.PDID
        INNER JOIN subcategories sc
            ON p.Subcategories_SCID = sc.SCID
        WHERE 
            h.InvStat = 1
            AND h.shop_SHID = ?
            AND d.ItemType = 1
            AND h.EffectiveDate BETWEEN ? AND ?
        GROUP BY sc.SCID
        ORDER BY TotalSales DESC";

$params = [$shop_id, $start, $end];

$data = $dbObj->getMultipleData($sql, $params);

if (!empty($data)) {
    foreach ($data as $row) {
        $categories[] = $row['SubCatName'];
        $amounts[] = (float)$row['TotalSales'];
    }
}

echo json_encode([
    "categories" => $categories,
    "amounts" => $amounts
]);