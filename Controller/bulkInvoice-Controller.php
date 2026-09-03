<?php 
// session_start();
    include '../Includes/includes.php';
if(isset($_POST["bulkInvoice"])) {
$dbObj = new DBTransactions();
    if(!empty($_POST["invoice"])) {
        $shop_id = $_SESSION["shop_id"];// Secure the filename


        for ($i = 0; $i < count($_POST["invoice"]); $i++) { 
            $sql = "SELECT * FROM shopreceipts WHERE ReceiptStat = 1 AND shop_id='$shop_id' AND RecieptType=2";
            $dbData = $dbObj->getData($sql);
            $invoice = empty($dbData) ? "wholesaleInvoice.php" : basename($dbData[0]['ReceiptPath']); 
            $invoice_id = $_POST["invoice"][$i]; // Ensure it's a valid integer
            $_GET["invoice"]=$invoice_id;
            $_GET["avoice"]=1;
            // echo "../Receipts/" . $invoice;
            include "../Receipts/retail-invoice.php";
        }
    } else {
        header("Location: ../Public/invoice-list.php");
        exit;
    }
}
