<?php 
include "../Includes/includes.php";
$shop_id = $_SESSION['shop_id'];

$dbObj = new DBTransactions();

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $response = [];
    $SCID = $_POST['SCID'];
    if(!empty($SCID))
    {
        $sql = "DELETE FROM `subcategories` WHERE SCID = $SCID";
        if($dbObj->executeTransaction($sql))
        {
            $response = [
                "status" => "success",
                "message" => "Category deleted successfully."
            ];
        }
        else
        {
            $response = [
                "status" => "error",
                "message" => "Failed to delete category."
            ];
        }
    }     //has category name
    else
    {
        $response = [
            "status" => "error",
            "message" => "Invalid ID provided."
        ];
    } //no category Name
    echo json_encode($response);
}  //save category
else
{
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request."
    ]);
    exit();
}