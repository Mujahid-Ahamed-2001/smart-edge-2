<?php
session_start();
$user_id = $_SESSION['user_id'];
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();
header('Content-Type: application/json');

$GHID = !empty($_POST["GHID"]) ? $_POST["GHID"] : "";

$response = [
    "success" => [],
    "error" => []
];

$uploadDir = "../../Assets/uploads/GRN_Attachments/";
$dbPath    = "Assets/uploads/GRN_Attachments/"; // for DB

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// ✅ Validate GHID early
if (empty($GHID) || $GHID == 0) {
    $response["error"][] = "Invalid GRN ID";
    echo json_encode($response);
    exit;
}

// ✅ Fetch once (IMPORTANT FIX)
$sql = "SELECT GRNHeaderNo FROM grnheader WHERE GHID='$GHID'";
$GRNdata = $dbObj->getData($sql);

if (!$GRNdata || empty($GRNdata[0]["GRNHeaderNo"])) {
    $response["error"][] = "GRN not found";
    echo json_encode($response);
    exit;
}

$GRNHeaderNo = $GRNdata[0]["GRNHeaderNo"];

// ✅ File check
if (!isset($_FILES['documents'])) {
    $response["error"][] = "No files received";
    echo json_encode($response);
    exit;
}

$allowed = ['jpg','jpeg','png','webp','pdf','doc','docx','zip'];
$maxSize = 5 * 1024 * 1024; // 5MB

$files = $_FILES['documents'];

for ($i = 0; $i < count($files['name']); $i++) {

    $originalName = $files['name'][$i];
    $tmpName      = $files['tmp_name'][$i];
    $size         = $files['size'][$i];
    $error        = $files['error'][$i];

    if ($error !== 0) {
        $response["error"][] = "$originalName failed to upload";
        continue;
    }

    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // ✅ Validate type
    if (!in_array($ext, $allowed)) {
        $response["error"][] = "$originalName type not allowed";
        continue;
    }

    // ✅ Validate size
    if ($size > $maxSize) {
        $response["error"][] = "$originalName exceeds 5MB";
        continue;
    }

    // ✅ Better unique name
    $newName = "DOC_" . $GRNHeaderNo . "_" . uniqid() . "." . $ext;

    $destination = $uploadDir . $newName;

    if (move_uploaded_file($tmpName, $destination)) {

        $sql = "INSERT INTO grn_attach_doc 
                (`grn_GHID`, `doc_path`, `doc_name`, `ori_name`, `created_by`) 
                VALUES 
                ('$GHID','$dbPath','$newName','$originalName', '$user_id')";

        $uploadAttachment = $dbObj->executeTransaction($sql);

        if ($uploadAttachment) {
            $response["success"][] = "$originalName uploaded successfully";
        } else {
            $response["error"][] = "$originalName uploaded but DB failed";
        }

    } else {
        $response["error"][] = "$originalName upload failed";
    }
}

echo json_encode($response);