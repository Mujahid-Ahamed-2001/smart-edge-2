var app_status = $(document).find("#app_status").val();
var query = $(document).find("#query").val();
console.log("App Status "+app_status);
let $table = $('#tbl_grnDetails');
let GHID = $("#grn_header").val();
let ShopID = $("#shop_id").val();
let grn_header = $("#grn_header").val();
let print_access = parseInt($("#print_access").val()) || 0;
let verify_access = parseInt($("#verify_access").val()) || 0;
let edit_access = parseInt($("#edit_access").val()) || 0;
let delete_access  = parseInt($("#delete_access").val()) || 0;
let userType = parseInt($("#userType").val()) || 0;
function TableLoading($table, TableName = "Data") {
    let colCount = $table.find('thead th').length;
    let loaderHtml = `
        <tr class="table-loading">
            <td colspan="${colCount}" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="mt-2 fw-semibold">Loading ${TableName}...</div>
            </td>
        </tr>
    `;
    $table.find('tbody').html(loaderHtml);
}
let currentGRNStatus = 0;


function applyGRNDocumentLock() {
    const isLocked =
        currentGRNStatus === 2 ||
        currentGRNStatus === 3;

    const $grnForm = $("#grnDetailForm");

    /*
     * Only lock fields inside the GRN form.
     */
    $grnForm
        .find("input, textarea")
        .not("#printGRN")
        .prop("readonly", isLocked);

    $grnForm
        .find("select, button")
        .not("#printGRN")
        .prop("disabled", isLocked);

    $grnForm
        .find("[contenteditable]")
        .not(".cke_editable")
        .not("#printGRN")
        .attr("contenteditable", isLocked ? "false" : "true");

    /*
     * Only disable links inside the GRN form.
     */
    $grnForm
        .find("a")
        .not("#printGRN")
        .toggleClass("grn-document-link-disabled", isLocked)
        .attr("aria-disabled", isLocked ? "true" : "false");

    /*
     * Refresh only the GRN form Select2 fields.
     */
    $grnForm
        .find("select")
        .trigger("change.select2");

    /*
     * Only lock the GRN CKEditor fields.
     */
    ["referenceNo", "addNotes"].forEach(function (instanceName) {
        if (
            typeof CKEDITOR === "undefined" ||
            !CKEDITOR.instances ||
            !CKEDITOR.instances[instanceName]
        ) {
            return;
        }

        const editor = CKEDITOR.instances[instanceName];

        if (
            editor.status === "ready" &&
            editor.editable()
        ) {
            editor.setReadOnly(isLocked);
        } else {
            editor.once("instanceReady", function () {
                if (
                    editor.status === "ready" &&
                    editor.editable()
                ) {
                    editor.setReadOnly(isLocked);
                }
            });
        }
    });

    /*
     * Print button always remains enabled.
     */
    $("#printGRN")
        .prop("disabled", false)
        .prop("readonly", false)
        .removeClass("grn-document-link-disabled")
        .attr("aria-disabled", "false");
}
function initializegrnSupplierSelect2() {
    
    $("#grnSupplier").select2({
        ajax:{
            url: '../AJAX/GRN/getSuppliers.php',
            dataType: 'json',
            delay: 250,
            data: function(params){
                var query = {
                    search: params.term,
                    type: 'item_search'
                };
                return query;
            },
            processResults: function(data){
                return {
                    results: data
                }
            }
        },
        cache: true,
        placeholder: 'Search By Supplier No/ Distributer/ Supplier Name/ Contact',
        minimumInputLength: 1,
        width: '70%',
    }).on('select2:open', function () {
                setTimeout(function () {
                    document.querySelector('.select2-search__field')?.focus();
                }, 0);
            });
}
function initializeSearchItemSelect2(ShopID) {
    
    $("#search-items").select2({
        ajax:{
            url: '../AJAX/GRN/getItems.php?shop_id=' + ShopID,
            dataType: 'json',
            delay: 250,
            data: function(params){
                var query = {
                    search: params.term,
                    type: 'item_search'
                };
                return query;
            },
            processResults: function(data){
                return {
                    results: data
                }
            }
        },
        cache: true,
        placeholder: 'Item name/Barcode/Itemcode',
        minimumInputLength: 1,
        width: '90%',
    }).on('select2:open', function () {
                setTimeout(function () {
                    document.querySelector('.select2-search__field')?.focus();
                }, 0);
            });
}
function showGrnHeaderLoading(update=0) {
    let $container = $("#GrnHeaderDetails");

    // Prevent duplicate loader
    if ($container.find(".grn-loading-overlay").length) return;

    $container.css("position", "relative");
    var text = "Loading GRN Header Details"
    if(update==1)
    {
        text = "Updating GRN Header Details"
    }
    let loader = `
        <div class="grn-loading-overlay">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="mt-2 fw-semibold">${text}...</div>
            </div>
        </div>
    `;

    $container.append(loader);
}
function hideGrnHeaderLoading() {
    $("#GrnHeaderDetails").find(".grn-loading-overlay").remove();
}
function showAttachmentLoading()
{
    let loader = `
        <div class="text-center py-3">
            <div class="spinner-border text-primary"></div>
            <div class="mt-2 fw-semibold">Loading Documents...</div>
        </div>
    `;

    $("#grnAttachedDoc").html(loader);
}

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

function hideAttachmentLoading(){}
function fetchGRNHeader(GHID, update=0)
{
    return $.ajax({
            url: '../AJAX/GRN/getGRNHeaderdata.php',
            method: 'POST',
            data: {
                GHID: GHID
            },
            dataType: 'json',
            beforeSend: function () {
                if(update==1)
                {

                }
                else
                {
                    showGrnHeaderLoading(update);
                }
                
            },
            success: function (response) {
                setTimeout(() => {
                    hideGrnHeaderLoading();    
                }, 2000);
                
                var GHID = response.GHID;
                var GRNHeaderNo = response.GRNHeaderNo;
                var SPID = response.SPID;
                var SupplierDetails = response.SupplierDetails;
                var refference = response.refference;
                var addNotes = response.add_notes;
                var GRNStat = response.GRNStat;
                var EffectiveDate = response.EffectiveDate;
                var shop_SHID = response.shop_SHID;
                var PurchDiscType = response.PurchDiscType;
                var PurchDisc = response.PurchDisc;
                if(PurchDiscType==0)
                {
                    PurchDiscType=1;
                }
                var supDetails = `${response.Contact || ""},<br>${response.SupplierName || ""} - ${response.SupplierNo || ""}`;
                // $("#GrnHeaderDetails").html(GrnHeaderDetailsHTML);
                $("#referenceNo").val(refference);
                CKEDITOR.instances.referenceNo.setData(refference);
                CKEDITOR.instances.addNotes.setData(addNotes);
                $("#grnStatus").val(GRNStat);
                $("#discType").val(PurchDiscType);
                $("#disc").val(PurchDisc);
                $("#purchaseDate").val(EffectiveDate);
                $("#shopLocation").val(shop_SHID);
                $("#grnSupDetails").html(supDetails);
                setPredefinedSupplier(SPID, SupplierDetails);    
                $("#grnNo").text(GRNHeaderNo);
                currentGRNStatus = parseInt(GRNStat, 10) || 0;
                applyGRNDocumentLock();

                fetchGRNAttachments(GHID);
                grandTotal();
            },
            error: function (xhr, status, error) {
                hideGrnHeaderLoading();
                console.log("AJAX Error:", error);
                toastr.error("An error occurred while feting GRN data.", "Error");
            }
        });
}
function uploadDocuments() 
{
    let files = $("#attachDoc")[0].files;
    if (files.length === 0) {
        toastr.error("Please select at least one file", "Error");
        return;
    }
    if (GHID=="" || GHID==0) {
        toastr.error("Invalid GRN ID", "Error");
        return;
    }

    let formData = new FormData();

    // Append multiple files
    $.each(files, function(index, file) {
        formData.append("documents[]", file);
    });
    formData.append("GHID", GHID);
    var uploadBtnHTML = $("#uploadBtn").html();
    return $.ajax({
        url: '../AJAX/GRN/uploadDocuments.php',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        beforeSend: function () {
            $("#uploadBtn").prop("disabled", true).html(`
                <span class="spinner-border spinner-border-sm"></span> Uploading...
            `);
        },
        success: function (response) {

            if (response.success) {
                response.success.forEach(function(msg){
                    toastr.success(msg, "Success");
                });
            }

            if (response.error) {
                response.error.forEach(function(msg){
                    toastr.error(msg, "Error");
                });
            }

            $("#attachDoc").val(""); // reset input
            fetchGRNAttachments(GHID);
        },
        error: function (xhr, status, error) {
            console.log("AJAX Error:", error);
            toastr.error("Upload failed", "Error");
        },
        complete: function () {
            $("#uploadBtn").prop("disabled", false).html(uploadBtnHTML);
        }
    });
}

function fetchGRNAttachments(GHID)
{
    return $.ajax({
        url: '../AJAX/GRN/getGRNAttachments.php',
        method: 'POST',
        data: {
            GHID: GHID
        },
        dataType: 'json',
        beforeSend: function () {
            showAttachmentLoading();
        },
        success: function (response) {

            hideAttachmentLoading();

            let html = "";

            if (response.data && response.data.length > 0) {

                response.data.forEach(function(item){

                    let fileUrl = "../" + item.doc_path + item.doc_name;

                    html += `
                        <div class="d-flex w-100 p-1 align-items-center">
                            <a href="${fileUrl}" target="_blank" class="w-75 text-truncate">
                                <i class="ti ti-files"></i> ${item.doc_name}
                            </a>
                            <a href="javascript:void(0)" 
                               class="deleteDoc w-25 text-danger text-end"
                               data-id="${item.id}"
                               data-file="${item.doc_name}">
                                <i class="ti ti-trash-x"></i>
                            </a>
                        </div>
                    `;
                });

            } else {
                html = `
                    <div class="text-center text-muted py-3">
                        No documents attached
                    </div>
                `;
            }

            $("#grnAttachedDoc").html(html);
        },
        error: function (xhr, status, error) {
            hideAttachmentLoading();
            console.log("AJAX Error:", error);
            toastr.error("Failed to fetch documents", "Error");
        }
    });
}

$(document).on("click", ".deleteDoc", function(e){
    e.preventDefault();
    let id = $(this).data("id");
    let file = $(this).data("file");

    if (!confirm("Are you sure you want to delete this document?")) {
        return;
    }

    $.ajax({
        url: '../AJAX/GRN/deleteGRNAttachment.php',
        method: 'POST',
        data: {
            id: id,
            file: file
        },
        dataType: 'json',
        success: function(response){

            if (response.success) {
                toastr.success("Document deleted", "Success");
                fetchGRNAttachments(GHID); // refresh
            }

            if (response.error && response.error.length > 0) {
                response.error.forEach(function(msg){
                    toastr.error(msg, "Error");
                });
            }
        },
        error: function(){
            toastr.error("Delete failed", "Error");
        }
    });
});

function updateGRN(GHID,grnSupplier,referenceNo,grnStatus,purchaseDate,shopLocation, addNotes="")
{
    grnStatus = Number(grnStatus);
    if (grnStatus === 2) 
    {
        let totalPayment = 0;
        const grandTotal = parseFloat($("#grandTotInput").val()) || 0;

        $(".payment-amount").each(function () {
            totalPayment += parseFloat($(this).val()) || 0;
        });

        // Round values to avoid floating-point comparison problems
        totalPayment = Number(totalPayment.toFixed(2));
        const roundedGrandTotal = Number(grandTotal.toFixed(2));

        if (roundedGrandTotal > totalPayment && !confirm("The full amount has not been paid. Are you sure you want to proceed as credit?") ) 
        {
            return false;
        }
    } 
    else if (grnStatus === 3) 
    {
        if (!confirm("Are you sure you want to cancel this GRN?")) {
            return false;
        }
    }
    return $.ajax({
            url: '../AJAX/GRN/updateGRNHeaderdata.php',
            method: 'POST',
            data: {
                GHID: GHID,
                grnSupplier: grnSupplier,
                referenceNo: referenceNo,
                grnStatus: grnStatus,
                purchaseDate: purchaseDate,
                shopLocation: shopLocation,
                addNotes: addNotes
            },
            dataType: 'json',
            beforeSend: function () {
            //   showGrnHeaderLoading(1);  
            },
            success: function (response) {
                var hasGRNHeaderUpdated = false;
                    if (response.success && Array.isArray(response.success)) {
                        response.success.forEach(function(successMsg) {
                            let msg = Array.isArray(successMsg) ? successMsg[0] : successMsg;
                            toastr.success(msg, "Success");

                            if (msg === "GRN Header Updated Successfully") {
                                hasGRNHeaderUpdated = true;
                            }
                        });
                    }
                    if (response["error"]) {
                        if (Array.isArray(response["error"])) {
                            response["error"].forEach(function(errorMsg) {
                                toastr.error(errorMsg, "Error");
                            });
                        } else {
                            toastr.error(response["error"], "Error");
                        }
                    }
                    if (hasGRNHeaderUpdated) {
                        TableLoading($table, "GRN Items");
                        fetchGRNHeader(GHID,1).always(function(){
                            fetchgrnDetail(GHID)
                        });
                    }
                    else
                    {
                        // hideGrnHeaderLoading(); 
                    }
                    applyGRNDocumentLock();
                
            },
            error: function (xhr, status, error) {
                // hideGrnHeaderLoading(); 
                console.log("AJAX Error:", error);
                toastr.error("An error occurred while updating GRN data.", "Error");
            }
        });
}
function get_payment_methods() {
    return $.ajax({
        url: '../AJAX/GRN/getpaymentMethods.php',
        method: 'POST',
        dataType: 'json'
    }).then(function (response) {
        if (Number(response.status) === 1) {
            return response.data || [];
        }

        toastr.error(
            response.message || "Unable to fetch payment methods.",
            "Error"
        );

        return $.Deferred().reject(response).promise();
    });
}
async function get_payments() {
    const $paymentContainer = $('#payment_grid');

    if (!GHID) {
        toastr.error("Invalid GRN ID.", "Error");
        return;
    }

    try {
        $paymentContainer.html(`
            <div class="text-center py-4 payment-loading">
                <div
                    class="spinner-border spinner-border-sm text-primary"
                    role="status"
                ></div>

                <span class="ms-2">Loading payments...</span>
            </div>
        `);

        /*
         * Load payments and payment methods simultaneously.
         */
        const [response, payMethods] = await Promise.all([
            $.ajax({
                url: '../AJAX/GRN/getGRNPayments.php',
                method: 'POST',
                data: {
                    GHID: GHID
                },
                dataType: 'json'
            }),

            get_payment_methods()
        ]);

        $paymentContainer.empty();

        if (Number(response.status) !== 1) {
            toastr.error(
                response.message || "Unable to fetch payments.",
                "Error"
            );
            return;
        }

        const payments = response.data || [];

        if (payments.length === 0) {
            $paymentContainer.html(`
                <div class="alert alert-light text-center my-3">
                    No payments have been added.
                </div>
            `);

            return;
        }

        let paymentHTML = "";

        payments.forEach(function (payment) {
            paymentHTML += create_payment_row(
                payment,
                payMethods
            );
        });

        $paymentContainer.html(paymentHTML);

    } catch (error) {
        console.error("Get payments error:", error);

        $paymentContainer.html(`
            <div class="alert alert-danger text-center my-3">
                Unable to load the payment records.
            </div>
        `);

        toastr.error(
            "An error occurred while fetching GRN payments.",
            "Error"
        );
    }
    applyGRNDocumentLock();
}
function create_payment_row(payment, payMethods) {
    const TRID = payment.TRID;
    const TransferAmount = payment.TransferAmount ?? 0;
    const paymethod_PMID = payment.paymethod_PMID ?? "";
    const tranDate = formatDateTimeLocal(payment.tran_date);

    let payHTML = '<option value="">Select Payment</option>';

    payMethods.forEach(function (method) {
        const selected =
            String(paymethod_PMID) === String(method.PMID)
                ? 'selected'
                : '';

        payHTML += `
            <option value="${method.PMID}" ${selected}>
                ${method.PaymethodName}
            </option>
        `;
    });

    return `
        <div
            class="row payment-row align-items-end"
            id="payment-row-${TRID}"
            data-transaction-id="${TRID}"
        >
            <div class="col-md-4 mt-3">
                <label
                    for="payment-amount-${TRID}"
                    class="form-label"
                >
                    Amount <span class="text-danger">*</span>
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="ti ti-cash-banknote"></i>
                    </span>

                    <input
                        type="number"
                        name="payment_amount[${TRID}]"
                        id="payment-amount-${TRID}"
                        class="form-control payment-amount"
                        data-transaction-id="${TRID}"
                        step="0.01"
                        min="0"
                        value="${TransferAmount}"
                    >
                </div>
            </div>

            <div class="col-md-4 mt-3">
                <label
                    for="payment-date-${TRID}"
                    class="form-label"
                >
                    Date <span class="text-danger">*</span>
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="ti ti-calendar-stats"></i>
                    </span>

                    <input
                        type="datetime-local"
                        name="payment_date[${TRID}]"
                        id="payment-date-${TRID}"
                        class="form-control payment-date"
                        data-transaction-id="${TRID}"
                        value="${tranDate}"
                    >
                </div>
            </div>

            <div class="col-md-3 mt-3">
                <label
                    for="payment-method-${TRID}"
                    class="form-label"
                >
                    Payment Method
                    <span class="text-danger">*</span>
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="ti ti-wallet"></i>
                    </span>

                    <select
                        name="payment_method[${TRID}]"
                        id="payment-method-${TRID}"
                        class="form-select payment-method"
                        data-transaction-id="${TRID}"
                    >
                        ${payHTML}
                    </select>
                </div>
            </div>

            <div class="col-md-1 mt-3">
                <button class="deletPayment btn btn-danger w-100" title="Delete payment" data-transaction-id="${TRID}"><i class="ti ti-trash-x"></i></button>
            </div>
        </div>
    `;
}

async function add_payment() {
    try {
        const response = await $.ajax({
            url: '../AJAX/GRN/addGRNPayment.php',
            method: 'POST',
            data: {
                GHID: GHID
            },
            dataType: 'json'
        });

        if (response.error) {
            if (Array.isArray(response.error)) {
                response.error.forEach(function (errorMsg) {
                    toastr.error(errorMsg, "Error");
                });
            } else {
                toastr.error(response.error, "Error");
            }

            return;
        }

        /*
         * response.data is one object, not an array.
         */
        if (!response.data || !response.data.TRID) {
            toastr.error(
                "Transaction details were not returned.",
                "Error"
            );
            return;
        }

        const transaction = response.data;

        const TRID = transaction.TRID;
        const TransferAmount = transaction.TransferAmount ?? 0;
        const paymethod_PMID = transaction.paymethod_PMID ?? "";
        const tranDate = formatDateTimeLocal(transaction.tran_date);

        /*
         * Wait until the payment methods are loaded.
         */
        const payMethods = await get_payment_methods();

        let payHTML = '<option value="">Select Payment</option>';

        payMethods.forEach(function (data) {
            const selected =
                String(paymethod_PMID) === String(data.PMID)
                    ? 'selected'
                    : '';

            payHTML += `
                <option value="${data.PMID}" ${selected}>
                    ${data.PaymethodName}
                </option>
            `;
        });

        const html = `
            <div
            class="row payment-row align-items-end"
            id="payment-row-${TRID}"
            data-transaction-id="${TRID}"
        >
            <div class="col-md-4 mt-3">
                <label
                    for="payment-amount-${TRID}"
                    class="form-label"
                >
                    Amount <span class="text-danger">*</span>
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="ti ti-cash-banknote"></i>
                    </span>

                    <input
                        type="number"
                        name="payment_amount[${TRID}]"
                        id="payment-amount-${TRID}"
                        class="form-control payment-amount"
                        data-transaction-id="${TRID}"
                        step="0.01"
                        min="0"
                        value="${TransferAmount}"
                    >
                </div>
            </div>

            <div class="col-md-4 mt-3">
                <label
                    for="payment-date-${TRID}"
                    class="form-label"
                >
                    Date <span class="text-danger">*</span>
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="ti ti-calendar-stats"></i>
                    </span>

                    <input
                        type="datetime-local"
                        name="payment_date[${TRID}]"
                        id="payment-date-${TRID}"
                        class="form-control payment-date"
                        data-transaction-id="${TRID}"
                        value="${tranDate}"
                    >
                </div>
            </div>

            <div class="col-md-3 mt-3">
                <label
                    for="payment-method-${TRID}"
                    class="form-label"
                >
                    Payment Method
                    <span class="text-danger">*</span>
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="ti ti-wallet"></i>
                    </span>

                    <select
                        name="payment_method[${TRID}]"
                        id="payment-method-${TRID}"
                        class="form-select payment-method"
                        data-transaction-id="${TRID}"
                    >
                        ${payHTML}
                    </select>
                </div>
            </div>

            <div class="col-md-1 mt-3">
                <button class="deletPayment btn btn-danger w-100" title="Delete payment" data-transaction-id="${TRID}"><i class="ti ti-trash-x"></i></button>
            </div>
        </div>
        `;

        /*
         * Change #payment-container to your actual container ID.
         */
        $('#payment_grid').append(html);

    } catch (error) {
        console.error("Payment AJAX error:", error);

        toastr.error(
            "An error occurred while adding the payment.",
            "Error"
        );
    }
}

function delete_payment(TRID) {
    const $paymentRow = $('#payment-row-' + TRID);
    const $deleteButton = $paymentRow.find('.remove-payment');

    $deleteButton.prop('disabled', true);

    return $.ajax({
        url: '../AJAX/GRN/deleteGRNPayment.php',
        method: 'POST',
        dataType: 'json',

        data: {
            TRID: TRID,
            GHID: GHID
        },

        success: function (response) {
            if (Number(response.status) !== 1) {
                toastr.error(
                    response.message || "Unable to delete payment.",
                    "Error"
                );

                $deleteButton.prop('disabled', false);
                return;
            }

            /*
             * Remove the deleted payment row with an animation.
             */
            $paymentRow.fadeOut(250, function () {
                $(this).remove();

                show_empty_payment_message();
            });

            toastr.success(
                response.message || "Payment deleted successfully.",
                "Success"
            );
        },

        error: function (xhr, status, error) {
            console.error("Delete payment AJAX error:", {
                status: status,
                error: error,
                response: xhr.responseText
            });

            toastr.error(
                "An error occurred while deleting the payment.",
                "Error"
            );

            $deleteButton.prop('disabled', false);
        }
    });
}
function update_payment(TRID) {
    const $paymentRow = $('#payment-row-' + TRID);

    const TransferAmount = $paymentRow
        .find('.payment-amount')
        .val();

    const tranDate = $paymentRow
        .find('.payment-date')
        .val();

    const PMID = $paymentRow
        .find('.payment-method')
        .val();

    if (
        TransferAmount === "" ||
        isNaN(TransferAmount) ||
        Number(TransferAmount) < 0
    ) {
        toastr.error(
            "Please enter a valid payment amount.",
            "Error"
        );

        return $.Deferred().reject().promise();
    }

    if (!tranDate) {
        toastr.error(
            "Please select the payment date.",
            "Error"
        );

        return $.Deferred().reject().promise();
    }

    if (!PMID) {
        toastr.error(
            "Please select a payment method.",
            "Error"
        );

        return $.Deferred().reject().promise();
    }

    $paymentRow.addClass('payment-updating');

    $paymentRow
        .find('input, select, button')
        .prop('disabled', true);

    return $.ajax({
        url: '../AJAX/GRN/updateGRNPayment.php',
        method: 'POST',
        dataType: 'json',

        data: {
            TRID: TRID,
            GHID: GHID,
            TransferAmount: TransferAmount,
            PMID: PMID,
            tran_date: tranDate
        },

        success: function (response) {
            if (Number(response.status) !== 1) {
                toastr.error(
                    response.message || "Unable to update payment.",
                    "Error"
                );

                return;
            }

            /*
             * Apply the formatted value returned by PHP.
             */
            if (response.data) {
                $paymentRow
                    .find('.payment-amount')
                    .val(response.data.TransferAmount);

                $paymentRow
                    .find('.payment-date')
                    .val(
                        formatDateTimeLocal(
                            response.data.tran_date
                        )
                    );

                $paymentRow
                    .find('.payment-method')
                    .val(response.data.paymethod_PMID);
            }

            toastr.success(
                response.message || "Payment updated.",
                "Success"
            );
        },

        error: function (xhr, status, error) {
            console.error("Update payment AJAX error:", {
                status: status,
                error: error,
                response: xhr.responseText
            });

            toastr.error(
                "An error occurred while updating the payment.",
                "Error"
            );
        },

        complete: function () {
            $paymentRow.removeClass('payment-updating');

            $paymentRow
                .find('input, select, button')
                .prop('disabled', false);
        }
    });
}
function formatDateTimeLocal(dateTime) {
    if (!dateTime) {
        return "";
    }

    /*
     * Converts:
     * 2026-08-20 10:30:00
     * into:
     * 2026-08-20T10:30
     */
    return String(dateTime)
        .replace(" ", "T")
        .substring(0, 16);
}
async function fetchgrnDetail(GHID) {
    try {
        TableLoading($table, "GRN Items");

        const response = await $.ajax({
            url: '../AJAX/GRN/getGRNItems.php',
            method: 'POST',
            data: {
                GHID: GHID
            },
            dataType: 'json'
        });

        $table.find("tbody").empty();

        if (response.error) {
            if (Array.isArray(response.error)) {
                response.error.forEach(function (errorMsg) {
                    toastr.error(errorMsg, "Error");
                });
            } else {
                toastr.error(response.error, "Error");
            }

            applyGRNDocumentLock();
            return;
        }

        const rowRequests = [];

        if (
            response.ItemName &&
            response.ItemName.length > 0
        ) {
            for (
                let i = 0;
                i < response.ItemName.length;
                i++
            ) {
                /*
                 * Save each addtocart AJAX request.
                 */
                rowRequests.push(
                    addtocart(
                        response.ItemName[i],
                        response.ProductNo[i],
                        response.Barcode[i],
                        response.PurchasePrice[i],
                        response.SellingPrice[i],
                        response.totqty[i],
                        response.PDID[i],
                        response.ExpDate[i],
                        response.MnfDate[i],
                        1,
                        response.GDID[i]
                    )
                );
            }
        }

        /*
         * Wait for all GRN rows to finish rendering.
         */
        await Promise.all(rowRequests);

        /*
         * Apply the lock after every row exists.
         */
        applyGRNDocumentLock();

        grandTotal();

    } catch (error) {
        $table.find("tbody").empty();

        console.error("Fetch GRN details error:", error);

        toastr.error(
            "An error occurred while fetching GRN details.",
            "Error"
        );

        /*
         * Keep the document locked even if fetching fails.
         */
        applyGRNDocumentLock();
    }
}
function updategrndetail(element,GDID)
{
    var elementid = element.attr("class");
    var qty = $("#qty-cartItem-"+GDID).val();
    var purchasePrice = $("#purchasePrice-"+GDID).val();
    var SellingPrice = $("#SellingPrice-"+GDID).val();
    var mfgDate = $("#mfgDate-"+GDID).val();
    var expDate = $("#expDate-"+GDID).val();
    return $.ajax({
            url: '../AJAX/GRN/updategrndetail.php',
            method: 'POST',
            data: {
                GDID: GDID,
                qty: qty,
                purchasePrice: purchasePrice,
                SellingPrice: SellingPrice,
                mfgDate: mfgDate,
                expDate: expDate
            },
            dataType: 'json',
            beforeSend: function () {
            //   showGrnHeaderLoading(1);  
            },
            success: function (response) {
                if (response["error"]) {
                    if (Array.isArray(response["error"])) {
                        response["error"].forEach(function(errorMsg) {
                            toastr.error(errorMsg, "Error");
                        });
                    } else {
                        toastr.error(response["error"], "Error");
                    }
                }
                else
                {

                }
                grandTotal();
            },
            error: function (xhr, status, error) {
                // hideGrnHeaderLoading(); 
                console.log("AJAX Error:", error);
                toastr.error("An error occurred while updating GRN details.", "Error");
            }
    })

}
function addtocart(ItemName, ProductNo, Barcode, PurchasePrice, SellingPrice, totqty, PDID, ExpDate, MnfDate, qty=1, GDID="")
{
    var shopid = $("#shopLocation").val();
    return $.ajax({
            url: '../AJAX/GRN/insertGRNdetails.php',
            method: 'POST',
            data: {
                ItemName: ItemName,
                ProductNo: ProductNo,
                Barcode: Barcode,
                PurchasePrice: PurchasePrice,
                SellingPrice: SellingPrice,
                qty: qty,
                PDID: PDID,
                ExpDate: ExpDate,
                MnfDate: MnfDate,
                GDID: GDID,
                shopid: shopid,
                grn_header: grn_header
            },
            dataType: 'json',
            beforeSend: function () {
            //   showGrnHeaderLoading(1);  
            },
            success: function (response) {
                    if (response["error"]) {
                        if (Array.isArray(response["error"])) {
                            response["error"].forEach(function(errorMsg) {
                                toastr.error(errorMsg, "Error");
                            });
                        } else {
                            toastr.error(response["error"], "Error");
                        }
                    }
                    else
                    {
                        var rowNo = $(".row-number").length + 1;
                        var CurrentQty = response["CurrentQty"];
                        var ExpDate = response["ExpDate"];
                        var GDID = response["GDID"];
                        var MnfDate = response["MnfDate"];
                        var TotalPurchasePrice = parseFloat(response["TotalPurchasePrice"]) || 0;
                        var TotalSellPrice = parseFloat(response["TotalSellPrice"]) || 0;
                        var UnitPurchasePrice = parseFloat(response["UnitPurchasePrice"]) || 0;
                        var UnitSellPrice = parseFloat(response["UnitSellPrice"]) || 0;
                        var profit = UnitSellPrice - UnitPurchasePrice;
                        var markup = UnitPurchasePrice > 0
                            ? (((UnitSellPrice - UnitPurchasePrice) / UnitPurchasePrice) * 100).toFixed(2)
                            : "0.00";
                        var products_PDID = response["products_PDID"];
                        var stock = response["stock"];
                        var ItemName = response["ItemName"];
                        var ProductNo = response["ProductNo"];
         
                        var Barcode = response["Barcode"];
                        var tr =`<tr class="grn-row" id="grn-row-${GDID}">
                                    <td>
                                        <span class="row-number" data-gdid="${GDID}">${rowNo}</span>
                                    </td>
                                    <td>
                                        <div class="product-info">
                                            <input type="hidden" name="pdid[]" data-gdid="${GDID}" value="${products_PDID}">
                                            <div class="product-name text-truncate" data-gdid="${GDID}">
                                                ${ItemName}
                                            </div>
                                            <div class="product-meta">
                                                <span>
                                                    <strong class="text-truncate" title="Code: ${ProductNo}" data-gdid="${GDID}">Code:</strong> ${ProductNo}
                                                </span>
                                                <span>
                                                    <strong class="text-truncate" title="Barcode: ${Barcode}" data-gdid="${GDID}">Barcode:</strong> ${Barcode}
                                                </span>
                                                <span class="stock-badge text-truncate" title="Stock: ${stock}" data-gdid="${GDID}">
                                                    Stock ${stock}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <a href="javascript:void(0)" class="input-group-text increase-qty" data-gdid="${GDID}"><i class="ti ti-plus"></i></a>
                                            <input type="number" name="qty[]" id="qty-cartItem-${GDID}" class="form-control qty border-none text-center grn-detail" value="${CurrentQty}" step="any" data-gdid="${GDID}" required>
                                            <a href="javascript:void(0)" class="input-group-text decrease-qty" data-gdid="${GDID}"><i class="ti ti-minus"></i></a>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" name="purchasePrice[]" id="purchasePrice-${GDID}" class="form-control modern-input purchasePrice grn-detail" step="any" value="${UnitPurchasePrice}" data-gdid="${GDID}">
                                    </td>
                                    <td>
                                        <input type="number" name="SellingPrice[]" id="SellingPrice-${GDID}" class="form-control modern-input SellingPrice grn-detail" step="any" value="${UnitSellPrice}" data-gdid="${GDID}">
                                    </td>
                                    <td>
                                        <input type="number" name="totpurchasePrice[]" id="totpurchasePrice-${GDID}" class="form-control modern-input totpurchasePrice grn-detail" step="any" value="${TotalPurchasePrice}" data-gdid="${GDID}" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="totSellingPrice[]" id="totSellingPrice-${GDID}" class="form-control modern-input totSellingPrice grn-detail" step="any" value="${TotalSellPrice}" data-gdid="${GDID}" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="markup[]" id="markup-${GDID}" class="form-control modern-input markup grn-detail" step="any" value="${markup}" data-gdid="${GDID}">
                                    </td>
                                    <td>
                                        <input type="date" name="mfgDate[]" id="mfgDate-${GDID}" class="form-control modern-input mfgDate grn-detail" value="${MnfDate}" data-gdid="${GDID}">
                                    </td>
                                    <td>
                                        <input type="date" name="expDate[]" id="expDate-${GDID}" class="form-control modern-input expDate grn-detail" value="${ExpDate}" data-gdid="${GDID}">
                                    </td>
                                    <td>
                                        <button class="deletRow btn btn-danger delete-btn" id="deleteRow-${GDID}" data-gdid="${GDID}">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </td>
                                </tr>`;
                        $table.find("tbody").append(tr);                            
                    }
                    grandTotal();
            },
            error: function (xhr, status, error) {
                // hideGrnHeaderLoading(); 
                console.log("AJAX Error:", error);
                toastr.error("An error occurred while inserting GRN details.", "Error");
            }
        });
}

function addLineItem(ItemId)
{
    var shopid = $("#shopLocation").val();
    if (ItemId)
    {
        // alert(ItemId);
        $("#search-items").val(null).trigger("change"); 
        return $.ajax({
            url: '../AJAX/GRN/getItems.php',
            method: 'POST',
            data: {
                ItemId: ItemId,
                shopid: shopid
            },
            dataType: 'json',
            beforeSend: function () {
            //   showGrnHeaderLoading(1);  
            },
            success: function (response) {
                    if (response["error"]) {
                        if (Array.isArray(response["error"])) {
                            response["error"].forEach(function(errorMsg) {
                                toastr.error(errorMsg, "Error");
                            });
                        } else {
                            toastr.error(response["error"], "Error");
                        }
                    }
                    else
                    {
                        // console.log("getItems"+response);
                        var ItemName = response["ItemName"];
                        var ProductNo = response["ProductNo"];
                        var Barcode = response["Barcode"];
                        var PurchasePrice = response["PurchasePrice"];
                        var SellingPrice = response["SellingPrice"];
                        var totqty = response["totqty"];
                        var PDID = response["PDID"];
                        var ExpDate = response["ExpDate"];
                        var MnfDate = response["MnfDate"];
                        addtocart(ItemName, ProductNo, Barcode, PurchasePrice, SellingPrice, totqty, PDID, ExpDate, MnfDate);                        
                    }
            },
            error: function (xhr, status, error) {
                // hideGrnHeaderLoading(); 
                console.log("AJAX Error:", error);
                toastr.error("An error occurred while fetching GRN details.", "Error");
            }
        });
    }
    
}

function setPredefinedSupplier(supplierId, supplierName) 
{
    // Create a new option with the predefined value
    var option = new Option(supplierName, supplierId, true, true);

    // Append the option to Select2 and trigger the change event
    $("#grnSupplier").append(option);
}
function deletegrnDetail(gdid)
{
    return $.ajax({
            url: '../AJAX/GRN/deletegrndetails2.php',
            method: 'POST',
            data: {
                gdid: gdid
            },
            dataType: 'json',
            beforeSend: function () {
            //   showGrnHeaderLoading(1);  
            },
            success: function (response) {
                    if (response["error"]) {
                        if (Array.isArray(response["error"])) {
                            response["error"].forEach(function(errorMsg) {
                                toastr.error(errorMsg, "Error");
                            });
                        } else {
                            toastr.error(response["error"], "Error");
                        }
                    }
                    else
                    {
                        toastr.success(response["success"], "success");                
                    }
            },
            error: function (xhr, status, error) {
                // hideGrnHeaderLoading(); 
                console.log("AJAX Error:", error);
                toastr.error("An error occurred while fetching GRN details.", "Error");
            }
        });
        grandTotal();
}
function getGRNpaymentDetails(GHID)
{
    return $.ajax({
            url: '../AJAX/GRN/getGRNpaymentDetails.php',
            method: 'POST',
            data: {
                GHID: GHID
            },
            dataType: 'json',
            beforeSend: function () {
            //   showGrnHeaderLoading(1);  
            },
            success: function (response) {
                    if (response["error"]) {
                        if (Array.isArray(response["error"])) {
                            response["error"].forEach(function(errorMsg) {
                                toastr.error(errorMsg, "Error");
                            });
                        } else {
                            toastr.error(response["error"], "Error");
                        }
                    }
                    else
                    {
                        toastr.success(response["success"], "success");                
                    }
            },
            error: function (xhr, status, error) {
                // hideGrnHeaderLoading(); 
                console.log("AJAX Error:", error);
                toastr.error("An error occurred while fetching GRN details.", "Error");
            }
        });
}
function grandTotal()
{
    
    var rowcount = $table.find("tbody tr").length;    
    $("#rowNoText").text(rowcount);
    var totPurchase = 0;
    var totSell = 0;

    $table.find("tbody .totpurchasePrice").each(function () {
        totPurchase += parseFloat($(this).val()) || 0;
    });

    $table.find("tbody .totSellingPrice").each(function () {
        totSell += parseFloat($(this).val()) || 0;
    });

    $("#totPurchText").text(totPurchase.toFixed(2));
    $("#totPurchInput").val(totPurchase.toFixed(2));
    var discountType = parseInt($("#discType").val()) || 1;
    var discount = parseFloat($("#disc").val()) || 0;

    var discountVal = 0;

    if (discountType == 1) {
        // Percentage
        discountVal = (discount * totPurchase) / 100;
    } else if (discountType == 2) {
        // Flat amount
        discountVal = discount;
    }
    var grandTotal = totPurchase - discountVal;
    $("#totDiscText").text(discountVal.toFixed(2));
    $("#totDiscInput").val(discountVal.toFixed(2));
    $("#grandTotText").text(grandTotal.toFixed(2));
    $("#grandTotInput").val(grandTotal.toFixed(2));
    return $.ajax({
            url: '../AJAX/GRN/updateGRNdiscount.php',
            method: 'POST',
            data: {
                GHID: GHID,
                PurchDiscType: discountType,
                PurchDisc: discount,
                TotalDisc: discountVal,
                ItemCount: rowcount,
                TotalPurchasePrice: totPurchase,
                TotalSellPrice: totSell,
            },
            dataType: 'json',
            beforeSend: function () {
            //   showGrnHeaderLoading(1);  
            },
            success: function (response) {
                    if (response["error"]) {
                        if (Array.isArray(response["error"])) {
                            response["error"].forEach(function(errorMsg) {
                                toastr.error(errorMsg, "Error");
                            });
                        } else {
                            toastr.error(response["error"], "Error");
                        }
                    }
                    else
                    {
                        // toastr.success(response["success"], "success");                
                    }
            },
            error: function (xhr, status, error) {
                // hideGrnHeaderLoading(); 
                console.log("AJAX Error:", error);
                toastr.error("An error occurred while updating GRN discount.", "Error");
            }
        });
    
}
$(document).ready(function(){
    $("#expenseDiv").hide();
    $("#paymentDiv").removeClass("col-md-6").addClass("col-md-12");
    if (GHID) {
        get_payments();
    }
    $(document).on( 'change', '.payment-date, .payment-method', function () 
        {
            const TRID = $(this).data('transaction-id');
            update_payment(TRID);
        }
    );
    $(document).on('click', '.deletPayment', function (e) {
        e.preventDefault();
        const TRID = $(this).data('transaction-id');

        if (!TRID) {
            toastr.error("Invalid transaction ID.", "Error");
            return;
        }

        const confirmed = confirm(
            "Are you sure you want to delete this payment?"
        );

        if (!confirmed) {
            return;
        }

        delete_payment(TRID);
    });
    $(document).on( 'blur', '.payment-amount', function () 
        {
            const TRID = $(this).data('transaction-id');

            update_payment(TRID);
        }
    );
    TableLoading($table, "GRN Items");
    $("#add_payment").on("click", function(e){
        e.preventDefault();
        add_payment();
    })
    $("#headerCollapse2").trigger("click");
    CKEDITOR.replace('referenceNo', {
        height: 200
    });
    // Optional: Clear the editor
    CKEDITOR.instances.referenceNo.setData('');
    CKEDITOR.replace('addNotes', {
        height: 200
    });
    // Optional: Clear the editor
    CKEDITOR.instances.addNotes.setData('');
    $(document).on('click', '.open-modal', function (e) {
      e.preventDefault();
      let url = $(this).attr('href');
      let title = $(this).data('title') || 'Company';
      openModal(url,title);
    });
    

    $(document).on("click", "#uploadBtn", function(e){
        e.preventDefault();
        uploadDocuments();
    });
    $("#search-items").on("change", function(e){
        addLineItem($(this).val());
    });
    showGrnHeaderLoading();
    initializeSearchItemSelect2(ShopID);
    initializegrnSupplierSelect2();
    fetchGRNHeader(grn_header).always(function(){
        fetchgrnDetail(grn_header);
    });
    
    let typingTimer;
    let typingDelay = 1500; // milliseconds (adjust if needed)

    CKEDITOR.instances.referenceNo.on("change", function () {

        clearTimeout(typingTimer);

        typingTimer = setTimeout(function () {

            
            var grnSupplier  = $("#grnSupplier").val();
            var referenceNo  = CKEDITOR.instances.referenceNo.getData();
            var grnStatus    = $("#grnStatus").val();
            var purchaseDate = $("#purchaseDate").val();
            var shopLocation = $("#shopLocation").val();

            updateGRN(
                GHID,
                grnSupplier,
                referenceNo,
                grnStatus,
                purchaseDate,
                shopLocation
            );

        }, typingDelay);

    });
    CKEDITOR.instances.addNotes.on("change", function () {

        clearTimeout(typingTimer);

        typingTimer = setTimeout(function () {

            
            var grnSupplier  = $("#grnSupplier").val();
            var referenceNo  = CKEDITOR.instances.referenceNo.getData();
            var addNotes  = CKEDITOR.instances.addNotes.getData();
            var grnStatus    = $("#grnStatus").val();
            var purchaseDate = $("#purchaseDate").val();
            var shopLocation = $("#shopLocation").val();

            updateGRN(
                GHID,
                grnSupplier,
                referenceNo,
                grnStatus,
                purchaseDate,
                shopLocation,
                addNotes
            );

        }, typingDelay);

    });
    $(".grn-foot").on("change, input", function(){
        clearTimeout(typingTimer);
        

        typingTimer = setTimeout(function () {
            $(".grn-foot").prop("disabled", true);
            grandTotal().always(function(){
                $(".grn-foot").prop("disabled", false);
            });

        }, typingDelay);
    })

    $(document).on("click", ".increase-qty", function () {
        var element = $(this);
        var GDID = element.data("gdid");
        var qtyelement = $("#qty-cartItem-"+GDID)
        var qty = parseFloat(qtyelement.val()) || 0;
        var newqty = qty + 1;
        qtyelement.val(newqty.toFixed(3)).trigger("input");
    });
    $(document).on("click", ".decrease-qty", function () {
        var element = $(this);
        var GDID = element.data("gdid");
        var qtyelement = $("#qty-cartItem-"+GDID)
        var qty = parseFloat(qtyelement.val()) || 0;
        var newqty = qty - 1;
        if(newqty <=0)
        {
            toastr.error("Qty cannot be 0 or negative values", "Error");
            qtyelement.val(qty.toFixed(3)).trigger("input");
        }
        else
        {
            qtyelement.val(newqty.toFixed(3)).trigger("input");
        }
        
    });
    $(document).on("click", ".delete-btn", function (e) {
        e.preventDefault();

        var element = $(this);
        var GDID = element.data("gdid");

        if (confirm("Are you sure you want to delete this record?")) {

            deletegrnDetail(GDID).done(function () {

                $("#grn-row-" + GDID).slideUp(300, function () {
                    $(this).remove();

                    $(".row-number").each(function (index) {
                        $(this).text(index + 1);
                    });
                    grandTotal();
                });
                

            });

        }
    });
    $(document).on("input", ".grn-detail", function () {
        console.log("grn-detail");
        

        var element = $(this);

        clearTimeout(typingTimer);

        typingTimer = setTimeout(function () {

            var GDID = element.data("gdid");
            if (element.hasClass("markup")) {

                // This is actually Markup (%)
                var markup = parseFloat(element.val()) || 1;
                var qty = parseFloat($("#qty-cartItem-" + GDID).val()) || 0;
                var purchasePrice = parseFloat($("#purchasePrice-" + GDID).val()) || 0;

                // Selling Price = Cost + Markup%
                var sellingPrice = purchasePrice * (1 + (markup / 100));

                $("#SellingPrice-" + GDID).val(sellingPrice.toFixed(2));

                var totalPurchasePrice = purchasePrice * qty;
                $("#totpurchasePrice-" + GDID).val(totalPurchasePrice.toFixed(2));

                var totalSellingPrice = sellingPrice * qty;
                $("#totSellingPrice-" + GDID).val(totalSellingPrice.toFixed(2));
            }
            else if(element.hasClass("qty"))
            {                
                var qty = parseFloat(element.val());

                if (isNaN(qty) || qty <= 0) 
                {
                    console.log("qty");
                    toastr.error("Qty cannot be 0 or negative values.", "Error");

                    qty = 1;
                    element.val(qty.toFixed(2));
                }

                var purchasePrice = parseFloat($("#purchasePrice-" + GDID).val()) || 0;
                var sellingPrice = parseFloat($("#SellingPrice-" + GDID).val()) || 0;

                var totalPurchasePrice = purchasePrice * qty;
                $("#totpurchasePrice-" + GDID).val(totalPurchasePrice.toFixed(2));

                var totalSellingPrice = sellingPrice * qty;
                $("#totSellingPrice-" + GDID).val(totalSellingPrice.toFixed(2));

                // Calculate Markup (%)
                var markup = purchasePrice > 0
                    ? (((sellingPrice - purchasePrice) / purchasePrice) * 100).toFixed(2)
                    : "0.00";

                $("#markup-" + GDID).val(markup);
            }
            else 
            {

                var qty = parseFloat($("#qty-cartItem-" + GDID).val()) || 1;
                var purchasePrice = parseFloat($("#purchasePrice-" + GDID).val()) || 0;
                var sellingPrice = parseFloat($("#SellingPrice-" + GDID).val()) || 0;

                var totalPurchasePrice = purchasePrice * qty;
                $("#totpurchasePrice-" + GDID).val(totalPurchasePrice.toFixed(2));

                var totalSellingPrice = sellingPrice * qty;
                $("#totSellingPrice-" + GDID).val(totalSellingPrice.toFixed(2));

                // Calculate Markup (%)
                var markup = purchasePrice > 0
                    ? (((sellingPrice - purchasePrice) / purchasePrice) * 100).toFixed(2)
                    : "0.00";

                $("#markup-" + GDID).val(markup);
            }
            updategrndetail(element, GDID);
        }, typingDelay);
    });
    $("#referenceNo").on("input", function () {
        clearTimeout(typingTimer);

        typingTimer = setTimeout(function () {
            var grnSupplier  = $("#grnSupplier").val();
            var referenceNo  = $("#referenceNo").val();
            var grnStatus    = $("#grnStatus").val();
            var purchaseDate = $("#purchaseDate").val();
            var shopLocation = $("#shopLocation").val();

            updateGRN(GHID, grnSupplier, referenceNo, grnStatus, purchaseDate, shopLocation);
        }, typingDelay);
    });
    $(".fieldData").on("change",function(e){
        e.preventDefault();
        var grnSupplier  = $("#grnSupplier").val();
        var referenceNo  = $("#referenceNo").val();
        var grnStatus    = $("#grnStatus").val();
        var purchaseDate = $("#purchaseDate").val();
        var shopLocation = $("#shopLocation").val();
        updateGRN(GHID,grnSupplier,referenceNo,grnStatus,purchaseDate,shopLocation)
    });
    $("#refresh").on("click", function () {
        let $btn = $(this);
        let $icon = $btn.find("i");
        $btn.prop("disabled", true);
        $icon.css({
            "animation": "spin 0.8s linear infinite",
            "display": "inline-block"
        });
        TableLoading($table, "GRN Items");
        fetchGRNHeader(GHID).always(function () {
            // Stop spinning when AJAX completes
            fetchgrnDetail(GHID);
            $btn.prop("disabled", false);
            $icon.css("animation", "none");
        });  
    });

})