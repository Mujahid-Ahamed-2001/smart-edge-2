<?php 
include '../Includes/includes.php';
include '../Includes/authcheck.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
    include '../View/head.php';
    include '../View/loader.php';
    ?>
    <style>
        .dis-none
        {
            display: none;
        }
        #mutlyEdit
        {
            z-index: 989;
            display: none;
        }
        .tbl_row
        {
            cursor: pointer;
        }
    </style>
    <link rel="stylesheet" href="../Assets/css/page.css">
</head>
<body>
    <div id="modal"></div>
    <div class="h-100">
        <div class="page-wrapper" id="main-wrapper">
            <!-- Sidebar Start -->
            <?php 
            include '../View/sidebar.php';
            $feature_id=16;
            include '../Includes/viewPermission.php';
            ?>
            <div class="body-wrapper">
                <!--  Header Start -->
                <?php 
                include '../View/header.php';
                ?>
                <?php
                // include "../View/modals/addproducts.php";
                include "../View/modals/editproducts.php";
                include "../View/modals/multieditproducts.php";
                include "../View/modals/product_barcode.php";
                
                ?>
                <!--  Header End -->
                <div class="container-fluid">
                    <section class="head-dashboard-head">

                        <div class="head-head-row">

                            <div class="head-heading">
                                <h1>Daily Business Summary <a href="javascript:void(0)" id="refresh"> <i class="ti ti-reload"></i> </a></h1> 

                                <div class="head-breadcrumb">
                                    <a href="javascript:void(0)">Home</a>
                                    <i class="ti ti-chevron-right"></i>

                                    <a href="javascript:void(0)">Daily Summary</a>
                                </div>
                            </div>
                            <div class="head-head-actions">

                            </div>
                        </div>
                    </section>
                    <?php $today = date('d-m-Y');?>
                    <?php $shop = $shopObj->getOneShop($shop_id);?>
                    <?php $query = isset($_GET['query']) && !empty($_GET['query']) ? $_GET['query'] : '';?>
                    <input type="hidden" name="app_status" value="<?= $app ?>" id="app_status">
                    <input type="hidden" name="query" value="<?= $query ?>" id="query">
                    <input type="hidden" name="" id="today" value="<?= $today ?>">
                    <input type="hidden" name="" id="print_access" value="<?= $print ?>">
                    <input type="hidden" name="" id="verify_access" value="<?= $verify ?>">
                    <input type="hidden" name="" id="edit_access" value="<?= $edit ?>">
                    <input type="hidden" name="" id="delete_access" value="<?= $delete ?>">
                    <input type="hidden" name="" id="userType" value="<?= $userType ?>">
                    <input type="hidden" name="" id="shop_name" value="<?=$shop[0]['ShopName']?>">
                    <input type="hidden" name="" id="shop_id" value="<?=$shop_id?>">
                    <input type="hidden" name="" id="shop_address_one" value="<?=$shop[0]['AddressLineOne']?> ">
                    <input type="hidden" name="" id="shop_address_two" value="<?=$shop[0]['AddressLineTwo']?>">
                    <input type="hidden" name="" id="shop_city" value="<?=$shop[0]['City']?>">
                    <input type="hidden" name="" id="shop_number" value="<?=$shop[0]['PhoneNumber']?> ">
                    <input type="hidden" name="" id="title" value="Subcategories">
                    <div class="row">
                        <div class="card col-md-12">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover products-table" id="tbl_products">
                                        <thead>
                                            <th>
                                                <input type="checkbox" name="allCheck" id="allCheck" class="form-checkbox allCheck">
                                            </th>
                                            <th width="400">
                                                Item Name
                                            </th>
                                            <th>
                                                Barcode
                                            </th>
                                            <th>
                                                Category
                                            </th>
                                            <th>
                                                Sub Category
                                            </th>                                            
                                            <th>
                                                Purchase Price
                                            </th>
                                            <th>
                                                Selling Price
                                            </th>
                                            <th>
                                                Dis(%) 
                                            </th>
                                            <th>
                                                Dis. 
                                            </th>
                                            <th>
                                                Type
                                            </th>
                                            <th>
                                                Status
                                            </th>
                                            <th>
                                                FP
                                            </th>
                                            <th>
                                                Is Low Stock
                                            </th>
                                            <th>
                                                Print Barcode
                                            </th>
                                            <th>
                                                Action
                                            </th>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <!-- footer Start  -->
    <?php include '../View/footer.php';?> 
    <script src="../Assets/jquery/daily-summary?v=1"></script>
    <!-- footer End  -->
</body>
</html>