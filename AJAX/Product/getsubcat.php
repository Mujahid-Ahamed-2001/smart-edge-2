<?php

session_start();

header('Content-Type: application/json');

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();

$sql = "SELECT
        SCID,
        SubCatName
    FROM subcategories
    ORDER BY SubCatName ASC
";

$data = $dbObj->getData($sql);

echo json_encode($data);
exit;