<div id="edit_subcategory_modal" class="modal fade show" tabindex="-1" aria-labelledby="bs-example-modal-md" aria-modal="true" role="dialog" style="display: none; background: #00000075;">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header d-flex align-items-center">
          <h4 class="modal-title" id="editmyModalLabel">
            Edit Subcategory
          </h4>
          <button type="button" class="btn-close" id="close_subcategory_modal" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        
        <form action="../Controller/AddSubCat.php?query=update" method="POST" id="update_subcatForm">
          <!-- hidden input -->
          <input type="hidden" name="hide_subcat_id" id="hide_subcat_id" value="0">
        <div class="row">
          <!-- left column -->
          <div class="col-lg-6">
            
            <label class="form-label" for="edit_cmb_main_category">Select Category</label>
            <select name="edit_cmb_main_category" id="edit_cmb_main_category" class="form-select dropdown-toggle mb-2" required>

            <?php 
            $dbObj = new DBTransactions();

            $sql = "SELECT * FROM `categories`;";

            $catDatas = $dbObj->getData($sql);

            foreach($catDatas as $rows)
            {
                ?>
                <option value="<?php echo $rows['CTID']?>"><?php echo $rows['CategoryName'];?></option>
                <?php 
            }//foreach
            ?>
            </select>
          </div>

          <!--- right column -->
          <div class="col-lg-6">
              <label class="form-label" for="edit_subcat_name" class="">Sub category Name</label>
              <input type="text" name="edit_subcat_name" id="edit_subcat_name" class="form-control mb-2" placeholder="Sub Category Name" required>
              <span id="edit_category_danger"></span>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" name="btn_save_subcat" class="btn bg-primary-subtle text-primary waves-effect" id="btn_update_subcategory">
            Update
          </button>
          <button type="button" class="btn bg-danger-subtle text-danger waves-effect" data-bs-dismiss="modal" id="close_subcategory_modal">
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