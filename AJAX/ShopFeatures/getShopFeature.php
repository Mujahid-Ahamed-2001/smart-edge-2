<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
$dbObj = new DBTransactions();

if(isset($_POST["SFID"]))
{
    $SFID = $_POST["SFID"];
    $sql = "SELECT * FROM `shopfeatures` WHERE SPFID='$SFID'";
    $itemData = $dbObj->getData($sql);
    if(!empty($itemData))
    {
        echo json_encode([
            "success"=> true,
            "data"=>$itemData,
            "SFID"=>$SFID
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
