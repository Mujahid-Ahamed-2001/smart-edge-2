<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$shop_id = $_SESSION['shop_id'];
$dbObj = new DBTransactions();
//shop data
$sql="SELECT * FROM shop WHERE SHID='$shop_id'";
$shopdata = $dbObj->getData($sql);

//company data
$company_id=$shopdata[0]["Company_CMID"];
$sql="SELECT * FROM company WHERE CMID='$company_id' ";
$companydata = $dbObj->getData($sql);
$is_multicategory = $companydata[0]["is_multicategory"];
$is_commonStock = $companydata[0]["is_commonStock"];
$is_minus=$shopdata[0]["is_minus"];
$add="";
$is_expire = $shopdata[0]["is_expire"] ?? false;

if($_GET['type'] == 'item_search')
{
    $txt_search = !empty($_GET['search']) ? $_GET['search']: '';
    $shop_id = isset($_SESSION['shop_id']) ? $_SESSION['shop_id']: '0';

    if($is_multicategory==1 )
    {
        $sql = "SELECT * FROM `products` p 
        INNER JOIN subcategories sc ON sc.SCID=p.Subcategories_SCID
        INNER JOIN shop s ON s.SHID=p.shop_SHID
        WHERE s.Company_CMID='$company_id' AND p.ProductStat=1 AND concat(p.Barcode, p.ItemName) LIKE '%".$txt_search."%'   ORDER BY p.ItemName,p.ItemType ASC;";
        $shopie=1;
    }
    else
    {
        $sql = "SELECT * FROM `products` p 
        INNER JOIN subcategories sc ON sc.SCID=p.Subcategories_SCID
        WHERE p.shop_SHID='$shop_id' AND p.ProductStat=1 AND concat(p.Barcode, p.ItemName) LIKE '%".$txt_search."%'   ORDER BY p.ItemName,p.ItemType ASC;";
    }
    $itemData = $dbObj->getData($sql); 

    if(!empty($itemData))
    {
        $itemResult = array();
        foreach($itemData as $row)
        {
            $data['id'] = $row['PDID'];
            $data['text'] = $row['Barcode'] ." - ". $row['ItemName'];

            array_push($itemResult, $data);
        }//foreach

    }//has items
    echo json_encode($itemResult);
}//has type