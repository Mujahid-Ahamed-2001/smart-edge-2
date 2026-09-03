<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
$dbObj = new DBTransactions();

$ItemName = isset($_POST["ItemName"]) && !empty($_POST["ItemName"]) ? $_POST["ItemName"] : "";
$ProductNo = isset($_POST["ProductNo"]) && !empty($_POST["ProductNo"]) ? $_POST["ProductNo"] : "";
$Barcode = isset($_POST["Barcode"]) && !empty($_POST["Barcode"]) ? $_POST["Barcode"] : "";
$PurchasePrice = isset($_POST["PurchasePrice"]) ? $_POST["PurchasePrice"] : 0;
$SellingPrice = isset($_POST["SellingPrice"]) ? $_POST["SellingPrice"] : 0;
$qty = isset($_POST["qty"])  ? $_POST["qty"] : 1;
$PDID = isset($_POST["PDID"]) && !empty($_POST["PDID"]) ? $_POST["PDID"] : "";
$ExpDate = isset($_POST["ExpDate"]) && !empty($_POST["ExpDate"]) ? $_POST["ExpDate"] : "";
$MnfDate = isset($_POST["MnfDate"]) && !empty($_POST["MnfDate"]) ? $_POST["MnfDate"] : "";
$grn_header = isset($_POST["grn_header"]) && !empty($_POST["grn_header"]) ? $_POST["grn_header"] : "";
$GDID = isset($_POST["GDID"]) && !empty($_POST["GDID"]) ? $_POST["GDID"] : "";
$shopid = isset($_POST["shopid"]) && !empty($_POST["shopid"]) ? $_POST["shopid"] : "";
$response =[];
if(empty($shopid))
{
    $response = ["error"=>"Invalid Shop ID"];
    echo json_encode($response);
    exit();
}
if(!empty($GDID) && !empty($shopid))
{
    $sql2="SELECT * , SUM(i.CurrentQty) AS stock, gd.CurrentQty AS gd_CurrentQty FROM grndetails gd 
    INNER JOIN products p ON p.PDID = gd.products_PDID
    LEFT JOIN inventory i ON i.products_PDID = p.PDID AND i.shop_SHID='$shopid'
    WHERE gd.GDID='$GDID' GROUP BY i.products_PDID";
    $result=$dbObj->getData($sql2);
    $result = $result[0];
    $datas["ExpDate"] = isset($result["ExpDate"]) && !empty($result["ExpDate"]) ? $result["ExpDate"] : "0000-00-00";
    $datas["GDID"] = isset($result["GDID"]) && !empty($result["GDID"]) ? $result["GDID"] : "";
    $datas["MnfDate"] = isset($result["MnfDate"]) && !empty($result["MnfDate"]) ? $result["MnfDate"] : "0000-00-00";
    $datas["TotalPurchasePrice"] = isset($result["TotalPurchasePrice"]) && !empty($result["TotalPurchasePrice"]) ? $result["TotalPurchasePrice"] : "0.00";
    $datas["TotalSellPrice"] = isset($result["TotalSellPrice"]) && !empty($result["TotalSellPrice"]) ? $result["TotalSellPrice"] : "0.00";
    $datas["UnitLabelPrice"] = isset($result["UnitLabelPrice"]) && !empty($result["UnitLabelPrice"]) ? $result["UnitLabelPrice"] : "0.00";
    $datas["UnitPurchasePrice"] = isset($result["UnitPurchasePrice"]) && !empty($result["UnitPurchasePrice"]) ? $result["UnitPurchasePrice"] : "0.00";
    $datas["UnitSellPrice"] = isset($result["UnitSellPrice"]) && !empty($result["UnitSellPrice"]) ? $result["UnitSellPrice"] : "0.00";
    $datas["products_PDID"] = isset($result["products_PDID"]) && !empty($result["products_PDID"]) ? $result["products_PDID"] :"";
    $datas["stock"] = isset($result["stock"]) && !empty($result["stock"]) ? $result["stock"] : "0.00";
    $datas["CurrentQty"] = isset($result["gd_CurrentQty"]) && !empty($result["gd_CurrentQty"]) ? $result["gd_CurrentQty"] : "0.00";
    $datas["ItemName"] = isset($result["ItemName"]) && !empty($result["ItemName"]) ? $result["ItemName"] : "";
    $datas["ProductNo"] = isset($result["ProductNo"]) && !empty($result["ProductNo"]) ? $result["ProductNo"] : "";
    $datas["Barcode"] = isset($result["Barcode"]) && !empty($result["Barcode"]) ? $result["Barcode"] : "";
    $response = $datas;
}
else
{
    $TotalSellPrice = $SellingPrice * $qty;
    $TotalPurchasePrice = $PurchasePrice * $qty;
    $sql = "INSERT INTO grndetails
    (CurrentQty, UnitPurchasePrice, UnitLabelPrice, UnitSellPrice, TotalPurchasePrice, TotalSellPrice, MnfDate, ExpDate, products_PDID, GRNHeader_GHID)
    VALUES (?,?,?,?,?,?,?,?,?,?)";

    $data = [ $qty, $PurchasePrice, $SellingPrice, $SellingPrice, $TotalPurchasePrice, $TotalSellPrice, $MnfDate, $ExpDate, $PDID, $grn_header];
    $GDID = $dbObj->executeTransactionAndReturnLastInsertID($sql,$data);
    if($GDID > 0)
    {
        $sql2="SELECT * , SUM(i.CurrentQty) AS stock, gd.CurrentQty AS gd_CurrentQty FROM grndetails gd 
        INNER JOIN products p ON p.PDID = gd.products_PDID
        LEFT JOIN inventory i ON i.products_PDID = p.PDID AND i.shop_SHID='$shopid'
        WHERE gd.GDID='$GDID' GROUP BY i.products_PDID";
        $result=$dbObj->getData($sql2);
        $result = $result[0];
        $datas["ExpDate"] = isset($result["ExpDate"]) && !empty($result["ExpDate"]) ? $result["ExpDate"] : "0000-00-00";
        $datas["GDID"] = isset($result["GDID"]) && !empty($result["GDID"]) ? $result["GDID"] : "";
        $datas["MnfDate"] = isset($result["MnfDate"]) && !empty($result["MnfDate"]) ? $result["MnfDate"] : "0000-00-00";
        $datas["TotalPurchasePrice"] = isset($result["TotalPurchasePrice"]) && !empty($result["TotalPurchasePrice"]) ? $result["TotalPurchasePrice"] : "0.00";
        $datas["TotalSellPrice"] = isset($result["TotalSellPrice"]) && !empty($result["TotalSellPrice"]) ? $result["TotalSellPrice"] : "0.00";
        $datas["UnitLabelPrice"] = isset($result["UnitLabelPrice"]) && !empty($result["UnitLabelPrice"]) ? $result["UnitLabelPrice"] : "0.00";
        $datas["UnitPurchasePrice"] = isset($result["UnitPurchasePrice"]) && !empty($result["UnitPurchasePrice"]) ? $result["UnitPurchasePrice"] : "0.00";
        $datas["UnitSellPrice"] = isset($result["UnitSellPrice"]) && !empty($result["UnitSellPrice"]) ? $result["UnitSellPrice"] : "0.00";
        $datas["products_PDID"] = isset($result["products_PDID"]) && !empty($result["products_PDID"]) ? $result["products_PDID"] : "";
        $datas["stock"] = isset($result["stock"]) && !empty($result["stock"]) ? $result["stock"] : "0.00";
        $datas["CurrentQty"] = isset($result["gd_CurrentQty"]) && !empty($result["gd_CurrentQty"]) ? $result["gd_CurrentQty"] : "0.00";
        $datas["ItemName"] = isset($result["ItemName"]) && !empty($result["ItemName"]) ? $result["ItemName"] : "";
        $datas["ProductNo"] = isset($result["ProductNo"]) && !empty($result["ProductNo"]) ? $result["ProductNo"] : "";
        $datas["Barcode"] = isset($result["Barcode"]) && !empty($result["Barcode"]) ? $result["Barcode"] : "";
        $response = $datas;
    }
    else
    {
        $response = ["error" => "Oops! Something went wrong"];
    }
}
echo json_encode($response);