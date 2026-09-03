<?php

session_start();

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

header("Content-Type: application/json; charset=UTF-8");

$dbObj = new DBTransactions();

$shop_id = isset($_SESSION["shop_id"])
    ? (int) $_SESSION["shop_id"]
    : 0;

$response = [
    "status" => 0,
    "message" => "Invalid shop ID",
    "data" => []
];

if ($shop_id > 0) {
    $sql = "SELECT sp.*, p.*
        FROM shoppaymethod sp
        INNER JOIN paymethod p 
            ON p.PMID = sp.paymethod_PMID
        WHERE sp.shop_SHID = {$shop_id}
        AND p.PMID NOT IN (4, 6, 9, 10, 12)
    ";

    $dbPaymethods = $dbObj->getData($sql);

    if (!empty($dbPaymethods)) {
        $response = [
            "status" => 1,
            "data" => $dbPaymethods
        ];
    } else {
        $sql = "SELECT *
            FROM paymethod
            WHERE PMID IN (1, 2, 3)
        ";

        $dbPaymethods = $dbObj->getData($sql);

        $response = [
            "status" => 1,
            "data" => $dbPaymethods
        ];
    }
}

echo json_encode($response);
exit;