<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();

$GHID = isset($_POST["GHID"])
    ? (int) $_POST["GHID"]
    : 0;

$shop_id = isset($_SESSION["shop_id"])
    ? (int) $_SESSION["shop_id"]
    : 0;

if ($GHID <= 0 || $shop_id <= 0) {
    echo json_encode([
        "status"  => 0,
        "message" => "Invalid GRN ID or shop ID.",
        "data"    => []
    ]);
    exit;
}

$sql = "
    SELECT
        st.TRID,
        st.TransferAmount,
        st.TransactionStat,
        st.paymethod_PMID,
        st.GRNHeader_GHID,
        st.tran_date
    FROM suppliertransactions st
    WHERE st.GRNHeader_GHID = {$GHID}
      AND st.TransactionStat = 1
    ORDER BY st.TRID ASC
";

$payments = $dbObj->getData($sql);

echo json_encode([
    "status"  => 1,
    "message" => empty($payments)
        ? "No payments found."
        : "Payments loaded successfully.",
    "data"    => $payments ?: []
]);

exit;