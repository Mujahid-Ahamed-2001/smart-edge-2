<?php 
include '../Includes/includes.php';
include '../Includes/authcheck.php';

$shop_id = $_SESSION['shop_id'];
$custObj= new Customer;
$Cus_id=1;
$customer=$custObj->getOneCustomer($Cus_id);
$customer_text="";
if(!empty($customer[0]['CustomerNo']))
{
    $customer_text.=$customer[0]['CustomerNo'];
}
if(!empty($customer[0]['CustName']))
{
    $customer_text.=" - ".$customer[0]['CustName'];
}
if(!empty($customer[0]['CustContact']))
{
    $customer_text.=" - ".$customer[0]['CustContact'];
}
?> 
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  include '../View/head.php';
  include '../View/loader.php';

  ?>
  <link rel="stylesheet" href="../Assets/css/create-quote.css">
</head>
<body>
    <div id="modal"></div>
    <div class="h-100vh">
        <div class="page-wrapper" id="main-wrapper">
            <?php    
                include '../View/modals/add-customer-quote.php';
            ?>
            <div class="body-wrapper">
                <?php 
                    include '../View/gui-header2.php';
                    $shop = $shopObj->getOneShop($shop_id);
                    $today = date('d-m-Y');
                ?>
                <div class="container-fluid h-100">
                    <h5 class="card-title fw-semibold mb-4 color_white" style="margin-top: 0px;">Create Quotation </h5>  
                    <input type="hidden" name="" id="today" value="<?= $today ?>">
                    <input type="hidden" name="" id="print_access" value="<?= $print ?>">
                    <input type="hidden" name="" id="userType" value="<?= $userType ?>">
                    <input type="hidden" name="" id="shop_name" value="<?=$shop[0]['ShopName']?>">
                    <input type="hidden" name="" id="shop_id" value="<?=$shop_id?>">
                    <input type="hidden" name="" id="shop_address_one" value="<?=$shop[0]['AddressLineOne']?> ">
                    <input type="hidden" name="" id="shop_address_two" value="<?=$shop[0]['AddressLineTwo']?>">
                    <input type="hidden" name="" id="shop_city" value="<?=$shop[0]['City']?>">
                    <input type="hidden" name="" id="shop_number" value="<?=$shop[0]['PhoneNumber']?> ">
                    <input type="hidden" name="" id="title" value="Quotation Report">
                    
                    <div class="card p-2">
                        <div class="card-body">
                            <form id="create-quote-form" action="../Controller/quotation.php?action=create" method="post">
                                <div class="row">                                
                                    <div class="col-md-3">
                                            <?php 
                                                
                                                $shopObj = new Shop();
                                                if($shopObj->hasCustomers($shop_id))
                                                {
                                                    ?>
                                                    <label for="cmb_customer" class="form-label">Customer Name/Phone No</label>
                                                    <div class="input-group">
                                                        <select name="cmb_customer" id="cmb_customer" class="form-select"></select>
                                                        <a href="../View/modals/customer-modal.php?condition=new&ref=create-quote" class="input-group-text open-modal"><i class="ti ti-user-plus" ></i></a>
                                                    </div>
                                                    <span id="p_customer" class="mt-2"><br><small>Customer -</small><b id="customer_text"> <?=$customer_text?></b><br></span>
                                                    <input type="hidden" name="predefinded_cus_id" id="predefinded_cus_id" class="form-control" value="<?=$Cus_id?>">
                                                    <input type="hidden" name="predefinded_customer_text" id="predefinded_customer_text" class="form-control" value="<?=$customer_text?>">
                                                    <?php
                                                }
                                                else
                                                {
                                                    ?>
                                                    <span id="p_customer" class="mt-2"><br><small>Customer -</small><b id="customer_text"> <?=$customer_text?></b><br></span>
                                                    <input type="hidden" name="cmb_customer" id="cmb_customer" class="form-control" value="<?=$Cus_id?>">
                                                    <?php
                                                }
                                            ?>
                                    </div>
                                    <div class="col-md-9 text-end">
                                        <?php 
                                            if($shopObj->hasInventory($shop_id))
                                            {
                                                ?>
                                                <!-- <a href="javascript:void(0)" class="btn btn-primary mt-3" id="moveToSecondScreen">Move To Second Screen</a> -->
                                                <a href="../View/modals/addproducts.php?condition=new&ref=guipos" class="btn btn-primary mt-3 open-modal">Add Item</a>
                                                <?php 
                                            }
                                        ?>
                                        <h5 class="text-end mt-2"><b>Quotation No: <span id="quoteNo">Q09042026-001</span></b></h5>
                                    </div>
                                    <div class="col-md-12 mt-5">
                                        <div id="option-wrapper">
                                            
                                        </div>                                    
                                    </div>
                                    <div class="col-md-12 p-3 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary me-2" id="save-quote-btn-print">Save Quotation & Print Quotations</button>
                                        <button type="submit" class="btn btn-primary me-2" id="save-quote-btn">Save Quotation</button>
                                    </div>                                
                                </div>
                            </form>
                        </div>
                    </div>
                    
                </div>
                <!-- footer Start  -->
                <?php include '../View/footer.php';?> 
                <!-- footer End  -->
            </div>
        </div>
    </div>
    <script src="../Assets/jquery/create-quote.js"></script>
</body>
</html>