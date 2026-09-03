<?php
session_start();

include "../Includes/config.php";
include "../Model/DB_Class.php";

$dbObj = new DBTransactions();
function createCount($prefix, $number)
{
    return $prefix . '_' . str_pad($number, 6, '0', STR_PAD_LEFT);
}
if(isset($_GET["condition"]) && !empty($_GET["condition"]))
{
    $condition = $_GET["condition"];
    $response=[];
    if($condition==="new")
    {
        $company_type = isset($_POST["company_type"]) && !empty($_POST["company_type"]) ? $_POST["company_type"] : "";
        $company_name = isset($_POST["company_name"]) && !empty($_POST["company_name"]) ? $_POST["company_name"] : "";
        $company_location = isset($_POST["company_location"]) && !empty($_POST["company_location"]) ? $_POST["company_location"] : "";
        $company_licence = isset($_POST["company_licence"]) && !empty($_POST["company_licence"]) ? $_POST["company_licence"] : "";
        $company_version = isset($_POST["company_version"]) && !empty($_POST["company_version"]) ? $_POST["company_version"] : "";
        $company_multi_cate = isset($_POST["company_multi_cate"]) && !empty($_POST["company_multi_cate"]) ? 1 : 0;
        $company_common_stock = isset($_POST["company_common_stock"]) && !empty($_POST["company_common_stock"]) ? 1 : 0;
        $company_status = isset($_POST["company_status"]) && !empty($_POST["company_status"]) ? 1 : 0;
        $company_startDate = isset($_POST["company_startDate"]) && !empty($_POST["company_startDate"]) ? $_POST["company_startDate"] : "";
        $company_expireDate = isset($_POST["company_expireDate"]) && !empty($_POST["company_expireDate"]) ? $_POST["company_expireDate"] : "";
        $company_logo = "";

        if(isset($_FILES["company_logo"]) && $_FILES["company_logo"]["error"] == 0)
        {
            $allowedTypes = [
                "image/jpeg" => "jpg",
                "image/png"  => "png"
            ];

            $maxSize = 5 * 1024 * 1024; // 5MB

            $fileTmp  = $_FILES["company_logo"]["tmp_name"];
            $fileSize = $_FILES["company_logo"]["size"];

            // Validate file size
            if($fileSize > $maxSize)
            {
                $response[] = [
                    "status" => 0,
                    "message" => "Logo size must be less than 5MB"
                ];

                echo json_encode($response);
                exit;
            }

            // Validate actual mime type
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($fileInfo, $fileTmp);
            finfo_close($fileInfo);

            if(!array_key_exists($mimeType, $allowedTypes))
            {
                $response[] = [
                    "status" => 0,
                    "message" => "Only JPG and PNG images are allowed"
                ];

                echo json_encode($response);
                exit;
            }

            $extension = $allowedTypes[$mimeType];

            // Generate unique filename
            $company_logo = uniqid("COMPANY_", true) . "." . $extension;

            $uploadPath = "../Assets/Images/Company_Logos/" . $company_logo;

            if(!move_uploaded_file($fileTmp, $uploadPath))
            {
                $response[] = [
                    "status" => 0,
                    "message" => "Failed to upload company logo"
                ];

                echo json_encode($response);
                exit;
            }
        }
        if(empty($company_type) && empty($company_name) && empty($company_location) && empty($company_licence) && empty($company_version) && empty($company_startDate))
        {
            $response[] =[
                "status" => 0,
                "message" => "Please fill all the required fields"
            ];
        }
        else
        {
            $sql_count = "SELECT MAX(CMID) AS comNO FROM `company` ";
            $com_no = $dbObj->getData($sql_count);
            $com_no = $com_no[0]["comNO"] + 1;
            $com_no = createCount("COM",$com_no);
            $sql="INSERT INTO `company`(`CompanyNo`, `ComName`, `CompanyLocation`, `LicenceNo`, `VersionNo`, `ComStartDate`, `ComExpireDate`, `ComStat`, `is_multicategory`, `is_commonStock`, `CompanyType_CTID`, `last_updateDate`, `ComLogo`) VALUES ('$com_no','$company_name','$company_location','$company_licence','$company_version','$company_startDate','$company_expireDate','$company_status','$company_multi_cate','$company_common_stock','$company_type',now(), '$company_logo')";
            $insert = $dbObj->executeTransaction($sql);
            if($insert)
            {
                $response[] =[
                    "status" => 1,
                    "message" => "Successfully created new company"
                ];
            }
            else
            {
                $response[] =[
                    "status" => 0,
                    "message" => "Oops! Something went wrong"
                ];

            }
        }
    }
    else if($condition==="edit")
    {
        $CMID = isset($_GET["CMID"]) && !empty($_GET["CMID"]) ? $_GET["CMID"] : "";
        if(empty($CMID))
        {
            $response[] =[
                "status" => 0,
                "message" => "No company id found"
            ];

        }
        else
        {
            $company_type = isset($_POST["company_type"]) && !empty($_POST["company_type"]) ? $_POST["company_type"] : "";
            $company_name = isset($_POST["company_name"]) && !empty($_POST["company_name"]) ? $_POST["company_name"] : "";
            $company_location = isset($_POST["company_location"]) && !empty($_POST["company_location"]) ? $_POST["company_location"] : "";
            $company_licence = isset($_POST["company_licence"]) && !empty($_POST["company_licence"]) ? $_POST["company_licence"] : "";
            $company_version = isset($_POST["company_version"]) && !empty($_POST["company_version"]) ? $_POST["company_version"] : "";
            $company_multi_cate = isset($_POST["company_multi_cate"]) && !empty($_POST["company_multi_cate"]) ? 1 : 0;
            $company_common_stock = isset($_POST["company_common_stock"]) && !empty($_POST["company_common_stock"]) ? 1 : 0;
            $company_status = isset($_POST["company_status"]) && !empty($_POST["company_status"]) ? 1 : 0;
            $company_startDate = isset($_POST["company_startDate"]) && !empty($_POST["company_startDate"]) ? $_POST["company_startDate"] : "";
            $company_expireDate = isset($_POST["company_expireDate"]) && !empty($_POST["company_expireDate"]) ? $_POST["company_expireDate"] : "";
            $company_logo = "";

            if(empty($company_type) || empty($company_name) || empty($company_location) || empty($company_licence) || empty($company_version) || empty($company_startDate))
            {
                $response[] =[
                    "status" => 0,
                    "message" => "Please fill all the required fields"
                ];
            }
            else
            {
                // Get current company
                $sqlCurrent = "SELECT * FROM company WHERE CMID='$CMID'";
                $currentData = $dbObj->getData($sqlCurrent);
                if(empty($currentData))
                {
                    $response[] =[
                        "status" => 0,
                        "message" => "Company not found"
                    ];
                    echo json_encode($response);
                    exit;

                }
                $sqlCurrent = "SELECT ComLogo FROM company WHERE CMID='$CMID'";
                $currentData = $dbObj->getData($sqlCurrent);

                if(empty($currentData))
                {
                    $response[] =[
                        "status" => 0,
                        "message" => "Company not found"
                    ];
                }
                else
                {
                    $company_logo = $currentData[0]["ComLogo"];

                    // New logo uploaded
                    if(isset($_FILES["company_logo"]) && $_FILES["company_logo"]["error"] == 0)
                    {
                        $allowedTypes = [
                            "image/jpeg" => "jpg",
                            "image/png"  => "png"
                        ];

                        $maxSize = 5 * 1024 * 1024;

                        $fileTmp  = $_FILES["company_logo"]["tmp_name"];
                        $fileSize = $_FILES["company_logo"]["size"];

                        if($fileSize > $maxSize)
                        {
                            $response[] = [
                                "status" => 0,
                                "message" => "Logo size must be less than 5MB"
                            ];

                            echo json_encode($response);
                            exit;
                        }

                        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mimeType = finfo_file($fileInfo, $fileTmp);
                        finfo_close($fileInfo);

                        if(!array_key_exists($mimeType, $allowedTypes))
                        {
                            $response[] = [
                                "status" => 0,
                                "message" => "Only JPG and PNG images are allowed"
                            ];

                            echo json_encode($response);
                            exit;
                        }

                        $extension = $allowedTypes[$mimeType];

                        $newLogo = "COMPANY_" .
                            date("YmdHis") . "_" .
                            bin2hex(random_bytes(8)) .
                            "." . $extension;

                        $uploadPath = "../Assets/Images/Company_Logos/" . $newLogo;

                        if(move_uploaded_file($fileTmp, $uploadPath))
                        {
                            // Delete old logo
                            if(!empty($company_logo))
                            {
                                $oldLogoPath = "../Assets/Images/Company_Logos/" . $company_logo;

                                if(file_exists($oldLogoPath))
                                {
                                    unlink($oldLogoPath);
                                }
                            }

                            $company_logo = $newLogo;
                        }
                        else
                        {
                            $response[] = [
                                "status" => 0,
                                "message" => "Failed to upload company logo"
                            ];

                            echo json_encode($response);
                            exit;
                        }
                    }

                    $sql = "UPDATE company SET
                            ComName='$company_name',
                            CompanyLocation='$company_location',
                            LicenceNo='$company_licence',
                            VersionNo='$company_version',
                            ComStartDate='$company_startDate',
                            ComExpireDate='$company_expireDate',
                            ComStat='$company_status',
                            is_multicategory='$company_multi_cate',
                            is_commonStock='$company_common_stock',
                            CompanyType_CTID='$company_type',
                            ComLogo='$company_logo',
                            last_updateDate=NOW()
                        WHERE CMID='$CMID'";

                    $update = $dbObj->executeTransaction($sql);

                    if($update)
                    {
                        $response[] = [
                            "status" => 1,
                            "message" => "Company updated successfully"
                        ];
                    }
                    else
                    {
                        $response[] = [
                            "status" => 0,
                            "message" => "Failed to update company"
                        ];
                    }
                }
            }
            
        }
    }
    else
    {
        $response[] =[
            "status" => 0,
            "message" => "Invalid Condition"
        ];
    }
    echo json_encode($response);
}