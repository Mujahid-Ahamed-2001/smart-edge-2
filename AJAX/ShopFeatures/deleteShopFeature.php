<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
$dbObj = new DBTransactions();

if(isset($_POST["SFID"]))
{
    $SFID = $_POST["SFID"];
    $sql = "DELETE FROM shopfeatures WHERE `shopfeatures`.`SPFID` = '$SFID'";
    $itemData = $dbObj->executeTransaction($sql);
    if(!empty($itemData))
    {
        echo json_encode([
            "success"=> true
        ]);
    }
    else
    {
        echo json_encode([
            'status' => false,
            'message' => "Error fetching data"
        ]);
    }
    
}
