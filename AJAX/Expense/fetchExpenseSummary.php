<?php

session_start();

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

header('Content-Type: application/json');

$dbObj = new DBTransactions();

$shop_id = $_SESSION['shop_id'] ?? 0;

if (empty($shop_id)) {

    echo json_encode([
        "status" => 0,
        "message" => "Invalid shop."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Helper
|--------------------------------------------------------------------------
*/

function getExpenseTotal($dbObj, $shop_id, $start_date = null, $end_date = null)
{
    $sql = "
        SELECT
            COALESCE(SUM(ExpenseAmount), 0) AS total
        FROM expenses
        WHERE shop_SHID = '$shop_id'
        AND is_deleted = 0
        AND status = 1
    ";

    if (!empty($start_date) && !empty($end_date)) {

        $sql .= "
            AND EffectiveDate BETWEEN '$start_date' AND '$end_date'
        ";
    }

    $data = $dbObj->getData($sql);

    return isset($data[0]['total'])
        ? (float)$data[0]['total']
        : 0;
}


/*
|--------------------------------------------------------------------------
| Total Expenses - ALL TIME
|--------------------------------------------------------------------------
*/

$total_expenses = getExpenseTotal(
    $dbObj,
    $shop_id
);


/*
|--------------------------------------------------------------------------
| Current Date
|--------------------------------------------------------------------------
*/

$today = new DateTime(date('Y-m-d'));


/*
|--------------------------------------------------------------------------
| This Month
|--------------------------------------------------------------------------
*/

$month_start = $today->format('Y-m-01');
$month_end   = $today->format('Y-m-t');

$month_total = getExpenseTotal(
    $dbObj,
    $shop_id,
    $month_start,
    $month_end
);


/*
|--------------------------------------------------------------------------
| Previous Month
|--------------------------------------------------------------------------
*/

$previousMonth = clone $today;
$previousMonth->modify('first day of previous month');

$previous_month_start = $previousMonth->format('Y-m-01');
$previous_month_end   = $previousMonth->format('Y-m-t');

$previous_month_total = getExpenseTotal(
    $dbObj,
    $shop_id,
    $previous_month_start,
    $previous_month_end
);


/*
|--------------------------------------------------------------------------
| This Week - Monday to Sunday
|--------------------------------------------------------------------------
*/

$dayOfWeek = (int)$today->format('N');

$weekStart = clone $today;
$weekStart->modify('-' . ($dayOfWeek - 1) . ' days');

$weekEnd = clone $weekStart;
$weekEnd->modify('+6 days');

$week_total = getExpenseTotal(
    $dbObj,
    $shop_id,
    $weekStart->format('Y-m-d'),
    $weekEnd->format('Y-m-d')
);


/*
|--------------------------------------------------------------------------
| Previous Week
|--------------------------------------------------------------------------
*/

$previousWeekStart = clone $weekStart;
$previousWeekStart->modify('-7 days');

$previousWeekEnd = clone $weekEnd;
$previousWeekEnd->modify('-7 days');

$previous_week_total = getExpenseTotal(
    $dbObj,
    $shop_id,
    $previousWeekStart->format('Y-m-d'),
    $previousWeekEnd->format('Y-m-d')
);


/*
|--------------------------------------------------------------------------
| Today
|--------------------------------------------------------------------------
*/

$today_date = $today->format('Y-m-d');

$today_total = getExpenseTotal(
    $dbObj,
    $shop_id,
    $today_date,
    $today_date
);


/*
|--------------------------------------------------------------------------
| Yesterday
|--------------------------------------------------------------------------
*/

$yesterday = clone $today;
$yesterday->modify('-1 day');

$yesterday_total = getExpenseTotal(
    $dbObj,
    $shop_id,
    $yesterday->format('Y-m-d'),
    $yesterday->format('Y-m-d')
);


/*
|--------------------------------------------------------------------------
| Percentage Change
|--------------------------------------------------------------------------
*/

function percentageChange($current, $previous)
{
    $current  = (float)$current;
    $previous = (float)$previous;

    if ($previous == 0) {

        if ($current > 0) {
            return 100;
        }

        return 0;
    }

    return (($current - $previous) / $previous) * 100;
}


/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/

echo json_encode([

    "status" => 1,

    "data" => [

        "total" => [
            "amount" => $total_expenses
        ],

        "month" => [
            "amount" => $month_total,
            "previous" => $previous_month_total,
            "percentage" => percentageChange(
                $month_total,
                $previous_month_total
            )
        ],

        "week" => [
            "amount" => $week_total,
            "previous" => $previous_week_total,
            "percentage" => percentageChange(
                $week_total,
                $previous_week_total
            )
        ],

        "today" => [
            "amount" => $today_total,
            "previous" => $yesterday_total,
            "percentage" => percentageChange(
                $today_total,
                $yesterday_total
            )
        ]

    ]

]);