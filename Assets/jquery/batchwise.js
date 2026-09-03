var app_status = $(document).find("#app_status").val();
console.log("App Status "+app_status);

    let ShopID = $("#shop_id").val();
    let $table = $('#tbl_batchwise');

    let print_access = parseInt($("#print_access").val()) || 0;
    let verify_access = parseInt($("#verify_access").val()) || 0;
    let edit_access = parseInt($("#edit_access").val()) || 0;
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

function fetchcustomrData() {
    return $.ajax({
        url: "../AJAX/Batchwise/fetchdata.php",
        method: "POST",
        dataType: "json",

        beforeSend: function () {
            TableLoading($table, "Batchwise Data");
        },

        success: function (response) {
            if ($.fn.DataTable.isDataTable($table)) {
                $table.DataTable().clear().destroy();
            }

            const $tbody = $table.find("tbody");
            const headerCount = $table.find("thead th").length;
            const inventory = response?.inventory || [];

            $tbody.empty();
            console.log("response?.total_selling ", response?.total_selling);
            console.log("response?.total_purchase ", response?.total_purchase);
            var totalSelling= `<b>${response?.total_selling || "0.00"}</b>`;
            var totalPurchase= `<b>${response?.total_purchase || "0.00"}</b>`;

            $("#totalSelling").html(totalSelling);
            $("#totalPurchase").html(totalPurchase);

            if (inventory.length === 0) {
                $tbody.html(`
                    <tr>
                        <td colspan="${headerCount}" class="text-center py-5">
                            No batchwise data available.
                        </td>
                    </tr>
                `);

                return;
            }

            let rowHtml = "";

            $.each(inventory, function (index, row) {
                rowHtml += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${row.Barcode || ""}</td>
                        <td>${row.ItemName || ""}</td>
                        <td>${row.ProductNo || ""}</td>
                        <td>${row.BatchNo || ""}</td>
                        <td>${row.CurrentQty || 0}</td>
                        <td>${row.SellingPrice || "0.00"}</td>
                        <td>${row.PurchasePrice || "0.00"}</td>
                        <td>${row.totalSellingPrice || "0.00"}</td>
                        <td>${row.totalPurchasePrice || "0.00"}</td>
                    </tr>
                `;
            });

            $tbody.html(rowHtml);

            $(".colspan").attr("colspan", headerCount - 2);

            $table.DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                pageLength: 10,

                // Current quantity column
                order: [[4, "asc"]]
            });

            exportTableButtons($table.attr("id"));
        },

        error: function (xhr, status, error) {
            console.error("AJAX Error:", error);
            console.error("Server response:", xhr.responseText);

            toastr.error(
                "An error occurred while loading batchwise data.",
                "Error"
            );
        }
    });
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
$(document).ready(function () {
    fetchcustomrData();
    $("#headerCollapse2").trigger("click");
    $("#refresh").on("click", function(){
        let $btn = $(this);
        let $icon = $btn.find("i");
        $btn.prop("disabled", true);
        $icon.css({
            "animation": "spin 0.8s linear infinite",
            "display": "inline-block"
        });
        fetchcustomrData().always(function () {
            // Stop spinning when AJAX completes
            $btn.prop("disabled", false);
            $icon.css("animation", "none");
        });
    })
});