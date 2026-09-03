<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$shop_id = $_SESSION['shop_id'];
$dbObj = new DBTransactions();
$sql = "SELECT * FROM shop
INNER JOIN company ON company.CMID = shop.Company_CMID
WHERE SHID = ".$shop_id.";";
$shopData = $dbObj->getData($sql);
$multi_category = $shopData[0]['is_multicategory'];

if($_GET['type'] == 'item_search')
{
    $txt_search = !empty($_GET['search']) ? $_GET['search']: '';
    $shop_id = isset($_SESSION['shop_id']) ? $_SESSION['shop_id']: '0';
    $sql = "SELECT * FROM customers WHERE concat(CustName, CustContact) LIKE '%$txt_search%' ;";

    if($multi_category==1)
    {
        $com_id=$shopData[0]['CMID'];
        $sql = "SELECT * FROM customers c
                INNER JOIN shop s ON s.SHID = c.shop_SHID
                WHERE concat(c.CustName, c.CustContact) LIKE '%$txt_search%' AND s.Company_CMID='$com_id';";
    }
   // $sql = "SELECT * FROM products WHERE concat(Barcode, ItemName) LIKE '%".$txt_search."%' AND shop_SHID=".$shop_id.";";
    
    $dbObj = new DBTransactions();
    $itemData = $dbObj->getData($sql);

    if(!empty($itemData))
    {
        $itemResult = array();
        foreach($itemData as $row)
        {
            $data['id'] = $row['CTID'];
            $data['text'] = $row['CustName'] ." - ". $row['CustContact'];
            $data['phone'] = $row['CustContact']; 
            $ref = isset($_GET['ref']) ? $_GET['ref'] : '';
            $data['full_text'] = $row['CustomerNo']." - ".$row['CustName']." - ".$row['country_code']." ".$row['CustContact'];
            if($row['CTID']!=1 && !empty($ref))
            {
                $data['full_text'].="<a href='../Public/customerProfile.php?cus_id=".$row['CTID']."&ref=".$ref."' class='open-modal'> View Customer Profile</a>";
                $data['full_text'] .='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="../View/modals/customer-modal.php?CTID='.$row['CTID'].'&condition=edit&ref='.$ref.'" data-title="Edit Customer" class="open-modal"><i class="ti ti-edit"></i></a>';
            }
            
            array_push($itemResult, $data);
        }//foreach

    }//has items
    else
    {
        $itemResult = array();
        $data['id'] = "Add";
        $data['text'] = "Add Customer";
        array_push($itemResult, $data);
    }
    echo json_encode($itemResult);
}//has type