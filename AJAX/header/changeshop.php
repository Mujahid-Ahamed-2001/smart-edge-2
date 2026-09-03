<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
include "../../Model/user_class.php";
$userObj = new User();
$user_id = $_SESSION['user_id'];
$user = $userObj->getOneUser($user_id);
$dbObj = new DBTransactions;
$date=date("Y-m-d");
$SHID = isset($_POST["SHID"]) ? $_POST["SHID"] : "";
$UserType=$user[0]["UserType"];
$response =[];
if(!empty($SHID))
{
    $sql="SELECT * FROM `shop` s INNER JOIN company c ON c.CMID=s.Company_CMID WHERE s.SHID='$SHID'";
    $data = $dbObj->getData($sql);
    if($UserType==1)
    {
        $_SESSION["shop_id"] = $SHID;
        $response[] =[
            "status" => 1,
            "message" => "Shop switched successfully"
        ];
    }
    else
    {
        if($date>$data[0]["ComExpireDate"] || $data[0]["ComStat"]==0)
        {
            $response[] =[
                "status" => 0,
                "message" => "Shop Expired/Inactive"
            ];

        }
        else
        {
            $_SESSION["shop_id"] = $SHID;
            $response[] =[
                "status" => 1,
                "message" => "Shop switched successfully"
            ];

        }
    }
}
else
{
    $response[] =[
        "status" =>0,
        "SHID" =>$SHID,
        "UserType" =>$UserType,
        "message" => "Invalid Data"
    ];
}
echo json_encode($response);