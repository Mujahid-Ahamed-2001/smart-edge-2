<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();

$shop_id = $_SESSION["shop_id"] ?? 0;


/*
|--------------------------------------------------------------------------
| Dates
|--------------------------------------------------------------------------
*/

$start = !empty($_POST['start'])
    ? $_POST['start']
    : date('Y-m-01');

$end = !empty($_POST['end'])
    ? $_POST['end']
    : date('Y-m-t');


if ($shop_id <= 0) {

    echo json_encode([
        "status" => 0,
        "message" => "Invalid shop."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Revenue + Cost
|--------------------------------------------------------------------------
*/

$sql = "SELECT

    /* Total Revenue */
    (
        SELECT IFNULL(SUM(NetAmount), 0)

        FROM invoiceheader

        WHERE shop_SHID = ?

        AND InvStat = 1

        AND EffectiveDate BETWEEN ? AND ?

        AND (
            SELECT COUNT(*)

            FROM invoicedetails d

            WHERE d.InvoiceHeader_IHID =
                invoiceheader.IHID

        ) > 0

    ) AS total_revenue,


    /* Total Cost */
    (
        SELECT IFNULL(SUM(d.total_cost), 0)

        FROM invoicedetails d

        INNER JOIN invoiceheader h
            ON h.IHID = d.InvoiceHeader_IHID

        WHERE h.shop_SHID = ?

        AND h.InvStat = 1

        AND h.EffectiveDate BETWEEN ? AND ?

        AND (
            SELECT COUNT(*)

            FROM invoicedetails x

            WHERE x.InvoiceHeader_IHID = h.IHID

        ) > 0

    ) AS total_cost
";


$params = [

    // Revenue
    $shop_id,
    $start,
    $end,

    // Cost
    $shop_id,
    $start,
    $end

];


$result = $dbObj->getMultipleData(
    $sql,
    $params
);


$totalRevenue = (float)(
    $result[0]["total_revenue"] ?? 0
);

$totalCost = (float)(
    $result[0]["total_cost"] ?? 0
);


/*
|--------------------------------------------------------------------------
| Gross Profit
|--------------------------------------------------------------------------
*/

$grossProfit =
    $totalRevenue - $totalCost;


/*
|--------------------------------------------------------------------------
| Expenses
|--------------------------------------------------------------------------
|
| IMPORTANT:
|
| I have used ExpenseName below.
|
| Change ExpenseName to the actual column in your expenses table
| that stores the expense description/category.
|
*/

$sqlExpense = "SELECT
        e.EPID,
        e.EffectiveDate,
        e.ExpenseAmount,
        e.ExpenseReason,
        e.expensecategory_id,
        ec.expense_ctg

    FROM expenses e

    LEFT JOIN expensecategory ec
        ON ec.ECID = e.expensecategory_id

    WHERE e.status = 1
    AND e.is_deleted != 1
    AND e.shop_SHID = ?
    AND e.EffectiveDate BETWEEN ? AND ?

    ORDER BY e.EffectiveDate ASC, e.EPID ASC
";

$paramsExpense = [
    $shop_id,
    $start,
    $end
];

$expenseResult = $dbObj->getMultipleData(
    $sqlExpense,
    $paramsExpense
);


/*
|--------------------------------------------------------------------------
| Expense Array
|--------------------------------------------------------------------------
*/

$expenses = [];
$totalExpenses = 0;

foreach ($expenseResult as $expense) {

    $amount = (float)($expense["ExpenseAmount"] ?? 0);

    $expenses[] = [
        "id" => (int)$expense["EPID"],
        "date" => $expense["EffectiveDate"],
        "category" => $expense["expense_ctg"] ?? "Uncategorized",
        "reason" => $expense["ExpenseReason"] ?? "",
        "amount" => round($amount, 2)
    ];

    $totalExpenses += $amount;
}


/*
|--------------------------------------------------------------------------
| Net Profit / Loss
|--------------------------------------------------------------------------
*/

$netProfit =
    $grossProfit - $totalExpenses;


/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/

echo json_encode([
    "status" => 1,

    "start" => $start,
    "end" => $end,

    "totalRevenue" => round($totalRevenue, 2),
    "totalCost" => round($totalCost, 2),
    "grossProfit" => round($grossProfit, 2),

    "expenses" => $expenses,

    "totalExpenses" => round($totalExpenses, 2),
    "netProfit" => round($netProfit, 2)
]);