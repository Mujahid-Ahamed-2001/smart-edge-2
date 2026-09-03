<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

include "../Includes/config.php";
include '../Model/DB_Class.php';
include '../Model/user_class.php';
include '../Model/shop_class.php';

$shop_id = $_SESSION['shop_id'];
$invoice_id = isset($_GET['invoice_id']) ? $_GET['invoice_id'] : 0;

$dbObj = new DBTransactions();

// INVOICE + CUSTOMER
$sql = "SELECT * FROM invoiceheader 
INNER JOIN customers ON customers.CTID = invoiceheader.customers_CTID
WHERE IHID = ".$invoice_id;
$invData = $dbObj->getData($sql);

$cust_name    = isset($invData[0]['CustName']) ? $invData[0]['CustName'] : '';
$cust_contact = isset($invData[0]['CustContact']) ? $invData[0]['CustContact'] : '';
$cust_address = isset($invData[0]['CustAddress']) ? $invData[0]['CustAddress'] : '';
$date         = isset($invData[0]['EffectiveDate']) ? $invData[0]['EffectiveDate'] : '';

// SHOP
$shopObj = new Shop();
$shopData = $shopObj->getOneShop($shop_id);

$shop_name    = isset($shopData[0]['ShopName']) ? $shopData[0]['ShopName'] : '';
$shop_phone   = isset($shopData[0]['PhoneNumber']) ? $shopData[0]['PhoneNumber'] : '';
$shop_address = 
    (isset($shopData[0]['AddressLineOne']) ? $shopData[0]['AddressLineOne'] : '') . "<br>" .
    (isset($shopData[0]['AddressLineTwo']) ? $shopData[0]['AddressLineTwo'] : '') . "<br>" .
    (isset($shopData[0]['City']) ? $shopData[0]['City'] : '');
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Delivery Note</title>

<style>
body {
    width: 80mm;
    font-family: Arial, sans-serif;
    font-size: 14px; /* BIGGER BASE */
}

.container {
    width: 100%;
}

.center {
    text-align: center;
}

.bold {
    font-weight: bold;
}

.title {
    font-size: 18px !important;
    font-weight: bold;
    text-align: center;
}

.urgent {
    border: 2px solid #000;
    padding: 8px;
    text-align: center;
    font-weight: bold;
    font-size: 16px;
    margin: 6px 0;
}

.box {
    border: 2px solid #000;
    padding: 8px;
    margin-top: 6px;
    font-size: 14px;
}

.label {
    font-weight: bold;
    font-size: 15px;
    margin-bottom: 3px;
}

.cod-box {
    border: 2px solid #000;
    padding: 10px;
    margin-top: 8px;
}

.cod-title {
    font-weight: bold;
    font-size: 16px;
}

.cod-line {
    border-bottom: 2px dotted #000;
    height: 35px; /* INCREASED HEIGHT */
    margin-top: 8px;
}

.signature-line {
    border-bottom: 2px dotted #000;
    height: 30px;
    margin-top: 10px;
}

.small {
    font-size: 13px;
}
</style>

</head>

<body>

<div class="container">

    <!-- TITLE -->
    <div class="title">DELIVERY NOTE</div>

    <!-- URGENT -->
    <div class="urgent">VERY URGENT</div>

    <!-- DATE -->
    <div class="small">
        <b>Date:</b> <?php echo $date; ?>
    </div>

    <!-- FROM -->
    <div class="box">
        <div class="label">FROM:</div>
        <p style="font-size:18px !important; line-height:18px !important;"><?php echo $shop_name; ?></p>
        <p style="font-size:18px !important; line-height:18px !important;"><?php echo $shop_address; ?></p>
        <p style="font-size:18px !important; line-height:18px !important;"><b>Tel:</b> <?php echo $shop_phone; ?></p>
    </div>

    <!-- TO -->
    <div class="box">
        <div class="label">TO:</div>
        <p style="font-size:18px !important; line-height:18px !important;"><?php echo $cust_name; ?></p>
        <p style="font-size:18px !important; line-height:18px !important;"><b>Tel:</b> <?php echo $cust_contact; ?></p>
        <p style="font-size:18px !important; line-height:18px !important;"><?php echo $cust_address; ?></p>
    </div>

    <!-- COD -->
    <div class="cod-box">
        <div class="cod-title">COD AMOUNT</div>
        <div class="cod-line"></div>
    </div>

    <!-- SIGNATURE -->
    <div class="box">
        <div class="label">Receiver Signature</div>
        <div class="signature-line"></div>
    </div>

</div>

<script>
window.print();
setTimeout(function(){
    window.close();
},300);
</script>

</body>
</html>