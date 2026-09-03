<?php 
session_start();
$shop_id = $_SESSION['shop_id'];
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
include "../../Model/shop_class.php";
$dbObj = new DBTransactions(); 
$sql="SELECT * FROM countries";
$country_codes = $dbObj->getData($sql);
$UserType = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : 0;

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
    <link rel="stylesheet" href="../../Assets/css/modal.css">

    <link rel="stylesheet" href="../../vendor/select2-develop/dist/css/select2.min.css">
    <script src="../../vendor/select2-develop/dist/js/select2.min.js"></script>
    <script src="../../Assets/jquery/toast.js"></script>
</head>
<body>
<?php 
$condition = $_GET["condition"];
if(!isset($condition) || empty($condition)) {
    ?>
    <script>
        parent.toastr.error("Error: Condition is missing.", "Error");
        parent.$("#modal").iziModal("close");
    </script>
    <?php
}
$title ="";
$subtitle ="";
$CTID ="";
$action ="../../Controller/productController.php";
$btnText ="<i class='ti ti-device-floppy'></i> Update Products";
if($condition=="multiEdit")
{
    $title ="Update Multiple Products";
    $subtitle ="Fill only the details you want to edit";
    $btnText ="<i class='ti ti-device-floppy'></i> Update Products";
    $action .="?query=multiEdit";
}
else
{
    ?>
    <script>
        parent.toastr.error("Error: Invalid condition.", "Error");
        parent.$("#modal").iziModal("close");
    </script>
    <?php
}
?>
<script>
    allCategory3();
    function allCategory3()
    {
        var id=1;
        var selected=$("#multieditcmb_category").val();
        $("#multieditcmb_subcategory").html("");
        $.get("../../AJAX/guiPos/getmaincat.php", {
            supplier_id : id
        }, function(data){
            // alert(data);
            const obj = JSON.parse(data);

            var html="<option value=''>Main Category</option>";
            obj.forEach(function(item) {
                html += `<option value='${item.CTID}'>${item.CategoryNo} - ${item.CategoryName} </option>`;
            });
            $("#multieditcmb_category").html(html);
        });
    }
    function subcat4()
    {
        // var id=1;
        var maincat=$("#multieditcmb_category").val();
        if(maincat=="")
        {
            parent.toastr.error("Select A Main Category","Error");
            $("#multieditcmb_category").focus();
        }
        else
        {
            var selected=$("#cmb_subcategory").val();
            $.get("../../AJAX/guiPos/getsubcat.php", {
                maincat : maincat
            }, function(data){
                // alert(data);
                const obj = JSON.parse(data);

                var html="<option value=''>Sub Category</option>";
                obj.forEach(function(item) {
                    html += `<option value='${item.SCID}' ${selected == item.SCID ? "selected" : ""}>${item.SubCatNo} - ${item.SubCatName} </option>`;
                });
                $("#multieditcmb_subcategory").html(html);
            });
        }
    }
    $(document).on("change", "#multieditcmb_category",function(){  
        subcat4();
    });
    $(document).on("click", "#refresh-cat",function(e){
        e.preventDefault();
        allCategory3();
    });
</script>
<?php 
if (empty($_GET["product_ids"])) {
    ?>
    <script>
        parent.toastr.error("No products selected to edit", "Error");
        parent.$("#modal").iziModal("close");
    </script>
    <?php
    exit;
}

$productIds = array_values(
    array_unique(
        array_filter(
            array_map(
                "intval",
                explode(",", $_GET["product_ids"])
            ),
            function ($id) {
                return $id > 0;
            }
        )
    )
);

if (empty($productIds)) {
    ?>
    <script>
        parent.toastr.error("No valid products selected", "Error");
        parent.$("#modal").iziModal("close");
    </script>
    <?php
    exit;
}

$productIdsValue = implode(",", $productIds);
?>
<script>
$(document).ready(function(){
    
    

    const dropzone = $("#multieditprodDropzone");
    const fileInput = $("#multieditprod_image");

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

    $("#multieditprod_image").on("change", function() {

        let file = this.files[0];
        let allowedExtensions = ['jpg','JPG','png','PNG','jpeg','JPEG','webp', 'WEBP'];
        let maxSize = 5 * 1024 * 1024;
        let defaultSrc = "../../Assets/Images/icons/product.png";

        $("#multieditdanger").hide();
        $("#multieditsuccess").hide();

        if(file) {

            let fileExt = file.name.split(".").pop().toLowerCase();

            if(!allowedExtensions.includes(fileExt)) {

                $("#multieditdanger").text("Invalid file type. Only jpg, png, jpeg, webp are allowed.").show();
                parent.toastr.error("Invalid file type. Only jpg, png, jpeg, webp are allowed.", "Error");

                $(this).val("");
                $("#multieditimg_product").attr("src",defaultSrc);

                return false;
            }

            if(file.size > maxSize) {

                $("#multieditdanger").text("Product image size cannot be higher than 5MB.").show();
                parent.toastr.error("Product image size cannot be higher than 5MB.","Error");

                $(this).val("");
                $("#multieditimg_product").attr("src",defaultSrc);

                return false;
            }

            let reader = new FileReader();

            reader.onload = function(e){
                $("#multieditimg_product").attr("src", e.target.result);
            }

            reader.readAsDataURL(file);

            $("#multieditsuccess").text("Image looks good!").show();
                parent.toastr.success("Image looks good!", "Good");
        }
        else{

            $("#multieditdanger").hide();
            $("#multieditsuccess").hide();
            $("#multieditimg_product").attr("src",defaultSrc);

            toastr.warning("Product image deselected.","Warning");
        }

    });

});
</script>
<div class="modal-form-wrapper">

    <!-- Header -->
    <div class="modal-form-header">
        <div class="modal-header-icon">
            <i class='ti ti-device-floppy'></i>
        </div>

        <div>
            <h3 class="modal-form-title"><?=$title?></h3>
            <p class="modal-form-subtitle">
                <?=$subtitle?>
            </p>
        </div>
    </div>
    <form action="<?=$action?>" id="modalForm" method="post" enctype="multipart/form-data">
        <input type="hidden" name="bulkhidden_PDID" class="d-none" id="bulkhidden_PDID" value="<?=$productIdsValue?>">
        <!-- Form Card -->
        <div class="modal-form-card">
            
            <div class="row g-4">
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
                <div class="col-lg-12 d-flex justify-content-end align-items-start gap-2 mt-4">
                    <a href="../../Public/category.php"
                    class="btn btn-primary shadow-btn"
                    target="_blank">
                        <i class="ti ti-category-2"></i>
                        Add Categories
                    </a>

                    <a href="../../Public/category.php"
                    class="btn btn-primary shadow-btn"
                    target="_blank">
                        <i class="ti ti-category-2"></i>
                        Add Sub Categories
                    </a>
                </div>
                <div class="col-lg-12 row mt-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="multieditcmb_category">Category</label>                        
                        <div class="filter-group">
                            <select name="multieditcmb_category" id="multieditcmb_category" class="form-select modal-input">
                            </select>
                            <button type="button" class="btn-refresh" id="refresh-cat">
                                <i class="ti ti-refresh"></i>
                            </button>  
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="multieditsmb_subcategory">Sub Category</label>
                        <select name="multieditcmb_subcategory" id="multieditcmb_subcategory" class="form-select modal-input">
                        </select>
                    </div>
                </div>
                <?php
            }
            ?>    
                <div class="col-lg-4 row">
                    <div class="mb-3 mt-4">
                        <label class="form-label d-block">Status</label>
                        <div class="d-flex align-items-center gap-4">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" name="status" id="status_active" value="1">
                                <label class="form-check-label" for="status_active">
                                    Active
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" name="status" id="status_inactive" value="0">
                                <label class="form-check-label" for="status_inactive">
                                    Inactive
                                </label>
                            </div>
                        </div>
                    </div>                 
                </div> 
                <div class="col-lg-4 row">
                    <div class="mb-3 mt-4">
                        <label class="form-label d-block">Fixed Price</label>
                        <div class="d-flex align-items-center gap-4">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" name="chk_fp" id="fp_active" value="1">
                                <label class="form-check-label" for="fp_active">
                                    Fixed Price
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" name="chk_fp" id="fp_inactive" value="0">
                                <label class="form-check-label" for="fp_inactive">
                                    Non-Fixed Price
                                </label>
                            </div>
                        </div>
                    </div>                  
                </div> 
                <div class="col-lg-4 row">
                    <div class="mb-3 mt-4">
                        <label class="form-label d-block">Low Stock Alert</label>
                        <div class="d-flex align-items-center gap-4">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" name="chk_ls" id="ls_active" value="1">
                                <label class="form-check-label" for="ls_active">
                                    Active
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" name="chk_ls" id="ls_inactive" value="0">
                                <label class="form-check-label" for="ls_inactive">
                                    Inactive
                                </label>
                            </div>
                        </div>
                    </div>               
                </div> 
                <div class="col-lg-12">
                    <span class="text-danger">Note: - Prices must be entered in the selling unit price.</span>                  
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="multieditprod_purchase_price" >Purchase Price</label>
                            <input type="number" step="0.01" name="multieditprod_purchase_price" id="multieditprod_purchase_price" class="form-control mb-2  modal-input" placeholder="0.00 ">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="multieditprod_selling_price" >Selling Price</label>
                            <input type="number" step="0.01" name="multieditprod_selling_price" id="multieditprod_selling_price" class="form-control mb-2  modal-input" placeholder="0.00 ">
                        </div>
                    </div>
                </div>   
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="multieditprod_Item_Dis" >Item Discount </label>
                            <input type="text" name="multieditprod_Item_Dis" id="multieditprod_Item_Dis" class="form-control  modal-input" step="0.01" placeholder="0.00 ">
                        </div>                           
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="multieditprod_Item_Dis_flat" >Item Flat Discount </label>
                            <input type="text" name="multieditprod_Item_Dis_flat" id="multieditprod_Item_Dis_flat" class="form-control  modal-input" step="0.01" placeholder="0.00 ">
                        </div>   
                    </div>
                </div> 
                <div class="col-md-6 mb-3 image">
                    <label class="form-label mt-2">Product Image</label>

                    <!-- Drag & Drop Area -->
                    <div id="multieditprodDropzone" class="dropzone border rounded-3 p-3 text-center bg-light" role="button" tabindex="0">
                        <div class="d-flex flex-column align-items-center gap-1">
                        <div class="dropzone-icon">📁</div>
                        <div class="fw-semibold">Drag & drop image here</div>
                        <div class="text-muted small">or click to browse</div>
                        <div class="text-muted small">Allowed: JPG, JPEG, PNG, WEBP • Max: 5MB</div>
                        </div>

                        <!-- keep your actual file input (hidden) -->
                        <input type="file" name="multieditprod_image" id="multieditprod_image" class="d-none" accept="image/*">
                    </div>

                    <div class="mt-2">
                        <span class="text-success d-none" id="multieditsuccess">Ok</span>
                        <span class="text-danger d-none" id="multieditdanger">⚠ Image too large or invalid type.</span>
                    </div>

                    <div class="mt-3 rounded">
                        <img src="../../Assets/Images/icons/product.png" id="multieditimg_product"
                            class="shadow img-fluid mx-auto d-block rounded bordered"
                            alt="Product Image">
                    </div>
                </div>  
            </div>

        </div>

        <!-- Footer -->
        <div class="modal-form-footer">

            <button type="button" class="btn btn-danger modal-btn-cancel">
                <i class="ti ti-x"></i>
                Cancel
            </button>

            <button type="submit" class="btn modal-btn-save" id="submitBtn">
                <?=$btnText?>
            </button>

        </div>    
    </form>
    <script>
        $(".modal-btn-cancel").on("click", function() {
            parent.$("#modal").iziModal("close");
        });
        let $condition = "<?=$condition?>";
        let ref = "<?=$_GET['ref']?>";
        let $UserType = "<?=$UserType?>";
        if($condition==="edit")
        {
            if($UserType != 1 && is_default == 1) 
            {
                parent.toastr.warning("You do not have permission to edit the default expense type.", "Warning");
                parent.$("#modal").iziModal("close");
            }
        }
        
        $("#modalForm").on("submit", function(e){
            e.preventDefault();
            var url = $(this).attr("action")
            var formData = new FormData(this);
            let submitter = e.originalEvent.submitter;
            let btnID = submitter ? submitter.id : (clickedBtn ? clickedBtn.attr("id") : null);
            var btnHTML1= $("#submitBtn").html();
            let $table = parent.$('#tbl_products');
            $.ajax({
                url: url,
                method: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $("#submitBtn").prop("disabled", true).html("Updating.....")
                },
                success: function(response){

                    var hasProductUpdated = false;
                    if (response.success && Array.isArray(response.success)) {
                        response.success.forEach(function(successMsg) {
                            let msg = Array.isArray(successMsg) ? successMsg[0] : successMsg;
                            parent.toastr.success(msg, "Success");

                            if (msg === "Product subcategory updated successfully" || msg === "Product % discount updated successfully" || msg === "Product flat discount updated successfully" || msg === "Product updated to service successfully" || msg === "Product updated to fixed price successfully" || msg ==="Product purchase price updated successfully" || msg ==="Product selling price updated successfully" || msg==="Product image updated successfully") {
                                hasProductUpdated = true;
                            }
                        });
                    }
                    if (response["error"]) {
                        if (Array.isArray(response["error"])) {
                            response["error"].forEach(function(errorMsg) {
                                parent.toastr.error(errorMsg, "Error");
                            });
                        } else {
                            parent.toastr.error(response["error"], "Error");
                        }
                    }
                        var PDID = $("#bulkhidden_PDID").val();
                        let pdidArray = PDID.split(",");
                        if(ref=="product")
                        {
                            pdidArray.forEach(function(id) {
                                id = $.trim(id);
                                if (id !== "") {
                                    parent.fetchProducts1($table, id);
                                }
                            });
                            parent.$("#mutlyEdit").stop(true, true).slideUp(200);
                        }

                        parent.$("#modal").iziModal("close");

                },
                complete: function(response){
                    $("#submitBtn").prop("disabled", false).html(btnHTML1);
                },
                error: function (xhr, status, error) {
                    $("#submitBtn").prop("disabled", false).html(btnHTML1);
                    console.log("AJAX Error:", status, error);
                    console.log("Response:", xhr.responseText);
                    parent.toastr.error("An error occurred while processing.","Error");
                }
            })
        });
        
    </script>

</div>  
</body>
</html>

