<?php 
include '../Includes/includes.php';
include '../Includes/authcheck.php';

if(isset($_POST["GHID"]) || isset($_GET["GHID"]))
{
    $GHID = isset($_POST["GHID"]) ? $_POST["GHID"] : (isset($_GET["GHID"]) ? $_GET["GHID"] : null);
    if(empty($GHID))
    {        
        $_SESSION['grnheader_update'] == 3;
        header("Location: grn-header.php");
        die("Error: no grn header id.");
    }
}
else
{
    $_SESSION['grnheader_update'] == 3;
    header("Location: grn-header.php");
    die("Error: no grn header id.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
    include '../View/head.php';
    ?>
    <style>
        .table>thead
        {
            background-image: linear-gradient(45deg, #432eca, #62c2e4);
            color: #fff;
        }
        .table>tbody
        {
            background: #f5f5f5;
            color: #000;
        }
        .table>tbody input
        {
            background: #fff;
            color: #000;
        }
        .table>thead th
        {
            border: 1px solid #fff;
        }
        .red-x-btn {
            background: transparent;
            color: #ff0100;
            font-size: 30px;
            line-height: 30px;
            font-weight: bold;
            cursor: pointer;
            padding: 1px;
            transition: 0.2s ease;
        }

        .red-x-btn:hover {
            font-size: 35px;
        }
    </style>
</head>
<body>
    <!--  Body Wrapper -->
    <div class="h-100vh">
        <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
            data-sidebar-position="fixed" data-header-position="fixed">
            <?php
            include '../View/sidebar.php';
            ?>
            <!--  Sidebar End -->
            <!--  Main wrapper start -->
            <div class="body-wrapper">
                <?php include '../View/header.php';?>
                <div class="container-fluid">
                    <h2 class="page-title">Add GRN</h2>
                    <div class="card p-3">
                        <form action="">
                            <div class="row">
                                <div class="col-md-3 mb-3 mt-3 ">
                                    <label for="Supplier" class="form-label">Supplier:<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <a href="javascript:void(0)" class="input-group-text"><i class="ti ti-user"></i></a>
                                        <select name="supplier" id="supplier-select" class="form-control supplier-select"></select>
                                        <a href="javascript:void(0)" class="input-group-text" id="add-supplier"><i class="ti ti-circle-plus"></i></a>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3 mt-3 ">
                                    <label for="ref" class="form-label">Reference No:</label>
                                    <input type="text" name="" id="" class="form-control">
                                </div>
                                <div class="col-md-3 mb-3 mt-3 ">
                                    <label for="purch_date" class="form-label">Purchase Date:<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <a href="javascript:void(0)" class="input-group-text"><i class="ti ti-calendar"></i></a>
                                        <input type="datetime" name="dateTime" id="purch_date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3 mt-3 ">
                                    <label for="purch_status" class="form-label">Purchase Status:<span class="text-danger">*</span></label>
                                    <select name="purch_status" id="purch_status" class="form-select"></select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <p class="sup_address">
                                        127, Wattalpola, <br>
                                        Panadura <br>
                                        Mr. Mujahid Ahamed
                                    </p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="shop_loc" class="form-label">Shop:<span class="text-danger">*</span></label>
                                    <select name="shop" id="shop_loc" class="form-select"></select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="doc" class="form-label">Attach Document:</label>
                                    <input type="file" name="doc" id="doc" class="form-control" accept=".pdf,.csv,.doc,.docx,.jpeg,.jpg,.png">
                                    <span class="small-text">Max File Size: 5MB <br>Allowed File: .pdf .csv .doc .docx .jpeg .jpg .png</span> 
                                </div>
                            </div>    
                        </form>                            
                    </div>
                    <div class="card p-3">
                        <div class="row mb-1">
                            <div class="col-md-2 mb-3 mt-3 d-flex justify-content-center">
                                <button class="btn btn-primary" id="ImportProducts">Import Products</button>
                            </div>
                            <div class="col-md-8 mb-3 mt-3">
                                <div class="input-group">
                                    <a href="javascript:void(0)" class="input-group-text"><i class="ti ti-search"></i></a>
                                    <select name="" id="search-product" class="form-select"></select>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3 mt-3">
                                <a href=""><strong><i class="ti ti-plus"></i> Add New Product</strong></a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="max-width:30px;">#</th>
                                        <th style="min-width:150px;">Product Name</th>
                                        <th style="min-width:150px;">Purchase Quantity</th>
                                        <th style="min-width:150px;">Unit Cost</th>
                                        <th style="min-width:150px;">Label Price</th>
                                        <th style="min-width:150px;">Selling Price</th>
                                        <th style="min-width:130px;">Mnf Date</th>
                                        <th style="min-width:130px;">Exp Date</th>
                                        <th style="max-width:30px;"><i class="ti ti-trash-x"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Economical Tote (PD_000001)</td>
                                        <td><input type="text" name="" id="" class="form-control"></td>
                                        <td><input type="text" name="" id="" class="form-control"></td>
                                        <td><input type="text" name="" id="" class="form-control"></td>
                                        <td><input type="text" name="" id="" class="form-control"></td>
                                        <td><input type="date" name="" id="" class="form-control"></td>
                                        <td><input type="date" name="" id="" class="form-control"></td>
                                        <td><a href="javascrip:void(0)" class="red-x-btn">&times;</a></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Economical Tote (PD_000001)</td>
                                        <td><input type="text" name="" id="" class="form-control"></td>
                                        <td><input type="text" name="" id="" class="form-control"></td>
                                        <td><input type="text" name="" id="" class="form-control"></td>
                                        <td><input type="text" name="" id="" class="form-control"></td>
                                        <td><input type="date" name="" id="" class="form-control"></td>
                                        <td><input type="date" name="" id="" class="form-control"></td>
                                        <td><a href="javascrip:void(0)" class="red-x-btn">&times;</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="foot mt-2 row p-2" style="border-top: 0.5px solid #8f8f8f;">
                            <div class="col-md-10 text-end">
                                <p class="p-0 m-0"><strong>Total Items:</strong></p>
                                <p class="p-0 m-0"><strong>Net Total:</strong></p>
                            </div>
                            <div class="col-md-1">
                                <p class="p-0 m-0">3.00</p>
                                <p class="p-0 m-0">Rs. 41,000.00</p>
                            </div>
                            <div class="col-md-1"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!--  Main wrapper end-->
        </div>
    </div>
    <script src="../Assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../Assets/js/sidebarmenu.js"></script>
    <script src="../Assets/js/app.min.js"></script>
    <script src="../Assets/libs/simplebar/dist/simplebar.js"></script>    
</body>
</html>