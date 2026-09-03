let $table = $('#tbl_expense_cat');
let ShopID = $("#shop_id").val();
let create_access = parseInt($("#create_access").val()) || 0;
let view_access = parseInt($("#view_access").val()) || 0;
let edit_access = parseInt($("#edit_access").val()) || 0;
let delete_access = parseInt($("#delete_access").val()) || 0;
let verify_access = parseInt($("#verify_access").val()) || 0;
let print_access = parseInt($("#print_access").val()) || 0;
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
function fetchExpenseCatData()
{
    return $.ajax({
        url: '../AJAX/Expense/fetchExpenseCatData.php',
        method: 'post',
        data: { ShopID: ShopID },
        dataType: 'json',
        beforeSend: function() {
            TableLoading($table, "Expense Category");
        },
        success: function(response) {
            if ($.fn.DataTable.isDataTable($table)) {
                $table.DataTable().clear().destroy();
            }
            // Clear previous data (including loader)
            $table.find('tbody').html("");
            var headcount = $table.find('thead th').length
            if (!response || response.length === 0) {
                $table.find('tbody').html(`
                    <tr>
                        <td colspan="${headcount}" class="text-center py-5">No expense data available.</td>
                    </tr>
                `);
                return;
            }
            $.each(response, function (index, row) {
                var sl = row.sl;
                var ECID = row.ECID; 
                var is_default = row.is_default;
                var status = row.status;
                var expense_category = row.expense_category;
                var expense_type = row.expense_type;
                var created_date = row.created_date;
                var UserName = row.UserName;
                var ModifiedBy = row.ModifiedBy;
                var modified_date = row.modified_date;
                var badge =``;
                var status_badge =``;
                if(is_default ==1)
                {
                    badge=`<span class="badge badge-success default_badge" data-ecid="${ECID}" data-is_default="${is_default}">
                                <i class="ti ti-check"></i>
                                Default
                            </span>`;
                }
                else
                {
                    badge=`<span class="badge badge-warning default_badge" data-ecid="${ECID}" data-is_default="${is_default}">
                                <i class="ti ti-alert-circle"></i>
                                Not Default
                            </span>`;
                }
                if(status ==1)
                {
                    status_badge=`<span class="badge badge-success status_badge" data-ecid="${ECID}">
                                <i class="ti ti-check"></i>
                                Active
                            </span>`;
                }
                else
                {
                    status_badge=`<span class="badge badge-warning status_badge" data-ecid="${ECID}">
                                <i class="ti ti-alert-circle"></i>
                                Inactive
                            </span>`;
                }
                var btn =``;
                if(edit_access==1)
                {
                    btn=`<a class="btn-action btn-edit open-modal" data-ecid="${ECID}" href="../View/modals/expense-categories.php?condition=edit&ref=AddExpenseCategories&ECID=${ECID}" data-title="Edit Expense Category">
                                <i class="ti ti-edit"></i>
                            </a>`;
                }
                if(delete_access==1)
                {
                    btn +=`<button class="btn-action btn-delete delete-ecid" data-ecid="${ECID}">
                                <i class="ti ti-trash"></i>
                            </button>`;
                }
                $table.find('tbody').append(`
                    <tr data-ecid="${ECID}">
                        <td>${sl}</td>
                        <td>
                            <div class="expense-name">
                                <div class="expense-color"></div>
                                ${expense_category}
                            </div>
                        </td>
                        <td>
                            ${expense_type}
                        </td>
                        <td>
                            ${status_badge}
                        </td>
                        <td>
                            ${badge}
                        </td>
                        <td>
                            ${UserName}
                        </td>
                        <td>
                            ${created_date}
                        </td>
                        <td>
                            ${ModifiedBy}
                        </td>
                        <td>
                            ${modified_date}
                        </td>
                        <td>
                            ${btn}
                        </td>
                    </tr>
                `);
            });
            let table = $table.DataTable({           
                                paging: true,
                                lengthChange: true,
                                searching: true,
                                pageLength: 10
                            });
            exportTableButtons('tbl_expense_cat');
        },
        error: function(xhr, status, error) {
            console.log("AJAX Error:", status, error);
            console.log("Response:", xhr.responseText);
            toastr.error("An error occurred while processing.", "Error");
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
function update_status(ECID)
{
    return $.ajax({
                url: '../Controller/AddExpenseCategoryController2.php?condition=update_status',
                method: 'GET',
                data: { ECID: ECID },
                dataType: 'json',
                success: function(response) {
                    if (response.status==1) {
                        toastr.success(response.message, "Success");
                        if(response.new_status==1)
                        {
                            // Update the clicked badge to "Default"
                            $(`.status_badge[data-ecid="${ECID}"]`).removeClass('badge-warning').addClass('badge-success').html('<i class="ti ti-check"></i> Active');
                        }
                        else
                        {
                            // Update the clicked badge to "Not Default"
                            $(`.status_badge[data-ecid="${ECID}"]`).removeClass('badge-success').addClass('badge-warning').html('<i class="ti ti-alert-circle"></i> Inactive');
                        }
                    }
                    else
                    {
                        toastr.error(response.message, "Error");
                    }
                },
                error: function(xhr, status, error) {
                    console.log("AJAX Error:", status, error);
                    console.log("Response:", xhr.responseText);
                    toastr.error("An error occurred while updating status.", "Error");
                }
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
    fetchExpenseCatData();
    $(document).on('click', '.delete-ecid', function (e) {
        e.preventDefault();
        var ecid = $(this).data('ecid');
        var tr = $(this).closest('tr');
        var defaultBadge = tr.find('.default_badge');
        var isDefault = defaultBadge.data('is_default');
        if (isDefault == 1) {
            toastr.warning("Default expense category cannot be deleted.", "Warning");
            return;
        }
        if(userType === 1 || delete_access === 1) 
        {
            if(confirm("Are you sure you want to delete this expense category?")) 
            {
                $.ajax({
                    url: '../Controller/AddExpenseCategoryController2.php?condition=delete',
                    method: 'GET',
                    data: { ECID: ecid },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status==1) {
                            toastr.success(response.message, "Success");
                            // tr.slideUp(500, function() {
                            //     tr.remove();
                            // });
                            tr.children('td').animate({
                                paddingTop: 0,
                                paddingBottom: 0,
                                opacity: 0
                            }, 500, function () {
                                tr.remove();
                            });
                        }
                        else
                        {
                            toastr.error(response.message, "Error");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX Error:", status, error);
                        console.log("Response:", xhr.responseText);
                        toastr.error("An error occurred while deleting.", "Error");
                    }
                })    
            }
            
        }
        else
        {
            toastr.warning("You Cannot Delete ", "Warning");
        }
        
    });
    $(document).on('click', '.default_badge', function (e) {
        e.preventDefault();
        var ecid = $(this).data('ecid');
        if(userType === 1) 
        {
            $.ajax({
                url: '../Controller/AddExpenseCategoryController2.php?condition=update_default',
                method: 'GET',
                data: { ECID: ecid },
                dataType: 'json',
                success: function(response) {
                    if (response.status==1) {
                        toastr.success(response.message, "Success");
                        if(response.new_is_default==1)
                        {
                            // Update the clicked badge to "Default"
                            $(`.default_badge[data-ecid="${ecid}"]`).removeClass('badge-warning').addClass('badge-success').html('<i class="ti ti-check"></i> Default').data('is_default', 1).attr('data-is_default', 1);
                        }
                        else
                        {
                            // Update the clicked badge to "Not Default"
                            $(`.default_badge[data-ecid="${ecid}"]`).removeClass('badge-success').addClass('badge-warning').html('<i class="ti ti-alert-circle"></i> Not Default').data('is_default', 0).attr('data-is_default', 0);
                        }
                    }
                    else
                    {
                        toastr.error(response.message, "Error");
                    }
                },
                error: function(xhr, status, error) {
                    console.log("AJAX Error:", status, error);
                    console.log("Response:", xhr.responseText);
                    toastr.error("An error occurred while updating default.", "Error");
                }
            })
        }
        else
        {
            toastr.warning("Default status cannot be changed.", "Warning");
        }
        
    });
    $(document).on('click', '.status_badge', function (e) {
        e.preventDefault();
        var ecid = $(this).data('ecid');
        var tr = $(this).closest('tr');
        var defaultBadge = tr.find('.default_badge');
        var isDefault = defaultBadge.data('is_default');
        if(edit_access === 1) 
        {
            if(userType === 1)
            {
                // if(isDefault == 1)
                // {
                //     toastr.warning("Status cannot be changed for default expense type.", "Warning");
                // }
                // else
                // {
                //     update_status(ecid);
                // }
                update_status(ecid);
            }
            else
            {
                if(isDefault == 1)
                {
                    toastr.warning("Status cannot be changed for default expense category.", "Warning");
                }
                else
                {
                    update_status(ecid);
                }
            }
        }
        else
        {
            toastr.warning("Status cannot be changed.", "Warning");
        }
        
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
            fetchExpenseCatData().always(function () {
                // Stop spinning when AJAX completes
                $btn.prop("disabled", false);
                $icon.css("animation", "none");
            });     
        // }, 3000);
                   
    });
})