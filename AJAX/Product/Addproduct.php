<?php

session_start();

header('Content-Type: application/json');

include_once __DIR__ . "/../../Includes/config.php";
include_once __DIR__ . "/../../Model/Products_class.php";


// ==========================================
// Session
// ==========================================

$user_id = isset($_SESSION['user_id'])
    ? (int) $_SESSION['user_id']
    : 0;

$shop_id = isset($_SESSION['shop_id'])
    ? (int) $_SESSION['shop_id']
    : 0;

$condition = $_GET["condition"] ?? "";

$response = [];


// ==========================================
// Validate Session
// ==========================================

if ($user_id <= 0 || $shop_id <= 0) {

    echo json_encode([
        "status" => 0,
        "msg" => "Session expired. Please login again."
    ]);

    exit;
}


// ==========================================
// Validate Condition
// ==========================================

if (empty($condition)) {

    echo json_encode([
        "status" => 0,
        "msg" => "Invalid condition."
    ]);

    exit;
}


// ==========================================
// Validate Request Method
// ==========================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "status" => 0,
        "msg" => "Invalid request method."
    ]);

    exit;
}


$data = $_POST;

$proObj = new newProduct();


// ==========================================
// Conditions
// ==========================================

switch ($condition) {


    // ======================================
    // CREATE NEW PRODUCT
    // ======================================

    case "new":


        // ==================================
        // 1. CREATE PRODUCT
        // ==================================

        $product = $proObj->addProduct(
            data: $data,
            user_USID: $user_id
        );


        if (!$product["status"]) {

            $response = [
                "status" => 0,

                "product" => [
                    "status" => 0,
                    "msg" => "Product could not be created.",
                    "rmsg" => $product["message"] ?? ""
                ],

                "msg" => "Product creation failed."
            ];

            break;
        }


        $PDID = (int) $product["PDID"];


        $response["product"] = [
            "status" => 1,
            "PDID" => $PDID,
            "msg" => "Product created successfully."
        ];



        // ======================================================
        // 2. CREATE DEFAULT INVENTORY
        //
        // CurrentQty   = 0
        // is_default   = 1
        // is_openStock = 0
        // ======================================================

        $defaultInventory = $proObj->create_inventory(
            PDID: $PDID,
            shop_SHID: $shop_id,
            CurrentQty: 0,
            OpeningStock: 0,
            is_default: 1
        );


        if ($defaultInventory["status"]) {

            $defaultINID = (int) $defaultInventory["data"]["INID"];
            $defaultBatchID = $defaultInventory["data"]["BatchID"];


            $response["default_inventory"] = [
                "status" => 1,
                "INID" => $defaultINID,
                "BatchID" => $defaultBatchID,
                "CurrentQty" => 0,
                "is_default" => 1,
                "is_openStock" => 0,
                "msg" => "Default inventory created successfully."
            ];


            // ==================================
            // Default Inventory Price History
            // ==================================

            $defaultPriceHistory = $proObj->create_pricehistory(
                INID: $defaultINID,
                BatchID: $defaultBatchID,
                PDID: $PDID,
                data: $data
            );


            if ($defaultPriceHistory["status"]) {

                $response["default_pricehistory"] = [
                    "status" => 1,
                    "PriceHistoryID" =>
                        $defaultPriceHistory["PriceHistoryID"] ?? null,
                    "msg" => "Default price history created successfully."
                ];

            } else {

                $response["default_pricehistory"] = [
                    "status" => 0,
                    "msg" => "Default price history could not be created.",
                    "rmsg" => $defaultPriceHistory["message"] ?? ""
                ];
            }

        } else {

            $response["default_inventory"] = [
                "status" => 0,
                "msg" => "Default inventory could not be created.",
                "rmsg" => $defaultInventory["message"] ?? ""
            ];


            $response["default_pricehistory"] = [
                "status" => 0,
                "skipped" => true,
                "msg" => "Default price history skipped because default inventory failed."
            ];
        }



        // ======================================================
        // 3. GET OPENING QUANTITY
        // ======================================================

        $openingQty = isset($data["opening_qty"])
            ? (float) trim($data["opening_qty"])
            : 0;


        // Negative opening quantity becomes 0
        $openingQty = max(0, $openingQty);



        // ======================================================
        // 4. CREATE OPENING STOCK INVENTORY
        //
        // CurrentQty   = opening_qty
        // is_default   = 0
        // is_openStock = 1
        //
        // ALWAYS CREATE THIS EVEN IF opening_qty = 0
        // ======================================================

        $openingInventory = $proObj->create_inventory(
            PDID: $PDID,
            shop_SHID: $shop_id,
            CurrentQty: $openingQty,
            OpeningStock: 1,
            is_default: 0
        );


        if ($openingInventory["status"]) {

            $openingINID = (int) $openingInventory["data"]["INID"];
            $openingBatchID = $openingInventory["data"]["BatchID"];


            $response["opening_inventory"] = [
                "status" => 1,
                "INID" => $openingINID,
                "BatchID" => $openingBatchID,
                "CurrentQty" => $openingQty,
                "is_default" => 0,
                "is_openStock" => 1,
                "msg" => "Opening stock inventory created successfully."
            ];


            // ==================================
            // Opening Inventory Price History
            // ==================================

            $openingPriceHistory = $proObj->create_pricehistory(
                INID: $openingINID,
                BatchID: $openingBatchID,
                PDID: $PDID,
                data: $data
            );


            if ($openingPriceHistory["status"]) {

                $response["opening_pricehistory"] = [
                    "status" => 1,
                    "PriceHistoryID" =>
                        $openingPriceHistory["PriceHistoryID"] ?? null,
                    "msg" => "Opening stock price history created successfully."
                ];

            } else {

                $response["opening_pricehistory"] = [
                    "status" => 0,
                    "msg" => "Opening stock price history could not be created.",
                    "rmsg" => $openingPriceHistory["message"] ?? ""
                ];
            }

        } else {

            $response["opening_inventory"] = [
                "status" => 0,
                "CurrentQty" => $openingQty,
                "msg" => "Opening stock inventory could not be created.",
                "rmsg" => $openingInventory["message"] ?? ""
            ];


            $response["opening_pricehistory"] = [
                "status" => 0,
                "skipped" => true,
                "msg" => "Opening stock price history skipped because opening inventory failed."
            ];
        }



        // ======================================================
        // 5. FINAL STATUS
        // ======================================================

        $defaultInventoryOK =
            ($response["default_inventory"]["status"] ?? 0) === 1;

        $defaultPriceOK =
            ($response["default_pricehistory"]["status"] ?? 0) === 1;

        $openingInventoryOK =
            ($response["opening_inventory"]["status"] ?? 0) === 1;

        $openingPriceOK =
            ($response["opening_pricehistory"]["status"] ?? 0) === 1;


        if (
            $defaultInventoryOK &&
            $defaultPriceOK &&
            $openingInventoryOK &&
            $openingPriceOK
        ) {

            $response["status"] = 1;

            $response["msg"] =
                "Product, default inventory, opening inventory and price histories created successfully.";

        } else {

            $response["status"] = 2;

            $response["msg"] =
                "Product was created, but one or more inventory or price history records failed.";
        }


        break;



    // ======================================
    // FUTURE CONDITIONS
    // ======================================

    case "update":

    $PDID = isset($_POST["PDID"])
        ? (int) $_POST["PDID"]
        : 0;


    if ($PDID <= 0) {

        $response = [
            "status" => 0,
            "msg" => "Invalid product ID."
        ];

        break;
    }


    // ==========================================
    // Update Product
    // ==========================================

    $updateProduct = $proObj->updateProduct(
        PDID: $PDID,
        data: $_POST,
        user_USID: $user_id
    );


    if (!$updateProduct["status"]) {

        $response = [
            "status" => 0,
            "product" => [
                "status" => 0,
                "msg" => "Product could not be updated.",
                "rmsg" => $updateProduct["message"] ?? ""
            ],
            "msg" => "Product update failed."
        ];

        break;
    }


    $response["product"] = [
        "status" => 1,
        "data" => $updateProduct["data"] ?? null,
        "msg" => "Product updated successfully."
    ];


    // ==========================================
    // Opening Quantity
    // ==========================================

    $openingQty = isset($_POST["opening_qty"])
        ? (float) $_POST["opening_qty"]
        : 0;

    $openingQty = max(0, $openingQty);


    // ==========================================
    // Update Opening Inventory
    // ==========================================

    $updateInventory = $proObj->updateOpeningInventory(
        PDID: $PDID,
        shop_SHID: $shop_id,
        CurrentQty: $openingQty
    );


    if ($updateInventory["status"]) {

        $response["inventory"] = [
            "status" => 1,
            "msg" => "Opening inventory updated successfully."
        ];

    } else {

        $response["inventory"] = [
            "status" => 0,
            "msg" => "Opening inventory could not be updated.",
            "rmsg" => $updateInventory["message"] ?? ""
        ];
    }


    // ==========================================
    // Update Price History
    // ==========================================

    $updatePriceHistory = $proObj->updateOpeningPriceHistory(
        PDID: $PDID,
        shop_SHID: $shop_id,
        data: $_POST
    );


    if ($updatePriceHistory["status"]) {

        $response["pricehistory"] = [
            "status" => 1,
            "msg" => "Price history updated successfully."
        ];

    } else {

        $response["pricehistory"] = [
            "status" => 0,
            "msg" => "Price history could not be updated.",
            "rmsg" => $updatePriceHistory["message"] ?? ""
        ];
    }


    // ==========================================
    // Final Status
    // ==========================================

    if (
        $response["inventory"]["status"] === 1 &&
        $response["pricehistory"]["status"] === 1
    ) {

        $response["status"] = 1;
        $response["msg"] = "Product updated successfully.";

    } else {

        $response["status"] = 2;
        $response["msg"] =
            "Product updated, but one or more related records could not be updated.";
    }

    break;

    default:

        $response = [
            "status" => 0,
            "msg" => "Invalid condition."
        ];

        break;
}


// ==========================================
// Final JSON Response
// ==========================================

echo json_encode($response);

exit;