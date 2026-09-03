<?php 
include '../Includes/includes.php';
include '../Includes/authcheck.php';
$shop_id = $_SESSION['shop_id'];
$user_id=$_SESSION['user_id'];
$slrtObj=new Sales_return_class();
$userObj = new User();
$user = $userObj->getOneUser($user_id);
$userType=$user[0]["UserType"];
$shops=$slrtObj->selectShop($userType,$user_id);

?>

<!doctype html>
<html lang="en">

<head>
  <?php 
    include '../View/head.php';
    // include '../View/loader.php';
  ?>
  <link rel="stylesheet" href="../Assets/css/batchwise.css">
</head>

<body>
    <div id="modal"></div>
    <!--  Body Wrapper -->
    <div class="h-100vh">
        <div class="page-wrapper" id="main-wrapper">
            <!-- Sidebar Start -->
            <?php 
            include '../View/sidebar.php';
            $feature_id=1;
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

                <div class="container-fluid ">
                    <?php 
                        $shop = $shopObj->getOneShop($shop_id);
                        ?>
                        <?php $today = date('d-m-Y');?>
                        <input type="hidden" name="app_status" value="<?= $app ?>" id="app_status">
                        <input type="hidden" name="" id="today" value="<?= $today ?>">
                        <input type="hidden" name="" id="print_access" value="<?= $print ?>">
                        <input type="hidden" name="" id="delete_access" value="<?= $delete ?>">
                        <input type="hidden" name="" id="verify_access" value="<?= $verify ?>">
                        <input type="hidden" name="" id="edit_access" value="<?= $edit ?>">
                        <input type="hidden" name="" id="userType" value="<?= $userType ?>">
                        <input type="hidden" name="" id="shop_name" value="<?=$shop[0]['ShopName']?>">
                        <input type="hidden" name="" id="shop_id" value="<?=$shop_id?>">
                        <input type="hidden" name="" id="shop_address_one" value="<?=$shop[0]['AddressLineOne']?> ">
                        <input type="hidden" name="" id="shop_address_two" value="<?=$shop[0]['AddressLineTwo']?>">
                        <input type="hidden" name="" id="shop_city" value="<?=$shop[0]['City']?>">
                        <input type="hidden" name="" id="shop_number" value="<?=$shop[0]['PhoneNumber']?> ">
                        <input type="hidden" name="" id="title" value="Customer Report">
                    <!-- messages -->
                    <div class="page-header">
                        <h1 class="page-title">Batchwise Report </h1>
                        <div class="page-breadcrumb">
                            <a href="#">Reports</a>
                            <span class="separator">
                                <i class="ti ti-chevron-right"></i>
                            </span>
                            <span class="active">Batchwise Report</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="card col-md-12 customer-card shadow-sm border-0">
                            <div class="card-header customer-card-header">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h4 class="mb-1">
                                                Batchwise Report <a href="javascript:void(0)" id="refresh"><i class="ti ti-reload"></i></a>
                                            </h4>
                                            <p class="mb-0">
                                                Manage your Inventroy Batchwise
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table batchwise-table align-middle mb-0" id="tbl_batchwise">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Barcode</th>
                                                <th width="350">Item Name</th>
                                                <th>Product Code</th>
                                                <th>Batch No</th>
                                                <th>Qty</th>
                                                <th>Unit Selling Price</th>
                                                <th>Unit Purchase Price</th>
                                                <th>Total Selling Price</th>
                                                <th>Total Purchase Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="colspan"></td>
                                                <td><b>Total Selling:</b></td>
                                                <td id="totalSelling"></td>
                                            </tr>
                                            <tr>
                                                <td class="colspan"></td>
                                                <td><b>Total Purchase:</b></td>
                                                <td id="totalPurchase"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>            
                </div>
            </div>
        </div>
    </div>
    <!--  Body Wrapper End -->

    <!-- footer Start  -->
    <?php include '../View/footer.php';?> 
    <!-- footer End  -->
    <script src="../Assets/jquery/batchwise.js"></script>

</body>
</html>