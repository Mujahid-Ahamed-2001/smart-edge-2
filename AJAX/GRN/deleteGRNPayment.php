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

$shop_id = isset($_SESSION["shop_id"])
    ? (int) $_SESSION["shop_id"]
    : 0;

if ($TRID <= 0 || $GHID <= 0 || $shop_id <= 0) {
    echo json_encode([
        "status"  => 0,
        "message" => "Invalid transaction, GRN, or shop ID."
    ]);
    exit;
}

/*
 * Confirm that the payment exists and belongs to this GRN.
 */
$checkSql = "
    SELECT
        TRID,
        TransferAmount,
        GRNHeader_GHID
    FROM suppliertransactions
    WHERE TRID = {$TRID}
      AND GRNHeader_GHID = {$GHID}
      AND TransactionStat = 1
    LIMIT 1
";

$transaction = $dbObj->getData($checkSql);

if (empty($transaction)) {
    echo json_encode([
        "status"  => 0,
        "message" => "Payment transaction was not found."
    ]);
    exit;
}

/*
 * Soft-delete the transaction.
 */
$deleteSql = "DELETE FROM suppliertransactions WHERE `suppliertransactions`.`TRID` = {$TRID} AND `suppliertransactions`.`GRNHeader_GHID` = {$GHID} ";

$deleted = $dbObj->executeTransaction($deleteSql);

if (!$deleted) {
    echo json_encode([
        "status"  => 0,
        "message" => "Unable to delete the payment."
    ]);
    exit;
}

echo json_encode([
    "status"  => 1,
    "message" => "Payment deleted successfully.",
    "data"    => [
        "TRID" => $TRID
    ]
]);

exit;