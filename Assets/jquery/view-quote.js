
var app_status = $(document).find("#app_status").val();
console.log("App Status "+app_status);
    let ShopID = $("#shop_id").val();
    let $table = $('#view_quote_table');

    let print_access = parseInt($("#print_access").val()) || 0;
    let verify_access = parseInt($("#verify_access").val()) || 0;
    let edit_access = parseInt($("#edit_access").val()) || 0;
    let userType = parseInt($("#userType").val()) || 0;
function fetchquotationData(start ="", end="", customerID="", status="",sellingPrice="", selling_operator="", created_by="", completion="") {
    return $.ajax({
        url: '../AJAX/Quotation/getquotedata.php',
        method: 'POST',
        data: {
            start: start,
            end: end,
            customerID: customerID,
            status: status,
            sellingPrice: sellingPrice,
            selling_operator: selling_operator,
            created_by: created_by,
            completion: completion,
        },
        // dataType: 'json',
        beforeSend: function () {
            TableLoading($table, "Quotations");
        },
        success: function (response) {
            console.log("response "+response);
            if ($.fn.DataTable.isDataTable($table)) {
                $table.DataTable().clear().destroy();
            }
            $table.find('tbody').html(response);
            $table.DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                pageLength: 25,
                columnDefs: [
                    { orderable: false, targets: 0 }
                ]
            });
            exportTableButtons('view_quote_table');
        },
        error: function (xhr, status, error) {
            console.log("AJAX Error:", error);
            toastr.error("An error occurred while loading quotation data.", "Error");
        }
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
    $("#quoteTotalOrder").html(loadingHTML);
    $("#quoteTotalValue").html(loadingHTML);
    $("#quotePending").html(loadingHTML);
    $table.find('tbody').html(loaderHtml);
}
function initializeSelect2() {
    
    var shop_id = $("#shop_id").val();

    $("#search-customer").select2({
        ajax: {
            url: '../AJAX/Quotation/getCustomers.php',
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
        placeholder: 'Search for Customers',
        minimumInputLength: 1,
        width: '85%'
    }).on('select2:open', function () {
        $('.select2-search__field').focus();
    });
}
function quote_status(quote_id, status)
{
    return $.ajax({
        url: '../AJAX/Quotation/update_status.php',
        method: 'POST',
        data: {
            quote_id: quote_id,
            status: status
        },
        dataType: 'json'
    }).then(function (response) 
    {
        if(response.status == "success")
        {
            toastr.success(response.message, "Success");
            return 1;
        }
        else
        {
            toastr.error(response.message, "Error");
            return 0;
        }
    }).catch(function (xhr, status, error) 
    {
        console.log("AJAX Error:", error);
        toastr.error("An error occurred while updating quotation status.", "Error");
        return 0;
    });
}
$(document).ready(function () {    
    initializeSelect2();
    fetchquotationData();
    $("#clearFilter").on("click", function(e){
        e.preventDefault();
        fetchquotationData();
    });
    $("#refresh").on("click", function(){
        let $btn = $(this);
        let $icon = $btn.find("i");
        $btn.prop("disabled", true);
        $icon.css({
            "animation": "spin 0.8s linear infinite",
            "display": "inline-block"
        });
        fetchquotationData().always(function () {
            // Stop spinning when AJAX completes
            $btn.prop("disabled", false);
            $icon.css("animation", "none");
        });
    });
    $("#quote-Filter").on("submit", function(e){
        e.preventDefault();
        var start = $("#start").val();
        var end = $("#end").val();
        var customerID = $("#search-customer").val();
        var status = $("#select-stat").val();
        var sellingPrice = $("#selling_price1").val();
        var selling_operator = $("#selling_operator").val();
        var created_by = $("#select-user").val();
        var completion = "";
        if($("#completion").is(":checked"))
        {
            completion = 1;
        }
        
        fetchquotationData(start , end, customerID, status,sellingPrice, selling_operator, created_by, completion)
    });
    $(document).on("change", ".status_select", function(){
        let element = $(this);
        let selectedValue = element.val();
        let quote_id = element.data("quote_id");

        let selectedOption = element.find("option:selected");
        let color = selectedOption.data("color");
        let bg_color = selectedOption.data("bg_color");

        quote_status(quote_id, selectedValue).then(function(result){
            console.log("result " + result);

            if(result == 1)
            {
                if(selectedValue == "")
                {
                    color = "#000";
                    bg_color = "#fff";
                }

                element.css({
                    "color": color,
                    "background-color": bg_color
                });
            }
        });
    });

    $("#select-stat").on("change", function(){
        var selectedValue = $(this).val();
        var color = $(this).find("option:selected").data("color");
        var bg_color = $(this).find("option:selected").data("bg_color");
        if(selectedValue == "")
        {
            color = "#000";
            bg_color = "#fff";
        }
        $("#select-stat").css({
            "color": color,
            "background-color": bg_color
        });

    });

});