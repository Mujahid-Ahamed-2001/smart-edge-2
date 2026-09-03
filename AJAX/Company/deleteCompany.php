<?php

session_start();

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

header('Content-Type: application/json');

$dbObj = new DBTransactions();

$response = [];

$CMID = isset($_POST["CMID"]) ? (int)$_POST["CMID"] : 0;

if($CMID <= 0)
{
    $response[] = [
        "status" => 0,
        "message" => "Invalid company id"
    ];
}
else
{
    $sql = "SELECT CMID, ComLogo FROM company WHERE CMID='$CMID'";
    $company = $dbObj->getData($sql);

    if(empty($company))
    {
        $response[] = [
            "status" => 0,
            "message" => "Company not found"
        ];
    }
    else
    {
        $logo = $company[0]["ComLogo"];

        $sql = "SELECT COUNT(SHID) AS countData
                FROM shop
                WHERE Company_CMID='$CMID'";

        $countData = $dbObj->getData($sql);
        $countData = (int)$countData[0]["countData"];

        if($countData > 0)
        {
            $response[] = [
                "status" => 2,
                "message" => "Cannot delete company because shops are associated with it"
            ];
        }
        else
        {
            $sql = "DELETE FROM company WHERE CMID='$CMID'";
            $delete = $dbObj->executeTransaction($sql);

            if($delete)
            {
                if(!empty($logo))
                {
                    $logoPath = "../../Assets/Images/Company_Logos/" . $logo;

                    if(file_exists($logoPath))
                    {
                        unlink($logoPath);
                    }
                }

                $response[] = [
                    "status" => 1,
                    "message" => "Company deleted successfully"
                ];
            }
            else
            {
                $response[] = [
                    "status" => 0,
                    "message" => "Oops! Something went wrong"
                ];
            }
        }
    }
}

echo json_encode($response);