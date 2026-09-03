<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$shop_id = $_SESSION['shop_id'];
$item_id = $_POST['item_id'];
if(!empty($item_id))
{
    $dbObj = new DBTransactions();
    //get company stat
    $sql = "SELECT 
        p.ProdDescription AS description,
        ph.PurchasePrice AS cost,
        ph.SellingPrice AS rate,
        ph.PHID AS id
    FROM products p
    LEFT JOIN (
        SELECT *
        FROM pricehistory
        WHERE ProductID = '$item_id'
        ORDER BY PHID DESC
        LIMIT 1
    ) ph ON ph.ProductID = p.PDID
    WHERE p.PDID = '$item_id' 
    AND p.ProductStat = 1;"; 
    $itemData = $dbObj->getData($sql);

    if(!empty($itemData))
    {
        $itemResult = array();
        foreach($itemData as $row)
        {
            $data['description'] = $row['description'];
            $data['cost'] = $row['cost'];
            $data['rate'] = $row['rate'];
            $data['id'] = $row['id'];
            array_push($itemResult, $data);
        }//foreach

    }//has items
    echo json_encode($itemResult);
}
    