<?php 
include '../Includes/includes.php';
include '../Includes/authcheck.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
    include '../View/head.php';
    include '../View/loader.php';
    ?>
    <style>
        .dis-none
        {
            display: none;
        }
        #mutlyEdit
        {
            z-index: 989;
            display: none;
        }
        .tbl_row
        {
            cursor: pointer;
        }
    </style>
    <link rel="stylesheet" href="../Assets/css/Products.css">
</head>
<body>
    <div id="modal"></div>
    <div class="h-100">
        <div class="page-wrapper" id="main-wrapper">
            <!-- Sidebar Start -->
            <?php 
            include '../View/sidebar.php';
            $feature_id=16;
            include '../Includes/viewPermission.php';
            ?>
            <div class="body-wrapper">
                <!--  Header Start -->
                <?php 
                include '../View/header.php';
                ?>
                <div class="w-100 position-fixed bg-primary p-2 " id="mutlyEdit">
                    <div class="row w-100">
                        <div class="col-md-8">
                            <h3 class="title ps-2 pt-2 text-white">Multi Edit Products</h3>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-warning editAll me-2"><i class="ti ti-edit"></i> Edit</button>
                                <a class="btn btn-warning me-2 shadow-btn editAll2" href="../View/modals/promultiedit.php?condition=multiEdit&ref=product"><i class="ti ti-edit"></i> Multiple Edit</a>
                                <button class="btn btn-danger deleteAll me-2"><i class="ti ti-trash-x"></i> Delete</button>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <?php
                // include "../View/modals/addproducts.php";
                include "../View/modals/editproducts.php";
                include "../View/modals/multieditproducts.php";
                include "../View/modals/product_barcode.php";
                
                ?>
                <!--  Header End -->
                <div class="container-fluid">
                    <section class="head-dashboard-head">

                        <div class="head-head-row">

                            <div class="head-heading">
                                <h1>Products Dashboard <a href="javascript:void(0)" id="refresh"> <i class="ti ti-reload"></i> </a></h1> 

                                <div class="head-breadcrumb">
                                    <a href="javascript:void(0)">Home</a>
                                    <i class="ti ti-chevron-right"></i>

                                    <a href="javascript:void(0)">Products</a>
                                    <i class="ti ti-chevron-right"></i>

                                    <span>Dashboard</span>
                                </div>
                            </div>
                            <div class="head-head-actions">
                                <?php
                                    if ($userType == 1 || $create == 1)
                                    {
                                    ?>
                                        <!-- Buttons -->
                                        <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-md-end">

                                            <a href="../View/modals/addproducts.php?condition=new&ref=product"
                                            class="btn btn-primary rounded-pill shadow-btn open-modal"
                                            title="Add Product">
                                                <small>Add Products</small>
                                            </a>

                                            <a href="../Public/MultiProduct.php"
                                            class="btn btn-primary rounded-pill shadow-btn"
                                            title="Add Multi Product"
                                            target="_blank">
                                                <small>Add Multi Products</small>
                                            </a>

                                            <a href="upload_product.php"
                                            class="btn btn-primary rounded-pill shadow-btn"
                                            id="import">
                                                <small>CSV File Upload</small>
                                            </a>

                                        </div>
                                    <?php
                                    }
                                    ?>
                            </div>
                        </div>
                    </section>
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
                    <input type="hidden" name="" id="title" value="Subcategories">
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
                                        <form id="product-Filter" action="#" method="post">
                                            <div class="row">
                                                <div class="col-md-6 mt-2">
                                                    <label for="category" class="form-label">Category</label>
                                                    <div class="input-group">
                                                        <a href="javascript:void(0)" class="input-group-text">
                                                            <i class="ti ti-category"></i>
                                                        </a>
                                                        <select name="category" id="category" class="form-select">

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <label for="subcategory" class="form-label">Subcategory</label>
                                                    <div class="input-group">
                                                        <a href="javascript:void(0)" class="input-group-text">
                                                            <i class="ti ti-category"></i>
                                                        </a>
                                                        <select name="subcategory" id="subcategory" class="form-select"></select>
                                                    </div>                                    
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <label for="search_item" class="form-label">Search Products</label>
                                                    <div class="input-group">
                                                        <a href="javascript:void(0)" class="input-group-text">
                                                            <i class="ti ti-shopping-cart"></i>
                                                        </a>
                                                        <input type="text" name="search_item" id="search_item" class="form-control" placeholder="Search By Item Name/Item Code/Barcode">
                                                    </div> 
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <label for="product_type" class="form-label">Product Type</label>
                                                    <div class="input-group">
                                                        <a href="javascript:void(0)" class="input-group-text">
                                                            <i class="ti ti-shopping-cart"></i>
                                                        </a>
                                                        <select name="product_type" id="product_type" class="form-select" >
                                                            <option value="P">Product</option>
                                                            <option value="S">Service</option>                                                            
                                                        </select>
                                                    </div> 
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <label for="product_status" class="form-label">Product Status</label>
                                                    <div class="input-group">
                                                        <a href="javascript:void(0)" class="input-group-text">
                                                            <i class="ti ti-shopping-cart-off"></i>
                                                        </a>
                                                        <select name="product_status" id="product_status" class="form-select" >
                                                            <option value="">Select Product Status</option>
                                                            <option value="1">Active</option>
                                                            <option value="0">Inactive</option>                                                            
                                                        </select>
                                                    </div> 
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <label for="fixed_price" class="form-label">Fixed Price</label>
                                                    <div class="input-group">
                                                        <a href="javascript:void(0)" class="input-group-text">
                                                            <i class="ti ti-coin-off"></i>
                                                        </a>
                                                        <select name="fixed_price" id="fixed_price" class="form-select" >
                                                            <option value=""></option>
                                                            <option value="1">Fixed Price</option>
                                                            <option value="0">Non-Fixed Price</option>                                                            
                                                        </select>
                                                    </div> 
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <label for="selling_price1" class="form-label">Selling Price <span class="text-danger dis-none selling_priceLabel">*</span></label>
                                                    <div class="input-group">
                                                        <a href="javascript:void(0)" class="input-group-text">
                                                            <i class="ti ti-coin"></i>
                                                        </a>
                                                        <input type="number"  step="any" name="selling_price1" id="selling_price1" class="form-control">
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
                                                    <label for="purchase_price1" class="form-label">Purchase Price <span class="text-danger dis-none purchase_priceLabel">*</span></label>
                                                    <div class="input-group">
                                                        <a href="javascript:void(0)" class="input-group-text">
                                                            <i class="ti ti-coin"></i>
                                                        </a>
                                                        <input type="number"  step="any" name="purchase_price1" id="purchase_price1" class="form-control">
                                                        <a href="javascript:void(0)" class="input-group-text">
                                                            <select name="purchasing_operator" id="purchasing_operator" class="border-0 bg-transparent">
                                                                <option value="=">=</option>
                                                                <option value=">">></option>
                                                                <option value="<"><</option>
                                                                <option value=">=">>=</option>
                                                                <option value="<="><=</option>
                                                            </select>
                                                        </a>
                                                    </div> 
                                                </div>
                                                <div class="col-md-12 mt-3 d-flex justify-content-center">
                                                        <button type="submit" class="btn btn-primary me-2" id="filter">Filter</button>
                                                        <!-- <input type="submit" value="Filter" class="btn btn-primary me-2"> -->
                                                        <a href="javascript:void(0)" class="btn btn-danger" id="clearFilter">Clear Filter</a>
                                                </div>
                                            </div>
                                        </form> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card col-md-12">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover products-table" id="tbl_products">
                                        <thead>
                                            <th>
                                                <input type="checkbox" name="allCheck" id="allCheck" class="form-checkbox allCheck">
                                            </th>
                                            <th width="400">
                                                Item Name
                                            </th>
                                            <th>
                                                Barcode
                                            </th>
                                            <th>
                                                Category
                                            </th>
                                            <th>
                                                Sub Category
                                            </th>                                            
                                            <th>
                                                Purchase Price
                                            </th>
                                            <th>
                                                Selling Price
                                            </th>
                                            <th>
                                                Dis(%) 
                                            </th>
                                            <th>
                                                Dis. 
                                            </th>
                                            <th>
                                                Type
                                            </th>
                                            <th>
                                                Status
                                            </th>
                                            <th>
                                                FP
                                            </th>
                                            <th>
                                                Is Low Stock
                                            </th>
                                            <th>
                                                Print Barcode
                                            </th>
                                            <th>
                                                Action
                                            </th>
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
    
    <!-- footer Start  -->
    <?php include '../View/footer.php';?> 
    <script src="../Assets/jquery/new-product.js"></script>
    <!-- footer End  -->
</body>
</html>