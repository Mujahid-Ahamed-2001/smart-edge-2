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
  <link rel="stylesheet" href="../Assets/css/AddExpenseTypes.css">
</head>
<body>
    <div id="modal"></div>
    <!--  Body Wrapper -->
    <div class="h-100vh">
        <div class="page-wrapper" id="main-wrapper">
            <!-- Sidebar Start -->
            <?php 
            include '../View/sidebar.php';
            $feature_id=11;
            include '../Includes/viewPermission.php';
            ?>
            <!--  Sidebar End -->
            <!--  Main wrapper -->
            <div class="body-wrapper">
                <!--  Header Start -->
                <?php 
                include '../View/header.php';
                ?>
                <?php 
                $shop = $shopObj->getOneShop($shop_id);
                ?>
                <?php $today = date('d-m-Y');?>
                <input type="hidden" name="" id="today" value="<?= $today ?>">
                <input type="hidden" name="" id="create_access" value="<?= $create ?>">
                <input type="hidden" name="" id="view_access" value="<?= $view ?>">
                <input type="hidden" name="" id="edit_access" value="<?= $edit ?>">
                <input type="hidden" name="" id="delete_access" value="<?= $delete ?>">
                <input type="hidden" name="" id="verify_access" value="<?= $verify ?>">
                <input type="hidden" name="" id="print_access" value="<?= $print ?>">
                <input type="hidden" name="" id="userType" value="<?= $userType ?>">
                <input type="hidden" name="" id="shop_name" value="<?=$shop[0]['ShopName']?>">
                <input type="hidden" name="" id="shop_id" value="<?=$shop_id?>">
                <input type="hidden" name="" id="shop_address_one" value="<?=$shop[0]['AddressLineOne']?> ">
                <input type="hidden" name="" id="shop_address_two" value="<?=$shop[0]['AddressLineTwo']?>">
                <input type="hidden" name="" id="shop_city" value="<?=$shop[0]['City']?>">
                <input type="hidden" name="" id="shop_number" value="<?=$shop[0]['PhoneNumber']?> ">
                <input type="hidden" name="" id="title" value="Expense Type Report">
                <!--  Header End -->

                <div class="container-fluid py-4">                       
                    <div class="page-breadcrumb d-flex align-items-center mb-4">
                        <a href="#" class="breadcrumb-link">Expenses</a>
                        <span class="mx-2">
                            <i class="ti ti-chevron-right"></i>
                        </span>
                        <span class="breadcrumb-active">Add Expense Categories</span>
                    </div>
                    <div class="expense-card">

                        <!-- Header -->
                        <div class="expense-card-header">

                            <div class="expense-title">

                                <div class="expense-icon">
                                    <i class="ti ti-receipt-2"></i>
                                </div>

                                <div>
                                    <h3>Expense Categories <a href="javascript:void(0)" id="refresh"><i class="ti ti-reload"></i></a></h3>
                                    <p>Create and manage different categories of expenses</p>
                                </div>

                            </div>
                            <?php 
                            if($create==1)
                            {
                                ?>
                                <a class="btn btn-add-expense open-modal" href="../View/modals/expense-categories.php?condition=new&ref=AddExpenseCategories" data-title="Add Expense Category">
                                    <i class="ti ti-plus"></i>
                                    Add Category
                                </a>    
                                <?php
                            }
                            ?>
                            

                        </div>

                        <!-- Divider -->
                        <div class="card-divider"></div>

                        <!-- Table -->
                        <div class="table-responsive">

                            <table class="table expense-table align-middle" id="tbl_expense_cat">
                                <thead>
                                    <tr>
                                        <th width="80">ID</th>
                                        <th width="450">Expense Category</th>
                                        <th width="350">Expense Type</th>
                                        <th width="150">Status</th>
                                        <th width="150">Default</th>
                                        <th width="150">Created By</th>
                                        <th width="150">Created At</th>
                                        <th width="150">Modified By</th>
                                        <th width="150">Modified At</th>
                                        <th width="250">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>

                            </table>

                        </div>
                    </div>

                    <!-- footer Start  -->
                    <?php include '../View/footer.php';?> 
                    <script src="../Assets/jquery/AddExpenseCtg2.js"></script>
                    <!-- footer End  -->
                </div>
            </div>
        </div>
    </div>
    <!--  Body Wrapper End -->

</body>
</html>