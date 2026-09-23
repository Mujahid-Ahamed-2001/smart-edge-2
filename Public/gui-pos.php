<?php 
include '../Includes/includes.php';
include '../Includes/authcheck.php';
$noRight=1;
$dbObj = new DBTransactions();
$shopObj=new Shop();
$shop_id = $_SESSION['shop_id'];
$guiObj= new guiPOS;
$counterObj = new Counter();
$user_id = $_SESSION['user_id'];
$shopData=$shopObj->getOneShop($shop_id);



$is_minus=$shopData[0]["is_minus"];
$is_under_cost=$shopData[0]["is_under_cost"];
$is_fixedprice=$shopData[0]["is_fixedprice"];



$doc=$guiObj->select_docno($shop_id);
    if(count($doc)==0) 
    {
        $inser_doc=$guiObj->insert_doc_no($shop_id);
        $doc=$guiObj->select_docno($shop_id);
    }
    $ws_no=$doc[0]["org_no"] + 1;
    $ws_no=$guiObj->getSequence($ws_no);
    $salesetings=$guiObj->getSaleSettings($shop_id);
    if(count($salesetings)>0)
    {
        if(isset($salesetings[0]["billNoHeader"]))
        {
            $ws_no=$salesetings[0]["billNoHeader"]."-".$ws_no;
        }
        else
        {
            $ws_no="INV-".$ws_no;
        }
    }
    else
    {
        $ws_no="INV-".$ws_no;
    }
function CounterCheck($user_id, $shop_id)
{
    //get current date time
    date_default_timezone_set("Asia/Colombo");
    $current_date = date("Y-m-d");

    $sql = "SELECT * FROM cashcounter WHERE user_USID='$user_id' AND shop_SHID='$shop_id' AND CounterStat = 1 AND CounterDate = '$current_date' ORDER BY CCID DESC LIMIT 1;";

    $dbObj = new DBTransactions();
    $dbData = $dbObj->getData($sql);
    // echo "sql "+$sql;

    if(!empty($dbData))
    {
        return true;
    }//has counter
    else
    {
        return false;
    }//no counter
}//counter check
//shop data
$sql="SELECT * FROM shop WHERE SHID='$shop_id'";
$shopdata = $dbObj->getData($sql);
//company data
$company_id=$shopdata[0]["Company_CMID"];
$sql="SELECT * FROM company WHERE CMID='$company_id' ";
$companydata = $dbObj->getData($sql);

$sql="SELECT * FROM salesmans WHERE SLID=1";
$salesmandata = $dbObj->getData($sql);

$sql="SELECT * FROM customers WHERE CTID=1";
$customerdata = $dbObj->getData($sql);
?>

<!doctype html>
<html lang="en">

<head>

  
<link href="../bootstrap_502/css/bootstrap.min.css" rel="stylesheet">
<!-- <link rel="stylesheet" href="../Assets/css/gui-pos.css"> -->
<link rel="stylesheet" href="../Assets/css/gui-pos-v2.css">
    <?php 
  include '../View/head.php';
  // include '../View/loader.php';
  ?>
</head>

<body>
    <div id="modal"></div>
<!--  Body Wrapper -->
<div class="h-100vh">
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        <?php 
        // include '../View/sidebar.php';
        ?>
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            <?php 
            include '../View/gui-header2.php';
            include "../View/modals/inventoryModal.php"; 
            include "../View/modals/HoldListModal.php"; 
            include "../View/modals/ShortcutListModal.php"; 
            include "../View/modals/productModal.php"; 
            // include '../View/modals/add-customer-wholesale.php';
            if($shopObj->hascounter($shop_id)==1)
            {
                //check cash counter
                if(!CounterCheck($user_id, $shop_id))
                {
                    // include "../View/modals/gui-close-counter.php";
                    $_SESSION['status']=3;
                    ?>
                    <!-- <input type="text" name="countID" id="countID" value=""> -->
                    <script>
                        
                        window.location="../Public/home.php";
                    </script>
                    <?php
                }//no counter
            }

            ?>
            <!--  Header End -->
            <form id="order_form" action="" method="post" class="preDefault">
                <?php 
                
                    include "../View/modals/termConditionModal.php";                                 
                    include "../View/modals/GUI-payment.php"; 
                ?>
                <div class="d-none">
                    <input type="hidden" name="is_minus" class="d-none" id="is_minus" value="<?=$is_minus?>">
                    <input type="hidden" name="is_under_cost" class="d-none" id="is_under_cost" value="<?=$is_under_cost?>">
                    <input type="hidden" name="is_fixedprice" class="d-none" id="is_fixedprice" value="<?=$is_fixedprice?>">
                    <input type="hidden" name="hasSerial" class="d-none" id="hasSerial" value="<?= ($shopObj->checkshoppermission(SHID:$shop_id, SPFID:11) == 1) ? 1 : 0 ?>">
                    <input type="hidden" name="invoices" id="invoices" value="retail-invoice.php">

                </div>
                <div class="pos-layout">

                    <!-- LEFT -->
                    <div class="invoice-panel">

                        <!-- Invoice Header -->
                        <div class="pos-card invoice-card">
                            <div class="invoice-grid">
                                <div class="invoice-number-card">
                                    <div class="invoice-icon">
                                        <i class="ti ti-receipt"></i>
                                    </div>
                                    <div style="width: calc(100% - 70px);">
                                        <span class="invoice-label">
                                            Invoice No
                                        </span>
                                        <h2 id="invoice-no"><?=$ws_no?></h2>
                                        <input type="hidden" name="HIID" id="HIID" value="">
                                        <label class="color-blue">Return No</label>
                                        <select class="form-select" id="return_no" name="return_no"> </select>
                                    </div>
                                </div>

                                <div class="invoice-field">
                                    <label class="color-blue">Salesman</label>
                                    <select class="form-select" id="search-salesmen" name="search-salesmen"></select>
                                    <span id="sales_man" class="mt-2 f12 color-grey"><small>Salesman -</small><b> <?=$salesmandata[0]["SalesmanNo"]." - ".$salesmandata[0]["SalesmansName"]?></b><br></span>
                                    <input type="hidden" name="salesmanid" id="salesmanid" value="1">
                                </div>
                                <div class="invoice-field">
                                    <div class="invoice-number-card">
                                        <a href="../View/modals/customer-modal.php?condition=new&ref=guipos" class="invoice-icon open-modal" id="add-customer">
                                            <i class="ti ti-user-circle"></i>
                                        </a>
                                        <div style="width: calc(100% - 70px);">
                                            <label class="color-blue">Customer</label>
                                            <select class="form-select" id="search-customers"></select>
                                            <span id="p_customer" class="mt-2 f12"><small>Customer -</small><b> <?=$customerdata[0]["CustomerNo"]." - ".$customerdata[0]["CustName"]?></b><br></span>
                                                    <input type="hidden" name="customerid" id="customerid" value="1">
                                                    <button id="use_exccese" class="btn btn-primary">Use Excess</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="invoice-field">
                                    <div class="barcode-card">
                                        <div class="invoice-icon">
                                            <i class="ti ti-barcode"></i>
                                        </div>
                                        <div style="width: calc(100% - 136px);">
                                            <label class="color-blue">Barcode / Scan Item</label>
                                            <input type="text" class="form-control barcode-search" id="barcode-search" placeholder="Scan barcode or enter code">    
                                        </div>
                                        <div class="side-btn"> 
                                            <a href="../View/modals/addproducts.php?condition=new&ref=guipos" class="btn-add-product d-flex justify-content-center align-items-center open-modal" ><i class="ti ti-plus"></i></a>    
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cart -->
                        <div class="pos-card cart-card">
                            <div class="card-invoice-details">
                                <table class="table pos-cart-table mb-0">
                                    <thead>
                                        <tr>
                                            <?php
                                            $itemw =30;
                                            $Quantityw =12;
                                            $Unitw =12;
                                            $Discountw =12;
                                            $Discountw =12;
                                            $Subtotalw =12;
                                            $Actionw =10;
                                            $Serialw =0;
                                            if($shopObj->checkshoppermission(SHID:$shop_id, SPFID:11)==1)
                                            {
                                                $Quantityw = $Quantityw - 2;
                                                $Unitw = $Unitw - 2;
                                                $Discountw = $Discountw - 2;
                                                $Discountw = $Discountw - 2;
                                                $Subtotalw = $Subtotalw - 2;
                                                $itemw = $itemw -5;
                                                $Serialw =15;

                                            }
                                            ?>
                                            <th width="<?=$itemw?>%">Item Name</th>
                                            <?php 
                                            if($shopObj->checkshoppermission(SHID:$shop_id, SPFID:11)==1)
                                            {
                                                ?>
                                                <th width="<?=$Serialw?>%">Serial No</th>
                                                <?php
                                            }
                                            ?>
                                            <th width="<?=$Quantityw?>%">Quantity</th>
                                            <th width="<?=$Unitw?>%">Unit Price</th>
                                            <th width="<?=$Discountw?>%">Discount Type</th>
                                            <th width="<?=$Discountw?>%">Discount</th>
                                            <th width="<?=$Subtotalw?>%">Subtotal</th>
                                            <th width="<?=$Actionw?>%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cart">
                                    </tbody>
                                </table>
                            </div>

                            <div class="cart-footer">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Send A Message To Customer
                                    </label>
                                </div>
                                <a href="#" class="tc">
                                    <i class="ti ti-file-text"></i>
                                    T&C
                                </a>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="pos-card footer-card">
                            <div class="footer-wrapper">
                                <!-- Discount Section -->
                                <div class="discount-section">
                                    <div class="discount-field">
                                        <label>
                                            Invoice Discount Type
                                        </label>
                                        <select name="invoice_discount_type" id="invoice_discount_type" class="form-select footer-select" onchange="grandTotal()">
                                            <option value="1">Percentage</option>
                                            <option value="2">Flat</option>
                                        </select>

                                    </div>

                                    <div class="discount-field">

                                        <label>
                                            Invoice Discount
                                        </label>
                                        <input type="text" name="invoice_discount" class="form-control footer-input" id="invoice_discount" onchange="grandTotal()" onkeyup="grandTotal()" onkeydown="grandTotal()" onkeypress="grandTotal()" placeholder="Invoice Discount" maxlength="3">
                                    </div>

                                </div>

                                <!-- Summary Section -->
                                <div class="summary-section">
                                    <div class="summary-card">
                                        <div class="summary-icon">
                                            <i class="ti ti-package"></i>
                                        </div>
                                        <div>
                                            <span>
                                                Quantity
                                            </span>

                                            <h5 id="qtyText">
                                                0.00
                                            </h5>
                                            <input type="hidden" name="totQty" id="totQty" class="form-control text-end" value="0.00" readonly>
                                        </div>

                                    </div>

                                    <div class="summary-card">

                                        <div class="summary-icon">
                                            <i class="ti ti-cash"></i>
                                        </div>

                                        <div>

                                            <span>
                                                Gross Amount
                                            </span>

                                            <h5 id="grossText">
                                                Rs. 0.00
                                            </h5>
                                            <input type="hidden" name="grossTotal" id="grossTotal" class="form-control text-end" value="0.00" readonly>
                                        </div>

                                    </div>

                                    <div class="summary-card">

                                        <div class="summary-icon">
                                            <i class="ti ti-discount"></i>
                                        </div>

                                        <div>

                                            <span>
                                                Total Discount
                                            </span>

                                            <h5 id="discountText">
                                                Rs. 0.00
                                            </h5>
                                            <input type="hidden" name="totalDiscount" id="totalDiscount" class="form-control text-end" value="0.00" readonly>
                                            <input type="hidden" name="totalDiscountLine" id="totalDiscountLine" class="form-control" value="0.00" readonly>
                                        </div>

                                    </div>

                                    <div class="summary-card">

                                        <div class="summary-icon">
                                            <i class="ti ti-refresh"></i>
                                        </div>

                                        <div>

                                            <span>
                                                Total Return
                                            </span>

                                            <h5 id="returnText">
                                                Rs. 0.00
                                            </h5>
                                            <input type="hidden" name="return_id" id="return_id" class="form-control" readonly>
                                            <input type="hidden" name="returnamount" id="returnamount" class="form-control" readonly>
                                        </div>

                                    </div>

                                    <div class="summary-card net-total-card">

                                        <div class="summary-icon">

                                            <i class="ti ti-wallet"></i>

                                        </div>

                                        <div>

                                            <span>
                                                Net Total
                                            </span>

                                            <h3 id="netTotalText">
                                                Rs. 0.00
                                            </h3>
                                            <input type="hidden" name="netamount" id="netamount" class="netamount">
                                        </div>

                                    </div>

                                </div>

                                <!-- Action Buttons -->

                                <div class="action-buttons"> 

                                    <button class="btn-pos btn-hold" id="invoiceHold">

                                        <i class="ti ti-player-pause"></i>

                                        HOLD

                                    </button>

                                    <button class="btn-pos btn-multiple" id="Multi-pay">

                                        <i class="ti ti-copy"></i>

                                        MULTIPLE

                                    </button>

                                    <button class="btn-pos btn-cash" id="pay_cash">

                                        <i class="ti ti-credit-card"></i>

                                        CASH

                                    </button>

                                    <button class="btn-pos btn-clear" id="clearCart">

                                        <i class="ti ti-trash"></i>

                                        CLEAR CART

                                    </button>

                                </div>

                            </div>
                        </div>

                    </div>

                    <!-- RIGHT -->
                    <div class="product-panel">
                        <div class="pos-card product-search-card">

                            <div class="product-filter-row">

                                <div class="filter-group">

                                    <select
                                        class="form-select product-select"
                                        id="main-cat">

                                    </select>

                                    <button
                                        class="btn-refresh-category" id="refresh-main-cat">

                                        <i class="ti ti-refresh"></i>

                                    </button>

                                </div>

                                <div class="filter-group">

                                    <select
                                        class="form-select product-select" id="subcat">

                                    </select>

                                    <button class="btn-refresh-category" id="refresh-sub-cat">

                                        <i class="ti ti-refresh"></i>

                                    </button>

                                </div>

                            </div>

                            <div class="search-product-row">

                                <div class="search-box">

                                    <i class="ti ti-search"></i>

                                    <input type="text" class="form-control preDefault" placeholder="Search Product" id="search-product">

                                </div>

                            </div>

                            <div class="product-result-count">

                                No of Results:
                                <span id="procount">0</span>

                            </div>
                            <div class="product-grid" id="product-list">


                            </div>
                        </div>

                        

                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
        
<div id="print"></div>
<!--  Body Wrapper End -->

    <!-- footer Start  -->
    <script src="../Assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../Assets/jquery/toast.js"></script>
    <script src="../Assets/izimodal/izimodal.min.js"></script>
    <!-- footer End  -->
    <script src="../Assets/jquery/guipos2.js"></script>
    
 
</body>
</html>