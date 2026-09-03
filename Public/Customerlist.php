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
  <link rel="stylesheet" href="../Assets/css/customerlist.css">
</head>

<body>
    <div id="modal"></div>
    <!--  Body Wrapper -->
    <div class="h-100vh">
        <div class="page-wrapper" id="main-wrapper">
            <!-- Sidebar Start -->
            <?php 
            include '../View/sidebar.php';
            $feature_id=18;
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
                        <h1 class="page-title">Customers </h1>
                        <div class="page-breadcrumb">
                            <a href="#">Settings</a>
                            <span class="separator">
                                <i class="ti ti-chevron-right"></i>
                            </span>
                            <span class="active">Customers</span>
                        </div>
                    </div>
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
                                                <h5 class="mb-0">Filter Customers</h5>
                                                <small>Search customers using advanced filters</small>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="customerFilter" class="accordion-collapse collapse show" aria-labelledby="headingFilter" data-bs-parent="#customerFilterAccordion">
                                    <div class="accordion-body">
                                        <form id="Customer-Filter">
                                            <div class="row g-4">
                                                <!-- Search -->
                                                <div class="col-lg-6">
                                                    <label class="form-label">
                                                        Search Customer
                                                    </label>
                                                    <div class="input-group modern-input">
                                                        <span class="input-group-text">
                                                            <i class="ti ti-search"></i>
                                                        </span>
                                                        <select class="form-select" id="search-customer">
                                                            <option>
                                                                Search by Customer...
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- Status -->
                                                <div class="col-lg-6">
                                                    <label class="form-label">
                                                        Status
                                                    </label>
                                                    <div class="input-group modern-input">
                                                        <span class="input-group-text">
                                                            <i class="ti ti-circle-check"></i>
                                                        </span>
                                                        <select class="form-select">
                                                            <option>All Customers</option>
                                                            <option>Active</option>
                                                            <option>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- Credit -->
                                                <div class="col-lg-6">
                                                    <label class="form-label">
                                                        Credit Amount
                                                    </label>
                                                    <div class="input-group modern-input">
                                                        <span class="input-group-text">
                                                            <i class="ti ti-coin"></i>
                                                        </span>
                                                        <input class="form-control" type="number" placeholder="0.00">
                                                        <select class="operator">
                                                            <option>=</option>
                                                            <option>></option>
                                                            <option><</option>
                                                            <option>>=</option>
                                                            <option><=</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- Has Credit -->
                                                <div class="col-lg-6 d-flex align-items-end">
                                                    <div class="form-check form-switch fs-6">
                                                        <input class="form-check-input" type="checkbox" id="has-credit">
                                                        <label class="form-check-label ms-2" for="has-credit">
                                                            Has Credit
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr class="my-4">
                                            <div class="d-flex justify-content-start gap-2">
                                                <button class="btn btn-primary px-4">
                                                    <i class="ti ti-search me-1"></i>
                                                    Filter
                                                </button>
                                                <button class="btn btn-outline-danger px-4" type="reset">
                                                    <i class="ti ti-trash me-1"></i>
                                                    Clear
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card col-md-12 customer-card shadow-sm border-0">
                            <div class="card-header customer-card-header">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h4 class="mb-1">
                                                Customers List <a href="javascript:void(0)" id="refresh"><i class="ti ti-reload"></i></a>
                                            </h4>
                                            <p class="mb-0">
                                                Manage and track your customers
                                            </p>
                                        </div>
                                    </div>
                                    <a href="../View/modals/customer-modal.php?condition=new&ref=customerlist" class="btn btn-primary add-btn open-modal" id="btn_open_customer">
                                        <i class="ti ti-plus"></i>
                                        Add New Customer
                                    </a>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table customer-table align-middle mb-0" id="tbl_customer">
                                        <thead>
                                            <tr>
                                                <th>Customer</th>
                                                <th>Address</th>
                                                <th>Contact</th>
                                                <th>Email</th>
                                                <th>Gender</th>
                                                <th>Date of Birth</th>
                                                <th>Credit</th>
                                                <th>Limit</th>
                                                <th>Status</th>
                                                <th>Created Date</th>
                                                <th width="120">Action</th>
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
    <!-- footer End  -->
    <script src="../Assets/jquery/customerlist.js"></script>

</body>
</html>