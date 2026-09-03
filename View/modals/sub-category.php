<div id="subcategory_modal" class="modal fade show" tabindex="-1" aria-labelledby="bs-example-modal-md" aria-modal="true" role="dialog" style="display: none; background: #00000075;">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header d-flex align-items-center">
          <h4 class="modal-title" id="myModalLabel">
            Add Subcategory
          </h4>
          <button type="button" class="btn-close" id="close_subcategory_modal" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        
        <form action="../Controller/AddSubCat.php?query=save" method="POST" id="add_subcatForm">
        <div class="row">
          <!-- left column -->
          <div class="col-lg-6">
            <label class="form-label" for="cmb_main_category">Select Category</label>
            <div class="input-group">
              <select name="cmb_main_category" id="cmb_main_category" class="form-select mb-2">
                <option value="">Main Category</option>
              </select>
              <a href="javascript:void(0)" id="refresh-main-cat" class="input-group-text mb-2">
                <i class="ti ti-refresh fs-4 "></i>
              </a>
            </div>
          </div>

          <!--- right column -->
          <div class="col-lg-6">
              <label class="form-label" for="subcat_name" class="">Sub category Name</label>
              <input type="text" name="subcat_name" id="subcat_name" class="form-control mb-2" placeholder="Sub Category Name" required>
              <span id="category_danger"></span>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" name="btn_save_subcat" class="btn bg-primary-subtle text-primary waves-effect" id="btn_save_subcategory">
            Save
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
  <script>
   $(document).ready(function () {
      allCategory();

      $("#refresh-main-cat").on("click", function () {
          allCategory();
      });
  });

  function allCategory() {
      var id = 1;
      var selected = $("#category").val();

      $.get("../AJAX/guiPos/getmaincat.php", {
          supplier_id: id
      }, function (data) {

          let obj;

          try {
              obj = JSON.parse(data);
          } catch (e) {
              console.error("Invalid JSON:", data);
              return;
          }

          var html = "<option value='' selected>Main Category</option>";

          obj.forEach(function (item) {
              html += `<option value="${item.CTID}" ${selected == item.CTID ? "selected" : ""}>
                          ${item.CategoryNo} - ${item.CategoryName}
                      </option>`;
          });

          $("#cmb_main_category").html(html);
      }).fail(function () {
          console.error("Failed to load categories");
      });
  }
  </script>