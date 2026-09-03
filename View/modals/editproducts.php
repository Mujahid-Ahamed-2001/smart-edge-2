<style>
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
<div id="editproduct_modal" class="modal fade show" tabindex="-1" aria-labelledby="bs-example-modal-md" aria-modal="true" role="dialog" style="display: none; background: #00000075;">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header d-flex align-items-center">
          <h4 class="modal-title" id="myModalLabel">
            Edit Products
          </h4>
          <button type="button" class="btn-close" id="close_products_modal" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        
        <form action="../Controller/productController.php?query=update" id="editProduct" method="POST"  enctype="multipart/form-data">
          <input type="hidden" name="hidden_PDID" class="d-none" id="hidden_PDID">
        <div class="row">
            <!-- hidden input -->
            <div class="form-check form-switch" id="statuss">
                <input class="form-check-input" type="checkbox" id="editprodStatus" name="editprodStatus" value="1">
                <label class="form-label" class="form-check-label" for="status">Status</label>
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
                    <label class="form-label" for="editcmb_category">Category <span class="text-danger text-alrt" >*</span></label>
                    <select name="editcmb_category" id="editcmb_category" class="form-select" required>
                    </select>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="editsmb_subcategory">Sub Category <span class="text-danger text-alrt" >*</span></label>
                    <select name="editcmb_subcategory" id="editcmb_subcategory" class="form-select" required>
                    </select>
                  </div>
                </div>
              </div>
              <?php 
            }//has categories
            else
            {
              ?>  
              <input type="hidden" name="editcmb_category" id="editcmb_category" class="d-none" value="1">
              <input type="hidden" name="editcmb_subcategory" id="editcmb_subcategory" class="d-none" value="1">
              <?php
            }
            ?>

            <div class="col-md-6 mb-3">
              <label class="form-label" for="editbarcode">Barcode</label>
              <input type="text" name="editbarcode" id="editbarcode" class="form-control mb-2 " placeholder="Barcode">
              <span style="display:none; color:red;" id="barcode_warning"></span>
            </div> 
            
            <div class="col-md-6 mb-3">
              <label class="form-label" for="editprod_name">Product Name <span class="text-danger text-alrt" >*</span></label>
              <input type="text" name="editprod_name" id="editprod_name" class="form-control mb-2 required" placeholder="Product Name" required>
            </div>
            <script>
              $(document).on("input", "#editprod_name", function () {
                  let cleanValue = $(this).val().replace(/[^a-zA-Z0-9\s\-_]/g, ""); // Allows only letters, numbers, - , _ , and spaces
                  $(this).val(cleanValue);
              });
            </script>
            <?php 
            if($shopObj->hasSecondLanguage($shop_id))
            {
              ?>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="editsecond_name" >Second Name </label>
                  <input type="text" name="editsecond_name" id="editsecond_name" class="form-control mb-2 required" placeholder="Second Name">
                </div>
              <?php 
            }//has second language
            ?>
            
            <!-- description -->
            <div class="col-md-6 mb-3">
              <label class="form-label" for="editprod_description" >Description (optional)</label>
              <textarea name="editprod_description" id="editprod_description" cols="30" rows="3" class="form-control" aria-label="Description">
              </textarea>
            </div>

            <?php 
              ?>
              <div class="col-md-12">
                <span class="text-danger">Note: - Prices must be entered in the selling unit price.</span>
              </div>
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="editprod_purchase_price" >Purchase Price <span class="text-danger text-alrt" >*</span></label>
                    <input type="number" step="any" name="editprod_purchase_price" id="editprod_purchase_price" class="form-control mb-2" placeholder="0.00 " value="0.00" required>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="editprod_selling_price" >Selling Price <span class="text-danger text-alrt" >*</span></label>
                    <input type="number" step="any" name="editprod_selling_price" id="editprod_selling_price" class="form-control mb-2" placeholder="0.00 " value="0.00" required>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="editprod_Item_Dis" >Item Discount </label>
                    <input type="text" name="editprod_Item_Dis" id="editprod_Item_Dis" class="form-control" value="0.00">
                  </div>   
                  
                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="editprod_Item_Dis_flat" >Item Flat Discount </label>
                    <input type="text" name="editprod_Item_Dis_flat" id="editprod_Item_Dis_flat" class="form-control" value="0.00">
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
                <label class="form-label" for="editchk_service">Service</label>
                <div class="form-check form-switch mb-3">
                  <input class="form-check-input" name="editchk_service" id="editchk_service" type="checkbox">
                  <label class="form-label" class="form-check-label" for="chk_service">Use as Service</label>
                </div>
              </div>
              <?php 
            }//has service
            ?>
            <div class="col-md-6 service">
              <label class="form-label" for="editchk_fp">FP</label>
              <div class="form-check form-switch mb-3">
                <input class="form-check-input" name="editchk_fp" id="editchk_fp" type="checkbox">
                <label class="form-label" class="form-check-label" for="editchk_fp">Fixed Price</label>
              </div>
            </div>
            
            <!-- Units -->
            <div class="col-md-12 p-2 border border-primary rounded">
              <div class="row">
                <div class="mb-1 col-4">
                  <label class="form-label" for="editcmb_purchase_unit" >Purchase Unit</label>
                  <select name="editcmb_purchase_unit" id="editcmb_purchase_unit" class="form-select dropdown-toggle">
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
                  <label class="form-label" for="editconversion_rate" >Conversion Rate</label>
                  <input type="number" step="0.001" value="1" name="editconversion_rate" id="editconversion_rate" class="form-control mb-2" placeholder="Conversion Rate" required>
                </div>
                  
                <div class="mb-1 col-4">
                  <label class="form-label" for="" >Selling Unit</label>
                  <select name="editcmb_selling_unit" id="editcmb_selling_unit" class="form-select dropdown-toggle">
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
              <div id="editprodDropzone" class="dropzone border rounded-3 p-3 text-center bg-light" role="button" tabindex="0">
                <div class="d-flex flex-column align-items-center gap-1">
                  <div class="dropzone-icon">📁</div>
                  <div class="fw-semibold">Drag & drop image here</div>
                  <div class="text-muted small">or click to browse</div>
                  <div class="text-muted small">Allowed: JPG, JPEG, PNG, WEBP • Max: 5MB</div>
                </div>

                <!-- keep your actual file input (hidden) -->
                <input type="file" name="editprod_image" id="editprod_image" class="d-none" accept="image/*">
              </div>

              <div class="mt-2">
                <span class="text-success d-none" id="editsuccess">Ok</span>
                <span class="text-danger d-none" id="editdanger">⚠ Image too large or invalid type.</span>
              </div>

              <div class="mt-3 rounded">
                <img src="../Assets/Images/icons/product.png" id="editimg_product"
                    class="shadow img-fluid mx-auto d-block rounded bordered"
                    alt="Product Image">
              </div>
            </div>
        </div>

        <div class="modal-footer">
            <!-- save -->
            <button type="submit" name="btn_update_product" class="btn bg-primary-subtle text-primary waves-effect" id="btn_update_product">
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

    const dropzone = $("#editprodDropzone");
    const fileInput = $("#editprod_image");

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

    $("#editprod_image").on("change", function() {

        let file = this.files[0];
        let allowedExtensions = ['jpg','JPG','png','PNG','jpeg','JPEG','webp', 'WEBP'];
        let maxSize = 5 * 1024 * 1024;
        let defaultSrc = $("#editimg_product").attr("src");

        $("#editdanger").hide();
        $("#editsuccess").hide();

        if(file) {

            let fileExt = file.name.split(".").pop().toLowerCase();

            if(!allowedExtensions.includes(fileExt)) {

                $("#editdanger").text("Invalid file type. Only jpg, png, jpeg, webp are allowed.").show();
                toastr.error("Invalid file type. Only jpg, png, jpeg, webp are allowed.", "Error");

                $(this).val("");
                $("#editimg_product").attr("src",defaultSrc);

                return false;
            }

            if(file.size > maxSize) {

                $("#editdanger").text("Product image size cannot be higher than 5MB.").show();
                toastr.error("Product image size cannot be higher than 5MB.","Error");

                $(this).val("");
                $("#editimg_product").attr("src",defaultSrc);

                return false;
            }

            let reader = new FileReader();

            reader.onload = function(e){
                $("#editimg_product").attr("src", e.target.result);
            }

            reader.readAsDataURL(file);

            $("#editsuccess").text("Image looks good!").show();
        }
        else{

            $("#editdanger").hide();
            $("#editsuccess").hide();
            $("#editimg_product").attr("src",defaultSrc);

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
  