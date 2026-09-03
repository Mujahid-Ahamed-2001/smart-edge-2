<?php 
include "../Includes/includes.php";
include "../Includes/authcheck.php";

//system Module 
//Insert
$sysModelsObj = new sysModels();
if(isset($_POST['btn_save_Features']))
{
    if(isset($_POST["shopFeatureName"]) && !empty($_POST["shopFeatureName"]))
    {
        $shopFeatureName = $_POST["shopFeatureName"];
        $status=0;
        if(isset($_POST["status"]) || !empty($_POST["status"]))
        {
            $status=1;
        }
        $insert = $sysModelsObj->insertShopFeature($shopFeatureName,$status);
        if($insert==1)
        {
            $_SESSION["success"]=1;
        }
        else
        {
            $_SESSION["Error"] = 1;
        }

    }
    else
    {
        $_SESSION["Error"]=0;
    }

    header("Location: ../Public/ShopFeaturesList.php");
}
elseif(isset($_POST["btn_Update_Feature"]))
{
    if(isset($_POST["shopFeatureName"]) && !empty($_POST["shopFeatureName"]) && isset($_POST["spfid"]) && !empty($_POST["spfid"]))
    {
        $SPFID = $_POST["spfid"];
        $shopFeatureName = $_POST["shopFeatureName"];
        $status=0;
        if(isset($_POST["status"]) || !empty($_POST["status"]))
        {
            $status=1;
        }
        $update = $sysModelsObj->updateShopFeature($shopFeatureName, $status, $SPFID);
        if($update==1)
        {
            $_SESSION["Usuccess"]=1;
        }
        else
        {
            $_SESSION["Error"] = 1;
        }
    }
    else
    {
        $_SESSION["Error"]=0;
    }

    header("Location: ../Public/ShopFeaturesList.php");
}