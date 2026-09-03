<?php 
include '../Includes/includes.php';
include '../Includes/authcheck.php';
if(!empty($_GET["grn_header"]) && $_GET["grn_header"]!=0)
{
    $grn_header = $_GET["grn_header"];
}
else
{
    header("location:../Public/grn-header.php");
}
$user_id=$_SESSION['user_id'];
$userObj = new User();
$user = $userObj->getOneUser($user_id);
$userType=$user[0]["UserType"];
$GRNtObj=new GRN();
$shops=$GRNtObj->selectShop($userType,$user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
    include '../View/head.php';    
    // include '../View/loader.php';
    ?>
    <link rel="stylesheet" href="../Assets/css/grn-details.css">
</head>
<body>
    <div id="modal"></div>
    <div class="h-100">
        <div class="page-wrapper" id="main-wrapper">
            <!-- Sidebar Start -->
            <?php 
            include '../View/sidebar.php';
            $feature_id=2;
            include '../Includes/viewPermission.php';
            if($edit==0)
            {
                header("location:../Public/grn-header.php");
            }
            ?>
            <div class="body-wrapper">
                <!--  Header Start -->
                <?php 
                include '../View/header.php';
                // include "../View/modals/addproducts.php";
                ?>
                <div class="container-fluid">
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
                    <input type="hidden" name="" id="grn_header" value="<?=$grn_header?> ">
                    <input type="hidden" name="" id="title" value="Subcategories">
                    <div class="row">
                        <form action="../Controller/grnController.php" method="post" id="grnDetailForm">
                            <div class="card col-md-12" id="GrnHeaderDetails">
                                <div class="card-header">
                                    <h3 class="card-title fw-semibold p-2">
                                    GRN Details <span class="badge bg-primary" id="grnNo">GRN_000001</span> <a href="javascript:void(0)" id="refresh"><i class="ti ti-reload"></i></a>
                                    <?php 
                                    if($userType==1 || $create==1)
                                    {
                                        ?>
                                        <a href="../View/modals/addproducts.php?condition=new&ref=guipos" class="btn btn-primary  float-end shadow-btn open-modal" id="btn_add_product">
                                            <small>
                                                <i class="ti ti-plus"></i> Add Products
                                            </small>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-md-4 p-3">
                                                <label for="grnSupplier" class="form-label">Supplier: <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <a href="javascript:void(0)" class="input-group-text">
                                                        <i class="ti ti-user"></i>
                                                    </a>
                                                    <select name="grnSupplier" id="grnSupplier" class="form-select fieldData">
                                                        <option value="">Default Supplier</option>
                                                    </select>
                                                    <a href="javascript:void(0)" id="add-supplier" class="input-group-text">
                                                        <i class="ti ti-user-plus"></i>
                                                    </a>
                                                </div> 
                                            </div>
                                            <div class="col-md-4 p-3">
                                                <label for="grnStatus" class="form-label">Status: <span class="text-danger">*</span></label>
                                                <select name="grnStatus" id="grnStatus" class="form-select fieldData">
                                                    <option value="0">Hold</option>
                                                    <option value="1">Pending</option>
                                                    <?php 
                                                    if($verify ==1)
                                                    {
                                                        ?>
                                                        <option value="2">Verified</option>
                                                        <option value="3">Cancelled</option>
                                                        <?php
                                                    }
                                                    ?>
                                                    
                                                </select>
                                            </div>
                                            <div class="col-md-4 p-3">
                                                <label for="purchaseDate" class="form-label">Effective Date: <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <a href="javascript:void(0)" class="input-group-text">
                                                        <i class="ti ti-calendar-event"></i>
                                                    </a>
                                                    <input type="date" name="purchaseDate" id="purchaseDate" class="form-control fieldData">
                                                </div> 
                                                
                                            </div>
                                            <div class="row col-md-8">
                                                <div class="col-md-6 p-3">
                                                    <label for="grnSupDetails" class="form-label w-100">Supplier Details:</label>
                                                    <!-- <span class="grnSupDetails" id="grnSupDetails">Body Cream,<br>Dinesh Nayakkara</span> -->
                                                    <span class="grnSupDetails" id="grnSupDetails"></span>
                                                </div>
                                                <div class="col-md-6 p-3">
                                                    <label for="shopLocation" class="form-label w-100">Shop Location: <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <a href="javascript:void(0)" class="input-group-text">
                                                            <i class="ti ti-building-store"></i>
                                                        </a>
                                                        <select name="shopLocation" id="shopLocation" class="form-select fieldData">
                                                            <?php 
                                                            foreach($shops AS $row)
                                                            {
                                                                ?>
                                                                <option value="<?=$row["SHID"]?>"><?=$row["ShopName"]?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>                                             
                                                </div>                                            
                                                <div class="col-md-12 p-3">
                                                    <label for="referenceNo" class="form-label">Reference No:</label>
                                                    <textarea name="referenceNo" id="referenceNo" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4 p-3">
                                                <label for="attachDoc" class="form-label w-100">Attach Document:</label>
                                                <div class="input-group">
                                                    <input 
                                                        class="form-control" 
                                                        type="file" 
                                                        id="attachDoc" 
                                                        accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.zip" 
                                                        multiple
                                                    >
                                                    <button class="btn btn-primary" type="button" id="uploadBtn">
                                                        <i class="ti ti-upload"></i> Upload
                                                    </button>
                                                </div>
                                                <div class="text-muted small mt-2">Allowed: JPG, JPEG, PNG, WEBP, PDF, DOC, DOCX, ZIP  • Max: 5MB</div>
                                                <span class="w-100" id="grnAttachedDoc">
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mt-3 p-3">
                                <div class="row">
                                    <div class="col-md-12 d-flex justify-content-center">
                                        <div class="input-group w-80">
                                            <a href="javascript:void(0)" class="input-group-text">
                                                <i class="ti ti-barcode"></i>
                                            </a>
                                            <select name="itemSearch" id="search-items" class="form-select">
                                                <option value=""></option>
                                            </select>
                                        </div> 
                                    </div>
                                    <div class="col-md-12">
                                        <div class="table-responsive grn-table-wrapper">
                                            <table class="table table-hover grn-table align-middle" id="tbl_grnDetails">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="40">#</th>
                                                        <th class="text-center" width="300">Product</th>
                                                        <th class="text-center" width="300">Qty</th>
                                                        <th class="text-center" width="150">Purchase</th>
                                                        <th class="text-center" width="150">Selling</th>
                                                        <th class="text-center" width="170">Total Purchase</th>
                                                        <th class="text-center" width="170">Total Selling</th>
                                                        <th class="text-center" width="130">Profit %</th>
                                                        <th class="text-center" width="160">MFG Date</th>
                                                        <th class="text-center" width="160">EXP Date</th>
                                                        <th class="text-center" width="60"><i class="ti ti-trash-x"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <hr class="mt-5 mb-3">
                                    </div>
                                    <div class="col-md-12 row">
                                        <div class="col-md-7">
                                            <label for="addNotes" class="form-label">Additional Notes</label>
                                            <textarea name="addNotes" id="addNotes" class="form-control" rows="6"></textarea>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="table-responsive summary-table">
                                                <table class="table table-hover" >
                                                    <tr>
                                                        <th class="text-end">No. of Rows</th>
                                                        <td class="text-end">
                                                            <span id="rowNoText">1</span>
                                                            <input type="hidden" name="rowNoInput" id="rowNoInput" class="grn-foot">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-end">Total Purchase</th>
                                                        <td class="text-end">
                                                            <span id="totPurchText">1.00</span>
                                                            <input type="hidden" name="totPurchInput" id="totPurchInput" class="grn-foot">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-end">Purch Disc. Type</th>
                                                        <td class="text-end">
                                                            <select name="discType" id="discType" class="form-select text-end grn-foot">
                                                                <option value="1">%</option>
                                                                <option value="2">Rs.</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-end">Purch Disc.</th>
                                                        <td class="text-end">
                                                            <input type="number" name="disc" id="disc" step=".01" value="0.00" class="form-control text-end grn-foot">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-end">Total Disc.</th>
                                                        <td class="text-end">
                                                            <span id="totDiscText">0.00</span>
                                                            <input type="hidden" name="totDiscInput" id="totDiscInput" class="grn-foot">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-end">Grand Total</th>
                                                        <td class="text-end">
                                                            <span id="grandTotText">0.00</span>
                                                            <input type="hidden" name="grandTotInput" id="grandTotInput" class="grn-foot">
                                                        </td>
                                                    </tr>
                                                </table>    
                                            </div>                                        
                                        </div>
                                    </div>                                
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6"></div>
                                <!-- <div class="col-md-6" id="expenseDiv">
                                    <div class="card mt-3 p-3">
                                        <div class="card-header">
                                            <h3 class="card-title fw-semibold p-2">
                                                Add Expense
                                            </h3>
                                        </div>
                                        <div class="card-body row">
                                            <div class="col-md-4">
                                                <label for="shippingDetail" class="form-label">Shipping Details:</label>
                                                <input type="text" name="shippingDetail" id="shippingDetail" class="form-control">
                                            </div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4">
                                                <label for="shippingCharge" class="form-label">Shipping Charges (+)</label>
                                                <input type="number" name="" id="" class="form-control">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="shippingDetail" class="form-label">Expense Details:</label>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="shippingCharge" class="form-label">Expense Category</label>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="shippingCharge" class="form-label">Expense Charges</label>
                                            </div>
                                            <div class="col-md-1">
                                                <label for="shippingCharge" class="form-label">Action</label>
                                            </div>
                                        </div>
                                        <div class="row" id="otherExpenses">
                                            <div class="col-md-4 mt-3">
                                                <input type="text" name="shippingDetail" id="shippingDetail" class="form-control">
                                            </div>
                                            <div class="col-md-4 mt-3">
                                                <input type="number" name="" id="" class="form-control">
                                            </div>
                                            <div class="col-md-3 mt-3">
                                                <input type="number" name="" id="" class="form-control">
                                            </div>
                                            <div class="col-md-1 mt-3">
                                                <button class="deletExpense btn btn-danger"><i class="ti ti-trash-x"></i></button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 d-flex justify-content-center mt-3">
                                                <button type="button" class="w-25 btn btn-primary">Add Expense <i class="ti ti-circle-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>    
                                </div> -->
                                <div class="col-md-6" id="paymentDiv">
                                    <div class="card mt-3 p-3">
                                        <div class="card-header">
                                            <h3 class="card-title fw-semibold p-2">
                                                Add Payment
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row" id="payment_grid">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 d-flex justify-content-center mt-3">
                                                    <button type="button" class="w-25 btn btn-primary" id="add_payment">Add Payment <i class="ti ti-cash-banknote"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                            </div>                           
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- footer Start  -->
    <?php include '../View/footer.php';?> 
    <script src="../Assets/jquery/grn-details.js"></script>
    <!-- footer End  -->
</body>
</html>