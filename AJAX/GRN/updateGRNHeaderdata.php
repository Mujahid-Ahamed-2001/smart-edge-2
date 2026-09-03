<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
include "../../Model/inventory_class.php";
include "../../Model/pricehistory_class.php";
include "../../Model/GRN_class.php";
$dbObj = new DBTransactions();
$invObj = new Inventory();
$priceObj = new PriceHistory();
$grnObj = new GRN();

$dbObj    = new DBTransactions();
$invObj   = new Inventory();
$priceObj = new PriceHistory();
$grnObj   = new GRN();

$GHID      = isset($_POST["GHID"]) ? (int) $_POST["GHID"] : 0;
$condition = isset($_POST["condition"]) ? (int) $_POST["condition"] : 1;
$user_id   = isset($_SESSION["user_id"]) ? (int) $_SESSION["user_id"] : 0;

$response = [
    "success" => [],
    "error"   => []
];

date_default_timezone_set("Asia/Colombo");
$effectiveDate = date("Y-m-d");

if ($GHID <= 0 || $condition !== 1) {
    $response["error"][] = ["Missing GRN ID"];
    echo json_encode($response);
    exit;
}

$grnSupplier = isset($_POST["grnSupplier"])
    ? (int) $_POST["grnSupplier"]
    : 0;

$referenceNo = isset($_POST["referenceNo"])
    ? trim($_POST["referenceNo"])
    : "";

$grnStatus = isset($_POST["grnStatus"])
    ? (int) $_POST["grnStatus"]
    : 0;

$purchaseDate = isset($_POST["purchaseDate"])
    ? trim($_POST["purchaseDate"])
    : "";

$shopLocation = isset($_POST["shopLocation"])
    ? (int) $_POST["shopLocation"]
    : 0;

$addNotes = isset($_POST["addNotes"])
    ? trim($_POST["addNotes"])
    : "";

if (
    $grnSupplier <= 0 ||
    $grnStatus =="" ||
    empty($purchaseDate) ||
    $shopLocation <= 0
) {
    $response["error"][] = ["Missing GRN Data"];
    echo json_encode($response);
    exit;
}

/*
 * Validate the GRN.
 */
$selectSql = "
    SELECT *
    FROM grnheader
    WHERE GHID = {$GHID}
    LIMIT 1
";

$grnHeaderData = $dbObj->getData($selectSql);

if (empty($grnHeaderData)) {
    $response["error"][] = ["GRN not found"];
    echo json_encode($response);
    exit;
}

$grnHeader = $grnHeaderData[0];
$currentStatus = (int) $grnHeader["GRNStat"];

if ($currentStatus === 2) {
    $response["error"][] = ["GRN is already verified"];
    echo json_encode($response);
    exit;
}

if ($currentStatus === 3) {
    $response["error"][] = ["GRN is already cancelled"];
    echo json_encode($response);
    exit;
}

/*
 * Use your DB class escaping method if it provides one.
 * This basic escaping prevents quotes from breaking the SQL.
 */
$referenceNo = addslashes($referenceNo);
$addNotes    = addslashes($addNotes);
$purchaseDate = addslashes($purchaseDate);

/*
 * Update the GRN header.
 *
 * add_notes is always updated so that the user can also clear
 * an existing note.
 */
$updateSql = "
    UPDATE grnheader
    SET
        EffectiveDate  = '{$purchaseDate}',
        refference     = '{$referenceNo}',
        GRNStat        = {$grnStatus},
        Suppliers_SPID = {$grnSupplier},
        shop_SHID      = {$shopLocation},
        add_notes      = '{$addNotes}'
    WHERE GHID = {$GHID}
";

$updated = $dbObj->executeTransaction($updateSql);

if (!$updated) {
    $response["error"][] = [
        "Oops! Something went wrong. Please try again."
    ];

    echo json_encode($response);
    exit;
}

$response["success"][] = ["GRN Header Updated Successfully"];

/*
 * Status 1 only updates the header.
 * Status 3 cancels the GRN.
 * Inventory processing happens only for status 2.
 */
if ($grnStatus !== 2) {
    echo json_encode($response);
    exit;
}

/*
 * Retrieve the GRN items for inventory verification.
 */
$grnDetailsSql = "
    SELECT
        gd.GDID,
        gd.products_PDID AS PDID,
        gd.VariationID AS VRID,
        gd.CurrentQty,
        gd.UnitPurchasePrice,
        gd.UnitLabelPrice,
        gd.UnitSellPrice,
        gd.MnfDate,
        gd.ExpDate,
        gd.Rack_RKID AS RKID
    FROM grndetails gd
    WHERE gd.GRNHeader_GHID = {$GHID}
";

$grnDetails = $dbObj->getData($grnDetailsSql);

if (empty($grnDetails)) {
    /*
     * Important:
     * Ideally, the earlier header update should be rolled back here.
     */
    $response["error"][] = ["Cannot verify a GRN without GRN items"];

    echo json_encode($response);
    exit;
}

$rowCount          = 0;
$totalPurchasePrice = 0;
$totalSellingPrice  = 0;

foreach ($grnDetails as $row) {
    $grnDetailID = (int) $row["GDID"];
    $productID   = (int) $row["PDID"];
    $variationID = isset($row["VRID"])
        ? (int) $row["VRID"]
        : 0;

    $rackID = isset($row["RKID"])
        ? (int) $row["RKID"]
        : 0;

    $currentQty = (float) $row["CurrentQty"];

    $purchasePrice = (float) $row["UnitPurchasePrice"];
    $sellingPrice  = (float) $row["UnitSellPrice"];
    $labelPrice    = (float) $row["UnitLabelPrice"];

    $manufacturedDate = !empty($row["MnfDate"])
        ? $row["MnfDate"]
        : null;

    $expiryDate = !empty($row["ExpDate"])
        ? $row["ExpDate"]
        : null;

    if ($productID <= 0 || $currentQty <= 0) {
        continue;
    }

    /*
     * Generate the product batch number using the existing logic.
     */
    $inventoryCountData = $invObj->getInventorywithproductID($productID);

    $inventoryCount = !empty($inventoryCountData)
        ? (int) $inventoryCountData[0]["procount"]
        : 0;

    $inventoryCount++;

    $batchID = "B" . $invObj->getSequence($inventoryCount);

    /*
     * Calculate totals.
     */
    $totalPurchasePrice += $purchasePrice * $currentQty;
    $totalSellingPrice  += $sellingPrice * $currentQty;
    $rowCount++;

    /*
     * Get the next inventory ID for price-history linkage.
     *
     * This follows your existing system. A database-generated ID
     * would be safer than MAX(INID) + 1.
     */
    $maxInventorySql = "
        SELECT COALESCE(MAX(INID), 0) AS MAXSID
        FROM inventory
    ";

    $maxInventoryData = $dbObj->getData($maxInventorySql);

    $newInventoryID =
        (int) $maxInventoryData[0]["MAXSID"] + 1;

    $billQty        = 0;
    $returnQty      = 0;
    $transferInQty  = 0;
    $transferOutQty = 0;

    /*
     * Insert the inventory record.
     */
    $invObj->setInventory(
        $currentQty,
        $billQty,
        $returnQty,
        $transferInQty,
        $transferOutQty,
        $productID,
        $shopLocation,
        $rackID,
        $batchID
    );

    /*
     * Insert the respective price-history record.
     */
    $priceObj->setPriceHistory(
        $productID,
        $variationID,
        $effectiveDate,
        $purchasePrice,
        $sellingPrice,
        $labelPrice,
        $manufacturedDate,
        $expiryDate,
        $batchID,
        $newInventoryID,
        $grnDetailID
    );
}

/*
 * Apply the GRN discount.
 */
$saleDiscount = isset($_POST["hiddenSaleDiscount"])
    ? (float) $_POST["hiddenSaleDiscount"]
    : 0;

$totalDiscount = isset($_POST["hiddenTotalDiscount"])
    ? (float) $_POST["hiddenTotalDiscount"]
    : 0;

$discountType = isset($_POST["hiddenDiscountType"])
    ? (int) $_POST["hiddenDiscountType"]
    : 0;

$netAmount = $totalPurchasePrice - $totalDiscount;

if ($netAmount < 0) {
    $netAmount = 0;
}

/*
 * Read the payments already added for this GRN.
 */
$paymentsSql = "
    SELECT
        TRID,
        TransferAmount,
        paymethod_PMID
    FROM suppliertransactions
    WHERE GRNHeader_GHID = {$GHID} AND TransactionStat=1
";

$payments = $dbObj->getData($paymentsSql);

$totalPayment = 0;
$defaultPaymentMethod = 1;

foreach ($payments as $paymentRow) {
    $paymentAmount = (float) $paymentRow["TransferAmount"];
    $paymentMethod = (int) $paymentRow["paymethod_PMID"];

    if ($paymentAmount <= 0) {
        continue;
    }

    $totalPayment += $paymentAmount;

    if ($defaultPaymentMethod === 1) {
        $defaultPaymentMethod = $paymentMethod;
    }

    /*
     * Payment method 9 represents supplier credit.
     */
    if ($paymentMethod === 9) {
        $grnObj->setCreditDebitSupplier(
            $effectiveDate,
            $paymentAmount,
            $effectiveDate,
            $GHID,
            $paymentMethod,
            $grnSupplier,
            $user_id
        );
    }
}

$totalPayment = round($totalPayment, 2);
$netAmount    = round($netAmount, 2);

$excessAmount = 0;
$creditAmount = 0;

/*
 * When the supplier has been paid more than the GRN amount,
 * record the excess as a supplier balance.
 */
if ($totalPayment > $netAmount) {
    $excessAmount = round($totalPayment - $netAmount, 2);

    $grnObj->setCreditDebitSupplier(
        $effectiveDate,
        $excessAmount,
        $effectiveDate,
        $GHID,
        $defaultPaymentMethod,
        $grnSupplier,
        $user_id
    );
}

/*
 * When the GRN amount is greater than the payment,
 * record the outstanding supplier credit.
 */
if ($netAmount > $totalPayment) {
    $creditAmount = round($netAmount - $totalPayment, 2);

    $grnObj->setCreditDebitSupplier(
        $effectiveDate,
        $creditAmount,
        $effectiveDate,
        $GHID,
        9,
        $grnSupplier,
        $user_id
    );
}

/*
 * Update header totals and verification information.
 */
$grnObj->editGRNHeaderStat(
    2,
    $GHID,
    $defaultPaymentMethod,
    $excessAmount,
    $excessAmount,
    $discountType,
    $saleDiscount,
    $totalDiscount
);

$grnObj->editGRNDetailStat(2, $GHID);

$grnObj->editGRNHeaderVerify(
    $effectiveDate,
    $rowCount,
    $totalPurchasePrice,
    $netAmount,
    $totalSellingPrice,
    $GHID
);

$response["success"][] = ["GRN Verified Successfully"];
$response["verification"] = [
    "net_amount"    => $netAmount,
    "total_payment" => $totalPayment,
    "credit_amount" => $creditAmount,
    "excess_amount" => $excessAmount,
    "items_added"   => $rowCount
];

echo json_encode($response);
exit;