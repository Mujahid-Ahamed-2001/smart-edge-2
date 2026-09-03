<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();
$shop_id = $_SESSION["shop_id"] ?? 0;

// Get date range or default to current month
$start = !empty($_GET['start']) ? $_GET['start'] : date('Y-m-01');
$end   = !empty($_GET['end']) ? $_GET['end'] : date('Y-m-t');

$PaymethodName = [];
$TotalAmount = [];
$sql = "SELECT 
            pm.PaymethodName,
            SUM(
                CASE 
                    WHEN pm.PMID = 1
                        THEN t.TransferAmount + IFNULL(h.CustBalance, 0)
                    ELSE t.TransferAmount
                END
            ) AS TotalAmount
        FROM transactions t
        INNER JOIN invoiceheader h 
            ON t.InvoiceHeader_IHID = h.IHID
        INNER JOIN paymethod pm 
            ON t.paymethod_PMID = pm.PMID
        WHERE 
            h.InvStat = 1
            AND h.shop_SHID = ?
            AND h.EffectiveDate BETWEEN ? AND ?
            AND t.TransactionStat = 1
        GROUP BY pm.PaymethodName
        ORDER BY TotalAmount DESC";

$params = [$shop_id, $start, $end];
$bestSellingProducts = $dbObj->getMultipleData($sql, $params);
$i=1;
if (!empty($bestSellingProducts)) {
    foreach ($bestSellingProducts as $row) {
        $PaymethodName[] = $i.". ".$row['PaymethodName'];
        $TotalAmount[] = (float)$row['TotalAmount'];
        $i++;
    }
}

echo json_encode([
    "PaymethodName" => $PaymethodName,
    "TotalAmount" => $TotalAmount
]);