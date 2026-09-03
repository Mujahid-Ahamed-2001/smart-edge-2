$(document).ready(function(){
    $("#headerCollapse2").trigger("click");
    let ShopID = $("#shop_id").val();
    let $table = $('#tbl_inventory');
    function TableLoading($table,TableName = "inventory")
    {
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
    fetchInventoryData();
    $("#refresh").click(function(){
        let $btn = $(this);
        let $icon = $btn.find("i");
        $btn.prop("disabled", true);
        $icon.css({
            "animation": "spin 0.8s linear infinite",
            "display": "inline-block"
        });
        fetchInventoryData().always(function () {
            // Stop spinning when AJAX completes
            $btn.prop("disabled", false);
            $icon.css("animation", "none");
        });            
    });
    function fetchInventoryData()
    {
        return $.ajax({
            url: '../AJAX/Store/getInventoryData.php',
            method: 'post',
            data: { ShopID: ShopID },
            dataType: 'json',
            beforeSend: function() {
                TableLoading($table, "Inventory");
            },
            success: function(response) {
                if ($.fn.DataTable.isDataTable($table)) {
                    $table.DataTable().clear().destroy();
                }
                // Clear previous data (including loader)
                $table.find('tbody').html("");

                if (!response || response.length === 0) {
                    $table.find('tbody').html(`
                        <tr>
                            <td colspan="10" class="text-center py-5">No inventory data available.</td>
                        </tr>
                    `);
                    return;
                }

                $.each(response, function (index, row) {
                    $table.find('tbody').append(`
                        <tr data-id="${row.product_id}">
                            <td>${row.sl ?? ''}</td>
                            <td><img src="${row.image ?? ''}" class="img-fluid" width="50" height="50"></td>
                            <td>${row.barcode ?? ''}</td>
                            <td>${row.item_name ?? ''}</td>
                            <td>${row.current_qty ?? 0}</td>
                            <td>${row.bill_qty ?? 0}</td>
                            <td>${row.return_qty ?? 0}</td>
                            <td>${row.transfer_in_qty ?? 0}</td>
                            <td>${row.transfer_out_qty ?? 0}</td>
                            <td><button class="btn border border-primary text-primary btn_view_price"><i class="ti ti-eye"></i> Price</button></td>
                        </tr>
                    `);

                });

                $table.DataTable({           
                                    paging: true,
                                    lengthChange: true,
                                    searching: true,
                                    pageLength: 25
                                });
                exportTableButtons('tbl_inventory');

            },
            error: function(xhr, status, error) {
                console.log("AJAX Error:", status, error);
                console.log("Response:", xhr.responseText);
                toastr.error("An error occurred while processing.", "Error");
            }
        });

    }
    $("#tbl_inventory").on('click', '.btn_view_price', function(){
        let row = $(this).closest('tr');
        let id = row.data('id');

        $.get("../AJAX/Store/getBatchPrice.php", {
            product_id: id
        }, function(data){
            // alert(data);
            const obj = JSON.parse(data);

            var barcode = obj[0]['Barcode'];
            var product_name = obj[0]['ItemName'];
            var content = "";

            content += "<p style='margin:2px; font-weight:bold;'>Barcode - "+barcode+"<br>";
            content += "Item - " + product_name ;
            content +="</p>";

            content +="<table>";
            content +="<tr>";
            content +="<th>Batch No</th>";
            // content +="<th>Qty</th>";
            content +="<th>Price</th>";
            content +="</tr>";

            $.each(obj, function (key, value) { 
                var batch_id = value['BatchID'];
                var selling_price = value['SellingPrice'];
                var batch_qty = parseFloat(value['CurrentQty']) * 1;

                console.log("batch = " + batch_id +" - "+ selling_price);

                content +="<tr>";
                content +="<td>"+ batch_id +"</td>";
                // content +="<td>"+ batch_qty +"</td>";
                content +="<td>"+ selling_price +"</td>";
                content +="</tr>";

            });

            content +="</table>";

            $("#modal_batch_price").modal('toggle');

            $("#div_batch_price").html(content);
        });
    });//show batch and qty

    $("#close_batch_price").click(function(){
        $("#modal_batch_price").modal('toggle');
    });

});//jQuery