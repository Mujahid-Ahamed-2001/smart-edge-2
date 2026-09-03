<?php
session_start();

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

header("Content-Type: application/json");

$shop_id = (int)($_SESSION["shop_id"] ?? 0);

$result = [
    "inventory"      => [],
    "total_selling"  => 0,
    "total_purchase" => 0
];

if ($shop_id <= 0) {
    echo json_encode($result);
    exit;
}

$dbObj = new DBTransactions();

$sql = "SELECT
            i.*,
            ph.SellingPrice,
            ph.PurchasePrice,
            p.PDID,
            p.ItemName,
            p.ProductNo,
            p.ProdImage,
            p.Barcode
        FROM inventory i
        INNER JOIN pricehistory ph
            ON ph.Inventory_INID = i.INID
        INNER JOIN products p
            ON p.PDID = i.products_PDID
        WHERE i.shop_SHID = ?
          AND p.ItemType = 'P'
        ORDER BY p.ItemName ASC";

$itemData = $dbObj->getMultipleData($sql, [$shop_id]);
$total_selling = 0;
$total_purchase = 0;
foreach ($itemData as $row) {
    $currentQty    = (float)($row["CurrentQty"] ?? 0);
    $sellingPrice  = (float)($row["SellingPrice"] ?? 0);
    $purchasePrice = (float)($row["PurchasePrice"] ?? 0);

    $totalSellingPrice  = $sellingPrice * $currentQty;
    $totalPurchasePrice = $purchasePrice * $currentQty;

    $total_selling  += $totalSellingPrice;
    $total_purchase += $totalPurchasePrice;

    // [] is important—it appends each inventory row.
    $result["inventory"][] = [
        "PDID"              => $row["PDID"],
        "ItemName"          => $row["ItemName"],
        "ProductNo"         => $row["ProductNo"],
        "SellingPrice"      => number_format($sellingPrice, 2, ".", ","),
        "PurchasePrice"     => number_format($purchasePrice, 2, ".", ","),
        "totalSellingPrice" => number_format($totalSellingPrice, 2, ".", ","),
        "totalPurchasePrice"=> number_format($totalPurchasePrice, 2, ".", ","),
        "BatchNo"           => $row["BatchID"],
        "CurrentQty"        => $currentQty,
        "BillQty"           => $row["BillQty"],
        "ReturnQty"         => $row["ReturnQty"],
        "TransferInQty"     => $row["TransferInQty"],
        "TransferOutQty"    => $row["TransferOutQty"],
        "ProdImage"         => $row["ProdImage"],
        "Barcode"           => $row["Barcode"]
    ];
}

$total_selling = number_format($total_selling, 2, ".", ",");
$total_purchase = number_format($total_purchase, 2, ".", ",");

echo json_encode([
    "inventory" => $result["inventory"],
    "total_selling" => $total_selling,
    "total_purchase" => $total_purchase
]);