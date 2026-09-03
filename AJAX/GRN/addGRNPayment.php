<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
$dbObj = new DBTransactions();

$GHID = isset($_POST["GHID"]) && !empty($_POST["GHID"]) ? $_POST["GHID"] : "";
$shop_id = isset($_SESSION["shop_id"]) && !empty($_SESSION["shop_id"]) ? $_SESSION["shop_id"] : "";
$response = [];
if(!empty($GHID) && !empty($shop_id))
{
    $sql = "SELECT * FROM shoppaymethod sp
    INNER JOIN paymethod p ON p.PMID = sp.paymethod_PMID
    WHERE sp.shop_SHID = ".$shop_id." AND (p.PMID!=6 AND p.PMID!=4  AND p.PMID!=9  AND p.PMID!=10 AND p.PMID!=12) LIMIT 1;";
    $dbPaymethods = $dbObj->getData($sql);
    $PMID=0;
    if(count($dbPaymethods) > 0)
    {
        $PMID = $dbPaymethods[0]["PMID"];
    }
    else
    {
        $sql = "SELECT * FROM paymethod WHERE PMID = 1 OR PMID = 2 OR PMID =3";
        $dbPaymethods = $dbObj->getData($sql);
        $PMID = $dbPaymethods[0]["PMID"];
    }
    $insert_sql = "INSERT INTO `suppliertransactions`(`TransferAmount`, `TransactionStat`, `paymethod_PMID`, `GRNHeader_GHID`, `tran_date`) VALUES ('0','1','$PMID','$GHID', now())";
    $insert = $dbObj->executeTransaction($insert_sql);
    if($insert)
    {
        $sql_select_transaction = "SELECT * FROM suppliertransactions WHERE TransactionStat=1 AND GRNHeader_GHID='$GHID' ORDER BY TRID DESC LIMIT 1";
        $transaction_data = $dbObj->getData($sql_select_transaction);
        $response = ["success" => "created", "data" =>$transaction_data[0]];
    }
    else
    {
        $response = ["error" => "Oops! Something went wrong."];
    }
    
}
else
{
    $response = ["error" => "Invalid GRN ID"];
}

echo json_encode($response);