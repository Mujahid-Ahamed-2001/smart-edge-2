<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
$dbObj = new DBTransactions();
if(isset($_GET["SHID"]))
{   
    $SHID = $_GET["SHID"];
    $sql = "SELECT * FROM `shopfeatures`";
    $itemData = $dbObj->getData($sql);
    foreach ($itemData as $item) 
    {
        $SPFID = $item["SPFID"];
        $sql2 = "SELECT * FROM `shoppermissions` WHERE ShopFeature_SPFID='$SPFID' AND Shop_SHID='$SHID'";
        $items = $dbObj->getData($sql2);
        if(count($items) > 0)
        {
            $checked = "checked";
        }
        else
        {
            $checked="";
        }
        ?>
        <div class="col-md-6">
            <div class="form-check form-switch">
                <label class="form-check-label" for="shopFeature<?=$item["SPFID"]?>"><?=$item["FeatureName"]?></label>
                <input class="form-check-input" name="shopFeature[]" type="checkbox" id="shopFeature<?=$item["SPFID"]?>" value="<?=$item["SPFID"]?>" <?=$checked?>>
            </div>
        </div>
        <?php
    }
}
else
{   
    $sql = "SELECT * FROM `shopfeatures` WHERE SFstatus='1'";
    $itemData = $dbObj->getData($sql);
    foreach ($itemData as $item) 
    {
        ?>
        <div class="col-md-6">
            <div class="form-check form-switch">
                <label class="form-check-label" for="shopFeature<?=$item["SPFID"]?>"><?=$item["FeatureName"]?></label>
                <input class="form-check-input" name="shopFeature[]" type="checkbox" id="shopFeature<?=$item["SPFID"]?>" value="<?=$item["SPFID"]?>">
            </div>
        </div>
        <?php
    }
}