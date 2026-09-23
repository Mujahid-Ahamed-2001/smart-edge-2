<?php

session_start();

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();

$response = [];

$shop_id = $_SESSION['shop_id'] ?? 0;

$start = $_POST["start_date"] ?? "";
$end   = $_POST["end_date"] ?? "";

$where = "";

if (!empty($start) && !empty($end)) {

    $where .= " AND e.EffectiveDate BETWEEN '$start' AND '$end'";
}


$sql = "SELECT 
        e.EPID,
        e.EffectiveDate,
        e.ExpenseReason,
        ec.expense_ctg AS expense_cat,
        e.status,
        e.ExpenseAmount,
        e.is_default,
        u.UserName AS Created_by,
        uu.UserName AS ModifiedBy,
        e.created_date,
        e.modified_date

    FROM expenses e

    LEFT JOIN expensecategory ec
        ON ec.ECID = e.expensecategory_id

    LEFT JOIN user u
        ON e.created_by = u.USID

    LEFT JOIN user uu
        ON e.modified_by = uu.USID

    WHERE e.is_deleted = 0
    AND e.shop_SHID = '$shop_id'

    $where

    ORDER BY e.EffectiveDate DESC
";


$dbData = $dbObj->getData($sql);

$sl = 1;

foreach ($dbData as $row) {

    $response[] = [

        "sl" => $sl++,

        "EPID" => (int)$row['EPID'],

        "EffectiveDate" => $row['EffectiveDate'] ?? '',

        "ExpenseReason" => $row['ExpenseReason'] ?? '',

        "is_default" => (int)$row['is_default'],

        "expense_cat" => $row['expense_cat'] ?? '',

        "ExpenseAmount" => $row['ExpenseAmount'] ?? '',

        "created_date" => $row['created_date'] ?? '',

        "Created_by" => $row['Created_by'] ?? '',

        "status" => (int)($row['status'] ?? 0),

        "ModifiedBy" => $row['ModifiedBy'] ?? '',

        "modified_date" => $row['modified_date'] ?? ''

    ];
}


header('Content-Type: application/json');

echo json_encode($response);

exit;