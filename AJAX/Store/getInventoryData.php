<?php
session_start();

include "../../Includes/config.php";
include "../../Model/DB_Class.php";
include "../../Model/shop_class.php";

header('Content-Type: application/json');

$shopObj = new Shop();
$dbObj   = new DBTransactions();
$shop_id = (int)($_SESSION['shop_id'] ?? 0);

$response = [];

if ($shop_id <= 0) {
    echo json_encode($response);
    exit;
}

$noStock = (isset($_GET["noStock"]) && (int)$_GET["noStock"] === 1);

// Build base SQL (single query - faster than N+1)
// $sql = "SELECT 
//         inventory.products_PDID AS product_id,
//         SUM(inventory.CurrentQty)      AS totalCurrentQty,
//         SUM(inventory.BillQty)         AS totalBillQty,
//         SUM(inventory.ReturnQty)       AS totalReturnQty,
//         SUM(inventory.TransferInQty)   AS totalTransferIn,
//         SUM(inventory.TransferOutQty)  AS totalTransferOut,
//         products.ProdImage,
//         products.Barcode,
//         products.ItemName
//     FROM inventory
//     INNER JOIN products ON products.PDID = inventory.products_PDID
//     WHERE inventory.shop_SHID = $shop_id AND inventory.is_default !=1
//       AND products.ItemType = 'P'
//       AND products.ProductStat = '1' 
// ";

// $sql .= " GROUP BY inventory.products_PDID ORDER BY products.ProductNo ASC";

$sql = "SELECT products.PDID AS product_id,
        COALESCE(SUM(inventory.CurrentQty), 0)     AS totalCurrentQty,
        COALESCE(SUM(inventory.BillQty), 0)        AS totalBillQty,
        COALESCE(SUM(inventory.ReturnQty), 0)      AS totalReturnQty,
        COALESCE(SUM(inventory.TransferInQty), 0)  AS totalTransferIn,
        COALESCE(SUM(inventory.TransferOutQty), 0) AS totalTransferOut,
        products.ProdImage,
        products.Barcode,
        products.ItemName
    FROM products
    LEFT JOIN inventory
        ON inventory.products_PDID = products.PDID
        AND inventory.shop_SHID = $shop_id
        AND inventory.is_default != 1
    WHERE products.ItemType = 'P'
    GROUP BY
        products.PDID,
        products.ProdImage,
        products.Barcode,
        products.ItemName,
        products.ProductNo
    ORDER BY products.ProductNo ASC";

$dbData = $dbObj->getData($sql);

$sl = 1;

foreach ($dbData as $row) {

    $img = $row['ProdImage'] ?? '';

    // Web paths (what you send to frontend)
    $webProdPath = "../Assets/Images/prod_images/" . $img;
    $webDefault  = "../Assets/Images/icons/product.png";

    // Server path (for file_exists check)
    $serverProdPath = __DIR__ . "/../../Assets/Images/prod_images/" . $img;

    if (!empty($img) && file_exists($serverProdPath)) {
        $imagePath = $webProdPath;
    } else {
        $imagePath = $webDefault;
    }

    $response[] = [
        "sl"               => $sl++,
        "product_id"       => (int)$row['product_id'],
        "item_name"        => $row['ItemName'] ?? '',
        "barcode"          => $row['Barcode'] ?? '',
        "image"            => $imagePath,
        "current_qty"      => (float)($row['totalCurrentQty'] ?? 0),
        "bill_qty"         => (float)($row['totalBillQty'] ?? 0),
        "return_qty"       => (float)($row['totalReturnQty'] ?? 0),
        "transfer_in_qty"  => (float)($row['totalTransferIn'] ?? 0),
        "transfer_out_qty" => (float)($row['totalTransferOut'] ?? 0)
    ];
}

echo json_encode($response);
exit;
