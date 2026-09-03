<?php 
ob_start();
include "../Includes/includes.php";
$shop_id = $_SESSION['shop_id'];
$user_id = $_SESSION['user_id'];

$invoice=$_GET["INVID"];
$wholesale= new wholesale_invoice();
 $dbObj = new DBTransactions();
 //Invoice data
 $sql_1 = "SELECT ih.*, ih.shop_SHID AS Invoiceshop_SHID,c.CTID AS CustomerID, c.CustName As CustomerName, ih.Salesmans_SLID AS Salesmans_SLID, c.CustContact AS CustContact, c.CustAddress AS CustAddress, u.UserName AS officer, sm.SalesmansName AS Salesmen FROM `invoiceheader` ih
 INNER JOIN customers c ON c.CTID=ih.customers_CTID
 INNER JOIN user u on u.USID=ih.user_USID
 INNER JOIN salesmans sm ON sm.SLID=ih.Salesmans_SLID
 WHERE ih.IHID= '$invoice';";

$invoiceData = $dbObj->getData($sql_1);
// print_r($invoiceData);
$invoiceCount = count($invoiceData);
$customers_CTID  = $invoiceData[0]['CustomerID'];
$Invoiceshop_SHID = $invoiceData[0]['Invoiceshop_SHID'];

if($Invoiceshop_SHID != $shop_id || $invoiceCount == 0)
{
   ?>
   <script>
    // //window.location="../Public/invoice-list.php";
window.history.back()
   </script>
   <?php
}
if(isset($invoiceData[0]["ReturnHeader_RHID"]) AND $invoiceData[0]["ReturnHeader_RHID"]!=0)
{
    echo "ReturnHeader_RHID";
    $return_header_id=$invoiceData[0]["ReturnHeader_RHID"];
    $sql = "UPDATE `retrun_invoice_header` SET `return_header_stat`='0' WHERE RIHID= '$return_header_id';";
    $update = $dbObj->executeTransaction($sql);
}

$return_header_id=$invoiceData[0]["ReturnHeader_RHID"];
$sql = "SELECT * FROM `creditcustomer` WHERE invoice_header_id='$invoice'";
$creditcustomer = $dbObj->getData($sql);
$creditcustomerCount=count($creditcustomer);
if($creditcustomerCount > 0)
{
    $sql = "UPDATE `creditcustomer` SET `CreditStat`='0' WHERE invoice_header_id= '$invoice';";
    $update = $dbObj->executeTransaction($sql);   
}
if($invoiceData[0]["Inv_Type"]==1)
{
    $sl=1;
    $sql_2 = "SELECT id.*, p.ItemName AS ItemName, p.Barcode AS ItemCode, p.ProdDescription AS Description FROM `invoicedetails` id 
    INNER JOIN products p ON p.PDID=id.products_PDID
    WHERE id.InvoiceHeader_IHID='$invoice';";
    $shopData = $dbObj->getData($sql_2);
    foreach ($shopData as $row) 
    {
        // print_r($row);
        $sql_3 = "SELECT * FROM `inventory` WHERE products_PDID='$row[products_PDID]' AND BatchID='$row[batch_no]'; ";
        $inventory = $dbObj->getData($sql_3);
        $INID = $inventory[0]["INID"];
        $newSoldQty=$inventory[0]["BillQty"]-$row["SellQty"];
        if($newSoldQty <= 0)
        {
            $sql4 = "UPDATE `inventory` SET `CurrentQty`=CurrentQty + '$row[SellQty]',`BillQty`=0 WHERE INID= '$INID';";
        }
        else
        {
          $sql4 = "UPDATE `inventory` SET `CurrentQty`=CurrentQty + '$row[SellQty]',`BillQty`=BillQty - '$row[SellQty]' WHERE INID= '$INID';";  
        }    
        // $update = $dbObj->executeTransaction($sql4);     
            // $update = $dbObj->executeTransaction($sql4);  
            if($dbObj->executeTransaction($sql4))
            {
                echo "Inventory Updated <br>";
            }      
    }
}
else if($invoiceData[0]["Inv_Type"]==2)
{
    $sql2="SELECT * FROM `inventory_consumption` WHERE invoice_headerID='$invoice' AND status=1";
    $inventoryData = $dbObj->getData($sql2);
    if(count($inventoryData) > 0)
    {
        foreach($inventoryData  as $row)
        {
            // print_r($row);
            $INID=$row["inventory_INID"];
            $sql_3 = "SELECT * FROM `inventory` WHERE INID='$INID'; ";
            $inventory = $dbObj->getData($sql_3);
            $quantity=$row["quantity"];
            $newSoldQty=$inventory[0]["BillQty"]-$row["quantity"];
            
            $sql4 = "UPDATE `inventory` SET `CurrentQty`=CurrentQty + '$quantity',`BillQty`=BillQty - '$quantity' WHERE INID= '$INID';";   
            // $update = $dbObj->executeTransaction($sql4);  
            if($dbObj->executeTransaction($sql4))
            {
                echo "Inventory Updated <br>";
            }
            $sql4 = "UPDATE `inventory_consumption` SET `status`='0' WHERE inventory_INID= '$INID';";  
            // $update = $dbObj->executeTransaction($sql4);  
            if($dbObj->executeTransaction($sql4))
            {
                echo "Inventory consumption Updated <br>";
            }
        }
    }
    
}

$sql_5 = "UPDATE `transactions` SET `TransactionStat`=0  WHERE InvoiceHeader_IHID= '$invoice';";
$update = $dbObj->executeTransaction($sql_5);

$sql_6 = "UPDATE `invoiceheader` SET `InvStat`=0  WHERE IHID= '$invoice';";
$update = $dbObj->executeTransaction($sql_6);
$remarks="Invoice Cancelled";
$invoice_remark=$wholesale->invoice_remark(remarks: $remarks,user: $user_id,invoice_header_id: $invoice,from_invoice: 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Processing...</title>
<style>
/* Full-screen loading screen */
#loading-screen {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: #111; /* dark theme */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: #fff;
    font-family: Arial, sans-serif;
    z-index: 9999;
}

/* Spinner animation */
.spinner {
    border: 6px solid rgba(255, 255, 255, 0.2);
    border-top: 6px solid #00bcd4;
    border-radius: 50%;
    width: 70px;
    height: 70px;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
</head>
<body>

<div id="loading-screen">
    <div class="spinner"></div>
    <h2>Please wait... Processing your request</h2>
</div>

<script>
// Redirect after 5 seconds (5000 ms)
setTimeout(function() {
    window.location.href = "../Public/view-wholesaleinovice.php?INVID=<?=$invoice?>";
}, 2000);
</script>

</body>
</html>

<?php

// header("Location:../Public/view-wholesaleinovice.php?INVID=$invoice");
//print date time
exit;
ob_end_flush();

