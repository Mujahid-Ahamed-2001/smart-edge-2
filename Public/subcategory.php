<?php 
include '../Includes/includes.php';
include '../Includes/authcheck.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
    include '../View/head.php';
    ?>
</head>
<body>
    <div class="h-100vh">
        <div class="page-wrapper" id="main-wrapper">
            <!-- Sidebar Start -->
            <?php 
            include '../View/sidebar.php';
            $feature_id=15;
            include '../Includes/viewPermission.php';
            ?>
            <!--  Sidebar End -->
            <div class="body-wrapper">
                <!--  Header Start -->
                <?php 
                include '../View/header.php';
                include "../View/modals/sub-category.php";
                include "../View/modals/edit-sub-category.php";
                $shop = $shopObj->getOneShop($shop_id);
                ?>
                <!--  Header End -->
                <div class="container-fluid">
                    <?php $today = date('d-m-Y');?>
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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title fw-semibold p-2">
                                Subcategories <a href="javascript:void(0)" id="refresh"><i class="ti ti-reload"></i></a>
                                <?php 
                                if($userType==1 || $create==1)
                                {
                                    ?>
                                    <button class="btn btn-primary rounded-pill float-end" id="btn_add_subcategory">
                                        <small>
                                            Add Subcategory
                                        </small>
                                    </button>
                                    <?php
                                }
                                ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
                                <table class="table table-hover" id="table_subcat">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Subcategory No</th>
                                            <th>Subcategory Name</th>
                                            <th>Category Name</th>
                                            <th class="text-center">Total Products</th>
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
            </div>
        </div>
    </div>
    <!-- footer Start  -->
    <?php include '../View/footer.php';?> 
    <!-- footer End  -->
    <?php
    if(isset($_GET["app_status"]))
    {
        ?>
        <script src="../Assets/jquery/subcategory.js"></script>
        <?php
    }
    else
    {
        ?>
        <script src="../Assets/jquery/subcategory.min.js"></script>
        <?php
    }
    ?>

</body>
</html>