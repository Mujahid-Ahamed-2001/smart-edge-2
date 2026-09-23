<?php

session_start();

header('Content-Type: application/json');

include_once __DIR__ . "/../../Includes/config.php";
include_once __DIR__ . "/../../Model/DB_Class.php";


$dbObj = new DBTransactions();

$shop_id = isset($_SESSION["shop_id"])
    ? (int) $_SESSION["shop_id"]
    : 0;
$user_id = isset($_SESSION["user_id"])
    ? (int) $_SESSION["user_id"]
    : 0;


// ==========================================
// Validate Session
// ==========================================

if ($shop_id <= 0) {

    echo json_encode([
        "status" => 0,
        "data" => [],
        "msg" => "Invalid shop."
    ]);

    exit;
}


// ==========================================
// Get Multi Products
// ==========================================

$sql = "SELECT
        p.PDID,
        p.Barcode,
        p.ItemName,
        p.Subcategories_SCID,
        p.ProdPurchasePrice,
        p.ProdSellPrice,
        p.low_stock_qty,

        COALESCE(
            (
                SELECT i.CurrentQty
                FROM inventory i

                WHERE i.products_PDID = p.PDID
                AND i.shop_SHID = {$shop_id}
                AND i.is_openStock = 1

                ORDER BY i.INID DESC

                LIMIT 1
            ),
            0
        ) AS opening_qty

    FROM products p

    WHERE p.multi = 1 AND p.user_USID='$user_id'

    ORDER BY p.PDID DESC
";


$data = $dbObj->getData($sql);


// ==========================================
// Response
// ==========================================

echo json_encode([
    "status" => 1,
    "data" => $data,
    "msg" => "Products fetched successfully."
]);

exit;