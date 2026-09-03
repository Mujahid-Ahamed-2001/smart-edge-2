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
    include '../View/loader.php';
  ?>
  <link rel="stylesheet" href="../Assets/css/grn.css">
</head>

<body>
<!--  Body Wrapper -->
<div class="h-100vh">
<div class="page-wrapper" id="main-wrapper">
    <div id="modal">

    </div>
    <!-- Sidebar Start -->
    <?php 
    include '../View/sidebar.php';
    $feature_id=2;
    include '../Includes/viewPermission.php';
    ?>
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
        <!--  Header Start -->
        <?php 
        include '../View/header.php';       
        
        include "../View/modals/main-category.php";
        ?>
        <?php include '../View/modals/grn.php';?>
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
                <input type="hidden" name="" id="title" value="GRN Header Report">
            <!-- messages -->
            <div class="row">
                <div class="accordion filter-accordion mb-4" id="customerFilterAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFilter">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#customerFilter" aria-expanded="true">
                                <div class="filter-heading">
                                    <div class="filter-icon">
                                        <i class="ti ti-filter"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">Filter GRNs</h5>
                                        <small>Search GRNs using advanced filters</small>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="customerFilter" class="accordion-collapse collapse show" aria-labelledby="headingFilter" data-bs-parent="#customerFilterAccordion">
                            <div class="accordion-body">
                                <form id="GRN-Filter" action="#" method="post">
                                    <div class="row g-4">
                                        <!-- Search -->
                                        <div class="col-lg-6">
                                            <label for="start" class="form-label">From Date <span class="text-danger">*</span></label>
                                            <div class="input-group modern-input">
                                                <span class="input-group-text">
                                                    <i class="ti ti-calendar"></i>
                                                </span>
                                                <input type="date" name="start" id="start" class="form-control" >
                                            </div>
                                        </div>
                                        <!-- Status -->
                                        <div class="col-lg-6">
                                            <label for="end" class="form-label">To Date <span class="text-danger">*</span></label>
                                            <div class="input-group modern-input">
                                                <span class="input-group-text">
                                                    <i class="ti ti-calendar"></i>
                                                </span>
                                                <input type="date" name="end" id="end" class="form-control" >
                                            </div>
                                        </div>
                                        <!-- Credit -->
                                        <div class="col-lg-6">
                                            <label for="search-suppliers" class="form-label">Search Supplier</label>
                                            <div class="input-group modern-input">
                                                <span class="input-group-text">
                                                    <i class="ti ti-user"></i>
                                                </span>
                                                <select name="suppliers" id="search-suppliers" class="form-select">
                                                    <option value="">Search By Supplier</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <label for="select-shop" class="form-label">Select Shop <span class="text-danger">*</span></label>
                                            <div class="input-group modern-input">
                                                <span class="input-group-text">
                                                    <i class="ti ti-home"></i>
                                                </span>
                                                <select name="shop_id" id="select-shop" class="form-select" required>
                                                    <?php 
                                                    $shopCount= count($shops);
                                                    foreach($shops AS $row)
                                                    {
                                                        ?>
                                                        <option value="<?=$row["SHID"]?>" <?php 
                                                        if($row["SHID"]==$shop_id)
                                                        {
                                                            echo "selected";
                                                        }
                                                        ?>><?=$row["ShopName"]?></option>
                                                        <?php
                                                    }
                                                    if($shopCount > 1)
                                                    {
                                                        ?>
                                                        <option value="all">All Shops</option>
                                                        <?php
                                                    }
                                                    ?>                                                    
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-4">
                                    <div class="d-flex justify-content-start gap-2">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="ti ti-search me-1"></i>
                                            Filter
                                        </button>
                                        <button class="btn btn-outline-danger px-4" type="reset" id="clearFilter">
                                            <i class="ti ti-trash me-1"></i>
                                            Clear
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="container-fluid py-4 px-3">
                        <div class="row align-items-start justify-content-between g-3">                            
                            <!-- Left content -->
                            <div class="col-md">
                                <h2 class="fw-semibold mb-2 text-dark">GRN</h2>
                                <p class="mb-0 fs-5 text-grey">Manage and track all GRN</p>
                            </div>
                            <!-- Right cards -->
                            <div class="col-md-auto">
                                <div class="d-flex flex-wrap gap-3 justify-content-md-end">                                    
                                    <!-- Card 1 -->
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body px-4 py-3">
                                            <div class="d-flex align-items-start gap-2 mb-2">
                                                <span class="text-primary fs-5"><i class="ti ti-package"></i></span>
                                                <span class="text-grey fw-medium">Total Orders</span>
                                            </div>
                                            <h2 class="mb-0 fw-semibold text-dark" id="grnTotalOrder">6</h2>
                                        </div>
                                    </div>
                                    <!-- Card 2 -->
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body px-4 py-3">
                                            <div class="d-flex align-items-start gap-2 mb-2">
                                                <span class="text-warning fs-5"><i class="ti ti-calendar-time"></i></span>
                                                <span class="text-grey fw-medium">Pending</span>
                                            </div>
                                            <h2 class="mb-0 fw-semibold text-dark" id="grnPending">2</h2>
                                        </div>
                                    </div>
                                    <!-- Card 3 -->
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body px-4 py-3">
                                            <div class="d-flex align-items-start gap-2 mb-2">
                                                <span class="text-success fs-5"><i class="ti ti-cash-banknote"></i></span>
                                                <span class="text-grey fw-medium">Total Value</span>
                                            </div>
                                            <h2 class="mb-0 fw-semibold text-dark" id="grnTotalValue">$109,071.5</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card col-md-12">
                    <div class="card-header">
                        <h5 class="card-title fw-semibold mb-2" style="margin-top: 0px;">
                            Goods Receive Note List <a href="javascript:void(0)" id="refresh"><i class="ti ti-reload"></i></a>
                            <?php 
                            if($userType!=1)
                            {
                                if($create==1)
                                {
                                    ?>
                                    <a class="btn btn-primary float-end open-modal" href="../View/modals/add-new-grn.php?condition=new&ref=grn-header">Add New GRN</a>
                                    <!-- <a href="../Public/grnBulkUpload.php" class="btn btn-primary float-end"><i class="ti ti-file"></i>Add File</a> -->
                                    <?php
                                }
                            }
                            else
                            {
                                ?>
                                    <a class="btn btn-primary float-end open-modal" href="../View/modals/add-new-grn.php?condition=new&ref=grn-header">Add New GRN</a>
                                <!-- <a href="../Public/grnBulkUpload.php" class="btn btn-primary float-end"><i class="ti ti-file"></i>Add File</a> -->
                                <?php
                            }
                            ?>
                            
                        </h5>
                    </div>
                    <div class="card-body">                    
                        <div class="container-fluid table-responsive">
                            <table class="table table-hover" id="tbl_grn_header">
                                <thead>
                                <tr>
                                    <!-- <td style="display: none;">0</td> -->
                                    <th>No</th>
                                    <th>GRN No</th>
                                    <th>Date</th>
                                    <th>Invoice No</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-end">Purchase Total</th>
                                    <th class="text-end">Selling Total</th>
                                    <th class="text-center">Reference</th>
                                    <th class="text-center">Shop</th>
                                    <th class="text-center">Supplier</th>
                                    <th>Added By</th>
                                    <th>Status</th>
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
</div>
<!--  Body Wrapper End -->

    <!-- footer Start  -->
    <?php include '../View/footer.php';?> 
    <script src="../Assets/jquery/grn.js"></script>

</body>
</html>