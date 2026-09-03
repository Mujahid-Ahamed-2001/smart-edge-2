<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
include "../../Model/shop_class.php";
include "../../Model/unit_class.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Smart Edge | Powered By Next Edge</title>
    <link rel="shortcut icon" type="image/png" href="../../Assets/Images/SystemLogo/favicon.png" />
    <link rel="stylesheet" href="../../Assets/css/styles.min.css" />

    <script src="../../JQuery_361.js"></script>

    <script src="../../Assets/jquery/jquery.min.js"></script>
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="../../Assets/css/toastr.min.css">
    <!-- jQuery (Required for Toastr) -->
    <script src="../../Assets/js/jquery-3.6.0.min.js"></script>
    <!-- Toastr JS -->
    <script src="../../Assets/jquery/toastr.min.js"></script>


    <script src="../../Assets/ckeditor/ckeditor.js"></script>

    <link rel="stylesheet" href="../../Assets/css/styles.css">
    <link rel="stylesheet" href="../../Assets/css/head.css">

    <link rel="stylesheet" href="../../vendor/select2-develop/dist/css/select2.min.css">
    <script src="../../vendor/select2-develop/dist/js/select2.min.js"></script>
    <script src="../../Assets/jquery/toast.js"></script>
    <style>
        .modal-form-wrapper{
            padding:20px;
        }

        .modal-form-header{
            display:flex;
            align-items:center;
            gap:15px;
            margin-bottom:25px;
        }

        .modal-header-icon{
            width:65px;
            height:65px;
            background:#f3f5ff;
            border-radius:18px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:30px;
            color:#5d5fef;
        }

        .modal-form-title{
            font-size:32px;
            font-weight:700;
            margin:0;
        }

        .modal-form-subtitle{
            color:#6c757d;
            margin:0;
        }

        .modal-form-card{
            background:#fff;
            border:1px solid #edf0f7;
            border-radius:20px;
            padding:25px;
        }

        .modal-input,
        .modal-input-group .input-group-text{
            height:55px;
        }

        .modal-input{
            border-radius:12px;
        }

        .modal-input-group .input-group-text{
            background:#fff;
            border-right:none;
        }

        .modal-input-group .form-control{
            border-left:none;
        }

        .required::after{
            content:" *";
            color:red;
        }

        .modal-settings-card{
            background:#fafbff;
            border:1px solid #edf0f7;
            border-radius:18px;
            padding:20px;
            margin-top:20px;
        }

        .settings-title{
            color:#5d5fef;
            margin-bottom:20px;
        }

        .setting-item{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:18px;
        }

        .setting-desc{
            font-size:13px;
            color:#6c757d;
        }

        .modal-upload-box{
            border:2px dashed #d6d9ea;
            border-radius:18px;
            text-align:center;
            padding:40px;
        }

        .upload-icon{
            font-size:60px;
            color:#5d5fef;
            margin-bottom:15px;
        }

        .upload-info{
            margin-top:12px;
            color:#6c757d;
            font-size:13px;
        }

        .modal-logo-preview{
            margin-top:20px;
            background:#fafbff;
            border:1px solid #edf0f7;
            border-radius:18px;
            text-align:center;
            padding:25px;
        }

        .modal-form-footer{
            display:flex;
            justify-content:flex-end;
            gap:10px;
            margin-top:25px;
        }

        .modal-btn-save:disabled{
            background:#5d5fef;
            color:#fff;
            border:none;
            padding:12px 30px;
            border-radius:12px;
            cursor: not-allowed;
        }
        .modal-btn-save{
            background:#5d5fef;
            color:#fff;
            border:none;
            padding:12px 30px;
            border-radius:12px;
        }

        .modal-btn-save:hover{
            background:#4b4de0;
            color:#fff;
        }

        .modal-btn-cancel{
            padding:12px 25px;
            border-radius:12px;
        }
        
      .dropzone {
        border-style: dashed !important;
        border-width: 2px !important;
        cursor: pointer;
        transition: 0.15s ease-in-out;
        user-select: none;
      }

      .dropzone:hover {
        transform: translateY(-1px);
      }

      .dropzone.dragover {
        background: #eaf4ff !important;
        border-color: #0d6efd !important; /* Bootstrap primary */
      }

      .dropzone.error {
        background: #fff0f0 !important;
        border-color: #dc3545 !important; /* Bootstrap danger */
      }

      .dropzone-icon {
        font-size: 28px;
        line-height: 1;
      }

    </style>
</head>
<body>
<?php 
$condition = $_GET["condition"];
$ref = isset($_GET["ref"]) && !empty($_GET["ref"]) ? $_GET["ref"] :"";
$title ="";
$subtitle ="";
$CMID ="";
$action ="../../Controller/productController.php";
if($condition=="new")
{
    $title ="Add New Product";
    $subtitle ="Fill in the details below to add a new product";
    $action .="?query=save";
    $btn ="<i class='ti ti-device-floppy'></i> Save Product";
}
else if($condition=="edit")
{
    $title ="Update Product";
    $subtitle ="Fill in the details below to update product details";
    $btn ="<i class='ti ti-device-floppy'></i> Update Product";
    if(isset($_GET["PDID"]) && !empty($_GET["PDID"]))
    {
        $PDID = $_GET["PDID"];
        $action .="?query=update_new&PDID=$PDID";
        ?>
        <script>
            $(document).ready(function () {
                const PDID = <?= json_encode((int)$PDID) ?>;
                let is_default = "";

                allCategory().always(function () {
                    $.ajax({
                        url: "../../Controller/productController.php",
                        method: "GET",
                        data: {
                            query: "fetch_update",
                            PDID: PDID
                        },
                        dataType: "json",

                        success: function (response) {
                            if (Number(response.status) === 1) {
                                const product = response.products.products;

                                const ProductNo                 = product.ProductNo;
                                const ProdImage                 = product.ProdImage;
                                const Barcode                   = product.Barcode;
                                const ItemName                  = product.ItemName;
                                const ProdDescription           = product.ProdDescription;
                                const SecondName                = product.SecondName;
                                const ProductStat               = product.ProductStat;
                                const ItemType                  = product.ItemType;
                                const Subcategories_SCID        = product.Subcategories_SCID;
                                const ProdPurchasePrice        = product.ProdPurchasePrice;
                                const ProdSellPrice        = product.ProdSellPrice;
                                const PurchaseUnit              = product.PurchaseUnit;
                                const UnitConversion            = product.UnitConversion;
                                const SellingUnit               = product.SellingUnit;
                                const prodDiscount              = product.prodDiscount;
                                const is_fixedPrice             = product.is_fixedPrice;
                                const is_lowStock               = product.is_lowStock;
                                const low_stock_qty             = product.low_stock_qty;
                                const prodFlatDiscount          = product.prodFlatDiscount;
                                const CTID                      = response.products.CTID;
                                const INID                      = response.products.INID;
                                // console.log("INID "+INID);
                                
                                const OpeningStock              = response.products.OpeningStock;
                                const PurchasePrice             = response.products.PurchasePrice;
                                const SellingPrice              = response.products.SellingPrice;
                                var title = $(".modal-form-title").text();
                                title = title+" - "+ProductNo;
                                $("#cmb_category").val(CTID).trigger("change");
                                subcat2().always(function(){
                                    $("#cmb_subcategory").val(Subcategories_SCID).trigger("change");    
                                })
                                $("#barcode").val(Barcode).trigger("change");
                                $("#prod_name").val(ItemName).trigger("change");
                                $("#prod_description").val(ProdDescription).trigger("change");
                                $("#second_name").val(SecondName).trigger("change");
                                $("#prod_purchase_price").val(parseFloat(ProdPurchasePrice).toFixed(2)).trigger("change");
                                $("#prod_selling_price").val(parseFloat(ProdSellPrice).toFixed(2)).trigger("change");
                                $("#prod_Item_Dis").val(parseFloat(prodDiscount).toFixed(2)).trigger("change");
                                $("#prod_Item_Dis_flat").val(parseFloat(prodFlatDiscount).toFixed(2)).trigger("change");
                                $("#openStock").val(parseFloat(OpeningStock).toFixed(2)).trigger("change");
                                $("#lowqty").val(parseFloat(low_stock_qty).toFixed(2)).trigger("change");
                                $("#cmb_purchase_unit").val(PurchaseUnit).trigger("change");
                                $("#conversion_rate").val(parseFloat(UnitConversion).toFixed(2)).trigger("change");
                                $("#cmb_selling_unit").val(SellingUnit).trigger("change");
                                let original_path = "../../Assets/Images/icons/product.png";
                                if (ProdImage && ProdImage.trim() !== "") 
                                {
                                    const path = "../../Assets/Images/prod_images/" + ProdImage;

                                    $("#img_product")
                                        .attr("src", path)
                                        .off("error")
                                        .on("error", function () {
                                            $(this).attr("src", original_path);
                                        });
                                } 
                                else 
                                {
                                    $("#img_product").attr("src", original_path);
                                }

                                if(is_fixedPrice==1)
                                {
                                    $("#chk_fp").prop("checked", true);
                                }
                                else
                                {
                                    $("#chk_fp").prop("checked", false);
                                }
                                if(is_lowStock==1)
                                {
                                    $("#chk_ls").prop("checked", true);
                                }
                                else
                                {
                                    $("#chk_ls").prop("checked", false);
                                }
                                if(ProductStat==1)
                                {
                                    $("#status").prop("checked", true);
                                }
                                else
                                {
                                    $("#status").prop("checked", false);
                                }

                                $(".modal-form-title").text(title);
                                
                                

                                // console.log(product);
                            } else {
                                parent.toastr.error(
                                    response.message || "Unable to fetch product data.",
                                    "Error"
                                );

                                parent.$("#modal").iziModal("close");
                            }
                        },

                        error: function (xhr, status, error) {
                            console.error("AJAX Error:", status, error);
                            console.error("Response:", xhr.responseText);

                            parent.toastr.error(
                                "An error occurred while fetching product data.",
                                "Error"
                            );

                            parent.$("#modal").iziModal("close");
                        }
                    });
                });
            });
        </script>
        <?php

    }
    else
    {
        // close modal
        ?>
        <script>
        parent.toastr.error("product ID Not Found", "Error");
        setTimeout(function () {
            parent.$("#modal").iziModal("close");
        }, 300);    
        </script>
        <?php
        
    }
}
?> 
  <div class="modal-form-wrapper">
    <div class="modal-form-header">
        <div class="modal-header-icon">
            <i class="ti ti-shopping-cart"></i>
        </div>

        <div>
            <h3 class="modal-form-title"><?=$title?></h3>
            <p class="modal-form-subtitle">
                <?=$subtitle?>
            </p>
        </div>
    </div>
    <form action="<?=$action?>" id="modalForm" method="post" enctype="multipart/form-data">
        <!-- Form Card -->
        <div class="modal-form-card">

            <div class="row">
              <!-- hidden input -->
              <div class="form-check form-switch" id="statuss">
                  <input class="form-check-input" type="checkbox" id="status" name="prodStatus" checked="" value="1">
                  <label class="form-label" class="form-check-label" for="status">Product Status</label>
              </div>         
              <?php 

              $shopObj = new Shop();
              $shop_id = $_SESSION['shop_id'];
              $dbObj = new DBTransactions();
              //get company stat
              $sql = "SELECT * FROM shop
              INNER JOIN company ON company.CMID = shop.Company_CMID
              WHERE SHID = ".$shop_id.";";

              $shopData = $dbObj->getData($sql);
              $multi_category = floatval($shopData[0]['is_multicategory']);
              $company_id = floatval($shopData[0]['CMID']);
              if($shopObj->hasCategories($shop_id))
              {
                ?>
                <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-6 mb-3">                     
                        <a href="../../Public/category.php" class="" window="new" target="_blank">
                        <div class="round-16 d-flex align-items-center justify-content-center">                      
                        </div>
                        <span class="hide-menu">Add Categories</span>
                        </a>
                    </div>                  
                    <div class="col-md-6 mb-3">                     
                        <a href="../../Public/subcategory.php" class="" window="new" target="_blank">
                        <div class="round-16 d-flex align-items-center justify-content-center">                      
                        </div>
                        <span class="hide-menu">Add Sub Categories</span>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label" for="cmb_category">Category <span class="text-danger text-alrt" >*</span></label>
                      <div class="input-group">
                        <select name="cmb_category" id="cmb_category" class="form-select" required>
                        </select>  
                        <a href="javascript:void(0)" id="refresh-main-cat" class="input-group-text">
                          <i class="ti ti-refresh fs-4"></i>
                        </a>
                      </div>                    
                      <span class="text-danger" id="alrt" style="display:none;">This Field is Required</span>
                    </div>

                    <div class="col-md-6 mb-3">
                      <label class="form-label" for="cmb_subcategory">Sub Category <span class="text-danger text-alrt" >*</span></label>
                      <select name="cmb_subcategory" id="cmb_subcategory" class="form-select" required>
                      </select>
                      <span class="text-danger" id="alrt" style="display:none;">This Field is Required</span>
                    </div>
                  </div>
                </div>
                <?php 
              }//has categories
              else
              {
                ?>  
                <input type="hidden" name="cmb_category" class="d-none" value="1">
                <input type="hidden" name="cmb_subcategory" class="d-none" value="1">
                <?php
              }
              ?>

              <div class="col-md-6 mb-3">
                <label class="form-label" for="barcode">Barcode</label>
                <input type="text" name="barcode" id="barcode" class="form-control mb-2 " placeholder="Barcode">
                <span style="display:none; color:red;" id="barcode_warning"></span>
              </div> 
              
              <div class="col-md-6 mb-3">
                <label class="form-label" for="prod_name">Product Name <span class="text-danger text-alrt" >*</span></label>
                <input type="text" name="prod_name" id="prod_name" class="form-control mb-2 required" placeholder="Product Name" required>
                <span class="text-danger" id="alrt" style="display:none;">This Field is Required</span>
              </div>
              <script>
                $(document).on("input", "#prod_name", function () {
                    let cleanValue = $(this).val().replace(/[^a-zA-Z0-9\s\-_]/g, ""); // Allows only letters, numbers, - , _ , and spaces
                    $(this).val(cleanValue);
                });
              </script>
              <?php 
              if($shopObj->hasSecondLanguage($shop_id))
              {
                ?>
                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="second_name">Second Name <span class="text-danger text-alrt" >*</span></label>
                    <input type="text" name="second_name" id="second_name" class="form-control mb-2 required" placeholder="Second Name">
                      <span class="text-danger" id="alrt" style="display:none;">This Field is Required</span>
                  </div>
                <?php 
              }//has second language
              ?>
              
              <!-- description -->
              <div class="col-md-6 mb-3">
                <label class="form-label" for="prod_description">Description (optional)</label>
                <textarea name="prod_description" id="prod_description" cols="30" rows="3" class="form-control" aria-label="Description"></textarea>
              </div>

              <?php 
                ?>
                <div class="col-md-12">
                  <span class="text-danger">Note: - Prices must be entered in the selling unit price.</span>
                </div>
                <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label" for="prod_purchase_price">Purchase Price <span class="text-danger text-alrt" >*</span></label>
                      <input type="number" step="any" name="prod_purchase_price" id="prod_purchase_price" class="form-control mb-2  required" placeholder="0.00 " value="0.00" required>
                    </div>

                    <div class="col-md-6 mb-3">
                      <label class="form-label" for="prod_selling_price">Selling Price <span class="text-danger text-alrt" >*</span></label>
                      <input type="number" step="any" name="prod_selling_price" id="prod_selling_price" class="form-control mb-2  required" placeholder="0.00 " value="0.00" required>
                    </div>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label" for="prod_Item_Dis">Item Discount </label>
                      <input type="text" name="prod_Item_Dis" id="prod_Item_Dis" class="form-control" value="0.00">
                    </div>   
                    
                    <div class="col-md-6 mb-3">
                      <label class="form-label" for="prod_Item_Dis_flat">Item Flat Discount </label>
                      <input type="text" name="prod_Item_Dis_flat" id="prod_Item_Dis_flat" class="form-control" value="0.00">
                    </div>   
                  </div>
                </div>

                <?php 
              ?>
              
              <?php 
              if($shopObj->hasService($shop_id))
              {
                ?>
                <div class="col-md-6 service">
                  <label class="form-label" for="chk_service">Service</label>
                  <div class="form-check form-switch mb-3">
                    <input class="form-check-input" name="chk_service" id="chk_service" type="checkbox">
                    <label class="form-label" class="form-check-label" for="chk_service">Use as Service</label>
                  </div>
                </div>
                <?php 
              }//has service
              ?>
              <div class="col-md-6 service">
                <label class="form-label" for="chk_fp">FP</label>
                <div class="form-check form-switch mb-3">
                  <input class="form-check-input" name="chk_fp" id="chk_fp" type="checkbox">
                  <label class="form-label" class="form-check-label" for="chk_fp">Fixed Price</label>
                </div>
              </div>
              <?php 
              if($shopObj->hasInventory($shop_id))
              {
                ?>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="openStock">Opening Stock</label>
                  <input type="text" name="openStock" id="openStock" class="form-control" step="0.01" value="0.00">
                </div>
                <?php
              }
              ?>
                <div class="col-md-6 service">
                    <label class="form-label" class="form-check-label" for="chk_ls">LS</label>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" name="chk_ls" id="chk_ls" type="checkbox">
                        <label class="form-label" class="form-check-label" for="chk_ls">Low Stock Alert</label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="lowqty">Low Stock Qty</label>
                  <input type="text" name="lowqty" id="lowqty" class="form-control" step="0.01" value="0.00">
                </div>
              <!-- Units -->
              <div class="col-md-12 p-2 border border-primary rounded">
                <div class="row">
                  <div class="mb-1 col-4">
                    <label class="form-label" for="cmb_purchase_unit">Purchase Unit</label>
                    <select name="cmb_purchase_unit" id="cmb_purchase_unit" class="form-select dropdown-toggle">
                      <?php 
                      $unitObj = new Unit();
                      $unitData = $unitObj->getAllUnits($shop_id,$multi_category,$company_id);
                      foreach($unitData as $row)
                      {
                        ?>
                        <option value="<?php echo $row['UNID'];?>"><?php echo $row['UnitName'] ." ~ ". $row['ShortName'];?></option>
                        <?php 
                      }//foreach
                      ?>
                    </select>
                  </div>
                  <div class="mb-1 col-4">
                    <label class="form-label" for="conversion_rate">Conversion Rate</label>
                    <input type="number" step="0.001" value="1" name="conversion_rate" id="conversion_rate" class="form-control mb-2" placeholder="Conversion Rate" required>
                  </div>
                    
                  <div class="mb-1 col-4">
                    <label class="form-label" for="cmb_selling_unit">Selling Unit</label>
                    <select name="cmb_selling_unit" id="cmb_selling_unit" class="form-select dropdown-toggle">
                      <?php 
                      $unitObj = new Unit();
                      $unitData = $unitObj->getAllUnits($shop_id,$multi_category,$company_id);
                      foreach($unitData as $row)
                      {
                        ?>
                        <option value="<?php echo $row['UNID'];?>"><?php echo $row['UnitName'] ." ~ ". $row['ShortName'];?></option>
                        <?php 
                      }//foreach
                      ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-6 mb-3 image">
                <label class="form-label mt-2">Product Image</label>

                <!-- Drag & Drop Area -->
                <div id="prodDropzone" class="dropzone border rounded-3 p-3 text-center bg-light" role="button" tabindex="0">
                  <div class="d-flex flex-column align-items-center gap-1">
                    <div class="dropzone-icon">📁</div>
                    <div class="fw-semibold">Drag & drop image here</div>
                    <div class="text-muted small">or click to browse</div>
                    <div class="text-muted small">Allowed: JPG, JPEG, PNG, WEBP • Max: 5MB</div>
                  </div>

                  <!-- keep your actual file input (hidden) -->
                  <input type="file" name="prod_image" id="prod_image" class="d-none" accept=".jpg,.jpeg,.png,image/jpeg,image/png,image/webp">
                </div>

                <div class="mt-2">
                  <span class="text-success d-none" id="success">Ok</span>
                  <span class="text-danger d-none" id="danger">⚠ Image too large or invalid type.</span>
                </div>

                <div class="mt-3 rounded">
                  <img src="../../Assets/Images/icons/product.png" id="img_product"
                      class="shadow img-fluid mx-auto d-block rounded bordered"
                      alt="Product Image">
                </div>
              </div>
          </div>

        </div>

        <!-- Footer -->
        <div class="modal-form-footer">

            <button type="button"
                class="btn btn-danger modal-btn-cancel">
                <i class="ti ti-x"></i>
                Cancel
            </button>

            <button type="submit"
                class="btn modal-btn-save" id="btn_save_product">
                <?=$btn?>
            </button>

        </div>    
    </form>
  </div>
  <script>
    <?php
        $PDID = isset($_GET["PDID"]) && !empty($_GET["PDID"]) ? $_GET["PDID"] : "";
        ?>
    let ref = "<?=$ref?>";
    let PDID = "<?=$PDID?>";
    let condition = "<?=$condition?>";
    let $table = parent.$('#tbl_products');
    var query = $(parent.document).find("#query").val();
    $(".modal-btn-cancel").on("click", function(){
      parent.$("#modal").iziModal("close");
    })
    function checkBarcode(barcode,id)
    {
        return $.ajax({
            url: '../../AJAX/Products/CheckBarcode.php',
            method: 'POST',
            data: { barcode: barcode, PDID: id },
            dataType: 'json'
        }).then(function(response) {

            if (response && typeof response.ProCount !== 'undefined') {
                return 0;
            } else {
                toastr.error("An error occurred while validating the Barcode. Please try again.", "Error");
                console.error("Invalid response:", response);
                return false;
            }

        }).catch(function(error) {
            console.error("AJAX error:", error);
            toastr.error("An error occurred while validating the Barcode. Please try again.", "Error");
            return false;
        });
    }
    $('#barcode').on('keypress', function(e) {
        if (e.which === 13) { // 13 is the key code for Enter
            e.preventDefault(); // Prevent the default Enter action
        }
        var barcode = $(this).val();
        var barcodeInput = $(this);
        if(barcode !="")
        {
            checkBarcode(barcode).then(function(exist){
                if(exist>0)
                {                    
                    parent.toastr.error("Barcode Already Exists, Please Try a New One.", "Error");
                    barcodeInput.focus();
                    barcodeInput.css("border-color","red");
                    $("#btn_save_product").prop("disabled", true);
                    return false;
                }
                else
                {
                    barcodeInput.css("border-color","");
                    $("#btn_save_product").prop("disabled", false);
                }
            });
        }
        else
        {
            barcodeInput.css("border-color","");
            $("#btn_save_product").prop("disabled", false);
        }
        
    });
    $("#barcode").on("input", function(e){
        e.preventDefault();
        var barcode = $(this).val();
        var barcodeInput = $(this);
        if(barcode !="")
        {
            checkBarcode(barcode).then(function(exist){
                if(exist>0)
                {                    
                    parent.toastr.error("Barcode Already Exists, Please Try a New One.", "Error");
                    barcodeInput.focus();
                    barcodeInput.css("border-color","red");
                    $("#btn_save_product").prop("disabled", true);
                    return false;
                }
                else
                {
                    barcodeInput.css("border-color","");
                    $("#btn_save_product").prop("disabled", false);
                }
            });
        }
        else
        {
            barcodeInput.css("border-color","");
            $("#btn_save_product").prop("disabled", false);
        }
        
    });
    $("#modalForm").on("submit", function(e){
        e.preventDefault();
        
        let formData = new FormData(this);
        let url = $(this).attr("action");
        let btn = $("#btn_save_product");
        let btnHtml = btn.html();        
        $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function() {
                    btn.prop("disabled", true);
                    btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
                },
                success: function(response) {
                    btn.prop("disabled", false);
                    btn.html(btnHtml);

                    /*
                    * EDIT PRODUCT RESPONSE
                    */
                    if (condition === "edit") {
                        var alerts = Array.isArray(response.alerts)
                            ? response.alerts
                            : [];

                        /*
                        * Display each backend alert separately.
                        */
                        $.each(alerts, function(index, alert) {
                            var message = alert.message || "Unknown response";
                            var section = alert.section || "product";

                            var title = section === "pricehistory"
                                                    ? "Price History"
                                                    : section === "inventory"
                                                        ? "Inventory"
                                                        : section === "image"
                                                            ? "Product Image"
                                                            : section === "product"
                                                                ? "Product"
                                                                : section === "validation"
                                                                    ? "Validation"
                                                                    : "Information";

                            switch (alert.type) {
                                case "success":
                                    parent.toastr.success(message, title);
                                    break;

                                case "error":
                                    parent.toastr.error(message, title);
                                    break;

                                case "warning":
                                    parent.toastr.warning(message, title);
                                    break;

                                case "info":
                                    parent.toastr.info(message, title);
                                    break;

                                default:
                                    parent.toastr.info(message, title);
                                    break;
                            }
                        });

                        /*
                        * Fallback for an unexpected backend response.
                        */
                        if (alerts.length === 0) {
                            parent.toastr.error(
                                response.message || "No update information was returned.",
                                "Error"
                            );

                            return;
                        }

                        /*
                        * Check whether at least one operation succeeded.
                        */
                        var hasSuccessfulUpdate = alerts.some(function(alert) {
                            return alert.type === "success";
                        });

                        /*
                        * Check whether the product itself was successfully processed.
                        * "info" means its existing values were already up to date.
                        */
                        var productProcessed = alerts.some(function(alert) {
                            return (
                                        (
                                            alert.section === "product" ||
                                            alert.section === "image"
                                        ) &&
                                        (
                                            alert.type === "success" ||
                                            alert.type === "info"
                                        )
                                    );
                        });

                        /*
                        * Refresh the product table when something was updated.
                        */
                        if (ref === "product" && hasSuccessfulUpdate) {
                            parent.fetchProducts1($table, PDID);
                        }

                        /*
                        * Close the modal only when the product itself was processed.
                        * Keep it open when the product update failed.
                        */
                        if (productProcessed) {
                            setTimeout(function() {
                                parent.$("#modal").iziModal("close");
                            }, 300);
                        }

                        return;
                    }


                    /*
                    * ADD PRODUCT RESPONSE
                    * Existing functionality is left unchanged.
                    */
                    if (response.status === "success") {
                        setTimeout(function() {
                            parent.$("#modal").iziModal("close");

                            if (ref === "product") {
                                parent.fetchProducts($table, query);
                            }

                            parent.toastr.success(
                                response.message,
                                "Success"
                            );
                        }, 300);
                    } else {
                        parent.toastr.error(
                            response.message,
                            "Error"
                        );
                    }
                },
                error: function(xhr, status, error) {
                    btn.prop("disabled", false);
                    btn.html(btnHtml);
                    console.log("AJAX Error:", error);
                    console.log("Server response:", xhr.responseText);
                    var errorMessage = condition === "edit"
                        ? "An error occurred while updating the product."
                        : "An error occurred while creating the product.";

                    parent.toastr.error(errorMessage, "Error");
                }
            });
        
    });
    
$(document).ready(function(){

    const dropzone = $("#prodDropzone");
    const fileInput = $("#prod_image");

    // Click dropzone -> open file browser
    dropzone.on("click", function(){
        fileInput[0].click();
    });


    // Drag over
    dropzone.on("dragover", function(e){
        e.preventDefault();
        $(this).addClass("dragover");
    });

    // Drag leave
    dropzone.on("dragleave", function(){
        $(this).removeClass("dragover");
    });


    // DROP FILE
    dropzone.on("drop", function(e){

        e.preventDefault();
        $(this).removeClass("dragover");

        let files = e.originalEvent.dataTransfer.files;

        if(files.length > 0){

            // Assign dropped file to input
            let dt = new DataTransfer();
            dt.items.add(files[0]);

            fileInput[0].files = dt.files;

            // Trigger change event
            fileInput.trigger("change");
        }

    });


    // ===== YOUR VALIDATION CODE =====

    $("#prod_image").on("change", function() {

        let file = this.files[0];
        let allowedExtensions = ['jpg','JPG','png','PNG','jpeg','JPEG','webp', 'WEBP'];
        let maxSize = 5 * 1024 * 1024;

        // $("#danger").hide();
        // $("#success").hide();

        if(file) {

            let fileExt = file.name.split(".").pop().toLowerCase();

            if(!allowedExtensions.includes(fileExt)) {

                // $("#danger").text("Invalid file type. Only jpg, png, jpeg, webp are allowed.").show();
                parent.toastr.error("Invalid file type. Only jpg, png, jpeg, webp are allowed.", "Error");

                $(this).val("");
                $("#img_product").attr("src","../../Assets/Images/icons/product.png");

                return false;
            }

            if(file.size > maxSize) {

                // $("#danger").text("Product image size cannot be higher than 5MB.").show();
                parent.toastr.error("Product image size cannot be higher than 5MB.","Error");

                $(this).val("");
                $("#img_product").attr("src","../../Assets/Images/icons/product.png");

                return false;
            }

            let reader = new FileReader();

            reader.onload = function(e){
                $("#img_product").attr("src", e.target.result);
            }

            reader.readAsDataURL(file);

            // $("#success").text("Image looks good!").show();
            parent.toastr.success("Image looks good!","Success");
        }
        else{

            $("#danger").hide();
            $("#success").hide();
            $("#img_product").attr("src","../../Assets/Images/icons/product.png");

            parent.toastr.warning("Product image deselected.","Warning");
        }

    });
  $("#refresh-main-cat").on("click", function(){
    allCategory();
  })
  allCategory();

});
function allCategory()
{
    var id=1;
    var selected=$("#category").val();
    return $.get("../../AJAX/guiPos/getmaincat.php", {
        supplier_id : id
    }, function(data){
        // alert(data);
        const obj = JSON.parse(data);

        var html="<option value='' selected>Main Category</option>";
        obj.forEach(function(item) {
            html += `<option value='${item.CTID}' ${selected == item.CTID ? "selected" : ""}>${item.CategoryNo} - ${item.CategoryName} </option>`;
        });
        $("#cmb_category").html(html);
        $("#cmb_subcategory").html("");
    });
}
$("#cmb_category").change(function(){
  subcat2();
});
function subcat2()
{
    // var id=1;
    var maincat=$("#cmb_category").val();
    if(maincat=="")
    {
        parent.toastr.error("Select A Main Category", "Error");
        $("#cmb_category").focus();
    }
    else
    {
        var selected=$("#cmb_subcategory").val();
        return $.get("../../AJAX/guiPos/getsubcat.php", {
            maincat : maincat
        }, function(data){
            // alert(data);
            const obj = JSON.parse(data);

            var html="<option value=''>Sub Category</option>";
            obj.forEach(function(item) {
                html += `<option value='${item.SCID}' ${selected == item.SCID ? "selected" : ""}>${item.SubCatNo} - ${item.SubCatName} </option>`;
            });
            $("#cmb_subcategory").html(html);
        });
    }
}
  </script>
</body>
</html>