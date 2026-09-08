<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();
$shop_id = $_SESSION["shop_id"] ?? 0;

// Get date range or default to current month
// $start = !empty($_GET['start']) ? $_GET['start'] : "2025/10/01";
// $end   = !empty($_GET['end'])   ? $_GET['end']   : "2025/10/31";
$start = !empty($_GET['start']) ? $_GET['start'] : date('Y-m-01');
$end   = !empty($_GET['end'])   ? $_GET['end']   : date('Y-m-t');
// var_dump($start, $end);
// exit;
$dates = [];
$earnings = [];
$expenses = [];

$sql = "SELECT 
    d.date,
    IFNULL(s.total_sales, 0) AS total_sales,
    IFNULL(e.total_expense, 0) AS total_expense
FROM
(
    SELECT EffectiveDate AS date
    FROM invoiceheader
    WHERE shop_SHID = ?
        AND InvStat = 1
        AND EffectiveDate BETWEEN ? AND ?
        AND (SELECT COUNT(*) AS COUNT FROM invoicedetails WHERE invoicedetails.InvoiceHeader_IHID = invoiceheader.IHID) > 0
    
    UNION
    
    SELECT EffectiveDate
    FROM expenses
    WHERE shop_SHID = ?
        AND EffectiveDate BETWEEN ? AND ?
) d

LEFT JOIN
(
    SELECT EffectiveDate, SUM(NetAmount) AS total_sales
    FROM invoiceheader
    WHERE InvStat = 1
        AND shop_SHID = ?
        AND EffectiveDate BETWEEN ? AND ?
        AND (SELECT COUNT(*) AS COUNT FROM invoicedetails WHERE invoicedetails.InvoiceHeader_IHID = invoiceheader.IHID) > 0
    GROUP BY EffectiveDate
) s ON d.date = s.EffectiveDate

LEFT JOIN
(
    SELECT EffectiveDate, SUM(ExpenseAmount) AS total_expense
    FROM expenses
    WHERE shop_SHID = ?
        AND EffectiveDate BETWEEN ? AND ?
        AND status=1 
        AND is_deleted!=1
    GROUP BY EffectiveDate
) e ON d.date = e.EffectiveDate

ORDER BY d.date ASC";

$params = [
    $shop_id, $start, $end,   // for first subquery
    $shop_id, $start, $end,   // for second subquery
    $shop_id, $start, $end,   // for sales join
    $shop_id, $start, $end    // for expense join
];

$Sales = $dbObj->getMultipleData($sql, $params);

// Loop through results
if (!empty($Sales)) {
    foreach ($Sales as $row) {
        $dates[] = date("d/m", strtotime($row['date']));
        $earnings[] = (float)$row['total_sales'];
        $expenses[] = (float)$row['total_expense'];
    }
}
// print_r($Sales);
// exit;
// If no data found, send empty arrays for chart
echo json_encode([
    "dates" => $dates,
    "earnings" => $earnings,
    "expenses" => $expenses
]);
