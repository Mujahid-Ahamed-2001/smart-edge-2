<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();

$TRID = isset($_POST["TRID"])
    ? (int) $_POST["TRID"]
    : 0;

$GHID = isset($_POST["GHID"])
    ? (int) $_POST["GHID"]
    : 0;

$TransferAmount = isset($_POST["TransferAmount"])
    ? trim($_POST["TransferAmount"])
    : "";

$PMID = isset($_POST["PMID"])
    ? (int) $_POST["PMID"]
    : 0;

$tran_date = isset($_POST["tran_date"])
    ? trim($_POST["tran_date"])
    : "";

$shop_id = isset($_SESSION["shop_id"])
    ? (int) $_SESSION["shop_id"]
    : 0;

/*
 * Validate required IDs.
 */
if ($TRID <= 0 || $GHID <= 0 || $shop_id <= 0) {
    echo json_encode([
        "status"  => 0,
        "message" => "Invalid transaction, GRN, or shop ID."
    ]);
    exit;
}

/*
 * Validate amount.
 */
if (
    $TransferAmount === "" ||
    !is_numeric($TransferAmount) ||
    (float) $TransferAmount < 0
) {
    echo json_encode([
        "status"  => 0,
        "message" => "Please enter a valid payment amount."
    ]);
    exit;
}

$TransferAmount = number_format(
    (float) $TransferAmount,
    2,
    ".",
    ""
);

/*
 * Validate payment method.
 */
if ($PMID <= 0) {
    echo json_encode([
        "status"  => 0,
        "message" => "Please select a payment method."
    ]);
    exit;
}

/*
 * Validate and convert the datetime-local value.
 *
 * Received:
 * 2026-08-20T10:30
 *
 * Stored:
 * 2026-08-20 10:30:00
 */
$dateObject = DateTime::createFromFormat(
    "Y-m-d\TH:i",
    $tran_date
);

$dateErrors = DateTime::getLastErrors();

if (
    !$dateObject ||
    (
        is_array($dateErrors) &&
        (
            $dateErrors["warning_count"] > 0 ||
            $dateErrors["error_count"] > 0
        )
    )
) {
    echo json_encode([
        "status"  => 0,
        "message" => "Please enter a valid payment date."
    ]);
    exit;
}

$formattedDate = $dateObject->format("Y-m-d H:i:s");

/*
 * Verify that the transaction belongs to this GRN and is active.
 */
$checkTransactionSql = "
    SELECT TRID
    FROM suppliertransactions
    WHERE TRID = {$TRID}
      AND GRNHeader_GHID = {$GHID}
      AND TransactionStat = 1
    LIMIT 1
";

$transaction = $dbObj->getData($checkTransactionSql);

if (empty($transaction)) {
    echo json_encode([
        "status"  => 0,
        "message" => "Payment transaction was not found."
    ]);
    exit;
}

/*
 * Verify that the selected payment method is available to this shop.
 */
$checkPaymentMethodSql = "
    SELECT p.PMID
    FROM paymethod p
    LEFT JOIN shoppaymethod sp
        ON sp.paymethod_PMID = p.PMID
       AND sp.shop_SHID = {$shop_id}
    WHERE p.PMID = {$PMID}
      AND p.PMID NOT IN (4, 6, 9, 10, 12)
      AND (
          sp.shop_SHID = {$shop_id}
          OR p.PMID IN (1, 2, 3)
      )
    LIMIT 1
";

$paymentMethod = $dbObj->getData($checkPaymentMethodSql);

if (empty($paymentMethod)) {
    echo json_encode([
        "status"  => 0,
        "message" => "The selected payment method is not available."
    ]);
    exit;
}

/*
 * Update the transaction.
 */
$updateSql = "
    UPDATE suppliertransactions
    SET
        TransferAmount = '{$TransferAmount}',
        paymethod_PMID = {$PMID},
        tran_date = '{$formattedDate}'
    WHERE TRID = {$TRID}
      AND GRNHeader_GHID = {$GHID}
      AND TransactionStat = 1
";

$updated = $dbObj->executeTransaction($updateSql);

if (!$updated) {
    echo json_encode([
        "status"  => 0,
        "message" => "Unable to update the payment."
    ]);
    exit;
}

/*
 * Return the updated transaction.
 */
$selectSql = "
    SELECT
        TRID,
        TransferAmount,
        TransactionStat,
        paymethod_PMID,
        GRNHeader_GHID,
        tran_date
    FROM suppliertransactions
    WHERE TRID = {$TRID}
      AND GRNHeader_GHID = {$GHID}
    LIMIT 1
";

$updatedPayment = $dbObj->getData($selectSql);

echo json_encode([
    "status"  => 1,
    "message" => "Payment updated successfully.",
    "data"    => !empty($updatedPayment)
        ? $updatedPayment[0]
        : []
]);

exit;