var app_status = $(document).find("#app_status").val();
console.log("App Status "+app_status);
let $table = $('#tbl_category');
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
function getNewCategoryNo(callback) {
    return $.ajax({
        url: '../AJAX/Categories/getNewCategoryNo.php',
        method: 'POST',
        dataType: 'json',
        success: function (response) {
            if (response && response.newCategoryNo) {
                callback(response.newCategoryNo);
            } else {
                console.error("Invalid response for new category number:", response);
                callback(null);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error while fetching new category number:", error);
            callback(null);
        }
    });
}
function checkDuplicateCategory(catName) {
    return $.ajax({
        url: "../AJAX/Categories/getDupliCategory2.php",
        method: 'POST',
        data: { cat_name: catName },
        dataType: 'json'
    }).then(function(response) {

        if (response && typeof response.catCount !== 'undefined') {
            return response.catCount > 0;
        } else {
            console.error("Invalid response:", response);
            return false;
        }

    }).catch(function(error) {
        console.error("AJAX error:", error);
        return false;
    });
}
function checkDuplicateCategoryWithID(catName, CTID) {
    return $.ajax({
        url: "../AJAX/Categories/checkDuplicateCategoryWithID.php",
        method: 'POST',
        data: { cat_name: catName, CTID: CTID },
        dataType: 'json'
    }).then(function(response) {

        if (response && typeof response.catCount !== 'undefined') {
            return response.catCount > 0;
        } else {
            toastr.error("An error occurred while validating the category name. Please try again.", "Error");
            console.error("Invalid response:", response);
            return false;
        }

    }).catch(function(error) {
        console.error("AJAX error:", error);
        toastr.error("An error occurred while validating the category name. Please try again.", "Error");
        return false;
    });
}
$(document).ready(function(){

    let ShopID = $("#shop_id").val();

    let print_access = parseInt($("#print_access").val()) || 0;
    let verify_access = parseInt($("#verify_access").val()) || 0;
    let edit_access = parseInt($("#edit_access").val()) || 0;
    let userType = parseInt($("#userType").val()) || 0;
    $("#btn_add_category").on("click", function(){
        $("#category_danger").hide();
        $("#cat_name").css("border-color", "");
        $("#btn_save_category").prop('disabled', false);
        $("#category_form_save")[0].reset();
        $("#category_modal").modal("toggle");
    });
    $("#category_form_update").on("submit", function(e){
        e.preventDefault();
        let $form = $(this);
        let formData = $form.serialize();
        let url = $form.attr("action");
        let btn = $("#btn_update_category");
        let btnHtml = $("#btn_update_category").html();
        let catName = $("#edit_cat_name").val().trim();
        if(catName === "")
        {
            toastr.error("Category name cannot be empty.", "Validation Error");
            return;
        }
        var CTID = $("#hide_category_id").val();
        checkDuplicateCategoryWithID(catName, CTID).then(function(isDuplicate) {
            if(isDuplicate) {
                $("#edit_category_danger").addClass("text-danger").removeClass("text-success");
                $("#edit_category_danger").fadeIn();
                $("#edit_category_danger").html("&#9888; Category already exists.");
                $("#edit_cat_name").css("border-color","red");
                $("#btn_update_category").prop('disabled', false);
                toastr.error("Category already exists. Please choose a different name.", "Validation Error");
            }
            else
            {
                $("#edit_category_danger").removeClass("text-danger").addClass("text-success");
                $("#edit_category_danger").fadeIn();
                $("#edit_category_danger").html("&#10004; Category available.");
                $("#edit_cat_name").css("border-color", "green");
                $("#btn_update_category").prop('disabled', false);
                $.ajax({
                    url: url,
                    method: "POST",
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        btn.prop("disabled", true);
                        btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
                    },
                    success: function(response) {
                        btn.prop("disabled", false);
                        btn.html(btnHtml);
                        if(response.status === "success")
                        {
                            toastr.success(response.message, "Success");
                            $("#edit_category_modal").modal("hide");
                            FetchCategory();
                        }
                        else
                        {
                            toastr.error(response.message, "Error");
                            console.error("Server response:", response);
                        }
                    },
                    error: function(xhr, status, error) {
                        btn.prop("disabled", false);
                        btn.html(btnHtml);
                        toastr.error("An error occurred while updating the category. Please try again.", "Error");
                        console.error("AJAX Error:", error);
                    }
                });
            }
        });
    });
    $("#category_form_save").on("submit", function(e){
        e.preventDefault();
        let $form = $(this);
        let formData = $form.serialize();
        let url = $form.attr("action");
        let btn = $("#btn_save_category");
        let btnHtml = $("#btn_save_category").html();
        let catName = $("#cat_name").val().trim();
        if(catName === "")
        {
            toastr.error("Category name cannot be empty.", "Validation Error");
            return;
        }
        checkDuplicateCategory(catName).then(function(isDuplicate) {
            if(isDuplicate) {
                $("#category_danger").addClass("text-danger").removeClass("text-success");
                $("#category_danger").fadeIn();
                $("#category_danger").html("&#9888; Category already exists.");
                $("#cat_name").css("border-color","red");
                toastr.error("Category already exists. Please choose a different name.", "Validation Error");
            }
            else
            {
                $("#category_danger").removeClass("text-danger").addClass("text-success");
                $("#category_danger").fadeIn();
                $("#category_danger").html("&#10004; Category available.");
                $("#cat_name").css("border-color", "green");
                $("#btn_save_category").prop('disabled', false);
                $.ajax({
                    url: url,
                    method: "POST",
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        btn.prop("disabled", true);
                        btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
                    },
                    success: function(response) {
                        btn.prop("disabled", false);
                        btn.html(btnHtml);
                        if(response.status === "success")
                        {
                            toastr.success(response.message, "Success");
                            $("#category_modal").modal("hide");
                            FetchCategory();
                        }
                        else
                        {
                            toastr.error(response.message, "Error");
                            console.error("Server response:", response);
                        }
                    },
                    error: function(xhr, status, error) {
                        btn.prop("disabled", false);
                        btn.html(btnHtml);
                        toastr.error("An error occurred while adding the category. Please try again.", "Error");
                        console.error("AJAX Error:", error);
                    }
                });
            }
        });
    });
    $("#cat_name").on("input", function(){
        let catName = $(this).val();
        if(catName.trim() === "")
        {
            $("#category_danger").hide();
            $("#cat_name").css("border-color", "");
            return;
        }
        $.get("../AJAX/Categories/getDupliCategory.php", {
            cat_name: catName,
            ShopID: ShopID
        }, function(data){
            let catCount = data;
            if(catCount > 0)
            {
                $("#category_danger").addClass("text-danger").removeClass("text-success");
                $("#category_danger").fadeIn();
                $("#category_danger").html("&#9888; Category already exists.");
                $("#cat_name").css("border-color","red");
            }
            else
            {
                $("#category_danger").removeClass("text-danger").addClass("text-success");
                $("#category_danger").fadeIn();
                $("#category_danger").html("&#10004; Category available.");
                $("#cat_name").css("border-color", "green");
                $("#btn_save_category").prop('disabled', false);
            }
        });
    });
    $("#edit_cat_name").on("input", function(){
        let catName = $(this).val();
        var CTID = $("#hide_category_id").val();
        if(catName.trim() === "")
        {
            $("#edit_category_danger").hide();
            $("#edit_cat_name").css("border-color", "");
            return;
        }
        checkDuplicateCategoryWithID(catName, CTID).then(function(isDuplicate) {
            if(isDuplicate) {
                $("#edit_category_danger").addClass("text-danger").removeClass("text-success");
                $("#edit_category_danger").fadeIn();
                $("#edit_category_danger").html("&#9888; Category already exists.");
                $("#edit_cat_name").css("border-color","red");
                $("#btn_update_category").prop('disabled', true);
            }
            else
            {
                $("#edit_category_danger").removeClass("text-danger").addClass("text-success");
                $("#edit_category_danger").fadeIn();
                $("#edit_category_danger").html("&#10004; Category available.");
                $("#edit_cat_name").css("border-color", "green");
                $("#btn_update_category").prop('disabled', false);
            }
        });
    });
    FetchCategory();
    $("#refresh").on("click", function(){
        let $btn = $(this);
        let $icon = $btn.find("i");
        $btn.prop("disabled", true);
        $icon.css({
            "animation": "spin 0.8s linear infinite",
            "display": "inline-block"
        });
        FetchCategory().always(function () {
            // Stop spinning when AJAX completes
            $btn.prop("disabled", false);
            $icon.css("animation", "none");
        });            
    });
    function EditCategory(CTID)
    {
        
        $.ajax({
            url: '../AJAX/Categories/getCategoriesID.php',
            method: 'POST',
            data: { CTID: CTID },
            dataType: 'json',
            success: function(response) {
                if(response.length > 0)
                {
                    let category = response[0];
                    var CTID = category.CTID;
                    var CategoryNo = category.CategoryNo;
                    var CategoryName = category.CategoryName;
                    var catDefault = category.default;
                    if(catDefault == 1)
                    {
                        toastr.warning("Default category cannot be edited.", "Warning");
                        return;
                    }
                    $("#edit_cat_name").val(CategoryName);
                    $("#myModalLabelEdit").text("Edit Category - " + CategoryNo + " - " + CategoryName);
                    $("#hide_category_id").val(CTID);
                    $("#edit_category_modal").modal("toggle");
                }
                else
                {
                    toastr.error("Category details not found.", "Error");
                }
            },
            error: function(xhr, status, error) {
                toastr.error("An error occurred while fetching category details.", "Error");
                console.error("AJAX Error:", error);
            }
        });
    }
    $(document).on("click", ".edit-category", function () {
        $("#edit_category_danger").hide();
        let CTID = $(this).data("id");
        
        EditCategory(CTID);
    });
    $(document).on("click", ".delete-category", function () {
        $("#edit_category_danger").hide();
        let CTID = $(this).data("id");
        if (confirm("Are you sure you want to delete this category? This action cannot be undone.")) {
            $.ajax({
                url: '../Controller/deleteCategory.php',
                method: 'POST',
                data: { CTID: CTID },
                dataType: 'json',
                success: function(response) {
                    if(response.status === "success") {
                        toastr.success(response.message, "Success");
                        FetchCategory();
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
    function FetchCategory()
    {
        return $.ajax({
            url: '../AJAX/Categories/getCategories.php',
            method: 'POST',
            dataType: 'json',
            beforeSend: function () {
                TableLoading($table, "Categories");
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
                                No Category data available.
                            </td>
                        </tr>
                    `);
                    return;
                }
                var ItemNo = 1;
                $.each(response, function (index, row) {
                    let CTID = row["CTID"];
                    let CategoryNo = row["CategoryNo"];
                    let CategoryName = row["CategoryName"];
                    let Catdefault = row["default"];
                    let TotalSubcategories = row["TotalSubcategories"];
                    let TotalProducts = row["TotalProducts"];
                    let dropdownItems = '';
                    if(userType === 1 || edit_access === 1)
                    {
                        dropdownItems += `
                            <li><a class="dropdown-item edit-category" href="javascript:void(0)" data-id="${CTID}">Edit</a></li>
                        `;
                    }
                    if(userType === 1 || delete_access === 1)
                    {
                        dropdownItems += `
                            <li><a class="dropdown-item delete-category" href="javascript:void(0)"  data-id="${CTID}">Delete</a></li>
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
                            <tr data-ctid="${CTID}" id="row_${CTID}">
                                <td>${ItemNo}</td>
                                <td>${CategoryNo}</td>
                                <td>${CategoryName}</td>
                                <td class="text-center"><a href="../Public/subcategory2.php?query=${CategoryName}" target="_blank_">${TotalSubcategories}</a></td>
                                <td class="text-center"><a href="../Public/product.php?Category=${CategoryName}" target="_blank_">${TotalProducts}</a></td>
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
                exportTableButtons('tbl_category');
            },
            error: function (xhr, status, error) {
                console.log("AJAX Error:", error);
                toastr.error("An error occurred while loading GRN data.", "Error");
            }
        });
    }
});