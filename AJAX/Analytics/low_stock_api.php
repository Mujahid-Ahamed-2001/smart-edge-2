<?php
session_start();

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

header("Content-Type: application/json");

$dbObj = new DBTransactions();

$shop_id = (int)($_SESSION["shop_id"] ?? 0);

$productNames = [];
$currentQty   = [];

$sql = "SELECT
            p.PDID,
            p.ItemName,
            p.low_stock_qty,
            SUM(i.CurrentQty) AS total_current_qty
        FROM inventory i
        INNER JOIN products p
            ON p.PDID = i.products_PDID
        WHERE i.shop_SHID = ?
          AND p.is_lowStock = 1
          AND p.ProductStat = 1
          AND p.ItemType = 'P'
          AND COALESCE(i.is_default, 0) != 1
        GROUP BY
            p.PDID,
            p.ItemName,
            p.low_stock_qty
        HAVING SUM(i.CurrentQty) <= p.low_stock_qty
        ORDER BY total_current_qty ASC
        ";

$params = [$shop_id];

$lowStockProducts = $dbObj->getMultipleData($sql, $params);

if (!empty($lowStockProducts)) {
    foreach ($lowStockProducts as $index => $row) {
        $productNames[] = ($index + 1) . ". " . $row["ItemName"];
        $currentQty[]   = (float)$row["total_current_qty"];
    }
}

echo json_encode([
    "ProductName" => $productNames,
    "CurrentQty"  => $currentQty
]);