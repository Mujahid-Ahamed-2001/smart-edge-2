<?php
session_start();

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj   = new DBTransactions();

$response = [];


// Build base SQL (single query - faster than N+1)
$sql = "SELECT et.ETID, et.is_default, et.expense_type, et.created_date, u.UserName, et.status, uu.UserName AS ModifiedBy, et.modified_date FROM expensetype et 
LEFT JOIN user u ON et.created_by = u.USID 
LEFT JOIN user uu ON et.modified_by = uu.USID 
WHERE et.is_deleted = 0 ORDER BY et.is_default DESC";


$dbData = $dbObj->getData($sql);

$sl = 1;

foreach ($dbData as $row) {

    $response[] = [
        "sl"                => $sl++,
        "ETID"              => (int)$row['ETID'],
        "is_default"        => (int)$row['is_default'],
        "expense_type"      => $row['expense_type'] ?? '',
        "created_date"      => $row['created_date'] ?? '',
        "UserName"          => $row['UserName'] ?? '',
        "status"            => $row['status'] ?? '',
        "ModifiedBy"        => $row['ModifiedBy'] ?? '',
        "modified_date"     => $row['modified_date'] ?? ''
    ];
}

echo json_encode($response);
exit;
