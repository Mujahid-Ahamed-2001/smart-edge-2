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
// Database
// =========================================================

require_once '../../Includes/config.php';


class InvoiceSummaryDb extends Dbh
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
        'Unable to load invoice summary.',

    'data' => null

];


try
{

    // =====================================================
    // Validate Shop
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
    // Validate Date
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
    // Connection
    // =====================================================

    $database =
        new InvoiceSummaryDb();


    $db =
        $database->getConnection();


    if (!$db)
    {
        throw new Exception(
            'Database connection failed.'
        );
    }
    // =====================================================
    // Shop Payment Method Configuration
    // =====================================================

    $paymentMethodSql = "SELECT
            paymethod_PMID

        FROM shoppaymethod

        WHERE
            shop_SHID = :shop_id

    ";


    $paymentMethodStmt =
        $db->prepare(
            $paymentMethodSql
        );


    $paymentMethodStmt->execute([

        ':shop_id' =>
            $shopId

    ]);


    $shopPaymentMethods =
        $paymentMethodStmt->fetchAll(
            PDO::FETCH_COLUMN
        );


    // Convert all values to integers
    $shopPaymentMethods =
        array_map(
            'intval',
            $shopPaymentMethods
        );


    // =====================================================
    // Determine Cash / Card Visibility
    //
    // No shoppaymethod entries:
    // Cash + Card are defaults.
    //
    // If entries exist:
    // only show payment methods assigned to shop.
    // =====================================================

    if (empty($shopPaymentMethods))
    {

        $showCashSales = true;

        $showCardSales = true;

    }
    else
    {

        $showCashSales =
            in_array(
                1,
                $shopPaymentMethods,
                true
            );


        $showCardSales =
            in_array(
                2,
                $shopPaymentMethods,
                true
            );

    }

    // =====================================================
    // Invoice Summary
    //
    // CREDIT:
    // total active payments < NetAmount
    //
    // CASH:
    // Fully paid AND only Cash payment used
    //
    // MIXED:
    // Fully paid AND more than one payment method used
    //
    // IMPORTANT:
    // Credit is checked separately from payment methods.
    // =====================================================

    $sql = "SELECT


            /* =========================================
               COMPLETED INVOICES
            ========================================= */

            COUNT(h.IHID)
                AS completed_invoices,

            COALESCE(
                SUM(
                    CASE

                        WHEN
                            COALESCE(t.total_paid, 0)
                                >=
                            COALESCE(h.NetAmount, 0)

                            AND

                            COALESCE(
                                t.payment_method_count,
                                0
                            ) = 1

                            AND

                            COALESCE(
                                t.cash_paid,
                                0
                            )
                                >=
                            COALESCE(
                                h.NetAmount,
                                0
                            )

                        THEN 1

                        ELSE 0

                    END
                ),
                0
            ) AS cash_sales,
            /* =========================================
                CARD SALES

                Fully paid
                AND only one payment method
                AND that payment method is Card
                ========================================= */

                COALESCE(
                    SUM(
                        CASE

                            WHEN
                                COALESCE(
                                    t.total_paid,
                                    0
                                )
                                    >=
                                COALESCE(
                                    h.NetAmount,
                                    0
                                )

                                AND

                                COALESCE(
                                    t.payment_method_count,
                                    0
                                ) = 1

                                AND

                                COALESCE(
                                    t.card_paid,
                                    0
                                )
                                    >=
                                COALESCE(
                                    h.NetAmount,
                                    0
                                )

                            THEN 1

                            ELSE 0

                        END
                    ),
                    0
                ) AS card_sales,


            /* =========================================
               CREDIT SALES

               Any invoice where total active payments
               are lower than NetAmount
            ========================================= */

            COALESCE(
                SUM(
                    CASE

                        WHEN
                            COALESCE(
                                t.total_paid,
                                0
                            )
                                <
                            COALESCE(
                                h.NetAmount,
                                0
                            )

                        THEN 1

                        ELSE 0

                    END
                ),
                0
            ) AS credit_sales,


            /* =========================================
               MIXED PAYMENTS

               Must be fully paid first.

               A partially-paid Cash + Card invoice is
               CREDIT, not Mixed.
            ========================================= */

            COALESCE(
                SUM(
                    CASE

                        WHEN
                            COALESCE(
                                t.total_paid,
                                0
                            )
                                >=
                            COALESCE(
                                h.NetAmount,
                                0
                            )

                            AND

                            COALESCE(
                                t.payment_method_count,
                                0
                            ) > 1

                        THEN 1

                        ELSE 0

                    END
                ),
                0
            ) AS mixed_payments,

            COALESCE(
                SUM(
                    d.items_sold
                ),
                0
            ) AS items_sold,

            COALESCE(
                SUM(
                    GREATEST(

                        COALESCE(
                            h.NetAmount,
                            0
                        )
                        -
                        COALESCE(
                            t.total_paid,
                            0
                        ),

                        0

                    )
                ),
                0
            ) AS credit_amount


        FROM invoiceheader h

        INNER JOIN
        (

            SELECT

                InvoiceHeader_IHID,


                COALESCE(
                    SUM(
                        CASE

                            WHEN ItemType = 1

                            THEN COALESCE(
                                SellQty,
                                0
                            )

                            ELSE 0

                        END
                    ),
                    0
                ) AS items_sold


            FROM invoicedetails


            GROUP BY

                InvoiceHeader_IHID

        ) d

            ON d.InvoiceHeader_IHID
                =
               h.IHID


        /* =============================================
           PAYMENTS / TRANSACTIONS

           Only active transactions are included.

           TransactionStat = 1
        ============================================= */

        LEFT JOIN
        (

            SELECT

                InvoiceHeader_IHID,


                /* Total Paid */

                COALESCE(
                    SUM(
                        CASE

                            WHEN TransactionStat = 1

                            THEN COALESCE(
                                TransferAmount,
                                0
                            )

                            ELSE 0

                        END
                    ),
                    0
                ) AS total_paid,


                /* Number of payment methods actually used */

                COUNT(
                    DISTINCT
                    CASE

                        WHEN
                            TransactionStat = 1

                            AND

                            COALESCE(
                                TransferAmount,
                                0
                            ) > 0

                        THEN paymethod_PMID

                        ELSE NULL

                    END
                ) AS payment_method_count,

                /* Amount paid through Cash */

                COALESCE(
                    SUM(
                        CASE

                            WHEN
                                TransactionStat = 1

                                AND

                                paymethod_PMID = 1

                            THEN COALESCE(
                                TransferAmount,
                                0
                            )

                            ELSE 0

                        END
                    ),
                    0
                ) AS cash_paid,


                /* Amount paid through Card */

                COALESCE(
                    SUM(
                        CASE

                            WHEN
                                TransactionStat = 1

                                AND

                                paymethod_PMID = 2

                            THEN COALESCE(
                                TransferAmount,
                                0
                            )

                            ELSE 0

                        END
                    ),
                    0
                ) AS card_paid


            FROM transactions


            GROUP BY

                InvoiceHeader_IHID

        ) t

            ON t.InvoiceHeader_IHID
                =
               h.IHID


        WHERE

            h.shop_SHID =
                :shop_id

            AND

            h.EffectiveDate =
                :selected_date

            AND

            h.InvStat = 1

    ";


    // =====================================================
    // Execute
    // =====================================================

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


    $row =
        $stmt->fetch(
            PDO::FETCH_ASSOC
        );


    // =====================================================
    // Results
    // =====================================================

    $completedInvoices =
        isset($row['completed_invoices'])
            ? (int)$row['completed_invoices']
            : 0;


    $cashSales =
        isset($row['cash_sales'])
            ? (int)$row['cash_sales']
            : 0;
    $cardSales =
        isset($row['card_sales'])
            ? (int)$row['card_sales']
            : 0;

    $creditSales =
        isset($row['credit_sales'])
            ? (int)$row['credit_sales']
            : 0;


    $mixedPayments =
        isset($row['mixed_payments'])
            ? (int)$row['mixed_payments']
            : 0;


    $itemsSold =
        isset($row['items_sold'])
            ? (float)$row['items_sold']
            : 0;


    $creditAmount =
        isset($row['credit_amount'])
            ? (float)$row['credit_amount']
            : 0;


    // =====================================================
    // Success
    // =====================================================

    $response = [

        'status' => 1,

        'message' =>
            'Invoice summary loaded successfully.',

        'data' => [

            'selectedDate' =>
                $selectedDate,

            'completed' =>
                $completedInvoices,

            'cashSales' =>
                $cashSales,

            'showCashSales' =>
                $showCashSales,


            'cardSales' =>
                $cardSales,

            'showCardSales' =>
                $showCardSales,

            'creditSales' =>
                $creditSales,

            'mixedPayments' =>
                $mixedPayments,

            'itemsSold' =>
                $itemsSold,

            'creditAmount' =>
                round(
                    $creditAmount,
                    2
                )

        ]

    ];

}
catch (Throwable $e)
{

    // =====================================================
    // Log Real Error
    // =====================================================

    error_log(

        'Invoice Summary Error: '
        .
        $e->getMessage()

    );


    // =====================================================
    // Safe Response
    // =====================================================

    $response = [

        'status' => 0,

        'message' =>
            'Unable to load invoice summary.',

        'data' => null

    ];

}


// =========================================================
// JSON
// =========================================================

echo json_encode(
    $response
);

exit;