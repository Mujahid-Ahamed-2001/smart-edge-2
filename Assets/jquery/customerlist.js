var app_status = $(document).find("#app_status").val();
console.log("App Status "+app_status);

    let ShopID = $("#shop_id").val();
    let $table = $('#tbl_customer');

    let print_access = parseInt($("#print_access").val()) || 0;
    let verify_access = parseInt($("#verify_access").val()) || 0;
    let edit_access = parseInt($("#edit_access").val()) || 0;
    let userType = parseInt($("#userType").val()) || 0;

function initializeSelect2() {
    
    var shop_id = $("#shop_id").val();

    $("#search-customer").select2({
        ajax: {
            url: '../AJAX/guiPos/getCustomers.php',
            dataType: 'json',
            delay: 250,
            cache: true,
            data: function (params) {
                return {
                    search: params.term,
                    type: 'item_search',
                    status: 1
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
    $table.find('tbody').html(loaderHtml);
}

function fetchcustomrData(selectShop, start = "", end = "", supplierID = "") {
    return $.ajax({
        url: '../AJAX/Customer/getCustomers.php',
        method: 'POST',
        data: {
            ShopID: selectShop,
            start: start,
            end: end,
            supplierID: supplierID
        },
        dataType: 'json',
        beforeSend: function () {
            TableLoading($table, "Customer Data");
        },
        success: function (response) {
            if ($.fn.DataTable.isDataTable($table)) {
                $table.DataTable().clear().destroy();
            }
            $table.find('tbody').empty();
            let headerCount = $table.find('thead th').length;
            if (!response || response.length === 0) {
                $table.find('tbody').html(`
                    <tr>
                        <td colspan="${headerCount}" class="text-center py-5">
                            No customer data available.
                        </td>
                    </tr>
                `);
                return;
            }
            $.each(response, function (index, row) {
                var CTID = row.CTID || '';
                var CustomerNo = row.CustomerNo || '';
                var CustName = row.CustName || '';
                var CustGender = row.CustGender || '';
                var CustDOB = row.CustDOB || '';
                var CustAddress = row.CustAddress || '';
                var country_code = row.country_code || '';
                var CustContact = row.CustContact || '';
                var MaxCreditAmount = parseFloat(row.MaxCreditAmount) || 0;
                var CustStat = row.CustStat || '';
                var Balance = parseFloat(row.Balance) || 0;
                var CustEmail = row.CustEmail || "";
                var created_date = row.created_date || "";
                if (CustGender==1)
                {
                    CustGender="Male";
                }
                else if (CustGender==2)
                {
                    CustGender="Female";
                }
                var editUrl = `../View/modals/customer-modal.php?CTID=${CTID}&condition=edit&ref=customerlist`;
                var statusBadge = CustStat == 1 ? `<span class="badge badge-active" data-ctid="${CTID}">Active</span>` : `<span class="badge badge-inactive" data-ctid="${CTID}">Inactive</span>`;
                var badgeclass = CustStat == 1 ? 'bg-success' : 'bg-danger';
                CustContact = country_code + CustContact;
                var addressLink = CustAddress ? `<a href="https://maps.google.com.au/maps?q=${CustAddress}" target="_blank" class="link ellipsis"><i class="ti ti-map-pin"></i> ${CustAddress}</a>` : '';
                var contactLink = CustContact ? `<a href="tel:${CustContact}" target="_blank" class="link ellipsis"><i class="ti ti-phone"></i> ${CustContact}</a>` : '';
                var emailLink = CustEmail ? `<a href="mailto:${CustEmail}" target="_blank" class="link ellipsis"><i class="ti ti-mail"></i> ${CustEmail}</a>` : '';
                var initials = CustName.split(/\s+/).slice(0, 2).map(word => word.charAt(0).toUpperCase()).join('');
                var editbtn = `<a href="${editUrl}" class="btn btn-sm btn-primary edit-btn" data-ctid="${CTID}">Edit</a>`;
                if (userType === 1 || edit_access === 1) {
                    editbtn = `<a href="${editUrl}" data-title="Edit Customer" class="open-modal">
                                                <i class="ti ti-edit"></i>
                                            </a>`;
                }
                else {
                    editbtn = `<button data-title="Edit Customer" class="" disabled>
                                                <i class="ti ti-edit"></i>
                                            </button>`;
                }
                if(CTID == 1)
                {
                    editbtn = `<button data-title="Edit Customer" class="" disabled>
                                                <i class="ti ti-edit"></i>
                                            </button>`;
                }
                var rowHtml = `<tr>
                                    <td>
                                        <div class="customer-info">
                                            <div class="avatar ${badgeclass}" id="customer-avatar-${CTID}">
                                                ${initials}
                                            </div>
                                            <div>
                                                <h6 class="ellipsis">
                                                    ${CustName}
                                                </h6>
                                                <span>${CustomerNo}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        ${addressLink}
                                    </td>
                                    <td>
                                        ${contactLink}
                                    </td>
                                    <td>
                                        ${emailLink}
                                    </td>
                                    <td>
                                        ${CustGender}
                                    </td>
                                    <td>
                                        ${CustDOB}
                                    </td>
                                    <td>
                                        Rs. ${Balance.toFixed(2)}
                                    </td>
                                    <td>
                                        Rs. ${MaxCreditAmount.toFixed(2)}
                                    </td>
                                    <td>
                                        ${statusBadge}
                                    </td>
                                    <td>
                                        ${created_date}
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="../Public/customerProfile.php?cus_id=${CTID}&ref=guipos" class="open-modal">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            ${editbtn}
                                        </div>
                                    </td>
                                </tr>`;
                $table.find('tbody').append(rowHtml);
            });
            $table.DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                pageLength: 10,
                order: [9, 'asc']
            });
            var tableid =  $table.attr("id");
            exportTableButtons(tableid);
        },
        error: function (xhr, status, error) {
            console.log("AJAX Error:", error);
            toastr.error("An error occurred while loading customer data.", "Error");
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
    initializeSelect2();
    fetchcustomrData();
    $("#headerCollapse2").trigger("click");
    $(document).on('click', '.open-modal', function (e) {
      e.preventDefault();
      let url = $(this).attr('href');
      let title = $(this).data('title') || 'Company';
      openModal(url,title);
    });
    $(document).on("click", ".badge", function (){
        console.log("Badge clicked");
        let $badge = $(this);
        let CTID = $badge.data("ctid");
        $.ajax({

            url: '../AJAX/Customer/updateCustomerStatus.php',
            method: 'POST',
            data: { CTID: CTID},
            dataType: "json",
            success: function (response) {
                console.log("Response from updateCustomerStatus.php:", response);
                if (response.status === 1) {
                    toastr.success(response.message, "Success");
                    if(response.CustStat == 1) {
                        $badge.removeClass("badge-inactive").addClass("badge-active").text("Active");
                        $(`#customer-avatar-${CTID}`).removeClass("bg-danger").addClass("bg-success");
                    }
                    else {
                        $badge.removeClass("badge-active").addClass("badge-inactive").text("Inactive");
                        $(`#customer-avatar-${CTID}`).removeClass("bg-success").addClass("bg-danger");
                    }
                } else {
                    toastr.error(response.message, "Error");
                }
            },
            error: function (xhr, status, error) {
                toastr.error("An error occurred while updating customer status.", "Error");
            }
        });
    });
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