<div id="grn_modal" class="modal fade" aria-labelledby="bs-example-modal-md" aria-modal="true" role="dialog" style="background: #00000075;">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header d-flex align-items-center">
          <h4 class="modal-title" id="myModalLabel">
            Add GRN
          </h4>
          <button type="button" class="btn-close" id="close_grn_modal" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="grn_create" action="../Controller/grnController.php" method="post">
            <div class="row">
              <div class="col-md-6">
                <div class="m-2">
                  <label for="grn_no" class="form-label">GRN No <span class="text-danger">*</span></label>
                  <input type="text" name="grn_no" id="grn_no" class="form-control" readonly>
                </div>
              </div>
  
              <?php 
              $shopObj = new Shop();
                  ?>
                  <div class="col-md-6">
                    <div class="m-2">
                      <label for="cmb_supplier" class="form-label">Select Supplier <span class="text-danger">*</span></label><br>
                      <select name="cmb_supplier" id="cmb_supplier" class="form-select" required>
                      </select>
                    </div>
                  </div>

              <div class="col-md-12">
                <div class="m-2">
                  <label for="Reference" class="form-label">Reference</label>
                  <input type="text" name="Reference" id="Reference" class="form-control">
                </div>
              </div>

              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" name="btn_add_grn" class="btn bg-primary-subtle text-primary  waves-effect" id="btn_add_grn">
                Create New GRN
              </button>
              <button type="submit" name="btn_add_grn_items" class="btn bg-primary-subtle text-primary  waves-effect" id="btn_add_grn_items">
                Create GRN & Enter Items
              </button>
              <button type="button" class="btn bg-warning-subtle text-warning  waves-effect" data-bs-dismiss="modal" id="close_grn_modal">
                Close
              </button>
            </div>
          </form>
      </div>
      <!-- /.modal-content -->
    </div>
  <!-- /.modal-dialog -->
  </div>
  <script>
    function initializegrnSupplierSelect2() {    
      var shop_id = $("#shop_id").val();
      $("#cmb_supplier").select2({
          dropdownParent: $('#grn_modal'),
          ajax: {
              url: '../AJAX/GRN/getSuppliers.php',
              dataType: 'json',
              delay: 250,
              cache: true,
              data: function (params) {
                  return {
                      search: params.term,
                      type: 'item_search',
                      shop_id: shop_id
                  };
              },
              processResults: function (data) {
                  return { results: data };
              }
          },
          placeholder: 'Select Suppliers',
          minimumInputLength: 1,
          width: '100%'
      }).on('select2:open', function () {
          $('.select2-search__field').focus();
      });
  }
  $(document).ready(function () {
    $(document).on("click", "#btn_open_grn", function () {
      if ($("#cmb_supplier").hasClass("select2-hidden-accessible")) {
          $("#cmb_supplier").select2("destroy");
      }

      initializegrnSupplierSelect2();

      $("#grn_modal").modal('toggle');

      $.get("../AJAX/GRN/getGRNNo.php", function (data) {
          $("#grn_no").val(data);
      });
    });
    let clickedBtn = null;

    $("#grn_create button[type=submit]").on("click", function () {
        clickedBtn = $(this);
    });
    $("#grn_create").on("submit",function(e){
      e.preventDefault();
      var formData = $(this).serialize();
      var url = $(this).attr('action');
      let btn = $("#btn_add_grn");      
      let btnHtml = btn.html(); 
      let submitter = e.originalEvent.submitter;
      let btnID = submitter ? submitter.id : (clickedBtn ? clickedBtn.attr("id") : null);
      console.log("btnID "+btnID);
      
      $.ajax({
          url: url+"?btn_add_grn=1",
          type: 'POST',
          data: formData,
          beforeSend: function() {
              btn.prop("disabled", true);
              btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
          },
          success: function(response) {
            btn.prop("disabled", false);
            btn.html(btnHtml);
            var res = JSON.parse(response);
            var shop_id = $("#shop_id").val();
            if(res.status == 'success')
            {
              toastr.success(res.message, "Success");
              $("#grn_modal").modal('toggle');
              fetchGRNData(shop_id);
              $("#cmb_supplier").val('').trigger('change');
              $("#cmb_supplier").select2("destroy");
              initializegrnSupplierSelect2();
              $("#grn_create")[0].reset();
              if(btnID === "btn_add_grn_items"){
                setTimeout(function(){
                  window.location.href = "./grn-details.php?grn_header="+res.ghid;
                }, 1000);
              }
            }
            else
            {
              toastr.error(res.message, "Error");
            }
          },
          error: function(xhr, status, error) {
            btn.prop("disabled", false);
            btn.html(btnHtml);
            console.log("AJAX Error:", error);
            toastr.error("An error occurred inserting GRN data.", "Error");
          }
      });
    });
  });

  </script>