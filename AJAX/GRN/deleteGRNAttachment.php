<?php
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();
header('Content-Type: application/json');

$id   = !empty($_POST["id"]) ? $_POST["id"] : "";
$file = !empty($_POST["file"]) ? $_POST["file"] : "";

$response = [
    "success" => [],
    "error" => []
];

// ✅ Validate
if (empty($id) || empty($file)) {
    $response["error"] = "Invalid request";
    echo json_encode($response);
    exit;
}

// ✅ Get file path from DB (safer than trusting frontend)
$sql = "SELECT doc_path, doc_name FROM grn_attach_doc WHERE GADID='$id'";
$result = $dbObj->getData($sql);

if (!$result || empty($result[0]["doc_name"])) {
    $response["error"] = "File not found";
    echo json_encode($response);
    exit;
}

$docPath = "../../" . $result[0]["doc_path"];
$docName = $result[0]["doc_name"];

$fullPath = $docPath . $docName;

// ✅ Delete file from server
if (file_exists($fullPath)) {
    if (!unlink($fullPath)) {
        $response["error"] = "Failed to delete file from server";
        echo json_encode($response);
        exit;
    }
}

// ✅ Delete from DB
$sql = "DELETE FROM grn_attach_doc WHERE GADID='$id'";
$delete = $dbObj->executeTransaction($sql);

if ($delete) {
    $response["success"][] = "Document deleted successfully";
} else {
    $response["error"] = "Failed to delete record from database";
}

echo json_encode($response);