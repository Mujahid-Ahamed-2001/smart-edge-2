<div class="modal" tabindex="-1" role="dialog" id="product_barcode_modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Print Barcode</h5>
        <button type="button" class="btn-close" id="btn_close_barcode_modal" data-dismiss="modal" aria-label="Close">
        </button>
      </div>

      <form action="../Controller/LabelController.php" method="post">
      <div class="modal-body">

        <input type="hidden" name="hide_label_product_id" id="hide_label_product_id" value="0">

        <label for="" class="form-label mt-3">Select Batch</label>
        <select name="cmb_batch_price" id="cmb_batch_price" class="form-select"></select>

        <label for="" class="form-label mt-3">Label Price</label>
        <input type="number" step="0.01" name="label_price" id="label_price" class="form-control">

      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" name="btn_print_barcode">Print Preview</button>
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
      </div>
      </form>

    </div>
  </div>
</div>