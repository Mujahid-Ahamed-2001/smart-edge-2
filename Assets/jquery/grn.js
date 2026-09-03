var app_status = $(document).find("#app_status").val();
console.log("App Status "+app_status);

    let ShopID = $("#shop_id").val();
    let $table = $('#tbl_grn_header');

    let print_access = parseInt($("#print_access").val()) || 0;
    let verify_access = parseInt($("#verify_access").val()) || 0;
    let edit_access = parseInt($("#edit_access").val()) || 0;
    let userType = parseInt($("#userType").val()) || 0;

function initializeSelect2() {
    
    var shop_id = $("#shop_id").val();

    $("#search-suppliers").select2({
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
        placeholder: 'Search for Suppliers',
        minimumInputLength: 1,
        width: 'calc(100% - 52px)',
    }).on('select2:open', function () {
        $('.select2-search__field').focus();
    });
}

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
    let loadingHTML = `<div class="table-loading">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>                             
                            </div>
                        </div>`;
    $("#grnTotalOrder").html(loadingHTML);
    $("#grnTotalValue").html(loadingHTML);
    $("#grnPending").html(loadingHTML);
    $table.find('tbody').html(loaderHtml);
}

function fetchGRNData(selectShop, start = "", end = "", supplierID = "") {
    return $.ajax({
        url: '../AJAX/GRN/getGRNdata.php',
        method: 'POST',
        data: {
            ShopID: selectShop,
            start: start,
            end: end,
            supplierID: supplierID
        },
        dataType: 'json',
        beforeSend: function () {
            TableLoading($table, "GRN Header");
        },
        success: function (response) {
            var totGrn=0;
            var totpurchase=0;
            var totPending=0;
            if ($.fn.DataTable.isDataTable($table)) {
                $table.DataTable().clear().destroy();
            }
            $table.find('tbody').empty();
            let headerCount = $table.find('thead th').length;
            if (!response || response.length === 0) {
                $table.find('tbody').html(`
                    <tr>
                        <td colspan="${headerCount}" class="text-center py-5">
                            No GRN data available.
                        </td>
                    </tr>
                `);
                totpurchase = parseFloat(totpurchase).toFixed(2);
                $("#grnTotalOrder").html(totGrn);
                $("#grnTotalValue").html("Rs. "+totpurchase);
                $("#grnPending").html(totPending);
                return;
            }
            var ItemNo = 1;
            $.each(response, function (index, row) {
                let GRNStat = parseInt(row.GRNStat);
                let status_tag = '';
                let btn_class = '';
                switch (GRNStat) {
                    case 0:
                        status_tag = `<span class="badge text-bg-info">Hold</span>`;
                        btn_class = 'btn-info';
                        break;
                    case 1:
                        status_tag = `<span class="badge text-bg-warning">Pending</span>`;
                        btn_class = 'btn-warning';
                        totPending++;
                        break;
                    case 2:
                        status_tag = `<span class="badge text-bg-success">Verified</span>`;
                        btn_class = 'btn-success';
                        break;
                    case 3:
                        status_tag = `<span class="badge text-bg-danger">Cancelled</span>`;
                        btn_class = 'btn-danger';
                        break;
                    default:
                        status_tag = `<span class="badge text-bg-dark">Undefined</span>`;
                        btn_class = 'btn-secondary';
                }
                let dropdownItems = '';
                if (GRNStat !== 3) {
                    if (userType === 1 || verify_access === 1 || edit_access === 1) {
                        dropdownItems += `
                            <li>
                                <a class="dropdown-item" href="./grn-details2.php?grn_header=${row.GHID}&GRNHeaderNo=${row.GRNHeaderNo}">Add/Edit Items</a>
                            </li>
                        `;
                    }
                    if (userType === 1 || print_access === 1) {
                        dropdownItems += `
                            <li>
                                <form action="../Reports/grn_detail.php?header_id=${row.GHID}&GRNHeaderNo=${row.GRNHeaderNo}" method="post">
                                    <button type="submit" class="dropdown-item">
                                        Print GRN
                                    </button>
                                </form>
                            </li>
                        `;
                    }
                }
                let actionBtn = `
                    <div class="btn-group">
                        <button type="button" class="btn ${btn_class}">Action</button>
                        <button type="button" class="btn ${btn_class} dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown"></button>
                        <ul class="dropdown-menu">
                            ${dropdownItems}
                        </ul>
                    </div>
                `;
                var TotalPurchasePrice = row.TotalPurchasePrice || 0;
                var TotalSellPrice = row.TotalSellPrice || 0;
                totpurchase = totpurchase + TotalPurchasePrice;
                TotalPurchasePrice = parseFloat(TotalPurchasePrice).toFixed(2);
                TotalSellPrice = parseFloat(TotalSellPrice).toFixed(2);
                $table.find('tbody').append(`
                    <tr>
                        <td>${ItemNo}</td>
                        <td>${row.GRNHeaderNo || ''}</td>
                        <td>${row.EffectiveDate || ''}</td>
                        <td>${row.InvoiceNo || ''}</td>
                        <td class="text-center">${row.ItemCount || 0}</td>
                        <td class="text-end">${TotalPurchasePrice}</td>
                        <td class="text-end">${TotalSellPrice}</td>
                        <td class="text-center">${row.refference || ''}</td>
                        <td class="text-center">${row.ShopName || ''}</td>
                        <td class="text-center">${row.SupplierName || ''}</td>
                        <td>${row.UserName || ''}</td>
                        <td>${status_tag}</td>
                        <td>${actionBtn}</td>
                    </tr>
                `);
                ItemNo++;
                totGrn++;
            });
            totpurchase = parseFloat(totpurchase).toFixed(2);
            $("#grnTotalOrder").html(totGrn);
            $("#grnTotalValue").html("Rs. "+totpurchase);
            $("#grnPending").html(totPending);
            $table.DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                pageLength: 10,
                order: [1, 'desc']
            });
            exportTableButtons('tbl_grn_header');
        },
        error: function (xhr, status, error) {
            console.log("AJAX Error:", error);
            toastr.error("An error occurred while loading GRN data.", "Error");
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
    $("#headerCollapse2").trigger("click"); 
    $(document).on('click', '.open-modal', function (e) {
      e.preventDefault();
      let url = $(this).attr('href');
      let title = $(this).data('title') || 'Company';
      openModal(url,title);
    });

    initializeSelect2();
    $("#select-shop").on("change", function () {
        $("#search-suppliers").val(null).trigger("change");
        $("#search-suppliers").select2("destroy");
        initializeSelect2();
    });

    $("#GRN-Filter").submit(function (e) {
        e.preventDefault();
        let start = $("#start").val();
        let end = $("#end").val();
        let filter = $("#filter").html();
        let supplierID = $("#search-suppliers").val();
        let selectShop = $("#select-shop").val();
        $("#filter").html('<i class="ti ti-reload" style="animation: spin 0.8s linear infinite; display:inline-block;"></i>')
        fetchGRNData(selectShop, start, end, supplierID).always(function () {
            $("#filter").html(filter);
        });
    });

    $("#clearFilter").on("click", function () {
        $("#start").val('');
        $("#end").val('');
        var html = $(this).html();
        $(this).html('<i class="ti ti-reload" style="animation: spin 0.8s linear infinite; display:inline-block;"></i>');

        let OriginalShopID = $("#shop_id").val();
        $("#select-shop").val(OriginalShopID).trigger('change');
        $("#search-suppliers").val(null).trigger("change");

        fetchGRNData(OriginalShopID).always(function () {
            $("#clearFilter").html(html);
        });
    });

    fetchGRNData(ShopID);
    
    $("#refresh").on("click", function(){
        let $btn = $(this);
        let $icon = $btn.find("i");
        $btn.prop("disabled", true);
        $icon.css({
            "animation": "spin 0.8s linear infinite",
            "display": "inline-block"
        });
        fetchGRNData(ShopID).always(function () {
            // Stop spinning when AJAX completes
            $btn.prop("disabled", false);
            $icon.css("animation", "none");
        });
    })
    // Open GRN modal
    
    
    $("#close_grn_modal").click(function () {
        $("#grn_modal").modal('toggle');
    });

});