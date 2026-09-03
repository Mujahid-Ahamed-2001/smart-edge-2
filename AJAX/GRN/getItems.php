<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
$dbObj = new DBTransactions();
if(isset($_GET['type']) && $_GET['type'] == 'item_search')
{
    $txt_search = !empty($_GET['search']) ? $_GET['search']: '';
    $sql = "SELECT * FROM products p
            WHERE (p.Barcode LIKE '%$txt_search%' 
                OR p.ItemName LIKE '%$txt_search%'  
                OR p.ProductNo LIKE '%$txt_search%') AND  p.ProductStat=1 AND ItemType='P'
            ORDER BY p.PDID DESC;";
   // $sql = "SELECT * FROM products WHERE concat(Barcode, ItemName) LIKE '%".$txt_search."%' AND shop_SHID=".$shop_id.";";
    
    
    
    $itemData = $dbObj->getData($sql);

    if(!empty($itemData))
    {
        $itemResult = array();
        foreach($itemData as $row)
        {
            $data['id'] = $row['PDID'];
            $data['text'] = $row['ProductNo']." - ";
            $data['text'] .= $row['ItemName']." - ";
            $data['text'] .= $row['Barcode'];

            array_push($itemResult, $data);
        }//foreach

    }//has items
    echo json_encode($itemResult);
}//has type
else if(isset($_POST["ItemId"]) && !empty($_POST["ItemId"]) && !empty($_POST["shopid"]))
{
    $PDID = $_POST["ItemId"];
    $shopid = $_POST["shopid"];
    $sql="SELECT * FROM products p 
    LEFT JOIN `inventory` i ON i.products_PDID = p.PDID
    LEFT JOIN pricehistory ON pricehistory.Inventory_INID = i.INID
    WHERE p.PDID = $PDID  ORDER BY p.PDID DESC LIMIT 1";
    $itemData = $dbObj->getData($sql);
    $sql1="SELECT SUM(i.CurrentQty) AS totqty FROM products p 
    INNER JOIN `inventory` i ON i.products_PDID = p.PDID
    WHERE p.PDID = $PDID AND p.ProductStat=1 AND i.shop_SHID='$shopid'  GROUP BY p.PDID ";
    $itemData1 = $dbObj->getData($sql1);
    $result = [];
    if(!empty($itemData) )
    {
        $PurchasePrice = $itemData[0]["PurchasePrice"];
        if(empty($PurchasePrice))
        {
            $PurchasePrice = $itemData[0]["ProdPurchasePrice"];
        }
        $SellingPrice = $itemData[0]["SellingPrice"];
        if(empty($SellingPrice))
        {
            $SellingPrice = $itemData[0]["ProdSellPrice"];
        }
        $totqty = isset($itemData1[0]["totqty"]) && !empty($itemData1[0]["totqty"]) ? $itemData1[0]["totqty"] : 0;
        $result =[
            "ItemName" => $itemData[0]["ItemName"],
            "ProductNo" => $itemData[0]["ProductNo"],
            "Barcode" => $itemData[0]["Barcode"],
            "MnfDate" => $itemData[0]["MnfDate"],
            "ExpDate" => $itemData[0]["ExpDate"],
            "PurchasePrice" => $PurchasePrice,
            "SellingPrice" => $SellingPrice,
            "totqty" => $totqty,
            "PDID" => $PDID,
        ];
    }
    else
    {
        $result =[
            "error" => "No product Found."
            ];
    }
    echo json_encode($result);
}