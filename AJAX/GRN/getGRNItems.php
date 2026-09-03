<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
$dbObj = new DBTransactions();

$GHID = isset($_POST["GHID"]) && !empty($_POST["GHID"]) ? $_POST["GHID"] : "";
$response = [];
if(!empty($GHID))
{
    $sql="SELECT * FROM grndetails gd 
    INNER JOIN products p ON p.PDID = gd.products_PDID
    WHERE gd.GRNHeader_GHID=$GHID";
    $results  = $dbObj->getData($sql);
    if(!empty($results))
    {
        foreach ($results  as $row) {
            $datas["ItemName"][] = isset($row["ItemName"]) && !empty($row["ItemName"]) ? $row["ItemName"] : "0000-00-00";
            $datas["ProductNo"][] = isset($row["ProductNo"]) && !empty($row["ProductNo"]) ? $row["ProductNo"] : "";
            $datas["Barcode"][] = isset($row["Barcode"]) && !empty($row["Barcode"]) ? $row["Barcode"] : "0000-00-00";
            $datas["PurchasePrice"][] = isset($row["UnitPurchasePrice"]) && !empty($row["UnitPurchasePrice"]) ? $row["UnitPurchasePrice"] : "0.00";
            $datas["SellingPrice"][] = isset($row["UnitSellPrice"]) && !empty($row["UnitSellPrice"]) ? $row["UnitSellPrice"] : "0.00";
            $datas["totqty"][] = 0;
            $datas["PDID"][] = isset($row["PDID"]) && !empty($row["PDID"]) ? $row["PDID"] : "";
            $datas["ExpDate"][] = isset($row["ExpDate"]) && !empty($row["ExpDate"]) ? $row["ExpDate"] : "";
            $datas["MnfDate"][] = isset($row["MnfDate"]) && !empty($row["MnfDate"]) ? $row["MnfDate"] :"";
            $datas["GDID"][] = isset($row["GDID"]) && !empty($row["GDID"]) ? $row["GDID"] : "";
            $datas["qty"][] = isset($row["CurrentQty"]) && !empty($row["CurrentQty"]) ? $row["CurrentQty"] : "0.00";
        }
        
        $response = $datas;    
    }
    else
    {
        
    }
    
}
else
{
    $response = ["error" => "Invalid GRN ID"];
}

echo json_encode($response);