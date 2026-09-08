<?php

session_start();

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

header('Content-Type: application/json');

$dbObj = new DBTransactions();

$GHID      = !empty($_POST["GHID"]) ? (int) $_POST["GHID"] : 0;
$shop_SHID = !empty($_POST["shop_SHID"]) ? (int) $_POST["shop_SHID"] : 0;
$sup_SPID  = !empty($_POST["sup_SPID"]) ? (int) $_POST["sup_SPID"] : 0;
$user_id   = !empty($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

$response = [];

if ($GHID <= 0) {
    echo json_encode([
        "error" => "Invalid GHID"
    ]);
    exit;
}

if ($shop_SHID <= 0 || $sup_SPID <= 0) {
    echo json_encode([
        "error" => "Invalid Shop/Supplier ID"
    ]);
    exit;
}

if ($user_id <= 0) {
    echo json_encode([
        "error" => "Invalid User Session"
    ]);
    exit;
}

try {

    $now = date("Y-m-d H:i:s");
    $sql = "SELECT MAX(SCQID) AS ChqNo FROM supcheq ";
    $dbData = $dbObj->getData($sql);
    $count = $dbData[0]["ChqNo"]+1;
    $chq_no="SCHQ-".$dbObj->getSequence($count);


    $data = [
        "type"           => 1,
        "chq_stat"       => 1,
        "chq_no"         => $chq_no,
        "sup_SPID"       => $sup_SPID,
        "effectiveDate"  => $now,
        "createdDate"    => $now,
        "GRNHeader_GHID" => $GHID,
        "user_USID"      => $user_id,
        "shop_SHID"      => $shop_SHID
    ];

    $SCQID = $dbObj->insertAndGetId("supcheq", $data);

    if ($SCQID <= 0) {
        throw new Exception("Unable to create supplier cheque.");
    }

    $data2 = [
        "bank"           => "",
        "chqNo"          => "",
        "chqDate"        => null,
        "GRNHeader_GHID" => $GHID,
        "SCQID"          => $SCQID,
        "chqAmount"      => 0
    ];

    $SCDID = $dbObj->insertAndGetId("supchqdetail", $data2);

    if ($SCDID <= 0) {
        throw new Exception("Unable to create cheque detail.");
    }

    $response = [
        "success" => true,
        "SCQID"   => $SCQID,
        "SCDID"   => $SCDID
    ];

} catch (Throwable $e) {

    $response = [
        "error" => "Oops! Something Went Wrong",
        "debug" => $e->getMessage()
    ];
}

echo json_encode($response);