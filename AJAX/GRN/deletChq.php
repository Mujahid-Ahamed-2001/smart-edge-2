<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();

$SCQID = isset($_POST["SCQID"])
    ? (int) $_POST["SCQID"]
    : 0;

    
if ($SCQID <= 0) {
    echo json_encode([
        "status"  => 0,
        "message" => "Invalid SCQID."
    ]);
    exit;
}

try {

    $sql = "DELETE FROM supchqdetail
        WHERE SCQID = $SCQID
    ";

    $Deletesupchqdetail = $dbObj->executeTransaction($sql);

    $sql = "DELETE FROM supcheq
        WHERE SCQID = $SCQID
    ";

    $Deletesupcheq = $dbObj->executeTransaction($sql);

    if (!$Deletesupchqdetail || !$Deletesupcheq) {
        echo json_encode([
            "status"  => 0,
            "message" => "Unable to update the cheque."
        ]);
        exit;
    }
    echo json_encode([
        "status"  => 1,
        "message" => "Cheque Deleted successfully."
    ]);

} catch (Throwable $e) {

    echo json_encode([
        "status"  => 0,
        "message" => "Unable to update cheque.",
        "debug"   => $e->getMessage()
    ]);
}

exit;