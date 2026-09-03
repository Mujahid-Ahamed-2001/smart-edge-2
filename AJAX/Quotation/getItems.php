<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
include "../../Model/shop_class.php";
include '../../Model/wholesale_invoice_class.php';
$shopObj=new Shop();

$shop_id = $_SESSION['shop_id'];
$hasMinus=$shopObj->hasMinus($shop_id);
$hasbatchNo=$shopObj->hasbatchNo($shop_id);
$wholesale_invoice= new wholesale_invoice();
if($_GET['type'] == 'item_search')
{
    $txt_search = !empty($_GET['search']) ? $_GET['search']: '';
    $shop_id = $_SESSION['shop_id'];
    $dbObj = new DBTransactions();
    //get company stat
    $sql = "SELECT * FROM products p WHERE (p.Barcode LIKE '%$txt_search%' OR p.ItemName LIKE '%$txt_search%'  OR p.ProductNo LIKE '%$txt_search%') AND p.ProductStat = 1 GROUP BY p.PDID;"; 
    $itemData = $dbObj->getData($sql);

    if(!empty($itemData))
    {
        $itemResult = array();
        foreach($itemData as $row)
        {
            $data['id'] = $row['PDID'];
            $data['text'] = $row['Barcode'];
            $data['text'] .= " - ".$row['ItemName'];
            array_push($itemResult, $data);
        }//foreach

    }//has items
    echo json_encode($itemResult);
}//has type