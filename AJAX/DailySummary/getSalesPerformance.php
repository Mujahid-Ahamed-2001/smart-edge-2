<?php

header('Content-Type: application/json');


// =========================================================
// Session
// =========================================================

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}


// =========================================================
// Database Config
// =========================================================

require_once '../../Includes/config.php';


// =========================================================
// PDO Access
// =========================================================

class SalesPerformanceDb extends Dbh
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

    'message' =>
        'Unable to load sales performance.',

    'data' => null

];


try
{

    // =====================================================
    // Validate Shop Session
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
    // Get Selected Date
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
    // Validate Date Format
    // =====================================================

    $dateObject =
        DateTime::createFromFormat(
            'Y-m-d',
            $selectedDate
        );


    if (
        !$dateObject ||
        $dateObject->format('Y-m-d')
            !==
        $selectedDate
    )
    {
        throw new Exception(
            'Invalid date format.'
        );
    }


    // =====================================================
    // Database Connection
    // =====================================================

    $database =
        new SalesPerformanceDb();


    $db =
        $database->getConnection();


    if (!$db)
    {
        throw new Exception(
            'Database connection failed.'
        );
    }


    // =====================================================
    // Hourly Sales Query
    //
    // EXISTS is used instead of joining invoicedetails
    // directly.
    //
    // This prevents NetAmount from being duplicated
    // when one invoice contains multiple products.
    // =====================================================

    $sql = "SELECT

            HOUR(
                h.InvStartTime
            ) AS sale_hour,

            COALESCE(
                SUM(h.NetAmount),
                0
            ) AS total_sales,

            COUNT(h.IHID)
                AS invoice_count

        FROM invoiceheader h

        WHERE

            h.shop_SHID = :shop_id

            AND h.InvStat = 1

            AND h.EffectiveDate =
                :selected_date

            AND h.InvStartTime
                IS NOT NULL

            AND EXISTS
            (
                SELECT 1

                FROM invoicedetails d

                WHERE
                    d.InvoiceHeader_IHID
                    =
                    h.IHID
            )

        GROUP BY

            HOUR(
                h.InvStartTime
            )

        ORDER BY

            sale_hour ASC

    ";


    $stmt =
        $db->prepare(
            $sql
        );


    $stmt->execute([

        ':shop_id' =>
            $shopId,

        ':selected_date' =>
            $selectedDate

    ]);


    $rows =
        $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );


    // =====================================================
    // Totals
    // =====================================================

    $totalRevenue = 0;

    $totalInvoices = 0;


    // =====================================================
    // Temporary Hour Map
    // =====================================================

    $hourMap = [];


    foreach ($rows as $row)
    {

        $hour =
            (int)$row['sale_hour'];


        $amount =
            (float)$row['total_sales'];


        $invoiceCount =
            (int)$row['invoice_count'];


        $hourMap[$hour] =
            $amount;


        $totalRevenue +=
            $amount;


        $totalInvoices +=
            $invoiceCount;

    }


    // =====================================================
    // Average Bill
    // =====================================================

    $averageBill = 0;


    if ($totalInvoices > 0)
    {

        $averageBill =
            $totalRevenue /
            $totalInvoices;

    }


    // =====================================================
    // Hourly Chart Data
    //
    // We fill missing hours between the first and last sale.
    //
    // Example:
    //
    // 9AM  = 5000
    // 10AM = no sale
    // 11AM = 4000
    //
    // Response:
    //
    // 9AM  = 5000
    // 10AM = 0
    // 11AM = 4000
    //
    // =====================================================

    $hourlySales = [];


    if (!empty($hourMap))
    {

        $hours =
            array_keys(
                $hourMap
            );


        $firstHour =
            min($hours);


        $lastHour =
            max($hours);


        for (
            $hour = $firstHour;
            $hour <= $lastHour;
            $hour++
        )
        {

            $hourlySales[] = [

                'hour' =>
                    $hour,

                'time' =>
                    formatHourLabel(
                        $hour
                    ),

                'amount' =>
                    isset(
                        $hourMap[$hour]
                    )
                        ? round(
                            $hourMap[$hour],
                            2
                        )
                        : 0

            ];

        }

    }


    // =====================================================
    // Success Response
    // =====================================================

    $response = [

        'status' => 1,

        'message' =>
            'Sales performance loaded successfully.',

        'data' => [

            'selectedDate' =>
                $selectedDate,


            'averageBill' =>
                round(
                    $averageBill,
                    2
                ),


            'totalRevenue' =>
                round(
                    $totalRevenue,
                    2
                ),


            'invoiceCount' =>
                $totalInvoices,


            'hourlySales' =>
                $hourlySales

        ]

    ];

}
catch (Throwable $e)
{

    // =====================================================
    // Log actual error
    // =====================================================

    error_log(

        'Sales Performance Error: '
        .
        $e->getMessage()

    );


    // =====================================================
    // Safe response
    // =====================================================

    $response = [

        'status' => 0,

        'message' =>
            'Unable to load sales performance.',

        'data' => null

    ];

}


// =========================================================
// JSON Response
// =========================================================

echo json_encode(
    $response
);

exit;


// =========================================================
// Format Hour Label
// =========================================================

function formatHourLabel($hour)
{

    $hour =
        (int)$hour;


    // Midnight
    if ($hour === 0)
    {
        return '12AM';
    }


    // 1 AM - 11 AM
    if ($hour < 12)
    {
        return $hour . 'AM';
    }


    // Noon
    if ($hour === 12)
    {
        return '12PM';
    }


    // 1 PM - 11 PM
    return (
        $hour - 12
    ) . 'PM';

}