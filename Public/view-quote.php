<?php 
include '../Includes/includes.php';
include '../Includes/authcheck.php';
$shop_id = $_SESSION['shop_id'];
$user_id=$_SESSION['user_id'];
$slrtObj=new Sales_return_class();
$userObj = new User();
$quoteObj = new quote_class();
$user = $userObj->getOneUser($user_id);
$userType=$user[0]["UserType"];
$quote_status=$quoteObj->getQuote_status();
$quote_users=$quoteObj->getusers();

?>

<!doctype html>
<html lang="en">

<head>
  <?php 
    include '../View/head.php';
    include '../View/loader.php';
  ?>
  <style>
    .select2-dropdown {
        z-index: 999999 !important;
    }
    .select2-results__options {
        max-height: 200px !important;
        overflow-y: auto !important;
    }
    .form-select
    {
        transition: all 0.3s ease;
    }
  </style>
</head>

<body>
<!--  Body Wrapper -->
<div class="h-100vh">
<div class="page-wrapper" id="main-wrapper">
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
                <input type="hidden" name="" id="title" value="Quotation Report">
            <!-- messages -->
            <div class="row">
                <div class="accordion col-md-12 mb-3" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header " id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" style="color: #000;">
                                <strong>Filteration</strong>
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <form id="quote-Filter" action="#" method="post">
                                    <div class="row">
                                        <div class="col-md-6 mt-2">
                                            <label for="start" class="form-label">From Date <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <a href="javascript:void(0)" class="input-group-text">
                                                    <i class="ti ti-calendar"></i>
                                                </a>
                                                <input type="date" name="start" id="start" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label for="end" class="form-label">To Date <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <a href="javascript:void(0)" class="input-group-text">
                                                    <i class="ti ti-calendar"></i>
                                                </a>
                                                <input type="date" name="end" id="end" class="form-control" >
                                            </div>                                    
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label for="search-customer" class="form-label">Search Customers</label>
                                            <div class="input-group">
                                                <a href="javascript:void(0)" class="input-group-text">
                                                    <i class="ti ti-user"></i>
                                                </a>
                                                <select name="customer" id="search-customer" class="form-select">
                                                    <option value="">Search By Customers</option>
                                                </select>
                                            </div> 
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label for="select-stat" class="form-label">Select Status </label>
                                            <div class="input-group">
                                                <a href="javascript:void(0)" class="input-group-text">
                                                    <i class="ti ti-square-check"></i>
                                                </a>
                                                <select name="stat" id="select-stat" class="form-select" >
                                                    <option value="">Select Status</option>
                                                    <?php 
                                                    foreach($quote_status AS $row)
                                                    {
                                                        $color = $row["color"] != "" ? $row["color"] : "#000";
                                                        $bg_color = $row["bg_color"] != "" ? $row["bg_color"] : "#fff";
                                                        ?>
                                                        <option data-color="<?=$color?>" data-bg_color="<?=$bg_color?>" value="<?=$row["QSID"]?>" style="color: <?=$color?> !important; background-color: <?=$bg_color?> !important;">
                                                            <?=$row["stat_name"]?>
                                                        </option>
                                                        <?php
                                                    }
                                                    ?>
                                                    
                                                </select>
                                            </div> 
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label for="selling_price1" class="form-label">Total Selling Price <span class="text-danger dis-none selling_priceLabel">*</span></label>
                                            <div class="input-group">
                                                <a href="javascript:void(0)" class="input-group-text">
                                                    <i class="ti ti-coin"></i>
                                                </a>
                                                <input type="number" placeholder="0.00" step="any" name="selling_price1" id="selling_price1" class="form-control">
                                                <a href="javascript:void(0)" class="input-group-text">
                                                    <select name="selling_operator" id="selling_operator" class="border-0 bg-transparent">
                                                        <option value="=">=</option>
                                                        <option value=">">></option>
                                                        <option value="<"><</option>
                                                        <option value=">=">>=</option>
                                                        <option value="<="><=</option>
                                                    </select>
                                                </a>
                                            </div> 
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label for="select-user" class="form-label">Created By</label>
                                            <div class="input-group">
                                                <a href="javascript:void(0)" class="input-group-text">
                                                    <i class="ti ti-user"></i>
                                                </a>
                                                <select name="user" id="select-user" class="form-select" >
                                                    <option value="">Select User</option>
                                                    <?php 
                                                    foreach($quote_users AS $row)
                                                    {
                                                        ?>
                                                        <option value="<?=$row["USID"]?>">
                                                            <?=$row["UserName"]?>
                                                        </option>
                                                        <?php
                                                    }
                                                    ?>
                                                    
                                                </select>
                                            </div> 
                                        </div>
                                        <div class="col-md-6 mt-2 d-flex align-items-center" style="min-height: 100px;">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" name="completion" type="checkbox" role="switch" id="completion">
                                                <label class="form-check-label form-label" for="completion">Completed</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-3 d-flex justify-content-center">
                                                <button type="submit" class="btn btn-primary me-2" id="filter">Filter</button>
                                                <a href="javascript:void(0)" class="btn btn-danger" id="clearFilter">Clear Filter</a>
                                        </div>
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
                                <h2 class="fw-semibold mb-2 text-dark">Quotation</h2>
                                <p class="mb-0 fs-5 text-grey">Manage and track all Quotations</p>
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
                                            <h2 class="mb-0 fw-semibold text-dark" id="quoteTotalOrder">0</h2>
                                        </div>
                                    </div>
                                    <!-- Card 2 -->
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body px-4 py-3">
                                            <div class="d-flex align-items-start gap-2 mb-2">
                                                <span class="text-warning fs-5"><i class="ti ti-calendar-time"></i></span>
                                                <span class="text-grey fw-medium">Pending</span>
                                            </div>
                                            <h2 class="mb-0 fw-semibold text-dark" id="quotePending">0</h2>
                                        </div>
                                    </div>
                                    <!-- Card 3 -->
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body px-4 py-3">
                                            <div class="d-flex align-items-start gap-2 mb-2">
                                                <span class="text-success fs-5"><i class="ti ti-cash-banknote"></i></span>
                                                <span class="text-grey fw-medium">Total Value</span>
                                            </div>
                                            <h2 class="mb-0 fw-semibold text-dark" id="quoteTotalValue">Rs. 0.00</h2>
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
                           Quotaion List <a href="javascript:void(0)" id="refresh"><i class="ti ti-reload"></i></a>
                            
                        </h5>
                    </div>
                    <div class="card-body">                    
                        <div class="container-fluid table-responsive">
                            <table id="view_quote_table" class="table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Quotation No</th>
                                        <th>Customer Name</th>
                                        <th>Customer Mobile</th>
                                        <th>Customer Address</th>
                                        <th>Status</th>
                                        <th>No of Options</th>
                                        <th>No of Items</th>
                                        <th>Total Amount</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="view_quote_table_body">
                                    
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
    <!-- footer End  -->
    <?php
    // if(isset($_GET["app_status"]))
    // {
        ?>
        <!-- <script src="../Assets/jquery/view-quote.js"></script> -->
        <?php
    // }
    // else
    // {
        ?>
        <!-- <script src="../Assets/jquery/view-quote.min.js"></script> -->
        <?php
    // }
    ?>
    <script src="../Assets/jquery/view-quote.js"></script>

</body>
</html>