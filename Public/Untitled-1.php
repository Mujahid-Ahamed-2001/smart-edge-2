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
    $feature_id=12;
    include '../Includes/viewPermission.php';
    ?>
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
        <!--  Header Start -->
        <?php 
        include '../View/header.php';
        ?>
        <!--  Header End -->

        <div class="container-fluid h-100">                       
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title fw-semibold mb-2 color_white" style="margin-top: 0px;">Expense Type <a href="javascript:void(0)" id="refresh"><i class="ti ti-reload"></i></a></h5>     
                </div>
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


                        <div class="table-responsive">
                        <table class="table table-hover" id="tbl_expense_type">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Expense Type</th>
                                    <th>Default</th>
                                    <th>Action</th>
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

</body>
</html>