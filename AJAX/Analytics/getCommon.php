<?php
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();

$shop_id = $_SESSION["shop_id"] ?? 0;

$start = !empty($_GET['start']) ? $_GET['start'] : date('Y-m-01');
$end   = !empty($_GET['end'])   ? $_GET['end']   : date('Y-m-t');

$sql = "SELECT

    /* Total Revenue */
    (
        SELECT IFNULL(SUM(NetAmount),0)
        FROM invoiceheader
        WHERE shop_SHID = ?
        AND InvStat = 1
        AND EffectiveDate BETWEEN ? AND ?
        AND (SELECT COUNT(*) AS COUNT FROM invoicedetails d WHERE d.InvoiceHeader_IHID = invoiceheader.IHID) > 0 
    ) AS total_revenue,

    /* Total Cost */
    (
        SELECT IFNULL(SUM(d.total_cost),0)
        FROM invoicedetails d
        INNER JOIN invoiceheader h
            ON h.IHID = d.InvoiceHeader_IHID
        WHERE h.shop_SHID = ?
        AND h.InvStat = 1
        AND h.EffectiveDate BETWEEN ? AND ?
    ) AS total_cost,

    /* Gross Profit */
    (
        (
            SELECT IFNULL(SUM(NetAmount),0)
            FROM invoiceheader
            WHERE shop_SHID = ?
            AND InvStat = 1
            AND EffectiveDate BETWEEN ? AND ?
            AND (SELECT COUNT(*) AS COUNT FROM invoicedetails d WHERE d.InvoiceHeader_IHID = invoiceheader.IHID) > 0
        )
        -
        (
            SELECT IFNULL(SUM(d.total_cost),0)
            FROM invoicedetails d
            INNER JOIN invoiceheader h
                ON h.IHID = d.InvoiceHeader_IHID
            WHERE h.shop_SHID = ?
            AND h.InvStat = 1
            AND h.EffectiveDate BETWEEN ? AND ?
        )
    ) AS gross_profit,

    /* Total Orders */
    (
        SELECT COUNT(*)
        FROM invoiceheader
        WHERE shop_SHID = ?
        AND InvStat = 1
        AND EffectiveDate BETWEEN ? AND ?
        AND (SELECT COUNT(*) AS COUNT FROM invoicedetails d WHERE d.InvoiceHeader_IHID = invoiceheader.IHID) > 0
    ) AS total_orders,

    /* Low Stock Items */
    (
        SELECT COUNT(*)
        FROM inventory
        WHERE shop_SHID = ?
        AND CurrentQty <= 10 AND is_default !=1
    ) AS low_stock_items
";

$params = [
    // Revenue
    $shop_id, $start, $end,

    // Cost
    $shop_id, $start, $end,

    // Revenue again (Profit)
    $shop_id, $start, $end,

    // Cost again (Profit)
    $shop_id, $start, $end,

    // Orders
    $shop_id, $start, $end,

    // Low stock
    $shop_id
];

$result = $dbObj->getMultipleData($sql, $params);

echo json_encode([
    "totalRevenue"  => (float)$result[0]["total_revenue"],
    "totalCost"     => (float)$result[0]["total_cost"],
    "grossProfit"   => (float)$result[0]["gross_profit"],
    "totalOrders"   => (int)$result[0]["total_orders"],
    "lowStockItems" => (int)$result[0]["low_stock_items"]
]);