var app_status = $(document).find("#app_status").val();
var query = $(document).find("#query").val();
console.log("App Status "+app_status);
let $table = $('#table_subcat');
let ShopID = $("#shop_id").val();
let print_access = parseInt($("#print_access").val()) || 0;
let verify_access = parseInt($("#verify_access").val()) || 0;
let edit_access = parseInt($("#edit_access").val()) || 0;
let delete_access  = parseInt($("#delete_access ").val()) || 0;
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
function checksubcatName(name, catID) {
    return $.ajax({
        url: '../AJAX/Subcategories/checkSubCatName.php',
        method: 'POST',
        data: { name: name, catID: catID },
        dataType: 'json'
    }).then(function(response) {

        if (response && typeof response.subcatCount !== 'undefined') {
            return response.subcatCount > 0;
        } else {
            toastr.error("An error occurred while validating the subcategory name. Please try again.", "Error");
            console.error("Invalid response:", response);
            return false;
        }

    }).catch(function(error) {
        console.error("AJAX error:", error);
        toastr.error("An error occurred while validating the subcategory name. Please try again.", "Error");
        return false;
    });
}
function checksubcatNameID(name, catID,SCID) {
    return $.ajax({
        url: '../AJAX/Subcategories/checkSubCatName.php',
        method: 'POST',
        data: { name: name, catID: catID, SCID:SCID },
        dataType: 'json'
    }).then(function(response) {

        if (response && typeof response.subcatCount !== 'undefined') {
            return response.subcatCount > 0;
        } else {
            toastr.error("An error occurred while validating the subcategory name. Please try again.", "Error");
            console.error("Invalid response:", response);
            return false;
        }

    }).catch(function(error) {
        console.error("AJAX error:", error);
        toastr.error("An error occurred while validating the subcategory name. Please try again.", "Error");
        return false;
    });
}
function fetchSubCategory($table, $query = '')
{
    return $.ajax({
        url: '../AJAX/Subcategories/getSubCategories.php?query=' + encodeURIComponent($query),
        method: 'POST',
        dataType: 'json',
        beforeSend: function () {
            TableLoading($table, "Subcategories");
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
                            No Subategory data available.
                        </td>
                    </tr>
                `);
                return;
            }
            var ItemNo = 1;
            $.each(response, function (index, row) {
                let SCID = row["SCID"];
                let SubCatNo = row["SubCatNo"];
                let SubCatName = row["SubCatName"];
                let CategoryName = row["CategoryName"];
                let Catdefault = row["default"];
                let TotalProducts = row["TotalProducts"];
                let dropdownItems = '';
                if(userType === 1 || edit_access === 1)
                {
                    dropdownItems += `
                        <li><a class="dropdown-item edit-subcategory" href="javascript:void(0)" data-id="${SCID}">Edit</a></li>
                    `;
                }
                if(userType === 1 || delete_access === 1)
                {
                    dropdownItems += `
                        <li><a class="dropdown-item delete-subcategory" href="javascript:void(0)"  data-id="${SCID}">Delete</a></li>
                    `;
                }
                var actionBtn = `
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary">Action</button>
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                                data-bs-toggle="dropdown"></button>
                            <ul class="dropdown-menu">
                                ${dropdownItems}
                            </ul>
                        </div>
                    `;
                if(Catdefault === 1)
                {
                    actionBtn = `
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary" disabled>Action</button>
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                                data-bs-toggle="dropdown" disabled></button>
                            <ul class="dropdown-menu">
                                ${dropdownItems}
                            </ul>
                        </div>`;
                }
                $table.find('tbody').append(`
                        <tr data-SCID="${SCID}" id="row_${SCID}">
                            <td>${ItemNo}</td>
                            <td>${SubCatNo}</td>
                            <td>${SubCatName}</td>
                            <td>${CategoryName}</td>
                            <td class="text-center"><a href="../Public/product.php?Subcateory=${SubCatName}" target="_blank_">${TotalProducts}</a></td>
                            <td>${actionBtn}</td>
                        </tr>
                    `);
                ItemNo++;
            });
            $table.DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                pageLength: 10,
                order: [0, 'asc']
            });
            exportTableButtons('table_subcat');
        },
        error: function (xhr, status, error) {
            console.log("AJAX Error:", error);
            toastr.error("An error occurred while loading Subcategory data.", "Error");
        }
    });
}
$(document).ready(function () {
    fetchSubCategory($table, query);
    $("#update_subcatForm").on("submit", function(e){
        e.preventDefault();
        console.log("form");
        
        let formData = $(this).serialize(); 
        let url = $(this).attr("action");
        let name = $("#edit_subcat_name").val().trim();
        let catID = $("#edit_cmb_main_category").val();
        let subcat_id = $("#hide_subcat_id").val();
        let btn = $("#btn_update_subcategory");
        let btnHtml = btn.html();
        if (name !== '' && catID !== '') {
            checksubcatNameID(name, catID, subcat_id).then(function(exists) {
                if (exists > 0) {
                    $("#edit_category_danger").html("&#9888; Subcategory already exists.").css("color", "red").show();
                    $("#edit_subcat_name").css("border-color", "red");
                    $("#btn_update_subcategory").prop('disabled', true);
                } else {
                    $("#edit_category_danger").html("&#10004; Subcategory available.").css("color", "green").show();
                    $("#edit_subcat_name").css("border-color", "green");
                    $("#btn_update_subcategory").prop('disabled', false);
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        dataType: "json",
                        beforeSend: function() {
                            btn.prop("disabled", true);
                            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
                        },
                        success: function(response) {
                            btn.prop("disabled", false);
                            btn.html(btnHtml);
                            if(response.status === "success") {
                                $("#edit_subcategory_modal").modal("toggle");
                                fetchSubCategory($table, query);
                                toastr.success(response.message, "Success");
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
                }
            });
        } 
        else 
        {
            $("#edit_category_danger").hide();
            $("#edit_subcat_name").css("border-color", "");
            $("#btn_update_subcategory").prop('disabled', false);
        }

    });
    $("#add_subcatForm").on("submit", function (e) {
        e.preventDefault();
        console.log("form");
        
        let formData = $(this).serialize(); 
        let url = $(this).attr("action");
        let name = $("#subcat_name").val().trim();
        let catID = $("#cmb_main_category").val();
        let btn = $("#btn_save_subcategory");
        let btnHtml = btn.html();
        if (name !== '' && catID !== '') {
            checksubcatName(name, catID).then(function(exists) {
                if (exists > 0) {
                    $("#category_danger").html("&#9888; Subcategory already exists.").css("color", "red").show();
                    $("#subcat_name").css("border-color", "red");
                    $("#btn_save_subcategory").prop('disabled', true);
                    toastr.error("Subcategory already exists. Please choose a different name.", "Validation Error");
                    return;
                }
                else
                {
                    $("#category_danger").html("&#10004; Subcategory available.").css("color", "green").show();
                    $("#subcat_name").css("border-color", "green");
                    $("#btn_save_subcategory").prop('disabled', false);
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        dataType: "json",
                        beforeSend: function() {
                            btn.prop("disabled", true);
                            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
                        },
                        success: function(response) {
                            btn.prop("disabled", false);
                            btn.html(btnHtml);
                            if(response.status === "success") {
                                $("#subcategory_modal").modal("toggle");
                                fetchSubCategory($table, query);
                                toastr.success(response.message, "Success");
                            } else {
                                toastr.error(response.message, "Error");
                            }
                        },
                        error: function(xhr, status, error) {
                            btn.prop("disabled", false);
                            btn.html(btnHtml);
                            console.log("AJAX Error:", error);
                            toastr.error("An error occurred while adding subcategory.", "Error");
                        }
                    });
                }
            });
        }
        else
        {
            $("#category_danger").html("&#9888; Please enter a subcategory name and select a main category.").css("color", "red").show();
            $("#subcat_name").css("border-color", "red");
            $("#cmb_main_category").css("border-color", "red");
            toastr.error("Please enter a subcategory name and select a main category.", "Validation Error");
        }
    });
    function EditSubCategory(SCID)
    {
        
        $.ajax({
            url: '../AJAX/Subcategories/getSubCategoriesID.php',
            method: 'POST',
            data: { SCID: SCID },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                
                if(response.length > 0)
                {
                    let category = response[0];
                    var CTID = category.CTID;
                    var SCID = category.SCID;
                    var SubCatNo = category.SubCatNo;
                    var SubCatName = category.SubCatName;
                    var catDefault = category.default;
                    if(catDefault == 1)
                    {
                        toastr.warning("Default subcategory cannot be edited.", "Warning");
                        return;
                    }
                    $("#edit_subcat_name").val(SubCatName);
                    $("#editmyModalLabel").text("Edit Category - " + SubCatNo + " - " + SubCatName);
                    $("#hide_subcat_id").val(SCID);
                    $("#edit_cmb_main_category").val(CTID);
                    $("#edit_subcategory_modal").modal("toggle");
                }
                else
                {
                    toastr.error("Subcategory details not found.", "Error");
                }
            },
            error: function(xhr, status, error) {
                toastr.error("An error occurred while fetching category details.", "Error");
                console.error("AJAX Error:", error);
            }
        });
    }
    $(document).on("click", ".edit-subcategory", function () {
        $("#edit_category_danger").hide();
        $("#edit_subcat_name").css("border-color", "");
        let SCID = $(this).data("id");
        
        EditSubCategory(SCID);
    });
    $(document).on("click", ".delete-subcategory", function () {
        let SCID = $(this).data("id");
        if (confirm("Are you sure you want to delete this category? This action cannot be undone.")) 
        {
            $.ajax({
                url: '../Controller/deleteSubcategory.php',
                method: 'POST',
                data: { SCID: SCID },
                dataType: 'json',
                success: function(response) {
                    if(response.status === "success") {
                        toastr.success(response.message, "Success");
                        fetchSubCategory($table);
                    } else {
                        toastr.error(response.message, "Error");
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error("An error occurred while deleting the category. Please try again.", "Error");
                    console.error("AJAX Error:", error);
                }
            });
        }
    });
    $("#refresh").on("click", function () {
        let $btn = $(this);
        let $icon = $btn.find("i");
        $btn.prop("disabled", true);
        $icon.css({
            "animation": "spin 0.8s linear infinite",
            "display": "inline-block"
        });
        fetchSubCategory($table).always(function () {
            // Stop spinning when AJAX completes
            $btn.prop("disabled", false);
            $icon.css("animation", "none");
        });  
    });
    $("#btn_add_subcategory").on("click", function () {
        $("#category_danger").hide();
        $("#subcat_name").css("border-color", "");
        $("#btn_save_subcategory").prop('disabled', false);
        $("#add_subcatForm")[0].reset();
        $("#subcategory_modal").modal("toggle");
    });
    $("#edit_cmb_main_category, #edit_subcat_name").on("change", function(){
        console.log("changed ");
        
        let name = $("#edit_subcat_name").val().trim();
        let catID = $("#cmb_main_category").val();
        let subcat_id = $("#hide_subcat_id").val();
        if (name !== '' && catID !== '') {
            checksubcatNameID(name, catID, subcat_id).then(function(exists) {
                if (exists > 0) {
                    $("#edit_category_danger").html("&#9888; Subcategory already exists.").css("color", "red").show();
                    $("#edit_subcat_name").css("border-color", "red");
                    $("#btn_update_subcategory").prop('disabled', true);
                } else {
                    $("#edit_category_danger").html("&#10004; Subcategory available.").css("color", "green").show();
                    $("#edit_subcat_name").css("border-color", "green");
                    $("#btn_update_subcategory").prop('disabled', false);
                }
            });
        } 
        else 
        {
            $("#edit_category_danger").hide();
            $("#edit_subcat_name").css("border-color", "");
            $("#btn_update_subcategory").prop('disabled', false);
        }

    });
    $("#subcat_name, #cmb_main_category").on("change", function () {
        let name = $("#subcat_name").val().trim();
        let catID = $("#cmb_main_category").val();
        if (name !== '' && catID !== '') {
            checksubcatName(name, catID).then(function(exists) {
                if (exists > 0) {
                    $("#category_danger").html("&#9888; Subcategory already exists.").css("color", "red").show();
                    $("#subcat_name").css("border-color", "red");
                    $("#btn_save_subcategory").prop('disabled', true);
                } else {
                    $("#category_danger").html("&#10004; Subcategory available.").css("color", "green").show();
                    $("#subcat_name").css("border-color", "green");
                    $("#btn_save_subcategory").prop('disabled', false);
                }
            });
        } 
        else 
        {
            $("#category_danger").hide();
            $("#subcat_name").css("border-color", "");
            $("#btn_save_subcategory").prop('disabled', false);
        }
    });
});