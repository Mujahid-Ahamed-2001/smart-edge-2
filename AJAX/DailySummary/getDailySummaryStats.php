<?php

header('Content-Type: application/json');


// =========================================================
// Session
// =========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// =========================================================
// Config
// =========================================================

require_once '../../Includes/config.php';


// =========================================================
// Access PDO connection
// connect() inside Dbh is protected,
// therefore expose it through a small child class.
// =========================================================

class DailySummaryDb extends Dbh
{
    public function getConnection()
    {
        return $this->connect();
    }
}


// =========================================================
// Default Response
// =========================================================

$response = [
    'status' => 0,
    'message' => 'Unable to load daily summary.',
    'data' => null
];


// =========================================================
// Percentage Change
// =========================================================

function calculatePercentageChange($current, $previous)
{
    $current = (float)$current;
    $previous = (float)$previous;


    // Both zero
    if ($previous == 0 && $current == 0) {
        return 0;
    }


    // Previous was zero, current now has value
    if ($previous == 0) {
        return $current > 0 ? 100 : -100;
    }


    return (
        ($current - $previous)
        /
        abs($previous)
    ) * 100;
}


// =========================================================
// Get Sales Data For One Date
// =========================================================

function getSalesSummary($db, $shopId, $date)
{
    /*
        IMPORTANT:

        Invoice details are aggregated BEFORE joining
        invoiceheader.

        Otherwise NetAmount would be duplicated when
        an invoice contains multiple items.
    */

    $sql = "

        SELECT

            COALESCE(
                SUM(h.NetAmount),
                0
            ) AS total_revenue,

            COUNT(h.IHID) AS invoice_count,

            COALESCE(
                SUM(d.total_cost),
                0
            ) AS total_cost

        FROM invoiceheader h

        INNER JOIN
        (
            SELECT

                InvoiceHeader_IHID,

                SUM(
                    COALESCE(total_cost, 0)
                ) AS total_cost

            FROM invoicedetails

            GROUP BY
                InvoiceHeader_IHID

        ) d
            ON d.InvoiceHeader_IHID = h.IHID

        WHERE

            h.shop_SHID = :shop_id

            AND h.InvStat = 1

            AND h.EffectiveDate = :selected_date

    ";


    $stmt = $db->prepare($sql);


    $stmt->execute([
        ':shop_id' => $shopId,
        ':selected_date' => $date
    ]);


    $row = $stmt->fetch(
        PDO::FETCH_ASSOC
    );


    return [

        'revenue' =>
            isset($row['total_revenue'])
                ? (float)$row['total_revenue']
                : 0,

        'invoiceCount' =>
            isset($row['invoice_count'])
                ? (int)$row['invoice_count']
                : 0,

        'cost' =>
            isset($row['total_cost'])
                ? (float)$row['total_cost']
                : 0

    ];
}


// =========================================================
// Get Expense Data For One Date
// =========================================================

function getExpenseSummary($db, $shopId, $date)
{
    $sql = "

        SELECT

            COALESCE(
                SUM(ExpenseAmount),
                0
            ) AS total_expenses,

            COUNT(EPID) AS expense_count

        FROM expenses

        WHERE

            shop_SHID = :shop_id

            AND EffectiveDate = :selected_date

            AND status = 1

            AND is_deleted = 0

    ";


    $stmt = $db->prepare($sql);


    $stmt->execute([
        ':shop_id' => $shopId,
        ':selected_date' => $date
    ]);


    $row = $stmt->fetch(
        PDO::FETCH_ASSOC
    );


    return [

        'expenses' =>
            isset($row['total_expenses'])
                ? (float)$row['total_expenses']
                : 0,

        'count' =>
            isset($row['expense_count'])
                ? (int)$row['expense_count']
                : 0

    ];
}


// =========================================================
// Main
// =========================================================

try
{

    // =====================================================
    // Check Logged-In Shop
    // =====================================================

    if (
        !isset($_SESSION['shop_id']) ||
        (int)$_SESSION['shop_id'] <= 0
    )
    {
        throw new Exception(
            'Invalid shop session.'
        );
    }


    $shopId =
        (int)$_SESSION['shop_id'];


    // =====================================================
    // Selected Date
    // =====================================================

    $selectedDate =
        isset($_POST['date'])
            ? trim($_POST['date'])
            : '';


    if ($selectedDate === '')
    {
        throw new Exception(
            'Please select a date.'
        );
    }


    // =====================================================
    // Validate Date YYYY-MM-DD
    // =====================================================

    $dateObject =
        DateTime::createFromFormat(
            'Y-m-d',
            $selectedDate
        );


    if (
        !$dateObject ||
        $dateObject->format('Y-m-d') !== $selectedDate
    )
    {
        throw new Exception(
            'Invalid date format.'
        );
    }


    // =====================================================
    // Previous Calendar Date
    // =====================================================

    $previousDateObject =
        clone $dateObject;


    $previousDateObject->modify(
        '-1 day'
    );


    $previousDate =
        $previousDateObject->format(
            'Y-m-d'
        );


    // =====================================================
    // PDO Connection
    // =====================================================

    $database =
        new DailySummaryDb();


    $db =
        $database->getConnection();


    if (!$db)
    {
        throw new Exception(
            'Database connection failed.'
        );
    }


    // =====================================================
    // Current Day Sales
    // =====================================================

    $currentSales =
        getSalesSummary(
            $db,
            $shopId,
            $selectedDate
        );


    // =====================================================
    // Previous Day Sales
    // =====================================================

    $previousSales =
        getSalesSummary(
            $db,
            $shopId,
            $previousDate
        );


    // =====================================================
    // Current Day Expenses
    // =====================================================

    $currentExpense =
        getExpenseSummary(
            $db,
            $shopId,
            $selectedDate
        );


    // =====================================================
    // Previous Day Expenses
    // =====================================================

    $previousExpense =
        getExpenseSummary(
            $db,
            $shopId,
            $previousDate
        );


    // =====================================================
    // CURRENT DAY CALCULATIONS
    // =====================================================

    $currentRevenue =
        $currentSales['revenue'];


    $currentCost =
        $currentSales['cost'];


    $currentGrossProfit =
        $currentRevenue
        -
        $currentCost;


    $currentExpenses =
        $currentExpense['expenses'];


    $currentNetProfit =
        $currentGrossProfit
        -
        $currentExpenses;


    // =====================================================
    // Gross Margin
    // =====================================================

    $grossMargin = 0;


    if ($currentRevenue > 0)
    {
        $grossMargin =
            (
                $currentGrossProfit
                /
                $currentRevenue
            )
            * 100;
    }


    // =====================================================
    // PREVIOUS DAY CALCULATIONS
    // =====================================================

    $previousRevenue =
        $previousSales['revenue'];


    $previousCost =
        $previousSales['cost'];


    $previousGrossProfit =
        $previousRevenue
        -
        $previousCost;


    $previousExpenses =
        $previousExpense['expenses'];


    $previousNetProfit =
        $previousGrossProfit
        -
        $previousExpenses;


    // =====================================================
    // Percentage Changes
    // =====================================================

    $revenuePercentageChange =
        calculatePercentageChange(
            $currentRevenue,
            $previousRevenue
        );


    $profitPercentageChange =
        calculatePercentageChange(
            $currentGrossProfit,
            $previousGrossProfit
        );


    $expensePercentageChange =
        calculatePercentageChange(
            $currentExpenses,
            $previousExpenses
        );


    $netProfitPercentageChange =
        calculatePercentageChange(
            $currentNetProfit,
            $previousNetProfit
        );


    // =====================================================
    // Response
    // =====================================================

    $response = [

        'status' => 1,

        'message' =>
            'Daily summary loaded successfully.',

        'data' => [

            // Date
            'selectedDate' =>
                $selectedDate,

            'previousDate' =>
                $previousDate,


            // Revenue
            'netRevenue' =>
                round(
                    $currentRevenue,
                    2
                ),

            'completedInvoices' =>
                $currentSales['invoiceCount'],


            // Gross Profit
            'grossProfit' =>
                round(
                    $currentGrossProfit,
                    2
                ),

            'grossMargin' =>
                round(
                    $grossMargin,
                    2
                ),


            // Expenses
            'totalExpenses' =>
                round(
                    $currentExpenses,
                    2
                ),

            'expenseCount' =>
                $currentExpense['count'],


            // Net Profit
            'netProfit' =>
                round(
                    $currentNetProfit,
                    2
                ),


            // Percentage Changes
            'revenuePercentageChange' =>
                round(
                    $revenuePercentageChange,
                    2
                ),

            'profitPercentageChange' =>
                round(
                    $profitPercentageChange,
                    2
                ),

            'expensePercentageChange' =>
                round(
                    $expensePercentageChange,
                    2
                ),

            'netProfitPercentageChange' =>
                round(
                    $netProfitPercentageChange,
                    2
                )

        ]

    ];

}
catch (Throwable $e)
{

    error_log(
        'Daily Summary Stats Error: '
        .
        $e->getMessage()
    );


    $response = [

        'status' => 0,

        'message' =>
            'Unable to load daily summary.',

        'data' => null

    ];

}


// =========================================================
// Output JSON
// =========================================================

echo json_encode(
    $response
);

exit;