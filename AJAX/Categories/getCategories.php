<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();

$sql = "SELECT 
    c.CTID,
    c.CategoryName,
    c.CategoryNo,
    c.default,
    COUNT(DISTINCT sc.SCID) AS TotalSubcategories,
    
    COUNT(DISTINCT p.PDID) AS TotalProducts

FROM categories c

LEFT JOIN subcategories sc 
    ON sc.categories_CTID = c.CTID

LEFT JOIN products p 
    ON p.Subcategories_SCID = sc.SCID

GROUP BY c.CTID, c.CategoryName

ORDER BY c.CategoryNo ASC";

$dbData = $dbObj->getData($sql);
$response = [];
foreach ($dbData as $row) {
    $response[] = $row;
}
echo json_encode($response);