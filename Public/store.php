<?php 
include '../Includes/includes.php';
include '../Includes/authcheck.php';

$shop_id = $_SESSION['shop_id'];

?>
<!doctype html>
<html lang="en">
<head>
  <?php 
  include '../View/head.php';
  // include '../View/loader.php';
  ?>
</head>
<body>
<!--  Body Wrapper -->
<div class="h-100vh">
<div class="page-wrapper" id="main-wrapper">
    <!-- Sidebar Start -->
    <?php 
    include '../View/sidebar.php';
    if($userType==0)
    {
        $userObj=new User();
        $feature_id=1;
        $checkview=$userObj->userAcces($userRole_id,$feature_id);
        $view=$checkview[0]["is_view"];
        $edit=$checkview[0]["is_edit"];
        $view=$checkview[0]["is_view"];
        $delete=$checkview[0]["is_delete"];
        $verify=$checkview[0]["is_verify"];
        $print=$checkview[0]["is_print"];
        if($view==1)
        {

        }
        else
        {
            ?>
            <script>
                window.location.href = "./home.php";
            </script>
            <?php
        }
    }
    ?>
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
        <!--  Header Start -->
        <?php 
        include '../View/header.php';
        include '../View/modals/batch-price.php';
        ?>
        <!--  Header End -->

        <div class="container-fluid h-100">           
            <div class="card">
                <div class="card-body">

                <div class="container-fluid">
                    <!-- <div class="form-group">
                        <label for="searchInput" class="form-label">Search: </label>
                        <input type="text" id="searchInput" class="w-30 mt-10 form-control">
                    </div> -->

                <?php 
                $shop = $shopObj->getOneShop($shop_id);
                ?>
                <?php $today = date('d-m-Y');?>
                <input type="hidden" name="" id="today" value="<?= $today ?>">
                <input type="hidden" name="" id="print_access" value="<?= $print ?>">
                <input type="hidden" name="" id="userType" value="<?= $userType ?>">
                <input type="hidden" name="" id="shop_name" value="<?=$shop[0]['ShopName']?>">
                <input type="hidden" name="" id="shop_id" value="<?=$shop_id?>">
                <input type="hidden" name="" id="shop_address_one" value="<?=$shop[0]['AddressLineOne']?> ">
                <input type="hidden" name="" id="shop_address_two" value="<?=$shop[0]['AddressLineTwo']?>">
                <input type="hidden" name="" id="shop_city" value="<?=$shop[0]['City']?>">
                <input type="hidden" name="" id="shop_number" value="<?=$shop[0]['PhoneNumber']?> ">
                <input type="hidden" name="" id="title" value="Store Report">
                <div class="page-header">
                    <h1 class="page-title">Inventory </h1>
                    <div class="page-breadcrumb">
                        <a href="#">Orders</a>
                        <span class="separator">
                            <i class="ti ti-chevron-right"></i>
                        </span>
                        <span class="active">Store</span> <a href="javascript:void(0)" id="refresh"><i class="ti ti-reload"></i></a>
                    </div>
                </div>


                    <div class="table-responsive">
                      <table class="table table-hover" id="tbl_inventory">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Image</th>
                                <th>Barcode</th>
                                <th>Item Name</th>
                                <th>Current Qty</th>
                                <th>Sold Qty</th>
                                <th>Return Qty</th>
                                <th>Transfer In Qty</th>
                                <th>Transfer Out Qty</th>
                                <th>Price</th>
                            </tr>
                        </thead>                        
                        <tbody>
                        </tbody>
                        </table>  
                    </div>
                    
                </div>
                </div>
            </div>
            <!-- footer Start  -->
            <?php include '../View/footer.php';?> 
            <!-- footer End  -->
        </div>
    </div>
</div>
</div>
<!--  Body Wrapper End -->

    <script src="../Assets/jquery/store.js"></script>
</body>
</html>