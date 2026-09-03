<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
$dbObj = new DBTransactions();
$gdid = isset($_POST["gdid"]) && !empty($_POST["gdid"]) ? $_POST["gdid"] : "";
$response = [];

if(!empty($gdid))
{
    $sql="DELETE FROM grndetails WHERE GDID = '$gdid'";
    if($dbObj->executeTransaction($sql))
    {
        $response = ["success" =>"Deleted successfully"];
    }
    else
    {
        $response = ["error" =>"Oops! Something went wrong"];
    }
}
else
{
    $response = ["error" =>"Invalid GDID"];
}

echo json_encode($response);