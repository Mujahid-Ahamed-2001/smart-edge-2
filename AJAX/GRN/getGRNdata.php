<?php 
include "../../Includes/config.php";
include "../../Model/GRN_class.php";

$shop_id = $_POST['ShopID'];
$start = isset($_POST['start']) ? $_POST['start'] : null;
$end = isset($_POST['end']) ? $_POST['end'] : null;
$supplierID = isset($_POST['supplierID']) ? $_POST['supplierID'] : null;
$grnObj = new GRN();
$grnData = $grnObj->getAllGRNHeader(shop_id: $shop_id, start: $start, end: $end, supplierID: $supplierID);
$response = []; // ✅ Initialize array
foreach ($grnData as $row) 
{
    $response[] = [
        "GHID"=> $row['GHID'],
        "GRNHeaderNo" => $row['GRNHeaderNo'] ?? '',
        "ShopName" => $row['ShopName'] ?? '',
        "EffectiveDate"=> $row['EffectiveDate'] ?? '',
        "InvoiceNo"=> $row['InvoiceNo'] ?? '',
        "ItemCount"=> $row['LineCount'] ?? 0,
        "TotalPurchasePrice"=> $row['TotalPurchasePrice'] ?? 0,
        "TotalSellPrice"=> $row['TotalSellPrice'] ?? 0,
        "refference"=> $row['refference'] ?? 0,
        "SupplierName"=> $row['SupplierName'] ?? 0,
        "UserName"=> $row['UserName'] ?? 0,
        "GRNStat"=> $row['GRNStat'] ?? 0
    ];
}
echo json_encode($response);
exit;