<?php

session_start();

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

header('Content-Type: application/json');

$dbObj = new DBTransactions();

$GHID = !empty($_POST["GHID"]) ? (int) $_POST["GHID"] : 0;

if ($GHID <= 0) {
    echo json_encode([
        "status" => 0,
        "message" => "Invalid GRN ID.",
        "data" => []
    ]);
    exit;
}

try {

    $sql = "
        SELECT 
            *
        FROM supcheq sc
        INNER JOIN supchqdetail  scd ON scd.SCQID= sc.SCQID
        WHERE sc.GRNHeader_GHID = ?
        AND sc.chq_stat = 1
        ORDER BY sc.SCQID ASC
    ";

    $data = $dbObj->getMultipleData($sql, [$GHID]);

    echo json_encode([
        "status" => 1,
        "message" => "Supplier cheques fetched successfully.",
        "data" => $data
    ]);

} catch (Throwable $e) {

    echo json_encode([
        "status" => 0,
        "message" => "Unable to fetch supplier cheques.",
        "debug" => $e->getMessage(),
        "data" => []
    ]);
}