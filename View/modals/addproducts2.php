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
<div id="product_modal" class="modal fade show" tabindex="-1" aria-labelledby="bs-example-modal-md" aria-modal="true" role="dialog" style="display: none; background: #00000075;">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header d-flex align-items-center">
          <h4 class="modal-title" id="myModalLabel">
            Add Products
          </h4>
          <button type="button" class="btn-close" id="close_products_modal" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        
        <form action="../Controller/productController.php?query=save" id="addProduct" method="POST"  enctype="multipart/form-data">
          <div class="row">
              <!-- hidden input -->
              <div class="form-check form-switch" id="statuss">
                  <input class="form-check-input" type="checkbox" id="status" name="prodStatus" checked="" value="1">
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
                        <a href="../Public/category.php" class="" window="new" target="_blank">
                        <div class="round-16 d-flex align-items-center justify-content-center">                      
                        </div>
                        <span class="hide-menu">Add Categories</span>
                        </a>
                    </div>                  
                    <div class="col-md-6 mb-3">                     
                        <a href="../Public/subcategory.php" class="" window="new" target="_blank">
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
                  <input type="file" name="prod_image" id="prod_image" class="d-none" accept="image/*">
                </div>

                <div class="mt-2">
                  <span class="text-success d-none" id="success">Ok</span>
                  <span class="text-danger d-none" id="danger">⚠ Image too large or invalid type.</span>
                </div>

                <div class="mt-3 rounded">
                  <img src="../Assets/Images/icons/product.png" id="img_product"
                      class="shadow img-fluid mx-auto d-block rounded bordered"
                      alt="Product Image">
                </div>
              </div>
          </div>

          <div class="modal-footer">
              <!-- save -->
              <button type="submit" name="btn_save_product" class="btn bg-primary-subtle text-primary waves-effect" id="btn_save_product">
                Save
              </button>        
              <button type="button" class="btn bg-danger-subtle text-danger  waves-effect" data-bs-dismiss="modal" id="close_product_modal">
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

        $("#danger").hide();
        $("#success").hide();

        if(file) {

            let fileExt = file.name.split(".").pop().toLowerCase();

            if(!allowedExtensions.includes(fileExt)) {

                $("#danger").text("Invalid file type. Only jpg, png, jpeg, webp are allowed.").show();
                toastr.error("Invalid file type. Only jpg, png, jpeg, webp are allowed.", "Error");

                $(this).val("");
                $("#img_product").attr("src","../Assets/Images/icons/product.png");

                return false;
            }

            if(file.size > maxSize) {

                $("#danger").text("Product image size cannot be higher than 5MB.").show();
                toastr.error("Product image size cannot be higher than 5MB.","Error");

                $(this).val("");
                $("#img_product").attr("src","../Assets/Images/icons/product.png");

                return false;
            }

            let reader = new FileReader();

            reader.onload = function(e){
                $("#img_product").attr("src", e.target.result);
            }

            reader.readAsDataURL(file);

            $("#success").text("Image looks good!").show();
        }
        else{

            $("#danger").hide();
            $("#success").hide();
            $("#img_product").attr("src","../Assets/Images/icons/product.png");

            toastr.warning("Product image deselected.","Warning");
        }

    });
  $("#refresh-main-cat").on("click", function(){
    allCategory();
  })

});
function allCategory()
{
    var id=1;
    var selected=$("#category").val();
    $.get("../AJAX/guiPos/getmaincat.php", {
        supplier_id : id
    }, function(data){
        // alert(data);
        const obj = JSON.parse(data);

        var html="<option value='' selected>Main Category</option>";
        obj.forEach(function(item) {
            html += `<option value='${item.CTID}' ${selected == item.CTID ? "selected" : ""}>${item.CategoryNo} - ${item.CategoryName} </option>`;
        });
        $("#category").html(html);
        $("#cmb_category").html(html);
        $("#cmb_subcategory").html("");
    });
}
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
  