<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
$dbObj = new DBTransactions();

if($_GET['type'] == 'item_search')
{
    $txt_search = !empty($_GET['search']) ? $_GET['search']: '';

    $sql = "SELECT SPID, CONCAT(SupplierNo,' - ',Distributer,' - ',SupplierName,' - ',Contact) AS SupplierDetails FROM suppliers WHERE concat(Distributer, SupplierName, Contact, SupplierNo) LIKE '%$txt_search%' AND SupplierStat=1 ;";
    
    $dbObj = new DBTransactions();
    $itemData = $dbObj->getData($sql);
    $itemResult = array();
    if(!empty($itemData))
    {
        
        foreach($itemData as $row)
        {
            $data['id'] = $row['SPID'];
            $data['text'] = $row['SupplierDetails'];

            array_push($itemResult, $data);
        }//foreach

    }//has items
    echo json_encode($itemResult);
}//has type
?>