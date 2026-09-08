<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();

$SCQID = isset($_POST["SCQID"])
    ? (int) $_POST["SCQID"]
    : 0;

$chqNo = isset($_POST["chqNo"])
    ? trim($_POST["chqNo"])
    : "";

$chqdate = !empty($_POST["chqdate"])
    ? $_POST["chqdate"]
    : null;

$chqbank = isset($_POST["chqbank"])
    ? trim($_POST["chqbank"])
    : "";

$chqamount = isset($_POST["chqamount"]) && $_POST["chqamount"] !== ""
    ? (float) $_POST["chqamount"]
    : 0.00;

if ($SCQID <= 0) {
    echo json_encode([
        "status"  => 0,
        "message" => "Invalid SCQID."
    ]);
    exit;
}

try {

    $sql = "UPDATE supchqdetail
        SET
            bank = '$chqbank',
            chqAmount = $chqamount,
            chqDate = '$chqdate',
            chqNo = '$chqNo'
        WHERE SCQID = $SCQID
    ";

    $updated = $dbObj->executeTransaction($sql);

    if (!$updated) {
        echo json_encode([
            "status"  => 0,
            "message" => "Unable to update the cheque."
        ]);
        exit;
    }

    $selectSql = "SELECT
            bank,
            chqAmount,
            chqDate,
            chqNo
        FROM supchqdetail
        WHERE SCQID = ?
        LIMIT 1
    ";

    $updatedPayment = $dbObj->getMultipleData(
        $selectSql,
        [$SCQID]
    );

    echo json_encode([
        "status"  => 1,
        "message" => "Cheque updated successfully.",
        "data"    => !empty($updatedPayment)
            ? $updatedPayment[0]
            : []
    ]);

} catch (Throwable $e) {

    echo json_encode([
        "status"  => 0,
        "message" => "Unable to update cheque.",
        "debug"   => $e->getMessage()
    ]);
}

exit;