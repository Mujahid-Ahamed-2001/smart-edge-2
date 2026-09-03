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
            <div class="body-wrapper">
                <?php 
                    include '../View/gui-header2.php';
                    $shop = $shopObj->getOneShop($shop_id);
                    $today = date('d-m-Y');
                ?>
                <div class="container-fluid h-100">
                    <!-- <h5 class="card-title fw-semibold mb-4 color_white" style="margin-top: 0px;">Create Quotation </h5>   -->
                    <input type="hidden" name="" id="today" value="<?= $today ?>">
                    <input type="hidden" name="" id="shop_name" value="<?=$shop[0]['ShopName']?>">
                    <input type="hidden" name="" id="shop_id" value="<?=$shop_id?>">
                    <input type="hidden" name="" id="shop_address_one" value="<?=$shop[0]['AddressLineOne']?> ">
                    <input type="hidden" name="" id="shop_address_two" value="<?=$shop[0]['AddressLineTwo']?>">
                    <input type="hidden" name="" id="shop_city" value="<?=$shop[0]['City']?>">
                    <input type="hidden" name="" id="shop_number" value="<?=$shop[0]['PhoneNumber']?> ">
                    <input type="hidden" name="" id="title" value="Quotation Report">
                    <div class="page-header">
                        <h1 class="page-title">Quotation </h1>
                        <div class="page-breadcrumb">
                            <a href="#">Orders</a>
                            <span class="separator">
                                <i class="ti ti-chevron-right"></i>
                            </span>
                            <span class="active">Quotation</span>
                        </div>
                    </div>
                    
                    <div class="p-2">
                        <div>
                            <form id="create-quote-form" action="../Controller/quotation.php?action=create" method="post">
                                <div class="quotation-header">
                                    <!-- LEFT SIDE : CUSTOMER -->
                                    <div class="quotation-customer-section">
                                        <label for="cmb_customer" class="quotation-label">
                                            Customer Name / Phone No
                                        </label>
                                        <?php 
                                        $shopObj = new Shop();

                                        if($shopObj->hasCustomers($shop_id))
                                        {
                                        ?>

                                            <div class="customer-search-wrapper">

                                                <div class="customer-select-wrapper">
                                                    <select 
                                                        name="cmb_customer" 
                                                        id="cmb_customer" 
                                                        class="form-select">
                                                    </select>
                                                </div>

                                                <a 
                                                    href="../View/modals/customer-modal.php?condition=new&ref=create-quote2"
                                                    class="customer-add-button open-modal"
                                                    title="Add Customer">
                                                    <i class="ti ti-user-plus"></i>
                                                </a>

                                            </div>

                                            <div id="p_customer" class="customer-profile-card">
                                                <div class="customer-avatar" id="customer-avatar">
                                                    CC
                                                </div>
                                                <div class="customer-profile-info">
                                                    <div class="customer-profile-name">
                                                        <span id="customer_text">
                                                            <?=$customer_text?>
                                                        </span>
                                                    </div>
                                                    <div class="customer-profile-meta">
                                                        Customer
                                                    </div>
                                                </div>
                                                <a href="javascript:void(0);" class="customer-profile-link open-modal" id="customer-profile-link">
                                                    View Profile
                                                    <i class="ti ti-arrow-right"></i>
                                                </a>
                                                <a href="javascript:void(0);" class="customer-profile-link open-modal" id="customer-edit-link">
                                                    Edit Profile
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                            </div>
                                            <input type="hidden" name="predefinded_cus_id" id="predefinded_cus_id" class="form-control" value="<?=$Cus_id?>">

                                            <input type="hidden" name="predefinded_customer_text" id="predefinded_customer_text" class="form-control" value="<?=$customer_text?>">

                                        <?php
                                        }
                                        else
                                        {
                                        ?>

                                            <div id="p_customer" class="customer-profile-card">

                                                <div class="customer-avatar">
                                                    CC
                                                </div>

                                                <div class="customer-profile-info">

                                                    <div class="customer-profile-name">
                                                        <span id="customer_text">
                                                            <?=$customer_text?>
                                                        </span>
                                                    </div>

                                                    <div class="customer-profile-meta">
                                                        Customer
                                                    </div>

                                                </div>

                                            </div>

                                            <input type="hidden" name="cmb_customer" id="cmb_customer" class="form-control" value="<?=$Cus_id?>">

                                        <?php
                                        }
                                        ?>

                                    </div>


                                    <!-- RIGHT SIDE : QUOTATION DETAILS -->
                                    <div class="quotation-details-section">

                                        <div class="quotation-number-block">

                                            <div class="quotation-small-label">
                                                Quotation No.
                                            </div>

                                            <div class="quotation-number">
                                                <span id="quoteNo">Q09042026-001</span>
                                            </div>

                                        </div>


                                        <div class="quotation-date-block">

                                            <div class="quotation-small-label">
                                                Date
                                            </div>

                                            <div class="quotation-date-input">

                                                <span>
                                                    <?=date('d M Y')?>
                                                </span>

                                                <i class="ti ti-calendar"></i>

                                            </div>

                                        </div>


                                        <?php 
                                        if($shopObj->hasInventory($shop_id))
                                        {
                                        ?>

                                            <a href="../View/modals/addproducts.php?condition=new&ref=guipos"
                                                class="quotation-add-item open-modal">

                                                <i class="ti ti-plus"></i>
                                                <span>Add Item</span>

                                            </a>

                                        <?php
                                        }
                                        ?>

                                    </div>

                                </div>


                                <!-- OPTIONS / ITEMS -->
                                <div class="quotation-options-wrapper">

                                    <div id="option-wrapper">
                                    </div>

                                </div>
                                <div class="card">
                                    <div class="row">
                                        <div class=" p-3 col-md-12 d-flex justify-content-end align-items-center">
                                            <button type="submit" class="btn btn-primary me-2" id="save-quote-btn-print"><i class="ti ti-printer"></i> Save & Print</button>
                                            <button type="submit" class="btn btn-primary me-2" id="save-quote-btn"><i class="ti ti-device-floppy"></i> Save Quotation</button>
                                        </div>  
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
    <script src="../Assets/jquery/create-quote2.js"></script>
</body>
</html>