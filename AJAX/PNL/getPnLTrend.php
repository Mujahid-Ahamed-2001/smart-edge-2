<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();

$shop_id = $_SESSION["shop_id"] ?? 0;

$start = !empty($_POST['start'])
    ? $_POST['start']
    : date('Y-m-01');

$end = !empty($_POST['end'])
    ? $_POST['end']
    : date('Y-m-t');


/*
|--------------------------------------------------------------------------
| Revenue Date Wise
|--------------------------------------------------------------------------
*/
$sqlRevenue = " SELECT
        EffectiveDate,
        IFNULL(SUM(NetAmount), 0) AS total_revenue
    FROM invoiceheader
    WHERE shop_SHID = ?
    AND InvStat = 1
    AND EffectiveDate BETWEEN ? AND ?
    AND (
        SELECT COUNT(*)
        FROM invoicedetails d
        WHERE d.InvoiceHeader_IHID = invoiceheader.IHID
    ) > 0
    GROUP BY EffectiveDate
    ORDER BY EffectiveDate ASC
";

$revenueData = $dbObj->getMultipleData(
    $sqlRevenue,
    [$shop_id, $start, $end]
);


/*
|--------------------------------------------------------------------------
| Cost Date Wise
|--------------------------------------------------------------------------
*/
$sqlCost = " SELECT
        h.EffectiveDate,
        IFNULL(SUM(d.total_cost), 0) AS total_cost
    FROM invoicedetails d
    INNER JOIN invoiceheader h
        ON h.IHID = d.InvoiceHeader_IHID
    WHERE h.shop_SHID = ?
    AND h.InvStat = 1
    AND h.EffectiveDate BETWEEN ? AND ?
    AND (
        SELECT COUNT(*)
        FROM invoicedetails d
        WHERE d.InvoiceHeader_IHID = h.IHID
    ) > 0
    GROUP BY h.EffectiveDate
    ORDER BY h.EffectiveDate ASC
";

$costData = $dbObj->getMultipleData(
    $sqlCost,
    [$shop_id, $start, $end]
);


/*
|--------------------------------------------------------------------------
| Expenses Date Wise
|--------------------------------------------------------------------------
*/
$sqlExpense = " SELECT
        EffectiveDate,
        IFNULL(SUM(ExpenseAmount), 0) AS total_expense
    FROM expenses
    WHERE status = 1
    AND is_deleted != 1
    AND shop_SHID = ?
    AND EffectiveDate BETWEEN ? AND ?
    GROUP BY EffectiveDate
    ORDER BY EffectiveDate ASC
";

$expenseData = $dbObj->getMultipleData(
    $sqlExpense,
    [$shop_id, $start, $end]
);


/*
|--------------------------------------------------------------------------
| Map Data By Date
|--------------------------------------------------------------------------
*/

$revenueMap = [];
$costMap = [];
$expenseMap = [];


foreach ($revenueData as $row) {

    $revenueMap[$row['EffectiveDate']] =
        (float)$row['total_revenue'];
}


foreach ($costData as $row) {

    $costMap[$row['EffectiveDate']] =
        (float)$row['total_cost'];
}


foreach ($expenseData as $row) {

    $expenseMap[$row['EffectiveDate']] =
        (float)$row['total_expense'];
}


/*
|--------------------------------------------------------------------------
| Create Every Date Between Start & End
|--------------------------------------------------------------------------
|
| This is important.
|
| Even if there were no sales on a particular day, that date will still
| appear on the trend chart with 0 values.
|
*/

$trend = [];

$currentDate = new DateTime($start);
$endDate = new DateTime($end);

while ($currentDate <= $endDate) {

    $date = $currentDate->format('Y-m-d');

    $revenue = $revenueMap[$date] ?? 0;
    $cost = $costMap[$date] ?? 0;
    $expense = $expenseMap[$date] ?? 0;

    /*
     * Gross Profit
     */
    $grossProfit = $revenue - $cost;

    /*
     * Net Profit / Loss
     */
    $netProfit = $grossProfit - $expense;


    $trend[] = [
        "date"        => $date,
        "revenue"     => round($revenue, 2),
        "cost"        => round($cost, 2),
        "grossProfit" => round($grossProfit, 2),
        "expense"     => round($expense, 2),
        "netProfit"   => round($netProfit, 2)
    ];


    $currentDate->modify('+1 day');
}


echo json_encode([
    "status" => 1,
    "start"  => $start,
    "end"    => $end,
    "trend"  => $trend
]);