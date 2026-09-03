<?php
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();
$shop_id = $_SESSION["shop_id"] ?? 0;

$start = !empty($_GET['start']) ? $_GET['start'] : date('Y-m-01');
$end   = !empty($_GET['end']) ? $_GET['end'] : date('Y-m-t');

$dates = [];
$revenue = [];
$cost = [];

$sql = "SELECT
            DATE(h.EffectiveDate) AS EffectiveDate,
            IFNULL(SUM(h.NetAmount), 0) AS TotalRevenue,
            IFNULL(SUM(dc.TotalCost), 0) AS TotalCost
        FROM invoiceheader h

        INNER JOIN (
            SELECT
                InvoiceHeader_IHID,
                SUM(IFNULL(total_cost, 0)) AS TotalCost
            FROM invoicedetails
            GROUP BY InvoiceHeader_IHID
        ) dc
            ON dc.InvoiceHeader_IHID = h.IHID

        WHERE h.InvStat = 1
          AND h.shop_SHID = ?
          AND h.EffectiveDate >= ?
          AND h.EffectiveDate < DATE_ADD(?, INTERVAL 1 DAY)

        GROUP BY DATE(h.EffectiveDate)
        ORDER BY DATE(h.EffectiveDate) ASC";

$params = [$shop_id, $start, $end];

$data = $dbObj->getMultipleData($sql, $params);

if (!empty($data)) {
    foreach ($data as $row) {
        $dates[] = date("d/m", strtotime($row['EffectiveDate']));
        $revenue[] = (float)$row['TotalRevenue'];
        $cost[] = (float)$row['TotalCost'];
    }
}

echo json_encode([
    "dates" => $dates,
    "revenue" => $revenue,
    "cost" => $cost
]);