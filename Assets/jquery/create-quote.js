function openModal(url, title = "Modal"){
  $("#modal").iziModal('destroy');
  $("#modal").iziModal({
      width: "75%",
      overlayClose: true,
      iframe: true,
      iframeURL: url,
      fullscreen: true,
      openFullscreen: false,
      borderBottom: false,
      borderRadius:"10px",
      overlayColor: 'rgba(0, 0, 0, 0.8)',
      responsive: true,
      // iframeHeight: "75vh"
      iframeHeight: window.innerHeight * 0.8
  });
  $("#modal").iziModal('open');
}
function  addOption(option_no, row_index=1) {
let option_html = `<div class="option" id="option-${option_no}" data-option-no="${option_no}">
                                            <div class="d-flex justify-between m-3">
                                                <div class="w-50">
                                                    <h5 class="title"><strong><input type="text" class="border-0 form-control bg-transparent fw-bolder" name="options_name[]"  value="Option ${option_no}"/></strong> </h5>    
                                                </div>
                                                <div class="w-50">
                                                    <div class="d-flex justify-content-end w-100" id="action${option_no}">
                                                        <a href="javascript:void(0);" class="btn btn-primary me-2 add_option" id="add_option"><i class="ti ti-plus"></i> Option</a>
                                                        <a href="javascript:void(0);" class="btn btn-danger delete_option" id="delete_option" data-option-no="${option_no}"><i class="ti ti-trash-x"> Option</i></a> 
                                                    </div>
                                                       
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Item Information</th>
                                                            <th>Item Description</th>
                                                            <th style="text-align:center;">Qty</th>
                                                            <th style="text-align:center;">Rate</th>
                                                            <th style="text-align:center;">Cost</th>
                                                            <th style="text-align:center;">Disc. Type</th>
                                                            <th style="text-align:center;">Disc. (Per Unit)</th>
                                                            <th style="text-align:center;">Total <span class="text-danger">*</span></th>
                                                            <th style="text-align:center;"><a href="javascript:void(0);" class="btn btn-primary me-2 add-row" id="add-row-${option_no}" data-option-no="${option_no}"><i class="ti ti-plus"></i> Row</a></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbl_item_${option_no}">
                                                        <tr class="item-row">
                                                            <td style="min-width:250px;">
                                                                <select  name="options[${option_no}][items][${row_index}][item_id]" id="item_${option_no}_${row_index}" class="item form-select">
                                                                    <option value=""></option>
                                                                </select>
                                                            </td>
                                                            <td style="min-width:350px;">
                                                                <textarea name="options[${option_no}][items][${row_index}][description]" id="description_${option_no}_${row_index}" cols="20" class="form-control description"></textarea>
                                                            </td>
                                                            <td style="min-width:100px;">
                                                                <input type="number" name="options[${option_no}][items][${row_index}][quantity]" id="quantity_${option_no}_${row_index}" class="form-control qty" step="0.01">
                                                            </td>
                                                            <td style="min-width:100px;">
                                                                <input type="number" name="options[${option_no}][items][${row_index}][rate]" id="rate_${option_no}_${row_index}" class="form-control rate" step="0.01">
                                                            </td>
                                                            <td style="min-width:100px;">
                                                                <input type="number" name="options[${option_no}][items][${row_index}][cost]" id="cost_${option_no}_${row_index}" class="form-control cost" step="0.01">
                                                            </td>
                                                            <td style="min-width:100px;">
                                                                <select name="options[${option_no}][items][${row_index}][disc_type]" id="discType_${option_no}_${row_index}" class="form-select disc_type">
                                                                    <option value="1">%</option>
                                                                    <option value="2">Flat</option>
                                                                </select>
                                                            </td>
                                                            <td style="min-width:150px;">
                                                                <input type="number" name="options[${option_no}][items][${row_index}][disc_amount]" id="disc_amount_${option_no}_${row_index}" class="form-control disc_amount" step="0.01">
                                                            </td>
                                                            <td style="min-width:150px;">
                                                                <input type="number" name="options[${option_no}][items][${row_index}][total]" id="total_${option_no}_${row_index}" class="form-control total" step="0.01" readonly>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex justify-content-end" id="action">
                                                                    <a href="javascript:void(0);" class="btn btn-warning me-2 duplicate-row" id="duplicate-row-${option_no}-${row_index}" data-option-no="${option_no}" data-row-index="${row_index}"><i class="ti ti-copy"></i> Row</a>
                                                                    <a href="javascript:void(0);" class="btn btn-danger delete-row" id="delete-row-${option_no}-${row_index}" data-option-no="${option_no}" data-row-index="${row_index}"><i class="ti ti-trash-x"></i> Row</a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="7" class="text-end"><b>Sub Total</b></td>
                                                            <td><input type="number" name="options[${option_no}][subtotal]" id="sub_total_${option_no}" class="form-control" step="0.01" readonly></td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7" class="text-end"><b>Sale Discount Type</b></td>
                                                            <td>
                                                                <select name="options[${option_no}][sale_discount_type]" id="sale_discount_type_${option_no}" class="form-select">
                                                                    <option value="1">%</option>
                                                                    <option value="2">Flat</option>
                                                                </select>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7" class="text-end"><b>Sale Discount</b></td>
                                                            <td><input type="number" name="options[${option_no}][sale_discount_amount]" id="sale_discount_amount_${option_no}" class="form-control" step="0.01"></td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7" class="text-end"><b>Total Discount</b></td>
                                                            <td><input type="number" name="options[${option_no}][total_discount]" id="total_discount_${option_no}" class="form-control" step="0.01" readonly></td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7" class="text-end"><b>Other Charges</b></td>
                                                            <td><input type="number" name="options[${option_no}][other_charges]" id="other_charges_${option_no}" class="form-control" step="0.01"></td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7" class="text-end"><b>Grand Total</b></td>
                                                            <td><input type="number" name="options[${option_no}][grand_total]" id="grand_total_${option_no}" class="form-control" step="0.01" readonly></td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>    
                                        </div>`;
    $("#option-wrapper").append(option_html);
    initItemSelect(`#item_${option_no}_${row_index}`);
    setTimeout(() => {
        $(`#item_${option_no}_${row_index}`).focus();
    }, 200);
}
function initItemSelect(element) {
    $(element).select2({
        ajax: {
            url: '../AJAX/Quotation/getItems.php',
            dataType: 'json',
            delay: 250,
            data: function(params){
                return {
                    search: params.term,
                    type: 'item_search'
                };
            },
            processResults: function(data){
                return { results: data };
            }
        },
        placeholder: 'Search for Items',
        minimumInputLength: 1,
        width: '100%'
    }).on('select2:open', function () {
                setTimeout(function () {
                    document.querySelector('.select2-search__field')?.focus();
                }, 0);
            });
}
function setPredefinedCustomer(customerId, customerName) 
{
    // Create a new option with the predefined value
    var option = new Option(customerName, customerId, true, true);

    // Append the option to Select2 and trigger the change event
    $("#cmb_customer").append(option).trigger('change');
}
function customerChange(customerName)
{
    $("#customer_text").html(customerName);
}

function initcustomerSelect(element) {
    $(element).select2({
        ajax: {
            url: '../AJAX/Quotation/getCustomers.php?ref=create-quote',
            dataType: 'json',
            delay: 250,
            data: function(params){
                return {
                    search: params.term,
                    type: 'item_search'
                };
            },
            processResults: function(data){
                return { results: data };
            }
        },
        placeholder: 'Search for Customers',
        minimumInputLength: 1,
        width: '100%'
    }).on('select2:open', function () {
                setTimeout(function () {
                    document.querySelector('.select2-search__field')?.focus();
                }, 0);
            });
}
function fetchItemDetails(selectElement) {

    let item_id = $(selectElement).val();

    if (!item_id) return;

    let row = $(selectElement).closest('.item-row');
    let option = $(selectElement).closest('.option');

    $.ajax({
        url: '../AJAX/Quotation/getItemDetails.php',
        type: 'POST',
        data: { item_id: item_id },
        dataType: 'json',
        success: function (res) {
            let data = res[0]; 
            // ✅ Fill fields
            row.find('.description').val(data.description || '');
            row.find('.rate').val(data.rate || 0);
            row.find('.cost').val(data.cost || 0);

            // Optional defaults
            row.find('.qty').val(1);
            row.find('.disc_amount').val(0);
            row.find('.disc_type').val(2);

            // ✅ Recalculate
            calculateOption(option);
            calculateGrandTotal(option);
        },
        error: function () {
            alert('Failed to fetch item details');
        }
    });
}
function calculateOption(option) {

    let subtotal = 0;
    let item_discount_total = 0;

    option.find('.item-row').each(function () {

        let qty = parseFloat($(this).find('.qty').val()) || 0;
        let rate = parseFloat($(this).find('.rate').val()) || 0;
        let disc = parseFloat($(this).find('.disc_amount').val()) || 0;
        let disc_type = $(this).find('.disc_type').val();

        // ✅ Subtotal (NO discount)
        let row_subtotal = rate * qty;

        // ✅ Discount per unit
        let discount_per_unit = disc;

        if (disc_type === '1') {
            discount_per_unit = (rate * disc) / 100;
        }

        let row_discount = discount_per_unit * qty;

        // ✅ Final row total (after discount)
        let row_total = row_subtotal - row_discount;

        $(this).find('.total').val(row_total.toFixed(2));

        subtotal += row_subtotal;
        item_discount_total += row_discount;
    });

    // ✅ Update subtotal (WITHOUT discount)
    option.find('[id^="sub_total_"]').val(subtotal.toFixed(2));

    // store item discount temporarily (for grand total calc)
    option.data('item_discount_total', item_discount_total);
}
function calculateGrandTotal(option) {

    let subtotal = parseFloat(option.find('[id^="sub_total_"]').val()) || 0;

    let item_discount_total = option.data('item_discount_total') || 0;

    // ✅ Net after item discount
    let net_amount = subtotal - item_discount_total;

    let discount_type = option.find('[id^="sale_discount_type_"]').val();
    let discount_value = parseFloat(option.find('[id^="sale_discount_amount_"]').val()) || 0;

    let sale_discount = 0;

    if (discount_type == '1') {
        // ✅ apply on net amount (correct)
        sale_discount = (net_amount * discount_value) / 100;
    } else {
        sale_discount = discount_value;
    }

    let total_discount = item_discount_total + sale_discount;

    let other_charges = parseFloat(option.find('[id^="other_charges_"]').val()) || 0;

    let grand_total = net_amount - sale_discount + other_charges;

    // update fields
    option.find('[id^="total_discount_"]').val(total_discount.toFixed(2));
    option.find('[id^="grand_total_"]').val(grand_total.toFixed(2));
}
function newQuoteNo(){
    return $.ajax({
        url: '../AJAX/Quotation/getNewQuoteNo.php',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            $('#quoteNo').text(res.new_quote_no);
        },
        error: function() {
            alert('Failed to fetch new quote number');
        }
    });
}
function addRow(option_no) {

    let tbody = $("#tbl_item_" + option_no);

    let row_index = Date.now(); // unique

    let row_html = `
    <tr class="item-row">
        <td style="min-width:250px;">
            <select  name="options[${option_no}][items][${row_index}][item_id]" id="item_${option_no}_${row_index}" class="item form-select">
                <option value=""></option>
            </select>
        </td>
        <td style="min-width:350px;">
            <textarea name="options[${option_no}][items][${row_index}][description]" id="description_${option_no}_${row_index}" cols="20" class="form-control description"></textarea>
        </td>
        <td style="min-width:100px;">
            <input type="number" name="options[${option_no}][items][${row_index}][quantity]" id="quantity_${option_no}_${row_index}" class="form-control qty" step="0.01">
        </td>
        <td style="min-width:100px;">
            <input type="number" name="options[${option_no}][items][${row_index}][rate]" id="rate_${option_no}_${row_index}" class="form-control rate" step="0.01">
        </td>
        <td style="min-width:100px;">
            <input type="number" name="options[${option_no}][items][${row_index}][cost]" id="cost_${option_no}_${row_index}" class="form-control cost" step="0.01">
        </td>
        <td style="min-width:100px;">
            <select name="options[${option_no}][items][${row_index}][disc_type]" id="discType_${option_no}_${row_index}" class="form-select disc_type">
                <option value="1">%</option>
                <option value="2">Flat</option>
            </select>
        </td>
        <td style="min-width:150px;">
            <input type="number" name="options[${option_no}][items][${row_index}][disc_amount]" id="disc_amount_${option_no}_${row_index}" class="form-control disc_amount" step="0.01">
        </td>
        <td style="min-width:100px;">
            <input type="number" name="options[${option_no}][items][${row_index}][total]" id="total_${option_no}_${row_index}" class="form-control total" step="0.01" readonly>
        </td>
        <td>
            <div class="d-flex justify-content-end" id="action">
                <a href="javascript:void(0);" class="btn btn-warning me-2 duplicate-row" id="duplicate-row-${option_no}-${row_index}" data-option-no="${option_no}" data-row-index="${row_index}"><i class="ti ti-copy"></i> Row</a>
                <a href="javascript:void(0);" class="btn btn-danger delete-row" id="delete-row-${option_no}-${row_index}" data-option-no="${option_no}" data-row-index="${row_index}"><i class="ti ti-trash-x"></i> Row</a>
            </div>
        </td>
    </tr>
    `;

    tbody.append(row_html);

    // init select2
    initItemSelect(`#item_${option_no}_${row_index}`);
}
function duplicateRow(button) {

    let current_row = $(button).closest('.item-row');
    let option = $(button).closest('.option');
    let option_no = option.data('option-no');

    let new_index = Date.now();

    // 🔥 CLONE FIRST
    let new_row = current_row.clone(false, false);

    // 🔥 CLEAN SELECT2 FROM CLONE ONLY
    new_row.find('select.item').each(function () {

        // remove select2 container if exists
        $(this).next('.select2-container').remove();

        // remove select2 classes safely
        $(this)
            .removeClass('select2-hidden-accessible')
            .removeAttr('data-select2-id')
            .removeAttr('tabindex')
            .removeAttr('aria-hidden');
    });

    // 🔥 update names + ids
    new_row.find('input, select, textarea').each(function () {

        let name = $(this).attr('name');
        let id = $(this).attr('id');

        if (name) {
            name = name.replace(/\[\d+\](?=\]\[)/, '[' + new_index + ']');
            $(this).attr('name', name);
        }

        if (id) {
            let parts = id.split('_');
            parts[parts.length - 1] = new_index;
            $(this).attr('id', parts.join('_'));
        }
    });

    // insert row
    current_row.after(new_row);

    // 🔥 RE-INIT SELECT2 ONLY FOR NEW ROW
    let new_select = new_row.find('.item');
    initItemSelect(new_select);

    // copy selected value
    let selected_val = current_row.find('.item').val();
    new_select.val(selected_val).trigger('change');

    // recalc
    calculateOption(option);
    calculateGrandTotal(option);
}
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
    });
}
function subcat2()
{
    // var id=1;
    var maincat=$("#cmb_category").val();
    if(maincat=="")
    {
        alert("Select A Main Category");
        $("#cmb_category").focus();
    }
    else
    {
        var selected=$("#cmb_subcategory").val();
        $.get("../AJAX/guiPos/getsubcat.php", {
            maincat : maincat
        }, function(data){
            // alert(data);
            const obj = JSON.parse(data);

            var html="<option value=''>Sub Category</option>";
            obj.forEach(function(item) {
                html += `<option value='${item.SCID}' ${selected == item.SCID ? "selected" : ""}>${item.SubCatNo} - ${item.SubCatName} </option>`;
            });
            $("#cmb_subcategory").html(html);
        });
    }
}
function checkBarcode(barcode,id)
{
    return $.ajax({
        url: '../AJAX/Products/CheckBarcode.php',
        method: 'POST',
        data: { barcode: barcode, PDID: id },
        dataType: 'json'
    }).then(function(response) {

        if (response && typeof response.ProCount !== 'undefined') {
            return response.ProCount > 0;
        } else {
            toastr.error("An error occurred while validating the Barcode. Please try again.", "Error");
            console.error("Invalid response:", response);
            return false;
        }

    }).catch(function(error) {
        console.error("AJAX error:", error);
        toastr.error("An error occurred while validating the Barcode. Please try again.", "Error");
        return false;
    });
}
$(document).ready(function(){
    $("#headerCollapse2").trigger("click");
    $(document).on('click', '.open-modal', function (e) {
      e.preventDefault();
      let url = $(this).attr('href');
      let title = $(this).data('title') || 'Company';
      openModal(url,title);
    });
    initcustomerSelect($("#cmb_customer"));
    allCategory();
    
    $("#cmb_customer").on("select2:select", function(e){
        let data = e.params.data;
        if(data.id === "Add") 
        {
            // $("#customer_modal").modal("toggle");
            console.log("Opening modal for adding new customer");
            openModal("../View/modals/customer-modal.php?condition=new&ref=create-quote","Add Customer");
        }
        else
        {
            customerChange(data.full_text);
        }
        
    });

    setPredefinedCustomer($("#predefinded_cus_id").val(), $("#predefinded_customer_text").val());
    addOption($(".option").length + 1);
    newQuoteNo();
    let clickedButton = "";
    $("button[type=submit]").click(function () {
        clickedButton = $(this).attr("id");
    });
    $("#create-quote-form").on("submit", function (e) {
        e.preventDefault();
            

        let formData = $(this).serialize();
        let url = $(this).attr("action");
        let save_quote_btn_print = $("#save-quote-btn-print").html();
        let save_quote_btn = $("#save-quote-btn").html();

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $("#save-quote-btn, #save-quote-btn-print").prop("disabled", true).text("Saving...");
            },
            success: function (res) {

                if (res.status === 'success') {
                    // alert('Quotation saved successfully');

                    // optional redirect
                    // window.location.href = 'quotation-list.php';
                    toastr.success(res.message || 'Quotation saved successfully', "Success");
                } else {
                    // alert(res.message || 'Error saving quotation');
                    toastr.error(res.message || 'Error saving quotation', "Error");
                }
                $("#save-quote-btn").prop("disabled", false).html(save_quote_btn);
                $("#save-quote-btn-print").prop("disabled", false).html(save_quote_btn_print);
                if(clickedButton === "save-quote-btn-print") {
                    window.open(`../Print/print-quote-${res.quotation_no}`, '_blank');
                    setTimeout(() => {
                        window.open(`create-quote.php`, '_self');                        
                    }, 1000);
                }
                if(clickedButton === "save-quote-btn")
                {
                    setTimeout(() => {
                        window.open(`create-quote.php`, '_self');                        
                    }, 1000);
                }
            },
            error: function (xhr, status, error) {
                console.log("AJAX Error:", status, error);
                console.log("Response:", xhr.responseText);
                toastr.error("An error occurred while creating quotation.", "Error");
                $("#save-quote-btn").prop("disabled", false).html(save_quote_btn);
                $("#save-quote-btn-print").prop("disabled", false).html(save_quote_btn_print);
            }
        });
    });
    $("body").on("click", ".add_option", function() {
        addOption($(".option").length + 1);
    });
    $("body").on("click", ".delete_option", function() {
        let option_no = $(this).data("option-no");
        var option_count = $(".option").length;
        if(option_count <= 1) {
            alert("At least one option is required.");
            return;
        }
        if(confirm(`Are you sure you want to delete option ${option_no}?`)) 
        {
            $(this).closest("#option-"+option_no).remove();
        }
        // $(`#option-${option_no}`).remove();
    });
    $("body").on("input", ".qty, .rate, .disc_amount, .disc_type", function () {
        let option = $(this).closest('.option');
        calculateOption(option);
        calculateGrandTotal(option);
    });

    $("body").on("input", "[id^='sale_discount_type_'], [id^='sale_discount_amount_'], [id^='other_charges_']", function () {
        let option = $(this).closest('.option');
        calculateGrandTotal(option);
    });
    $("body").on("click", ".add-row", function () {
        let option_no = $(this).data("option-no");
        addRow(option_no);
    });
    $("body").on("click", ".delete-row", function () {
        let option = $(this).closest('.option');
        $(this).closest('tr').remove();
        calculateOption(option);
        calculateGrandTotal(option);
    });
    $("body").on("click", ".duplicate-row", function () {
        duplicateRow(this);
    });
    $("body").on("change", ".item", function () {
        fetchItemDetails(this);
    });
    $("body").on("click", "#add-item-btn", function (e) {
        e.preventDefault();
        // Open the modal
        $("#product_modal").modal("toggle");
    });
    $("#cmb_category").change(function(){
        subcat2();
    });
    $('#barcode').on('keypress', function(e) {
        if (e.which === 13) { // 13 is the key code for Enter
            e.preventDefault(); // Prevent the default Enter action
        }
        var barcode = $(this).val();
        var barcodeInput = $(this);
        if(barcode !="")
        {
            checkBarcode(barcode).then(function(exist){
                if(exist>0)
                {                    
                    toastr.error("Barcode Already Exists, Please Try a New One.", "Error");
                    barcodeInput.focus();
                    barcodeInput.css("border-color","red");
                    $("#btn_save_product").prop("disabled", true);
                    return false;
                }
                else
                {
                    barcodeInput.css("border-color","");
                    $("#btn_save_product").prop("disabled", false);
                }
            });
        }
        else
        {
            barcodeInput.css("border-color","");
            $("#btn_save_product").prop("disabled", false);
        }
        
    });
    $("#barcode").on("input", function(e){
        e.preventDefault();
        var barcode = $(this).val();
        var barcodeInput = $(this);
        if(barcode !="")
        {
            checkBarcode(barcode).then(function(exist){
                if(exist>0)
                {                    
                    toastr.error("Barcode Already Exists, Please Try a New One.", "Error");
                    barcodeInput.focus();
                    barcodeInput.css("border-color","red");
                    $("#btn_save_product").prop("disabled", true);
                    return false;
                }
                else
                {
                    barcodeInput.css("border-color","");
                    $("#btn_save_product").prop("disabled", false);
                }
            });
        }
        else
        {
            barcodeInput.css("border-color","");
            $("#btn_save_product").prop("disabled", false);
        }
        
    });
    $("#addProduct").on("submit", function(e){
        e.preventDefault();
        let formData = new FormData(this);
        let url = $(this).attr("action");
        let barcode = $("#barcode").val();
        let btn = $("#btn_save_product");
        let btnHtml = btn.html();        
        var barcodeInput = $("#barcode");
        if(barcode !="")
        {
            checkBarcode(barcode).then(function(exist){
                if(exist>0)
                {                    
                    toastr.error("Barcode Already Exists, Please Try a New One.", "Error");
                    barcodeInput.focus();
                    barcodeInput.css("border","red");
                    $("#btn_save_product").prop("disabled", true);
                    return false;
                }
                else
                {
                    barcodeInput.css("border","");
                    $("#btn_save_product").prop("disabled", false);
                }
            });
        }
        else
        {
            barcodeInput.css("border","");
            $("#btn_save_product").prop("disabled", false);
        }
        $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function() {
                    btn.prop("disabled", true);
                    btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
                },
                success: function(response) {
                    btn.prop("disabled", false);
                    btn.html(btnHtml);
                    if(response.status === "success") {
                        $("#product_modal").modal("toggle");
                        toastr.success(response.message, "Success");
                        $('#addProduct')[0].reset();
                        allCategory();
                        $("#img_product").attr("src","../Assets/Images/icons/product.png")

                    } else {
                        toastr.error(response.message, "Error");
                    }
                },
                error: function(xhr, status, error) {
                    btn.prop("disabled", false);
                    btn.html(btnHtml);
                    console.log("AJAX Error:", error);
                    toastr.error("An error occurred while updating subcategory.", "Error");
                }
            });
        
    });
    $("#add-customer").on("click", function(e){
        e.preventDefault();
        $("#customer_modal").modal("toggle");
    });
    $("#add-customer-form").on("submit", function(e){
        e.preventDefault();
        var form = $(this);
        var formData = $(this).serialize();
        var url = $(this).attr("action");
        let btn = $("#btn_save_customer");
        let btnHtml = btn.html();   
        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            dataType: "json",
            beforeSend: function() {
                btn.prop("disabled", true);
                btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
            },
            success: function(response){
                btn.prop("disabled", false);
                btn.html(btnHtml);
                console.log(response);                
                if(response.status === "success"){
                    $("#customer_modal").modal("toggle");
                    form[0].reset();
                    toastr.success(response.message, "Success");
                    console.log("response.customerDetail "+response.customerDetail);
                    console.log("response.customerDetail.CTID "+response.customerDetail.CTID);
                    console.log("response.customerDetail.length "+response.customerDetail.length);
                    
                    if (response.customerDetail && response.customerDetail.CTID) 
                    {
                        var customerId = response.customerDetail['CTID'];
                        var customer_text="";
                        if(response.customerDetail['CustomerNo'].length > 0)
                        {
                            customer_text+=response.customerDetail['CustomerNo'];
                        }
                        if(response.customerDetail['CustName'].length > 0)
                        {
                            customer_text+=" - "+response.customerDetail['CustName'];
                        }
                        if(response.customerDetail['CustContact'].length > 0)
                        {
                            customer_text+=" - "+response.customerDetail['country_code']+" "+response.customerDetail['CustContact'];
                        }
                        setPredefinedCustomer(customerId, customer_text);
                        customerChange(customer_text);
                    }
                }
                if(response.status === "error"){
                    toastr.error(response.message, "Error");
                }
            },
            error: function(xhr, status, error) {
                btn.prop("disabled", false);
                btn.html(btnHtml);
                console.log("AJAX Error:", error);
                toastr.error("An error occurred while updating subcategory.", "Error");
            }
        })
    });

});