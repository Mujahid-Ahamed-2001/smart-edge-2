<div id="category_modal" class="modal fade show" tabindex="-1" aria-labelledby="bs-example-modal-md" aria-modal="true" role="dialog" style="display: none; background: #00000075;">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header d-flex align-items-center">
          <h4 class="modal-title" id="myModalLabel">
            Add New Category
          </h4>
          <button type="button" class="btn-close" id="close_category_modal" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">        
          <form action="../Controller/addNewCategory.php" method="POST" id="category_form_save">
            <div class="row">
              <!-- left column -->
              <div class="col">
                <!-- hidden input -->
                <label for="cat_name" class="form-label">Category Name</label>
                <input type="text" name="cat_name" id="cat_name" class="form-control mb-2" placeholder="Category Name" required>
                <span class="text-danger" id="category_danger" style="display: none;">&#9888; Category already exists.</span>
              </div>
            </div>

          <div class="modal-footer">
            <!-- save -->
            <button type="submit" name="btn_save_category" class="btn bg-primary-subtle text-primary waves-effect" id="btn_save_category">
              Save
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