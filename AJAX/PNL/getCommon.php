<?php
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();

$shop_id = $_SESSION["shop_id"] ?? 0;

$start = !empty($_POST['start']) ? $_POST['start'] : date('Y-m-01');
$end   = !empty($_POST['end'])   ? $_POST['end']   : date('Y-m-t');

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
        AND (SELECT COUNT(*) AS COUNT FROM invoicedetails d WHERE d.InvoiceHeader_IHID = h.IHID) > 0 
    ) AS total_cost
";

$params = [
    // Revenue
    $shop_id, $start, $end,

    // Cost
    $shop_id, $start, $end
];

$result = $dbObj->getMultipleData($sql, $params);
$gross_profit = (float)$result[0]["total_revenue"] - (float)$result[0]["total_cost"];
$sql2 = "SELECT SUM(ExpenseAmount) AS totExp FROM expenses WHERE status=1 AND is_deleted!=1 AND shop_SHID=? AND EffectiveDate BETWEEN ? AND ?";
$params2 = [
    $shop_id, $start, $end
];
$result2 = $dbObj->getMultipleData($sql2, $params2);

$netProfit = (float)$gross_profit - (float)$result2[0]["totExp"];

echo json_encode([
    "totalRevenue"  => number_format((float)$result[0]["total_revenue"], 2,".", ","),
    "totalCost"     => number_format((float)$result[0]["total_cost"], 2,".", ","),
    "grossProfit"   => number_format((float)$gross_profit, 2,".", ","),
    "netProfit"   => number_format((float)$netProfit, 2,".", ",")
]);