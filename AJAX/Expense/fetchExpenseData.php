<?php
session_start();

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj   = new DBTransactions();

$response = [];


// Build base SQL (single query - faster than N+1)
$sql = "SELECT ec.ECID, ec.expense_ctg, ec.is_default, ec.expense_ctg, ec.created_date, u.UserName, ec.status, uu.UserName AS ModifiedBy, ec.modified_date, et.expense_type FROM expensecategory ec 
LEFT JOIN expensetype et ON ec.expense_ETID = et.ETID
LEFT JOIN user u ON ec.created_by = u.USID 
LEFT JOIN user uu ON ec.modified_by = uu.USID 
WHERE ec.is_deleted = 0 ORDER BY ec.is_default DESC;";
$sql="SELECT e.EPID, e.EffectiveDate, e.ExpenseReason, ec.expense_ctg AS expense_cat, e.status, e.is_default, u.UserName AS Created_by, uu.UserName AS ModifiedBy, e.created_date, e.modified_date FROM expenses e
LEFT JOIN expensecategory ec ON ec.ECID = e.expensecategory_id
LEFT JOIN user u ON e.created_by = u.USID 
LEFT JOIN user uu ON e.modified_by = uu.USID 
WHERE e.is_deleted = 0 ORDER BY e.is_default DESC;";


$dbData = $dbObj->getData($sql);

$sl = 1;

foreach ($dbData as $row) {

    $response[] = [
        "sl"                => $sl++,
        "EPID"              => (int)$row['EPID'],
        "EffectiveDate"  => $row['EffectiveDate'] ?? '',
        "ExpenseReason"  => $row['ExpenseReason'] ?? '',
        "is_default"        => (int)$row['is_default'],
        "expense_cat"      => $row['expense_cat'] ?? '',
        "created_date"      => $row['created_date'] ?? '',
        "Created_by"          => $row['Created_by'] ?? '',
        "status"            => $row['status'] ?? '',
        "ModifiedBy"        => $row['ModifiedBy'] ?? '',
        "modified_date"     => $row['modified_date'] ?? ''
    ];
}

echo json_encode($response);
exit;
