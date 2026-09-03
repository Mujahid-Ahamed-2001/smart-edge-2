<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
$dbObj = new DBTransactions();

$GHID = !empty($_POST["GHID"]) ? $_POST["GHID"] : "";

if(!empty($GHID))
{
    $response = [];
    $sql = "SELECT GH.GHID, GH.GRNHeaderNo, GH.refference, GH.GRNStat, GH.EffectiveDate, GH.shop_SHID, CONCAT(s.SupplierNo,' - ',s.Distributer,' - ',s.SupplierName,' - ',s.Contact) AS SupplierDetails, s.SPID, s.SupplierName, s.Contact, s.SupplierNo, GH.PurchDiscType, GH.PurchDisc, GH.add_notes FROM grnheader GH 
    LEFT JOIN suppliers s ON s.SPID=GH.Suppliers_SPID 
    WHERE GH.GHID='$GHID'";
    $GRNData=$dbObj->getData($sql);
    
    if(!empty($GRNData))
    {
        $response = $GRNData[0];
    }

    echo json_encode($response);
}