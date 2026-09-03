<div id="multieditproducts_modal" class="modal fade show" tabindex="-1" aria-labelledby="bs-example-modal-md" aria-modal="true" role="dialog" style="display: none; background: #00000075;">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header d-flex align-items-center">
          <h4 class="modal-title" id="myModalLabel">
            Bulk Product Management
          </h4>
          <button type="button" class="btn-close" id="close_products_modal" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        
        <form action="../Controller/productController.php?query=multiEdit" id="multieditproducts" method="POST"  enctype="multipart/form-data">
          <input type="hidden" name="bulkhidden_PDID" class="d-none" id="bulkhidden_PDID">
        <div class="row">      
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
                      <a href="../Public/category.php" class="sidebar-link sidebar-link2" window="new" target="_blank">
                      <div class="round-16 d-flex align-items-center justify-content-center">                      
                      </div>
                      <span class="hide-menu">Add Categories</span>
                      </a>
                  </div>                  
                  <div class="col-md-6 mb-3">                     
                      <a href="../Public/subcategory.php" class="sidebar-link sidebar-link2" window="new" target="_blank">
                      <div class="round-16 d-flex align-items-center justify-content-center">                      
                      </div>
                      <span class="hide-menu">Add Sub Categories</span>
                      </a>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="multieditcmb_category">Category</label>
                    <select name="multieditcmb_category" id="multieditcmb_category" class="form-select">
                    </select>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="multieditsmb_subcategory">Sub Category</label>
                    <select name="multieditcmb_subcategory" id="multieditcmb_subcategory" class="form-select">
                    </select>
                  </div>
                </div>
              </div>
              <?php 
            }//has categories
            ?>
              <div class="col-md-12">
                <span class="text-danger">Note: - Prices must be entered in the selling unit price.</span>
              </div>
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="multieditprod_purchase_price" >Purchase Price</label>
                    <input type="number" step="any" name="multieditprod_purchase_price" id="multieditprod_purchase_price" class="form-control mb-2" placeholder="0.00 ">
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="multieditprod_selling_price" >Selling Price</label>
                    <input type="number" step="any" name="multieditprod_selling_price" id="multieditprod_selling_price" class="form-control mb-2" placeholder="0.00 ">
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="multieditprod_Item_Dis" >Item Discount </label>
                    <input type="text" name="multieditprod_Item_Dis" id="multieditprod_Item_Dis" class="form-control">
                  </div>   
                  
                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="multieditprod_Item_Dis_flat" >Item Flat Discount </label>
                    <input type="text" name="multieditprod_Item_Dis_flat" id="multieditprod_Item_Dis_flat" class="form-control">
                  </div>   
                </div>
              </div>
            <?php 
            if($shopObj->hasService($shop_id))
            {
              ?>
              <div class="col-md-6 service">
                <label class="form-label" for="multieditchk_service">Service</label>
                <div class="form-check form-switch mb-3">
                  <input class="form-check-input" name="multieditchk_service" id="multieditchk_service" type="checkbox">
                  <label class="form-label" class="form-check-label" for="chk_service">Use as Service</label>
                </div>
              </div>
              <?php 
            }//has service
            ?>
            <div class="col-md-6 service">
              <label class="form-label" for="multieditchk_fp">FP</label>
              <div class="form-check form-switch mb-3">
                <input class="form-check-input" name="multieditchk_fp" id="multieditchk_fp" type="checkbox">
                <label class="form-label" class="form-check-label" for="multieditchk_fp">Fixed Price</label>
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
                <img src="../Assets/Images/icons/product.png" id="multieditimg_product"
                    class="shadow img-fluid mx-auto d-block rounded bordered"
                    alt="Product Image">
              </div>
            </div>
        </div>

        <div class="modal-footer">
            <!-- save -->
            <button type="submit" name="bulk_btn_update_product" class="btn bg-primary-subtle text-primary waves-effect" id="bulk_btn_update_product">
              Update
            </button>        
            <button type="button" class="btn bg-danger-subtle text-danger waves-effect" data-bs-dismiss="modal" id="close_product_modal">
              Close
            </button>

        </div>

        </form>

        </div>
        
      </div>
      <!-- /.modal-content -->
    </div>
  <!-- /.modal-dialog -->
  </div>
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
        let defaultSrc = "../Assets/Images/icons/product.png";

        $("#multieditdanger").hide();
        $("#multieditsuccess").hide();

        if(file) {

            let fileExt = file.name.split(".").pop().toLowerCase();

            if(!allowedExtensions.includes(fileExt)) {

                $("#multieditdanger").text("Invalid file type. Only jpg, png, jpeg, webp are allowed.").show();
                toastr.error("Invalid file type. Only jpg, png, jpeg, webp are allowed.", "Error");

                $(this).val("");
                $("#multieditimg_product").attr("src",defaultSrc);

                return false;
            }

            if(file.size > maxSize) {

                $("#multieditdanger").text("Product image size cannot be higher than 5MB.").show();
                toastr.error("Product image size cannot be higher than 5MB.","Error");

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
  <?php 
  $shopObj=new Shop();
  $hasPrescription=$shopObj->hasPrescription($shop_id);
  if($hasPrescription==1)
  {
    ?>
    <script>
    
      $("#prod_name").keyup(function(){
          var value=$(this).val();
          $("#prod_description").val(value)
      });
    </script>
    <?php
  }
  ?>
  