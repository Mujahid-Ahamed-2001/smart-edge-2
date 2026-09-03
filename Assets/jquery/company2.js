function TableLoading($table, TableName = "Inventory")
{
    let colCount = $table.find('thead th').length;

    $table.find('tbody').html(`
        <tr class="table-loading">
            <td colspan="${colCount}" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="mt-2 fw-semibold">Loading ${TableName}...</div>
            </td>
        </tr>
    `);
}

function fetcompanyList($table)
{
    return $.ajax({
        url: '../AJAX/Company/getCompanyList.php',
        method: 'POST',
        dataType: 'json',

        beforeSend: function () {

            if ($.fn.DataTable.isDataTable($table)) {
                $table.DataTable().clear().destroy();
            }

            TableLoading($table, "Company List");
        },

        success: function (response) {

            let colCount = $table.find('thead th').length;

            if (!response || response.length === 0) {

                $table.find('tbody').html(`
                    <tr>
                        <td colspan="${colCount}" class="text-center py-5">
                            No company data available.
                        </td>
                    </tr>
                `);

                return;
            }

            let html = '';

            $.each(response, function (index, row) {

                let statusBadge = row.status == 1
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';
                let dropdownItems = '';
                dropdownItems += `
                            <li>
                                <a href="../View/modals/company-modal.php?condition=edit&CMID=${row.CMD}" class="dropdown-item open-modal"><i class="ti ti-edit"></i> Edit</a>
                            </li>
                        `;
                dropdownItems += `
                            <li>
                                <button type="button" class="dropdown-item delete-cmid" data-cmid="${row.CMD}"><i class="ti ti-trash"></i> Delete</button>
                            </li>
                        `;
                let actionBtn = `
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary">Action</button>
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown"></button>
                        <ul class="dropdown-menu">
                            ${dropdownItems}
                        </ul>
                    </div>
                `;
                html += `
                    <tr id="com-${row.CMD}">
                        <td>${row.no}</td>

                        <td>
                            <img src="${row.logo}"
                                 alt="${row.name}"
                                 class="rounded-circle"
                                 width="70"
                                 height="70"
                                 style="object-fit:cover;">
                        </td>

                        <td>${row.name}</td>

                        <td>${row.type ?? ''}</td>

                        <td>${row.version ?? ''}</td>

                        <td>${row.expiry ?? ''}</td>

                        <td>${statusBadge}</td>

                        <td>${row.SHOPCOUNT}</td>

                        <td>${actionBtn}</td>
                    </tr>
                `;
            });

            $table.find('tbody').html(html);

            $table.DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                pageLength: 25,
                responsive: true
            });
        },

        error: function (xhr, status, error) {

            console.log("AJAX Error:", status, error);
            console.log("Response:", xhr.responseText);

            toastr.error(
                "An error occurred while loading company data.",
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
    $("#headerCollapse2").on("click", function () {
        console.log("Clicked");
        
    });
    $(document).on("click", ".delete-cmid",function(e){
        e.preventDefault();
        var CMID = $(this).data("cmid");
        console.log("CMID: "+CMID);
        if(!confirm("Are you sure you want to delete this company?"))
        {
            return;
        }
        $.ajax({
            url:"../AJAX/Company/deleteCompany.php",
            method: 'POST',
            dataType: 'json',
            data: {
                CMID:CMID
            },
            beforeSend: function(){
                toastr.info(
                    "Please Wait While Processing...",
                    "Processing"
                );
            },
            success:function(response){
                if(response[0].status==1)
                {
                    toastr.success(response[0].message, "Success");
                    if($.fn.DataTable.isDataTable("#tbl_company"))
                    {
                        $("#tbl_company")
                            .DataTable()
                            .row($("#com-" + CMID))
                            .remove()
                            .draw(false);
                    }
                    else
                    {
                        $("#com-" + CMID).fadeOut(300, function(){
                            $(this).remove();
                        });
                    }
                }
                else if(response[0].status==2)
                {
                    toastr.warning(response[0].message, "Warning");
                }
                else
                {
                    toastr.error(response[0].message, "Error");
                }
            },
            error: function (xhr, status, error) {

                console.log("AJAX Error:", status, error);
                console.log("Response:", xhr.responseText);
                toastr.error(
                    "An error occurred while deleting company data.",
                    "Error"
                );
            }
        })
        
    });
    $(document).on('click', '.open-modal', function (e) {

        e.preventDefault();

        let url = $(this).attr('href');
        let title = $(this).data('title') || 'Company';

        openModal(url,title);
    });

    let $table = $("#tbl_company");

    fetcompanyList($table);

    $("#refresh").on("click", function () {

        let $btn = $(this);
        let $icon = $btn.find("i");

        $btn.prop("disabled", true);

        $icon.css({
            animation: "spin 0.8s linear infinite",
            display: "inline-block"
        });

        fetcompanyList($table)
            .always(function () {

                $btn.prop("disabled", false);

                $icon.css({
                    animation: "none"
                });

            });
    });

});