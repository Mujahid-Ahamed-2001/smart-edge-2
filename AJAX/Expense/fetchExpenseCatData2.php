<?php
session_start();

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

header('Content-Type: application/json; charset=UTF-8');

$dbObj = new DBTransactions();

$response = [];

$sql = "SELECT 
            ec.ECID,
            ec.expense_ctg,
            ec.is_default
        FROM expensecategory ec 
        WHERE ec.is_deleted = 0 
        ORDER BY ec.is_default DESC";

$dbData = $dbObj->getData($sql);

$sl = 1;

foreach ($dbData as $row) {

    $response[] = [
        "sl"               => $sl++,
        "ECID"             => (int) $row['ECID'],
        "is_default"       => (int) $row['is_default'],
        "expense_category" => $row['expense_ctg'] ?? ''
    ];
}

echo json_encode([
    "status"  => 1,
    "message" => "Expense categories fetched successfully.",
    "data"    => $response
]);

exit;