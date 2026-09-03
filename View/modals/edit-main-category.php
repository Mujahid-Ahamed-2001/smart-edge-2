<div id="edit_category_modal" class="modal fade show" tabindex="-1" aria-labelledby="bs-example-modal-md" aria-modal="true" role="dialog" style="display: none; background: #00000075;">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header d-flex align-items-center">
          <h4 class="modal-title" id="myModalLabelEdit">
            Edit Category
          </h4>
          <button type="button" class="btn-close" id="close_category_modal" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">        
          <form action="../Controller/editCategory.php" method="POST" id="category_form_update">
            <div class="row">
              <!-- left column -->
              <div class="col">
                <!-- hidden input -->
                <input type="hidden" name="hide_category_id" id="hide_category_id" value="0">
                <label for="edit_cat_name" class="form-label">Category Name</label>
                <input type="text" name="edit_cat_name" id="edit_cat_name" class="form-control mb-2" placeholder="Category Name" required>
                <span class="text-danger" id="edit_category_danger" style="display: none;">&#9888; Category already exists.</span>
              </div>
            </div>

          <div class="modal-footer">
            <!-- save -->
            <button type="submit" name="btn_update_category" class="btn bg-primary-subtle text-primary waves-effect" id="btn_update_category">
              Update
            </button>
            <!-- close -->
            <button type="button" class="btn bg-danger-subtle text-danger  waves-effect" data-bs-dismiss="modal" id="close_category_modal">
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