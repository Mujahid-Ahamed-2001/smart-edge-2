let $table = $('#tbl_expense_type');
let ShopID = $("#shop_id").val();
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
// ========================================================
// OPEN NATIVE PHONE CAMERA
// ========================================================

$(document).on("click", "#scan", function (e) {

    e.preventDefault();
    e.stopPropagation();

    // Clear previous image so same barcode/photo can be selected again
    $("#barcode-photo-input").val("");

    // Open iPhone camera
    $("#barcode-photo-input").trigger("click");

});
$(document).on("keydown", ".barcode", function (e) {
    if (e.key === "Enter") {
        e.preventDefault();
        return false;
    }
});
$(document).on("keydown", ".barcodes", function (e) {

    if (e.key === "Enter") {

        e.preventDefault();

        var $barcodes = $(".barcodes");
        var currentIndex = $barcodes.index(this);

        var $nextBarcode = $barcodes.eq(currentIndex + 1);

        if ($nextBarcode.length) {
            $nextBarcode.focus().select();
        }

        return false;
    }

});

// ========================================================
// PHOTO CAPTURED
// ========================================================

$(document).on("change", "#barcode-photo-input", async function (e) {

    const file = e.target.files[0];

    if (!file) {
        return;
    }

    toastr.info("Reading barcode...");

    try {

        // =============================================
        // Create image from captured photo
        // =============================================

        const imageUrl = URL.createObjectURL(file);

        const image = new Image();

        image.src = imageUrl;


        await new Promise(function (resolve, reject) {

            image.onload = resolve;

            image.onerror = reject;

        });


        console.log(
            "Captured image:",
            image.naturalWidth,
            image.naturalHeight
        );


        // =============================================
        // ZXING
        // =============================================

        const codeReader =
            new ZXing.BrowserMultiFormatReader();


        const result =
            await codeReader.decodeFromImage(
                image
            );


        // =============================================
        // BARCODE FOUND
        // =============================================

        if (result) {

            const barcode =
                result.getText();


            console.log(
                "Barcode detected:",
                barcode
            );


            // Put barcode into your existing field
            $("#Barcode")
                .val(barcode)
                .trigger("change");


            toastr.success(
                "Barcode scanned: " + barcode
            );


            // Move user to product name
            $("#ItemName").focus();

        }


        // Clean temporary browser URL
        URL.revokeObjectURL(imageUrl);

    }
    catch (error) {

        console.error(
            "Barcode detection error:",
            error
        );


        toastr.warning(
            "Barcode could not be detected. Take a closer, clearer photo and try again."
        );

    }

});
function getsubcategories(elementid, subid = 0) {

    return $.ajax({
        url: "../AJAX/Product/getsubcat.php",
        type: "POST",
        dataType: "json",

        success: function (response) {

            var html = `<option value="">Select Subcategory</option>`;

            response.forEach(function (element) {

                var selected = (element.SCID == subid)
                    ? "selected"
                    : "";

                html += `
                    <option value="${element.SCID}" ${selected}>
                        ${element.SubCatName}
                    </option>
                `;
            });

            $(`#${elementid}`).html(html);
        },

        error: function (xhr, status, error) {

            console.error("AJAX Error:", error);
            console.error("Server Response:", xhr.responseText);

            toastr.error(
                "Something went wrong while fetching the subcategories."
            );
        }
    });
}
function appendProductRow(product) {

    var PDID = product.PDID ?? "";
    var Barcode = product.Barcode ?? "";
    var ItemName = product.ItemName ?? "";
    var Subcategories_SCID = product.Subcategories_SCID ?? "";
    var ProdPurchasePrice = product.ProdPurchasePrice ?? 0;
    var ProdSellPrice = product.ProdSellPrice ?? 0;
    var opening_qty = product.opening_qty ?? 0;
    var low_stock_qty = product.low_stock_qty ?? 0;

    var row = `
        <tr class="row-saved" data-pdid="${PDID}">

            <td>
                <input type="text"
                    class="form-control barcodes"
                    name="Barcode"
                    value="${Barcode}">
            </td>

            <td>
                <input type="text"
                    class="form-control product_name"
                    name="ItemName"
                    value="${ItemName}">
            </td>

            <td>
                <select
                    class="form-select category"
                    name="Subcategories_SCID">
                </select>
            </td>

            <td>
                <input type="number"
                    class="form-control cost"
                    name="ProdPurchasePrice"
                    value="${ProdPurchasePrice}"
                    step="any">
            </td>

            <td>
                <input type="number"
                    class="form-control selling_price"
                    name="ProdSellPrice"
                    value="${ProdSellPrice}"
                    step="any">
            </td>

            <td>
                <input type="number"
                    class="form-control opening_qty"
                    name="opening_qty"
                    value="${opening_qty}"
                    step="any">
            </td>

            <td>
                <input type="number"
                    class="form-control low_stock"
                    name="low_stock_qty"
                    value="${low_stock_qty}"
                    step="any">
            </td>

            <td>
                <button type="button"
                    class="btn-action btn-update-product"
                    data-pdid="${PDID}">

                    <i class="ti ti-device-floppy"></i>

                </button>
            </td>

        </tr>
    `;


    $("#product-table-body").append(row);


    // Newly appended row
    var $newRow = $(
        `#product-table-body tr[data-pdid="${PDID}"]`
    ).last();


    // Load category and select existing value
    var $category = $newRow.find(".category");

    getsubcategoriesForRow(
        $category,
        Subcategories_SCID
    );
}
function loadMultiProducts() {

    return $.ajax({
        url: "../AJAX/Product/getMultiProducts.php",
        type: "GET",
        dataType: "json",

        beforeSend: function () {

            // Optional loading row
            $("#product-table-body").html(`
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                        <span class="ms-2">Loading products...</span>
                    </td>
                </tr>
            `);
        },

        success: function (response) {

            // Clear loading row
            $("#product-table-body").empty();


            // ==========================================
            // Request Failed
            // ==========================================

            if (response.status != 1) {

                toastr.error(
                    response.msg || "Unable to load products."
                );

                return;
            }


            // ==========================================
            // No Products
            // ==========================================

            if (
                !response.data ||
                response.data.length === 0
            ) {

                $("#product-table-body").html(`
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            No products found.
                        </td>
                    </tr>
                `);

                return;
            }


            // ==========================================
            // Append Products
            // ==========================================

            response.data.forEach(function (product) {

                appendProductRow(product);

            });

        },

        error: function (xhr, status, error) {

            console.error("AJAX Error:", error);
            console.error("Server Response:", xhr.responseText);

            $("#product-table-body").html(`
                <tr>
                    <td colspan="8" class="text-center py-4 text-danger">
                        Unable to load products.
                    </td>
                </tr>
            `);

            toastr.error(
                "Something went wrong while loading the products."
            );
        }
    });
}
function getsubcategoriesForRow($element, subid = 0) {

    return $.ajax({
        url: "../AJAX/Product/getsubcat.php",
        type: "POST",
        dataType: "json",

        success: function (response) {

            var html = `
                <option value="">Select Subcategory</option>
            `;

            response.forEach(function (element) {

                var selected = (element.SCID == subid)
                    ? "selected"
                    : "";

                html += `
                    <option value="${element.SCID}" ${selected}>
                        ${element.SubCatName}
                    </option>
                `;
            });

            $element.html(html);
        },

        error: function (xhr, status, error) {

            console.error("AJAX Error:", error);
            console.error("Server Response:", xhr.responseText);

            toastr.error(
                "Something went wrong while fetching the subcategories."
            );
        }
    });
}
function validateSellingPrice(cost, selling, $sellingInput) {

    cost = parseFloat(cost) || 0;
    selling = parseFloat(selling) || 0;

    // Selling price must be higher than cost price
    if (selling < cost) {

        toastr.error(
            "Selling price must be higher than the cost price."
        );

        $sellingInput.focus();
        $sellingInput.select();

        return false;
    }

    return true;
}
function updateProductRow($row) {

    var PDID = $row.data("pdid");

    if (!PDID) {
        toastr.error("Invalid product ID.");
        return;
    }
    var $costInput = $row.find(".cost");
    var $sellingInput = $row.find(".selling_price");

    var cost = $costInput.val();
    var selling = $sellingInput.val();


    if (!validateSellingPrice(cost,selling,$sellingInput)) 
    {
        return;
    }

    var data = {
        PDID: PDID,
        Barcode: $row.find(".barcodes").val(),
        ItemName: $row.find(".product_name").val(),
        Subcategories_SCID: $row.find(".category").val(),
        ProdPurchasePrice: $row.find(".cost").val(),
        ProdSellPrice: $row.find(".selling_price").val(),
        opening_qty: $row.find(".opening_qty").val(),
        low_stock_qty: $row.find(".low_stock").val()
    };


    $.ajax({
        url: "../AJAX/Product/Addproduct.php?condition=update",
        type: "POST",
        data: data,
        dataType: "json",

        beforeSend: function () {

            $row.addClass("updating");

        },

        success: function (response) {

            console.log(response);


            if (response.status == 1) {

                if (
                    response.product &&
                    response.product.data &&
                    response.product.data.Barcode
                ) {

                    $row.find(".barcodes").val(
                        response.product.data.Barcode
                    );
                }

                toastr.success(
                    response.msg || "Product updated successfully."
                );

                return;
            }


            if (response.status == 2) {

                toastr.success("Product details updated.");

                if (
                    response.inventory &&
                    response.inventory.status == 0
                ) {
                    toastr.error(
                        response.inventory.rmsg ||
                        response.inventory.msg
                    );
                }

                if (
                    response.pricehistory &&
                    response.pricehistory.status == 0
                ) {
                    toastr.error(
                        response.pricehistory.rmsg ||
                        response.pricehistory.msg
                    );
                }

                return;
            }


            toastr.error(
                response.msg || "Product could not be updated."
            );
        },

        error: function (xhr, status, error) {

            console.error("AJAX Error:", error);
            console.error("Server Response:", xhr.responseText);

            toastr.error(
                "Something went wrong while updating the product."
            );

        },

        complete: function () {

            $row.removeClass("updating");

        }
    });
}
$(document).ready(function(){
    $("#headerCollapse2").trigger("click");
    getsubcategories("Subcategories_SCID");
    loadMultiProducts();
    $(document).on("change", "#product-table-body .row-saved input, #product-table-body .row-saved select", function () 
    {

        var $row = $(this).closest(".row-saved");

        updateProductRow($row);
    });
    $("#refresh").click(function(){
        let $btn = $(this);
        let $icon = $btn.find("i");
        $btn.prop("disabled", true);
        $icon.css({
            "animation": "spin 0.8s linear infinite",
            "display": "inline-block"
        });
        // setTimeout(() => {
            loadMultiProducts().always(function () {
                // Stop spinning when AJAX completes
                $btn.prop("disabled", false);
                $icon.css("animation", "none");
            });     
        // }, 3000);
                   
    });
    $("#add-row").on("click", function (e) {
        e.preventDefault();

        $.ajax({
            url: "../AJAX/Product/Addproduct.php?condition=new",
            type: "POST",

            data: {
                Barcode: "",
                ItemName: "",
                Subcategories_SCID: "",
                ProdPurchasePrice: "",
                ProdSellPrice: "",
                opening_qty: "",
                low_stock_qty: "",
                multi: 1
            },

            dataType: "json",

            beforeSend: function () {
                $("#add-row").prop("disabled", true);
            },

            success: function (response) {

                console.log(response);

                if (response.status == 0) {
                    toastr.error(
                        response.product?.rmsg ||
                        response.product?.msg ||
                        response.msg ||
                        "Product could not be created."
                    );
                    return;
                }

                if (response.status == 1 || response.status == 2) {

                    var product = {
                        PDID: response.product.PDID,
                        Barcode: "",
                        ItemName: "",
                        Subcategories_SCID: "",
                        ProdPurchasePrice: 0,
                        ProdSellPrice: 0,
                        opening_qty: 0,
                        low_stock_qty: 0
                    };

                    appendProductRow(product);

                    toastr.success("New empty row created.");

                    return;
                }

                toastr.error("Unexpected server response.");
            },

            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
                console.error("Server Response:", xhr.responseText);

                toastr.error("Could not create new product row.");
            },

            complete: function () {
                $("#add-row").prop("disabled", false);
            }
        });
    });
    $("#add-product").on("submit", function (e) {

        e.preventDefault();

        var form = this;
        var $costInput = $(form).find('[name="ProdPurchasePrice"]');

        var $sellingInput = $(form).find('[name="ProdSellPrice"]');

        var cost = $costInput.val();
        var selling = $sellingInput.val();


        // ==========================================
        // Validate Selling Price
        // ==========================================

        if (!validateSellingPrice(cost,selling,$sellingInput)) 
        {
            return;
        }
        var url = $(form).attr("action");
        var data = new FormData(form);

        $.ajax({
            url: url,
            type: "POST",
            data: data,
            dataType: "json",
            processData: false,
            contentType: false,

            beforeSend: function () {
                $("#btn-submit").prop("disabled", true);
            },

            success: function (response) {

                console.log(response);


                // ==========================================
                // Product Creation Failed
                // ==========================================

                if (response.status == 0) {

                    var message =
                        response.product?.rmsg ||
                        response.product?.msg ||
                        response.msg ||
                        "Product could not be created.";

                    toastr.error(message);

                    return;
                }


                // ==========================================
                // Everything Successful
                // ==========================================

                if (response.status == 1) {

                    toastr.success(
                        response.msg || "Product created successfully."
                    );

                    // ==========================================
                    // Build product data from submitted form
                    // ==========================================

                    var product = {
                        PDID: response.product.PDID,

                        Barcode: data.get("Barcode") || "",

                        ItemName: data.get("ItemName") || "",

                        Subcategories_SCID:
                            data.get("Subcategories_SCID") || "",

                        ProdPurchasePrice:
                            data.get("ProdPurchasePrice") || 0,

                        ProdSellPrice:
                            data.get("ProdSellPrice") || 0,

                        opening_qty:
                            data.get("opening_qty") || 0,

                        low_stock_qty:
                            data.get("low_stock_qty") || 0
                    };


                    // ==========================================
                    // Append row
                    // ==========================================

                    appendProductRow(product);


                    // ==========================================
                    // Reset Add Product Form
                    // ==========================================

                    form.reset();

                    getsubcategories("Subcategories_SCID");

                    return;
                }


                // ==========================================
                // Partial Success
                // Product created, but something else failed
                // ==========================================

                if (response.status == 2) {

                    // Product itself was successfully created
                    toastr.success("Product created successfully.");


                    // --------------------------------------
                    // Default Inventory
                    // --------------------------------------

                    if (
                        response.default_inventory &&
                        response.default_inventory.status == 0
                    ) {

                        toastr.error(
                            response.default_inventory.rmsg ||
                            response.default_inventory.msg ||
                            "Default inventory could not be created."
                        );
                    }


                    // --------------------------------------
                    // Default Price History
                    // --------------------------------------

                    if (
                        response.default_pricehistory &&
                        response.default_pricehistory.status == 0 &&
                        !response.default_pricehistory.skipped
                    ) {

                        toastr.error(
                            response.default_pricehistory.rmsg ||
                            response.default_pricehistory.msg ||
                            "Default price history could not be created."
                        );
                    }


                    // --------------------------------------
                    // Opening Inventory
                    // --------------------------------------

                    if (
                        response.opening_inventory &&
                        response.opening_inventory.status == 0
                    ) {

                        toastr.error(
                            response.opening_inventory.rmsg ||
                            response.opening_inventory.msg ||
                            "Opening stock inventory could not be created."
                        );
                    }


                    // --------------------------------------
                    // Opening Price History
                    // --------------------------------------

                    if (
                        response.opening_pricehistory &&
                        response.opening_pricehistory.status == 0 &&
                        !response.opening_pricehistory.skipped
                    ) {

                        toastr.error(
                            response.opening_pricehistory.rmsg ||
                            response.opening_pricehistory.msg ||
                            "Opening stock price history could not be created."
                        );
                    }

                    return;
                }


                // ==========================================
                // Unexpected Response
                // ==========================================

                toastr.error("Unexpected server response.");

            },

            error: function (xhr, status, error) {

                console.error("AJAX Error:", error);
                console.error("Server Response:", xhr.responseText);

                toastr.error(
                    "Something went wrong while submitting the product."
                );

            },

            complete: function () {

                $("#btn-submit").prop("disabled", false);

            }
        });

    });
})