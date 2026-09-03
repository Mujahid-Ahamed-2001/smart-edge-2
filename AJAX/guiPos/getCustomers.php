<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$shop_id = $_SESSION['shop_id'];
$dbObj = new DBTransactions();

if($_GET['type'] == 'item_search')
{
    $txt_search = !empty($_GET['search']) ? $_GET['search']: '';

    $sql = "SELECT * FROM customers WHERE concat(CustName, CustContact) LIKE '%$txt_search%' ";

    // if(!isset($_GET['status']) && empty($_GET['status'])) {
    //     $sql .= " AND CustStat=1";
    // }
    
    $dbObj = new DBTransactions();
    $itemData = $dbObj->getData($sql);

    if(!empty($itemData))
    {
        $itemResult = array();
        foreach($itemData as $row)
        {
            $data['id'] = $row['CTID'];
            $data['text'] = $row['CustName'] ." - ".$row["country_code"]. $row['CustContact'];

            if($row['CustStat'] == 0)
            {
                $data['text'] .= " (Inactive)";
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