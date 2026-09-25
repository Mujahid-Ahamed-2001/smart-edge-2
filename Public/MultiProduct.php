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
  <style>
    button.btn-action:focus {
    border: 1px solid red;
}

  </style>
  <link rel="stylesheet" href="../Assets/css/MultiProduct.css">
</head>
<body>
    <div id="modal"></div>
<!--  Body Wrapper -->
    <div class="h-100vh">
        <div class="page-wrapper" id="main-wrapper">
            <input
    type="file"
    id="barcode-photo-input"
    accept="image/*"
    capture="environment"
    style="display:none;"
>
            <!-- Sidebar Start -->
            <?php 
            include '../View/sidebar.php';
            $feature_id=16;
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
                        <a href="#" class="breadcrumb-link">Products</a>
                        <span class="mx-2">
                            <i class="ti ti-chevron-right"></i>
                        </span>
                        <span class="breadcrumb-active">Add Multi Products</span>
                    </div>
                    <div class="main-card">

                        <!-- Header -->
                        <div class="main-card-header">

                            <div class="main-title">

                                <div class="main-icon">
                                    <i class="ti ti-brand-producthunt"></i>
                                </div>

                                <div>
                                    <h3>Multi Products <a href="javascript:void(0)" id="refresh"><i class="ti ti-reload"></i></a></h3>
                                    <p>Create and manage multiple products</p>
                                </div>

                            </div>

                        </div>

                        <!-- Divider -->
                        <div class="card-divider"></div>
                    </div>
                    <div class="main-card">
                        <form action="../AJAX/Product/Addproduct.php?condition=new" method="post" class="add-product" id="add-product">
                            <div class="product-grid-wrapper">
                                <div class="table-responsive product-table-responsive">
                                    <table class="table main-table mb-0" id="tbl_main">
                                        <thead>
                                            <tr>
                                                <th class="col-barcode">Barcode</th>
                                                <th class="col-product">Product Name</th>
                                                <th class="col-category">Category</th>
                                                <th class="col-cost">Cost Type</th>
                                                <th class="col-cost">Cost (Rs.)</th>
                                                <th class="col-price">Selling Price (Rs.)</th>
                                                <th class="col-qty">Opening Qty</th>
                                                <th class="col-stock">Low Stock</th>
                                                <th class="col-status">Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr class="row-saved">
                                                <td>
                                                    <input type="text" class="form-control barcode" name="Barcode" id="Barcode">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control product_name" name="ItemName" id="ItemName">
                                                </td>
                                                <td>
                                                    <select class="form-select category" name="Subcategories_SCID" id="Subcategories_SCID">
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select cost_type" name="cost_type" id="cost_type">
                                                        <option value="1">Rs.</option>
                                                        <option value="2">%</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control cost" name="ProdPurchasePrice" id="ProdPurchasePrice">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control selling_price" name="ProdSellPrice" id="ProdSellPrice">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control opening_qty" name="opening_qty" id="opening_qty" >
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control low_stock" name="low_stock_qty" id="low_stock_qty">
                                                    <input type="hidden" class="form-control" name="multi" id="multi" value="1">
                                                </td>
                                                <td>
                                                    <button type="submit" class="btn-action">
                                                        <i class="ti ti-plus"></i>
                                                    </button>
                                                    <button type="button" class="btn-action" id="scan">
                                                        <i class="ti ti-scan"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="main-card">

                        <div class="product-grid-wrapper">

                            <div class="product-grid-topbar">

                                <div class="product-grid-title">
                                    <i class="ti ti-table"></i>
                                    <span>Product Entry Grid</span>
                                </div>
                                <div class="product-grid-title">
                                    <button type="button" class="btn btn-primary" id="add-row"><i class="ti ti-plus"></i> Add Row</button>
                                </div>
                            </div>

                            <div class="table-responsive product-table-responsive">

                                <table class="table main-table mb-0" id="tbl_main2">

                                    <thead>
                                        <tr>
                                            <th class="col-barcode">Barcode</th>
                                            <th class="col-product">Product Name</th>
                                            <th class="col-category">Category</th>
                                            <th class="col-cost">Cost (Rs.)</th>
                                            <th class="col-price">Selling Price (Rs.)</th>
                                            <th class="col-qty">Opening Qty</th>
                                            <th class="col-stock">Low Stock</th>
                                            <th class="col-status">Status</th>
                                        </tr>
                                    </thead>

                                    <tbody id="product-table-body">
                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                    <!-- footer Start  -->
                    <?php include '../View/footer.php';?> 
                    <!-- <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script> -->
                    <!-- <script src="https://unpkg.com/@zxing/library@0.21.3/umd/index.min.js"></script> -->
                    <script src="https://unpkg.com/@zxing/library@0.21.3/umd/index.min.js"></script>
                    <script src="../Assets/jquery/MultiProduct.js?v=16"></script>
                    <!-- footer End  -->
                </div>
            </div>
        </div>
    </div>
    <!--  Body Wrapper End -->

</body>
</html>