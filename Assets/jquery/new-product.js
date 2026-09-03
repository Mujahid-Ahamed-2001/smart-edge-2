var app_status = $(document).find("#app_status").val();
var query = $(document).find("#query").val();
console.log("App Status "+app_status);
let $table = $('#tbl_products');
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
function fetchProducts($table, $query='', formData='',PDID='')
{
    let tableID = $table.attr('id');
    return $.ajax({
        url: '../AJAX/Products/getProductsData.php?query=' + encodeURIComponent($query),
        method: 'POST',
        data: formData,
        dataType: 'json',
        cache: false,
        beforeSend: function () {
            TableLoading($table, "Products");
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
                            No Product data available.
                        </td>
                    </tr>
                `);
                return;
            }
            var ItemNo = 1;
            $.each(response, function (index, row) {
                let PDID = row["PDID"];
                let ProductNo = row["ProductNo"];
                let ProdImage = "../"+row["ProdImage"];
                let Barcode = row["Barcode"];
                let ItemName = row["ItemName"];
                let ProdPurchasePrice = row["ProdPurchasePrice"];
                let ProdSellPrice = row["ProdSellPrice"];
                let ProductStat = row["ProductStat"];
                let ItemType = row["ItemType"];
                let prodDiscount = row["prodDiscount"];
                let prodFlatDiscount = row["prodFlatDiscount"];
                let is_fixedPrice = row["is_fixedPrice"];
                let is_lowStock = row["is_lowStock"];
                let CatName = row["CatName"];
                let SubCatName = row["SubCatName"];
                let CurrentQty = row["CurrentQty"];
                let low_stock_qty = row["low_stock_qty"];
                let dropdownItems = '';
                var statusBadge = ``;
                if(ProductStat==1)
                {
                    if(userType === 1 || edit_access === 1)
                    {
                        statusBadge =`<button class="badge bg-primary productStatus shadow-btn" data-id="${PDID}" id="productStatus_${PDID}">Active</button>`;
                    }
                    else
                    {
                        statusBadge =`<span class="badge bg-primary shadow-btn" data-id="${PDID}" id="productStatus_${PDID}">Active</span>`;
                    }
                    
                }
                else
                {
                    if(userType === 1 || edit_access === 1)
                    {
                        statusBadge =`<button class="badge bg-danger productStatus shadow-btn" data-id="${PDID}" id="productStatus_${PDID}">Inactive</button>`;
                    }
                    else
                    {
                        statusBadge =`<span class="badge bg-danger shadow-btn" data-id="${PDID}" id="productStatus_${PDID}">Inactive</span>`;
                    }
                    
                }
                var ItemTypeBadge = ``;
                if(ItemType=="P")
                {
                    ItemTypeBadge =`<p class="text-success" style="font-weight: 700;"><i class="ti ti-box"></i> Product</p>`;
                }
                else
                {
                    ItemTypeBadge =`<p class="text-warning" style="font-weight: 700;"><i class="ti ti-man"></i> Service</p>`;
                }
                var is_fixedPriceBadge = ``;
                if(is_fixedPrice==1)
                {
                    if(userType === 1 || edit_access === 1)
                    {
                        is_fixedPriceBadge =`<button class="badge bg-primary chk_fp shadow-btn" data-id="${PDID}" id="chk_fp_${PDID}">Fixed Price</button>`;
                    }
                    else
                    {
                        is_fixedPriceBadge =`<span class="badge bg-primary shadow-btn" data-id="${PDID}" id="chk_fp_${PDID}">Fixed Price</span>`;
                    }
                    
                    
                }
                else
                {
                    if(userType === 1 || edit_access === 1)
                    {
                        is_fixedPriceBadge =`<button class="badge bg-danger chk_fp shadow-btn" data-id="${PDID}" id="chk_fp_${PDID}">No Fixed Price</button>`;
                    }
                    else
                    {
                        is_fixedPriceBadge =`<span class="badge bg-danger shadow-btn" data-id="${PDID}" id="chk_fp_${PDID}">No Fixed Price</span>`;
                    }
                    
                    
                }
                var is_lowStockBadge = ``;
                if(is_lowStock==1)
                {
                    if(userType === 1 || edit_access === 1)
                    {
                        is_lowStockBadge =`<button class="badge bg-primary shadow-btn chk_ls" data-id="${PDID}" id="chk_ls_${PDID}">Is Low Stock</button>`;
                    }
                    else
                    {
                        is_lowStockBadge =`<span class="badge bg-primary shadow-btn" data-id="${PDID}" id="chk_ls_${PDID}">Is Low Stock</span>`;
                    }
                    
                    
                }
                else
                {
                    if(userType === 1 || edit_access === 1)
                    {
                        is_lowStockBadge =`<button class="badge bg-danger shadow-btn chk_ls" data-id="${PDID}" id="chk_ls_${PDID}">Not Low Stock</button>`;
                    }
                    else
                    {
                        is_lowStockBadge =`<span class="badge bg-danger shadow-btn" data-id="${PDID}" id="chk_ls_${PDID}">Not Low Stock</span>`;
                    }
                    
                    
                }
                if(userType === 1 || edit_access === 1)
                {
                    dropdownItems += `
                        <li><a class="dropdown-item open-modal" href="../View/modals/addproducts.php?condition=edit&ref=product&PDID=${PDID}" data-id="${PDID}">Edit</a></li>
                    `;
                }
                if(userType === 1 || delete_access === 1)
                {
                    dropdownItems += `
                        <li><a class="dropdown-item delete-product" href="javascript:void(0)"  data-id="${PDID}">Delete</a></li>
                    `;
                }
                var actionBtn = ``;
                if(userType === 1 || delete_access === 1 || edit_access === 1)
                {
                    actionBtn = `
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary">Action</button>
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                                data-bs-toggle="dropdown"></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                ${dropdownItems}
                            </ul>
                        </div>
                    `;
                }
                var barcodeBtn =`<button class="btn_open_barcode border-0 bg-transparent w-100" style="font-size: 1.5rem;" data-id="${PDID}"><i class="ti ti-barcode"></i></button>`;
                $table.find('tbody').append(`
                        <tr data-id="${PDID}"  data-itemno="${ItemNo}" id="row_${PDID}" class="tbl_row">
                            <td><input type="checkbox" class="form-checkbox proCheck" value="${PDID}" data-id="${PDID}"></td><td>
                                <div style="display:flex; align-items:center;">
                                    <img class="img" src="${ProdImage}" style="width:50px; height:50px; margin-right:15px; border-radius:5px; box-shadow: 5px 5px 5px #000000a1;">
                                    <div>
                                        <div>${ItemNo + " " + ItemName}</div>
                                        <small>Product No: ${ProductNo}</small><br>
                                        <small>Available Stock: ${CurrentQty}</small><br>
                                        <small>Low Stock Qty: ${low_stock_qty}</small>
                                    </div>
                                </div>
                            </td>
                            <td>${Barcode}</td>
                            <td>${CatName}</td>
                            <td>${SubCatName}</td>
                            <td>${ProdPurchasePrice}</td>
                            <td>${ProdSellPrice}</td>
                            <td>${prodDiscount}</td>
                            <td>${prodFlatDiscount}</td>
                            <td>${ItemTypeBadge}</td>
                            <td>${statusBadge}</td>
                            <td>${is_fixedPriceBadge}</td>
                            <td>${is_lowStockBadge}</td>
                            <td>${barcodeBtn}</td>
                            <td>${actionBtn}</td>
                        </tr>
                    `);
                ItemNo++;
            });
            $table.DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                pageLength: 25,
                columnDefs: [
                    { orderable: false, targets: 0 }
                ]
            });
            exportTableButtons(tableID);

        },
        error: function (xhr, status, error) {
            console.log("AJAX Error:", error);
            toastr.error("An error occurred while loading Product data.", "Error");
        }
    });
}
function fetchProducts1($table,PDID)
{
    let tableID = $table.attr('id');
    let ItemNo = $("#row_" + PDID).data("itemno");
    return $.ajax({
        url: '../AJAX/Products/getProductsData1.php?PDID=' + PDID,
        method: 'POST',
        dataType: 'json',
        cache: false,
        beforeSend: function(){
            let $row = $("#row_" + PDID);
            $row.addClass("loading-row");
            $row.find("td").each(function () {
                $(this).html('<div class="skeleton"></div>');
            });
        },
        success: function (response) {
            setTimeout(() => {
                $("#row_"+PDID).removeClass("loading-row");
                $.each(response, function (index, row) {
                    let PDID = row["PDID"];
                    let ProductNo = row["ProductNo"];
                    let ProdImage = "../"+row["ProdImage"];
                    let Barcode = row["Barcode"];
                    let ItemName = row["ItemName"];
                    let ProdPurchasePrice = row["ProdPurchasePrice"];
                    let ProdSellPrice = row["ProdSellPrice"];
                    let ProductStat = row["ProductStat"];
                    let ItemType = row["ItemType"];
                    let prodDiscount = row["prodDiscount"];
                    let prodFlatDiscount = row["prodFlatDiscount"];
                    let is_fixedPrice = row["is_fixedPrice"];
                    let CatName = row["CatName"];
                    let SubCatName = row["SubCatName"];
                    let CurrentQty = row["CurrentQty"];
                    let is_lowStock = row["is_lowStock"];
                    let dropdownItems = '';
                    var statusBadge = ``;
                    if(ProductStat==1)
                    {
                        if(userType === 1 || edit_access === 1)
                        {
                            statusBadge =`<button class="badge bg-primary productStatus shadow-btn" data-id="${PDID}" id="productStatus_${PDID}">Active</button>`;
                        }
                        else
                        {
                            statusBadge =`<span class="badge bg-primary shadow-btn" data-id="${PDID}" id="productStatus_${PDID}">Active</span>`;
                        }
                        
                    }
                    else
                    {
                        if(userType === 1 || edit_access === 1)
                        {
                            statusBadge =`<button class="badge bg-danger productStatus shadow-btn" data-id="${PDID}" id="productStatus_${PDID}">Inactive</button>`;
                        }
                        else
                        {
                            statusBadge =`<span class="badge bg-danger shadow-btn" data-id="${PDID}" id="productStatus_${PDID}">Inactive</span>`;
                        }
                        
                    }
                    var ItemTypeBadge = ``;
                    if(ItemType=="P")
                    {
                        ItemTypeBadge =`<p class="text-success" style="font-weight: 700;"><i class="ti ti-box"></i> Product</p>`;
                    }
                    else
                    {
                        ItemTypeBadge =`<p class="text-warning" style="font-weight: 700;"><i class="ti ti-man"></i> Service</p>`;
                    }
                    var is_fixedPriceBadge = ``;
                    if(is_fixedPrice==1)
                    {
                        if(userType === 1 || edit_access === 1)
                        {
                            is_fixedPriceBadge =`<button class="badge bg-primary chk_fp shadow-btn" data-id="${PDID}" id="chk_fp_${PDID}">Fixed Price</button>`;
                        }
                        else
                        {
                            is_fixedPriceBadge =`<span class="badge bg-primary shadow-btn" data-id="${PDID}" id="chk_fp_${PDID}">Fixed Price</span>`;
                        }
                        
                        
                    }
                    else
                    {
                        if(userType === 1 || edit_access === 1)
                        {
                            is_fixedPriceBadge =`<button class="badge bg-danger chk_fp shadow-btn" data-id="${PDID}" id="chk_fp_${PDID}">No Fixed Price</button>`;
                        }
                        else
                        {
                            is_fixedPriceBadge =`<span class="badge bg-danger shadow-btn" data-id="${PDID}" id="chk_fp_${PDID}">No Fixed Price</span>`;
                        }
                        
                        
                    }
                    var is_lowStockBadge = ``;
                    if(is_lowStock==1)
                    {
                        if(userType === 1 || edit_access === 1)
                        {
                            is_lowStockBadge =`<button class="badge bg-primary shadow-btn chk_ls" data-id="${PDID}" id="chk_ls_${PDID}">Is Low Stock</button>`;
                        }
                        else
                        {
                            is_lowStockBadge =`<span class="badge bg-primary shadow-btn" data-id="${PDID}" id="chk_ls_${PDID}">Is Low Stock</span>`;
                        }
                        
                        
                    }
                    else
                    {
                        if(userType === 1 || edit_access === 1)
                        {
                            is_lowStockBadge =`<button class="badge bg-danger shadow-btn chk_ls" data-id="${PDID}" id="chk_ls_${PDID}">Not Low Stock</button>`;
                        }
                        else
                        {
                            is_lowStockBadge =`<span class="badge bg-danger shadow-btn" data-id="${PDID}" id="chk_ls_${PDID}">Not Low Stock</span>`;
                        }
                        
                        
                    }
                    if(userType === 1 || edit_access === 1)
                    {
                        dropdownItems += `<li><a class="dropdown-item open-modal" href="../View/modals/addproducts.php?condition=edit&ref=product&PDID=${PDID}" data-id="${PDID}">Edit</a></li>
                        `;
                    }
                    if(userType === 1 || delete_access === 1)
                    {
                        dropdownItems += `
                            <li><a class="dropdown-item delete-product" href="javascript:void(0)"  data-id="${PDID}">Delete</a></li>
                        `;
                    }
                    var actionBtn = ``;
                    if(userType === 1 || delete_access === 1 || edit_access === 1)
                    {
                        actionBtn = `
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary">Action</button>
                                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                                    data-bs-toggle="dropdown"></button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    ${dropdownItems}
                                </ul>
                            </div>
                        `;
                    }
                    var barcodeBtn =`<button class="btn_open_barcode border-0 bg-transparent w-100" style="font-size: 1.5rem;" data-id="${PDID}"><i class="ti ti-barcode"></i></button>`;
                    $table.find('tbody #row_'+PDID).html(`<td><input type="checkbox" class="form-checkbox proCheck" value="${PDID}" data-id="${PDID}"></td><td>
                                    <div style="display:flex; align-items:center;">
                                        <img class="img" src="${ProdImage}" style="width:50px; height:50px; margin-right:15px; border-radius:5px; box-shadow: 5px 5px 5px #000000a1;">
                                        <div>
                                            <div>${ItemNo + " " + ItemName}</div>
                                            <small>Product No: ${ProductNo}</small><br>
                                            <small>Available Stock: ${CurrentQty}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>${Barcode}</td>
                                <td>${CatName}</td>
                                <td>${SubCatName}</td>
                                <td class="text-end">${ProdPurchasePrice}</td>
                                <td class="text-end">${ProdSellPrice}</td>
                                <td class="text-end">${prodDiscount}</td>
                                <td class="text-end">${prodFlatDiscount}</td>
                                <td>${ItemTypeBadge}</td>
                                <td>${statusBadge}</td>
                                <td>${is_fixedPriceBadge}</td>
                                <td>${is_lowStockBadge}</td>
                                <td>${barcodeBtn}</td>
                                <td>${actionBtn}</td>
                        `);
                });
            }, 1500);
            
            

        },
        error: function (xhr, status, error) {
            $("#row_"+PDID).removeClass("loading-row");
            $("#row_"+PDID).find("td").removeClass("skeleton");
            console.log("AJAX Error:", error);
            toastr.error("An error occurred while loading Product data.", "Error");
        }
    });
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

        var html="<option value=''>Main Category</option>";
        obj.forEach(function(item) {
            html += `<option value='${item.CTID}' ${selected == item.CTID ? "selected" : ""}>${item.CategoryNo} - ${item.CategoryName} </option>`;
        });
        $("#category").html(html);
        $("#cmb_category").html(html);
    });
}
function allCategory2()
{
    var id=1;
    var selected=$("#editcmb_category").val();
    $.get("../AJAX/guiPos/getmaincat.php", {
        supplier_id : id
    }, function(data){
        // alert(data);
        const obj = JSON.parse(data);

        var html="<option value=''>Main Category</option>";
        obj.forEach(function(item) {
            html += `<option value='${item.CTID}' ${selected == item.CTID ? "selected" : ""}>${item.CategoryNo} - ${item.CategoryName} </option>`;
        });
        $("#editcmb_category").html(html);
    });
}
function allCategory3()
{
    var id=1;
    var selected=$("#multieditcmb_category").val();
    $.get("../AJAX/guiPos/getmaincat.php", {
        supplier_id : id
    }, function(data){
        // alert(data);
        const obj = JSON.parse(data);

        var html="<option value=''>Main Category</option>";
        obj.forEach(function(item) {
            html += `<option value='${item.CTID}' ${selected == item.CTID ? "selected" : ""}>${item.CategoryNo} - ${item.CategoryName} </option>`;
        });
        $("#multieditcmb_category").html(html);
    });
}
function subcat()
{
    // var id=1;
    var maincat=$("#category").val();
    if(maincat=="")
    {
        alert("Select A Main Category");
        $("#category").focus();
    }
    else
    {
        var selected=$("#subcategory").val();
        $.get("../AJAX/guiPos/getsubcat.php", {
            maincat : maincat
        }, function(data){
            // alert(data);
            const obj = JSON.parse(data);

            var html="<option value=''>Sub Category</option>";
            obj.forEach(function(item) {
                html += `<option value='${item.SCID}' ${selected == item.SCID ? "selected" : ""}>${item.SubCatNo} - ${item.SubCatName} </option>`;
            });
            $("#subcategory").html(html);
        });
    }
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
function subcat3()
{
    // var id=1;
    var maincat=$("#editcmb_category").val();
    if(maincat=="")
    {
        alert("Select A Main Category");
        $("#editcmb_category").focus();
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
            $("#editcmb_subcategory").html(html);
        });
    }
}
function subcat4()
{
    // var id=1;
    var maincat=$("#multieditcmb_category").val();
    if(maincat=="")
    {
        alert("Select A Main Category");
        $("#multieditcmb_category").focus();
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
            $("#multieditcmb_subcategory").html(html);
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
function changeStatus(PDID, btn){
    return $.ajax({
        url: '../AJAX/Products/ChangeStatus.php',
        method: 'POST',
        data: {PDID:PDID},
        dataType: 'json',
        beforeSend: function(){
            btn.addClass("status-changing");
        },
        success: function (response) {

            if(response.status === "success")
            {
                toastr.success(response.message, "Success");

                setTimeout(function(){

                    if(response.ProductStat === 1){
                        btn.removeClass("bg-danger")
                           .addClass("bg-primary")
                           .text("Active");
                    }
                    else{
                        btn.removeClass("bg-primary")
                           .addClass("bg-danger")
                           .text("Inactive");
                    }

                    btn.removeClass("status-changing");

                },200);

            }
            else
            {
                toastr.error(response.message, "Error");
            }
        },
        error: function (xhr, status, error) {
            console.log("AJAX Error:", error);
            toastr.error("An error occurred while updating Product status.", "Error");
        }
    });
}
function changeFixedPrice(PDID, btn){
    return $.ajax({
        url: '../AJAX/Products/changeFixedPrice.php',
        method: 'POST',
        data: {PDID:PDID},
        dataType: 'json',
        beforeSend: function(){
            btn.addClass("status-changing");
        },
        success: function (response) {

            if(response.status === "success")
            {
                toastr.success(response.message, "Success");

                setTimeout(function(){

                    if(response.is_fixedPrice === 1){
                        btn.removeClass("bg-danger")
                           .addClass("bg-primary")
                           .text("Fixed Price");
                    }
                    else{
                        btn.removeClass("bg-primary")
                           .addClass("bg-danger")
                           .text("No Fixed Price");
                    }

                    btn.removeClass("status-changing");

                },200);

            }
            else
            {
                toastr.error(response.message, "Error");
            }
        },
        error: function (xhr, status, error) {
            console.log("AJAX Error:", error);
            toastr.error("An error occurred while updating Product fixed price.", "Error");
        }
    });
}
function changeLowStock(PDID, btn){
    return $.ajax({
        url: '../AJAX/Products/changeLowStock.php',
        method: 'POST',
        data: {PDID:PDID},
        dataType: 'json',
        beforeSend: function(){
            btn.addClass("status-changing");
        },
        success: function (response) {

            if(response.status === "success")
            {
                toastr.success(response.message, "Success");                

                setTimeout(function(){

                    if(response.is_lowStock === 1){
                        btn.removeClass("bg-danger")
                           .addClass("bg-primary")
                           .text("Is Low Stock");
                    }
                    else{
                        btn.removeClass("bg-primary")
                           .addClass("bg-danger")
                           .text("Not Low Stock");
                    }

                    btn.removeClass("status-changing");

                },200);

            }
            else
            {
                toastr.error(response.message, "Error");
            }
        },
        error: function (xhr, status, error) {
            console.log("AJAX Error:", error);
            toastr.error("An error occurred while updating Product fixed price.", "Error");
        }
    });
}

function deleteProduct(PDID,btn="")
{
    return $.ajax({
        url: '../AJAX/Products/deleteProduct.php',
        method: 'POST',
        data: {PDID:PDID},
        dataType: 'json',
        beforeSend: function(){
            if(btn){
                btn.prop("disabled", true);
            }
            
        },
        success: function (response) {

            if(response.status === "success")
            {
                toastr.success(response.message, "Success");
            }
            else
            {
                toastr.error(response.message, "Error");
            }
            if(btn){
                btn.prop("disabled", false);
            }
        },
        error: function (xhr, status, error) {
            if(btn){
                btn.prop("disabled", false);
            }
            console.log("AJAX Error:", error);
            toastr.error("An error occurred while deleting Product status.", "Error");
        }
    });
}
function getProductDetails(PDID, btn)
{
    return $.ajax({
        url: '../AJAX/Products/getOneProduct.php?product_id=' + encodeURIComponent(PDID),
        method: 'POST',
        data: {PDID:PDID},
        dataType: 'json',
        beforeSend: function(){
            btn.prop("disabled", true);
        },
        success: function (response) {

            
            if (!response || response.length === 0) {
                toastr.error("An error occurred while fetching Product data.", "Error");
                btn.prop("disabled", false);
                return;
            }
            var PDIDs = response.PDID;
            var CTID = response.CTID;
            var SCID = response.SCID;
            var Barcode = response.Barcode;
            var ItemName = response.ItemName;
            var SecondName = response.SecondName;
            var ProdDescription = response.ProdDescription;
            var ProdPurchasePrice = response.ProdPurchasePrice;
            var ProdSellPrice = response.ProdSellPrice;
            var ItemType = response.ItemType;
            var PurchaseUnit = response.PurchaseUnit;
            var UnitConversion = response.UnitConversion;
            var SellingUnit = response.SellingUnit;
            var prodDiscount = response.prodDiscount;
            var is_fixedPrice = response.is_fixedPrice;
            var prodFlatDiscount = response.prodFlatDiscount;
            var editprodStatus = response.ProductStat;
            var ProdImage = response.ProdImage;
            $("#editcmb_category").val(CTID).trigger("change");
            setTimeout(() => {
                $("#editcmb_subcategory").val(SCID).trigger("change");    
                console.log("changed");
                
            }, 1000);
            
            if(editprodStatus==1)
            {
                $("#editprodStatus").prop("checked", true);
            }
            else
            {
                $("#editprodStatus").prop("checked", false);
            }
            $("#editbarcode").val(Barcode);
            $("#editprod_name").val(ItemName);
            $("#editsecond_name").val(SecondName);
            $("#editprod_description").val(ProdDescription);
            $("#editprod_purchase_price").val(ProdPurchasePrice);
            $("#editprod_selling_price").val(ProdSellPrice);
            $("#editprod_Item_Dis").val(prodDiscount);
            $("#editprod_Item_Dis_flat").val(prodFlatDiscount);
            if(ItemType=="S")
            {
                $("#editchk_service").prop("checked", true);
            }
            else
            {
                $("#editchk_service").prop("checked", false);
            }
            if(is_fixedPrice==1)
            {
                $("#editchk_fp").prop("checked", true);
            }
            else
            {
                $("#editchk_fp").prop("checked", false);
            }
            $("#editcmb_purchase_unit").val(PurchaseUnit);
            $("#editconversion_rate").val(UnitConversion);
            $("#editcmb_selling_unit").val(SellingUnit);
            $("#hidden_PDID").val(PDID);
            $("#editimg_product").attr("src","../"+ProdImage);
            $("#editproduct_modal").modal("toggle");
            // console.log("PDID "+PDID);
            
            // console.log("editprodStatus"+editprodStatus);
            
            btn.prop("disabled", false);
        },
        error: function (xhr, status, error) {
            btn.prop("disabled", false);
            console.log("AJAX Error:", error);
            toastr.error("An error occurred while fetching Product data.", "Error");
        }
    });
}
function toggleMultiEdit(table) {
    var checkedCount = $(table.rows().nodes()).find(".proCheck:checked").length;
    if (checkedCount > 0) {
        $("#mutlyEdit").stop(true, true).slideDown(200);
    } else {
        $("#mutlyEdit").stop(true, true).slideUp(200);
    }
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
function openModal2(url, title = "Modal") {
    $("#modal").iziModal("destroy");

    $("#modal").iziModal({
        // title: title,
        width: "75%",
        overlayClose: true,
        iframe: true,
        iframeURL: url,
        fullscreen: true,
        openFullscreen: false,
        borderBottom: false,
        borderRadius: "10px",
        overlayColor: "rgba(0, 0, 0, 0.8)",
        responsive: true,
        iframeHeight: window.innerHeight * 0.8
    });

    $("#modal").iziModal("open");
}
$(document).ready(function () {
    $(document).on('click', '.open-modal', function (e) {

        e.preventDefault();

        let url = $(this).attr('href');
        let title = $(this).data('title') || 'Company';

        openModal(url,title);
    });
    $("#headerCollapse2").trigger("click");
    $("#headerCollapse2").on("click", function () {
        console.log("Clicked");        
    });
    // $("#multieditproducts_modal").modal("toggle");
    fetchProducts($table, query);
    allCategory();
    allCategory2();
    allCategory3();
    $("body").on("click", ".tbl_row", function (e) {
        if ( $(e.target).closest( "button, a, span, input, select, textarea, label" ).length ) 
        {
            return;
        }

        var checkbox = $(this).find(".proCheck");
        checkbox.prop("checked", !checkbox.prop("checked")).trigger("change");
        var table = $("#tbl_products").DataTable();
        toggleMultiEdit(table);
    });
    $table.on('click', '.btn_open_barcode', function(){
        let row = $(this).closest('tr');
        let id = $(this).data('id');

        $.get("../AJAX/Products/getProductBatch.php", {
            product_id : id
        }, function(data){
            // alert(data);
            var obj = JSON.parse(data);
            $("#product_barcode_modal").modal('toggle');

            $("#hide_label_product_id").val(id);

            // alert("prod - " + id);

            var opt_data = "";
            var price = obj[0]['SellingPrice'];
            $.each(obj, function (key, value) { 
                var batch = value['BatchID'];
                opt_data += "<option value='"+ price +"'>"+batch+" - "+price+"</option>";
            });

            $("#cmb_batch_price").html(opt_data);
            $("#label_price").val(price);
            
        });
    });
    $("#cmb_batch_price").change(function(){
        var label_price = $(this).val();
        $("#label_price").val(label_price);
    });//change
    $("#multieditproducts").on("submit", function(e){
        e.preventDefault();
        let formData = new FormData(this);
        let url = $(this).attr("action");
        let btn = $("#bulk_btn_update_product");
        let btnHtml = btn.html();        
        var PDID = $("#bulkhidden_PDID").val();
        var table = $("#tbl_products").DataTable();
        if (PDID !== undefined && PDID !== "")
        {
            submitProductUpdate()
        }
        else
        {
            toastr.error("An error occurred while fetching PDID.", "Error");
        }
        function submitProductUpdate()
        {
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function() {
                    btn.prop("disabled", true);
                    btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
                },
                success: function(response) {
                    btn.prop("disabled", false);
                    btn.html(btnHtml);
                    var hasProductUpdated = false;
                    if (response.success && Array.isArray(response.success)) {
                        response.success.forEach(function(successMsg) {
                            let msg = Array.isArray(successMsg) ? successMsg[0] : successMsg;
                            toastr.success(msg, "Success");

                            if (msg === "Product subcategory updated successfully" || msg === "Product % discount updated successfully" || msg === "Product flat discount updated successfully" || msg === "Product updated to service successfully" || msg === "Product updated to fixed price successfully" || msg ==="Product purchase price updated successfully" || msg ==="Product selling price updated successfully" || msg==="Product image updated successfully") {
                                hasProductUpdated = true;
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
                    if (hasProductUpdated) {
                        $("#multieditproducts_modal").modal("toggle");
                        let pdidArray = PDID.split(",");

                        pdidArray.forEach(function(id) {
                            id = $.trim(id);
                            if (id !== "") {
                                fetchProducts1($table, id);
                            }
                        });
                        $(table.rows().nodes()).find(".proCheck").prop("checked", false);
                        toggleMultiEdit(table);
                        
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
    })
    $(document).on("click", ".editAll", function ()
    { 
        var btn = $(this);
         var btnHTML = btn.html();
         var table = $("#tbl_products").DataTable();
         var selectedProducts = [];
         $(table.rows().nodes()).find(".proCheck:checked").each(function () { 
            selectedProducts.push($(this).val());

         });
         if (selectedProducts.length === 0) { 
            toastr.error("No products selected", "Error");
            return;

         } 
         $("#bulkhidden_PDID").val(selectedProducts);
         e.preventDefault();
         let url = $(this).attr('href');
         let title = $(this).data('title') || 'Company';
         openModal(url,title);

    });
    $(document).on("click", ".editAll2", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        const btn = $(this);
        const table = $("#tbl_products").DataTable();
        const selectedProducts = [];

        $(table.rows().nodes())
            .find(".proCheck:checked")
            .each(function () {
                selectedProducts.push($(this).val());
            });

        if (selectedProducts.length === 0) {
            toastr.error("No products selected", "Error");
            return;
        }

        const separator = btn.attr("href").includes("?") ? "&" : "?";

        const url =
            btn.attr("href") +
            separator +
            "product_ids=" +
            encodeURIComponent(selectedProducts.join(","));

        const title = btn.data("title") || "Edit Products";

        openModal2(url, title);
    });
    $(document).on("click", ".deleteAll", function () {
        var btn = $(this);
        var btnHTML = btn.html();
        var table = $("#tbl_products").DataTable();
        var selectedProducts = [];

        $(table.rows().nodes()).find(".proCheck:checked").each(function () {
            selectedProducts.push($(this).val());
        });

        if (selectedProducts.length === 0) {
            toastr.error("No products selected", "Error");
            return;
        }

        if (!confirm("Are you sure you want to delete these products")) {
            return;
        }

        btn.prop("disabled", true);
        btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...');

        var requests = [];

        selectedProducts.forEach(function (PDID) {
            if (PDID !== undefined && PDID !== "") {
                requests.push(
                    deleteProduct(PDID).done(function (response) {
                        if (response.status === "success") {
                            $(`#row_${PDID}`).fadeOut(300, function () {
                                $(this).remove();
                            });
                        }
                    })
                );
            } else {
                toastr.error("An error occurred while deleting Product data.", "Error");
            }
        });

        $.when.apply($, requests).always(function () {
            btn.prop("disabled", false);
            btn.html(btnHTML);
        });
        $("#mutlyEdit").stop(true, true).slideUp(200);
    });

    $(document).on("click", ".allCheck", function (){
        var isChecked = $(this).prop("checked");
        var table = $("#tbl_products").DataTable();

        $(table.rows().nodes()).find(".proCheck").prop("checked", isChecked);
        toggleMultiEdit(table);
    });
    $(document).on("click", ".proCheck", function () {
        var table = $("#tbl_products").DataTable();
        var total = $(".proCheck").length;
        var total = $(table.rows().nodes()).find(".proCheck").length;
        var checked = $(table.rows().nodes()).find(".proCheck:checked").length;
        
        

        if (total === checked) {
            $(".allCheck").prop("checked", true);
        } else {
            $(".allCheck").prop("checked", false);
        }
        toggleMultiEdit(table);

    });
    $("#btn_add_product").on("click", function(){        
        $("#img_product").attr("src","../Assets/Images/icons/product.png");
        $("#addProduct")[0].reset();
        $("#product_modal").modal("toggle");
    });
    $("body").on("click", ".productStatus",function(e){
        e.preventDefault();
        var btn = $(this)
        var btnParent = btn.parent();
        var PDID = btn.data("id")
        changeStatus(PDID,btn)
    });
    $("body").on("click", ".delete-product",function(e){
        e.preventDefault();
        var btn = $(this);
        var PDID = btn.data("id");
        // changeStatus(PDID,btn)
        if(typeof PDID !== "undefined" && PDID !== "")
        {
            if(confirm("Are you sure to delete this product? This may cause miscalculations in previouse invoices."))
            {
                deleteProduct(PDID,btn ).done(function (response) {
                    if (response.status === "success") {
                        $(`#row_${PDID}`).fadeOut(300, function () {
                            $(this).remove();
                        });
                    }
                });
            }
        }
        else
        {
            toastr.error("An error occurred while fetching Product data.", "Error");
        }
    });
    $("body").on("click", ".edit-product", function(e){
        e.preventDefault();
        var btn = $(this);
        var PDID = btn.data("id");
        if(typeof PDID !== "undefined" && PDID !== "")
        {
            $("#editProduct")[0].reset();
            getProductDetails(PDID,btn);
        }
        else
        {
            toastr.error("An error occurred while fetching Product data.", "Error");
        }
    })
    $("body").on("click", ".chk_fp",function(e){
        e.preventDefault();
        var btn = $(this)
        var btnParent = btn.parent();
        var PDID = btn.data("id")
        changeFixedPrice(PDID,btn)
    });
    $("body").on("click", ".chk_ls",function(e){
        e.preventDefault();
        var btn = $(this)
        var btnParent = btn.parent();
        var PDID = btn.data("id")
        changeLowStock(PDID,btn)
    });
    // var barcode = "PD_000001";
    // checkBarcode(barcode, 1);
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
    $("#editbarcode").on('keypress', function(e) {
        if (e.which === 13) { // 13 is the key code for Enter
            e.preventDefault(); // Prevent the default Enter action
        }
        var barcode = $(this).val();
        var barcodeInput = $(this);
        var PDID = $("#hidden_PDID").val();
        if(barcode !="")
        {
            checkBarcode(barcode, PDID).then(function(exist){
                if(exist>0)
                {                    
                    toastr.error("Barcode Already Exists, Please Try a New One.", "Error");
                    barcodeInput.focus();
                    barcodeInput.css("border-color","red !important");
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
    $("#editbarcode").on("input", function(e){
        e.preventDefault();
        var barcode = $(this).val();
        var barcodeInput = $(this);
        var PDID = $("#hidden_PDID").val();
        if(barcode !="")
        {
            checkBarcode(barcode, PDID).then(function(exist){
                if(exist>0)
                {                    
                    toastr.error("Barcode Already Exists, Please Try a New One.", "Error");
                    barcodeInput.focus();
                    barcodeInput.css("border-color","red !important");
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
    $("#editProduct").on("submit", function(e){
        e.preventDefault();
        let formData = new FormData(this);
        let url = $(this).attr("action");
        let barcode = $("#editbarcode").val();
        let btn = $("#btn_update_product");
        let btnHtml = btn.html();        
        var barcodeInput = $("#editbarcode");
        var PDID = $("#hidden_PDID").val();
        function submitProductUpdate()
        {
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function() {
                    btn.prop("disabled", true);
                    btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
                },
                success: function(response) {
                    btn.prop("disabled", false);
                    btn.html(btnHtml);
                    var hasProductUpdated = false;
                    if (response.success && Array.isArray(response.success)) {
                        response.success.forEach(function(successMsg) {
                            let msg = Array.isArray(successMsg) ? successMsg[0] : successMsg;
                            toastr.success(msg, "Success");

                            if (msg === "Product updated successfully") {
                                hasProductUpdated = true;
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
                    if (hasProductUpdated) {
                        $("#editproduct_modal").modal("toggle");
                        fetchProducts1($table, PDID);
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
        if(barcode !="")
        {
            checkBarcode(barcode, PDID).then(function(exist){
                if(exist>0)
                {                    
                    toastr.error("Barcode Already Exists, Please Try a New One.", "Error");
                    barcodeInput.focus();
                    barcodeInput.css("border","red");
                    btn.prop("disabled", true);
                    return false;
                }
                else
                {
                    submitProductUpdate();
                    barcodeInput.css("border","");
                    btn.prop("disabled", false);
                }
            });
        }
        else
        {
            submitProductUpdate();
            barcodeInput.css("border","");
            btn.prop("disabled", false);
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
                        fetchProducts($table, query);
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
        
    });
    $("#product-Filter").on("submit", function(e){
        e.preventDefault();
        let formData = $(this).serialize(); 
        fetchProducts($table, query, formData);
    });
    $("#clearFilter").on("click", function () {
        $("#product-Filter")[0].reset();
        fetchProducts($table, query);
    });
    $("#category").change(function(){
        subcat();
    });
    $("#cmb_category").change(function(){
        subcat2();
    });
    $("#editcmb_category").change(function(){
        subcat3();
    });
    $("#multieditcmb_category").change(function(){
        subcat4();
    });
    $("#refresh").on("click", function () {
        let $btn = $(this);
        let $icon = $btn.find("i");
        $btn.prop("disabled", true);
        $icon.css({
            "animation": "spin 0.8s linear infinite",
            "display": "inline-block"
        });
        fetchProducts($table, query).always(function () {
            // Stop spinning when AJAX completes
            $btn.prop("disabled", false);
            $icon.css("animation", "none");
        });  
    });
});