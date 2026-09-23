<?php

session_start();

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

header('Content-Type: application/json');

$dbObj = new DBTransactions();

$shop_id = $_SESSION['shop_id'] ?? 0;

$start_date = $_POST['start_date'] ?? '';
$end_date   = $_POST['end_date'] ?? '';

if (empty($shop_id)) {

    echo json_encode([
        "status" => 0,
        "message" => "Invalid shop."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Date condition
|--------------------------------------------------------------------------
*/

$dateWhere = "";

if (!empty($start_date) && !empty($end_date)) {

    $dateWhere = "
        AND e.EffectiveDate BETWEEN '$start_date' AND '$end_date'
    ";
}


/*
|--------------------------------------------------------------------------
| Expenses Overview - By Category
|--------------------------------------------------------------------------
*/

$sqlOverview = "
    SELECT
        COALESCE(ec.expense_ctg, 'Uncategorized') AS category,
        COALESCE(SUM(e.ExpenseAmount), 0) AS total

    FROM expenses e

    LEFT JOIN expensecategory ec
        ON ec.ECID = e.expensecategory_id

    WHERE e.shop_SHID = '$shop_id'
    AND e.is_deleted = 0
    AND e.status = 1

    $dateWhere

    GROUP BY
        e.expensecategory_id,
        ec.expense_ctg

    ORDER BY total DESC
";

$overviewData = $dbObj->getData($sqlOverview);


/*
|--------------------------------------------------------------------------
| Expenses Trend - By Date
|--------------------------------------------------------------------------
*/

$sqlTrend = "
    SELECT
        e.EffectiveDate,
        COALESCE(SUM(e.ExpenseAmount), 0) AS total

    FROM expenses e

    WHERE e.shop_SHID = '$shop_id'
    AND e.is_deleted = 0
    AND e.status = 1

    $dateWhere

    GROUP BY e.EffectiveDate

    ORDER BY e.EffectiveDate ASC
";

$trendData = $dbObj->getData($sqlTrend);


/*
|--------------------------------------------------------------------------
| Format Overview
|--------------------------------------------------------------------------
*/

$overview = [];

foreach ($overviewData as $row) {

    $overview[] = [
        "label" => $row['category'],
        "value" => (float)$row['total']
    ];
}


/*
|--------------------------------------------------------------------------
| Format Trend
|--------------------------------------------------------------------------
*/

$trend = [];

foreach ($trendData as $row) {

    $trend[] = [
        "date" => $row['EffectiveDate'],
        "value" => (float)$row['total']
    ];
}


/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/

echo json_encode([
    "status" => 1,

    "data" => [

        "overview" => $overview,

        "trend" => $trend

    ]
]);

exit;