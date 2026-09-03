<?php
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();
header('Content-Type: application/json');

$GHID = !empty($_POST["GHID"]) ? $_POST["GHID"] : "";

$response = [
    "data" => [],
    "error" => []
];

// ✅ Validate
if (empty($GHID) || $GHID == 0) {
    $response["error"][] = "Invalid GRN ID";
    echo json_encode($response);
    exit;
}

// ✅ Fetch
$sql = "SELECT GADID, doc_path, doc_name, ori_name 
        FROM grn_attach_doc 
        WHERE grn_GHID = '$GHID'
        ORDER BY GADID DESC";

$result = $dbObj->getData($sql);


if (!$result || !is_array($result)) {
    echo json_encode($response);
    exit;
}
// ✅ Loop
foreach ($result as $results) {

    $docPath = $results["doc_path"];
    $docName = $results["doc_name"];

    $fullPath =   "../../" . $docPath . $docName;

    if (!file_exists($fullPath)) {
        continue;
    }

    $response["data"][] = [
        "id"        => $results["GADID"], // 👈 IMPORTANT
        "doc_path"  => $docPath,
        "doc_name"  => $docName,
        "ori_name"  => $results["ori_name"]
    ];

}

echo json_encode($response);