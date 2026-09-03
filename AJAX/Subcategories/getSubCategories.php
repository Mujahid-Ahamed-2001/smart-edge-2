<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();

$searchCondition = "";
$params = [];

if(isset($_GET['query']) && !empty($_GET['query'])) {
    $searchQuery = "%" . $_GET['query'] . "%";
    $searchCondition = "WHERE sc.SubCatName LIKE ? 
                        OR c.CategoryName LIKE ? 
                        OR sc.SubCatNo LIKE ?";
    $params = [$searchQuery, $searchQuery, $searchQuery];
}

$sql = "SELECT 
            sc.*,
            c.CategoryName,
            COUNT(p.PDID) AS TotalProducts

        FROM subcategories sc

        LEFT JOIN categories c 
            ON c.CTID = sc.categories_CTID

        LEFT JOIN products p 
            ON p.Subcategories_SCID = sc.SCID

        $searchCondition

        GROUP BY sc.SCID

        ORDER BY sc.SubCatNo ASC";

$dbData = $dbObj->getMultipleData($sql, $params);
$response = [];
foreach ($dbData as $row) {
    $response[] = $row;
}
echo json_encode($response);