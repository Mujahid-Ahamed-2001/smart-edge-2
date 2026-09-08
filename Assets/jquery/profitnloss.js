let $table = $('#tbl_pnls');
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
function TableLoading2($table,TableName = "", loading="Loading ", dot="...")
{
    let loaderHtml = `<div class="d-flex flex-column justify-content-center align-items-center text-center" style="min-height: 50px;">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="mt-2 fw-semibold">${loading+" "+TableName+dot}</div>
    </div>`;
    $table.html(loaderHtml);
}
function getCommon(startDate = "", endDate = "") {

    if (!startDate) {
        const now = new Date();

        startDate =
            now.getFullYear() + "-" +
            String(now.getMonth() + 1).padStart(2, "0") +
            "-01";
    }

    if (!endDate) {
        const now = new Date();

        const lastDay = new Date(
            now.getFullYear(),
            now.getMonth() + 1,
            0
        );

        endDate =
            lastDay.getFullYear() + "-" +
            String(lastDay.getMonth() + 1).padStart(2, "0") + "-" +
            String(lastDay.getDate()).padStart(2, "0");
    }

    return $.ajax({
        url: '../AJAX/PNL/getCommon.php',
        method: 'POST',
        dataType: 'json',

        data: {
            start: startDate,
            end: endDate
        },

        beforeSend: function() {
            TableLoading2($('.getCommon'),"","","");
        },

        success: function(response) {

            setTimeout(() => {
                $("#totRevenue").html("Rs. "+response.totalRevenue);
                    $("#totCost").html("Rs. "+response.totalCost);
                    $("#grossProfit").html("Rs. "+response.grossProfit);
                    $("#netProfit").html("Rs. "+response.netProfit);
            }, 1200);

        },

        error: function(xhr, status, error) {
            console.log("AJAX Error:", status, error);
            console.log("Response:", xhr.responseText);

            toastr.error(
                "An error occurred while processing common data.",
                "Error"
            );
        }
    });
}

let pnlTrendChart = null;

function getPnLTrend(startDate = "", endDate = "") {

    /*
     * Default start date:
     * First day of current month
     */
    if (!startDate) {

        const now = new Date();

        startDate =
            now.getFullYear() + "-" +
            String(now.getMonth() + 1).padStart(2, "0") +
            "-01";
    }


    /*
     * Default end date:
     * Last day of current month
     */
    if (!endDate) {

        const now = new Date();

        const lastDay = new Date(
            now.getFullYear(),
            now.getMonth() + 1,
            0
        );

        endDate =
            lastDay.getFullYear() + "-" +
            String(lastDay.getMonth() + 1).padStart(2, "0") + "-" +
            String(lastDay.getDate()).padStart(2, "0");
    }


    return $.ajax({

        url: '../AJAX/PNL/getPnLTrend.php',

        method: 'POST',

        dataType: 'json',

        data: {
            start: startDate,
            end: endDate
        },
        beforeSend: function() {
            TableLoading2($('#pnlTrendChart'),"","","");
        },
        success: function (response) {

            if (!response.status) {

                toastr.error(
                    response.message ?? "Unable to load P&L trend."
                );

                return;
            }


            const trend = response.trend;


            /*
             * Dates
             */
            const labels = trend.map(function (item) {

                return item.date;

            });


            /*
             * Net Profit / Loss
             */
            const netProfitData = trend.map(function (item) {

                return parseFloat(item.netProfit);

            });


            const canvas = document.getElementById(
                "pnlTrendChart"
            );


            if (!canvas) {
                return;
            }


            /*
             * Destroy existing chart before
             * creating a new chart.
             *
             * Important when changing date ranges.
             */
            if (pnlTrendChart) {

                pnlTrendChart.destroy();

            }


            /*
             * Create Trend Chart
             */
            pnlTrendChart = new Chart(canvas, {

                type: 'line',

                data: {

                    labels: labels,

                    datasets: [

                        {
                            label: 'Net Profit / Loss',

                            data: netProfitData,

                            borderWidth: 2,

                            tension: 0.35,

                            fill: false,

                            pointRadius: 4,

                            pointHoverRadius: 6
                        }

                    ]
                },


                options: {

                    responsive: true,

                    maintainAspectRatio: false,


                    interaction: {

                        mode: 'index',

                        intersect: false

                    },


                    plugins: {

                        legend: {
                            display: true
                        },


                        tooltip: {

                            callbacks: {

                                label: function (context) {

                                    const value =
                                        context.parsed.y ?? 0;

                                    if (value < 0) {

                                        return "Loss: Rs. " +
                                            Math.abs(value)
                                                .toLocaleString(
                                                    undefined,
                                                    {
                                                        minimumFractionDigits: 2,
                                                        maximumFractionDigits: 2
                                                    }
                                                );
                                    }


                                    return "Profit: Rs. " +
                                        value.toLocaleString(
                                            undefined,
                                            {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            }
                                        );
                                }
                            }
                        }
                    },


                    scales: {

                        x: {

                            grid: {
                                display: false
                            }

                        },


                        y: {

                            beginAtZero: true,

                            ticks: {

                                callback: function (value) {

                                    return "Rs. " +
                                        Number(value)
                                            .toLocaleString();

                                }
                            }
                        }
                    }
                }
            });
        },


        error: function (xhr, status, error) {

            console.log(
                "P&L Trend AJAX Error:",
                status,
                error
            );

            console.log(
                "Response:",
                xhr.responseText
            );

            toastr.error(
                "An error occurred while loading P&L trend.",
                "Error"
            );
        }
    });
}
function getPnLTable(startDate = "", endDate = "") {

    // First day of current month
    if (!startDate) {
        const now = new Date();

        startDate =
            now.getFullYear() + "-" +
            String(now.getMonth() + 1).padStart(2, "0") +
            "-01";
    }

    // Last day of current month
    if (!endDate) {
        const now = new Date();

        const lastDay = new Date(
            now.getFullYear(),
            now.getMonth() + 1,
            0
        );

        endDate =
            lastDay.getFullYear() + "-" +
            String(lastDay.getMonth() + 1).padStart(2, "0") + "-" +
            String(lastDay.getDate()).padStart(2, "0");
    }


    return $.ajax({
        url: '../AJAX/PNL/getPnLTable.php',
        method: 'POST',
        dataType: 'json',

        data: {
            start: startDate,
            end: endDate
        },

        beforeSend: function () {
            TableLoading($("#tbl_pnls"), "pnls");
            if ($.fn.DataTable.isDataTable('#tbl_pnls')) {
                $('#tbl_pnls').DataTable().clear().destroy();
            }
        },

        success: function (response) {

            if (!response.status) {
                toastr.error(
                    response.message || "Unable to load Profit/Loss data."
                );
                return;
            }

            let html = '';

            let rowNo = 1;

            /*
            |--------------------------------------------------------------------------
            | Sales Revenue
            |--------------------------------------------------------------------------
            */

            html += `
                <tr>
                    <td>${rowNo++}</td>

                    <td>
                        Sales Revenue
                    </td>

                    <td style="text-align:right;">
                        ${formatPnLAmount(response.totalRevenue)}
                    </td>

                    <td></td>
                    <td></td>
                </tr>
            `;


            /*
            |--------------------------------------------------------------------------
            | Cost Of Goods Sold
            |--------------------------------------------------------------------------
            */

            html += `
                <tr>
                    <td>${rowNo++}</td>

                    <td>
                        Cost Of Goods Sold
                    </td>

                    <td style="text-align:right;">
                        ${formatPnLAmount(
                            response.totalCost,
                            true
                        )}
                    </td>

                    <td></td>
                    <td></td>
                </tr>
            `;


            /*
            |--------------------------------------------------------------------------
            | Gross Profit
            |--------------------------------------------------------------------------
            */

            html += `
                <tr>
                    <td>${rowNo++}</td>

                    <td>
                        <b style="
                            font-size:25px;
                            color:#000;
                        ">
                            Gross Profit
                        </b>
                    </td>
                    <td>
                    </td>

                    <td
                        style="text-align:right;">

                        <b style="
                            font-size:25px;
                            color:#000;
                        ">
                            ${formatPnLProfitLoss(
                                response.grossProfit
                            )}
                        </b>

                    </td>
                    <td></td>
                </tr>
            `;


            /*
            |--------------------------------------------------------------------------
            | Expense Rows
            |--------------------------------------------------------------------------
            */

            if (
                response.expenses &&
                response.expenses.length > 0
            ) {
            console.log(response.expenses);

                response.expenses.forEach(function (expense) {

                    html += `
                        <tr>
                            <td>${rowNo++}</td>

                            <td>
                                <div style="font-weight:600; color:#000;">
                                    ${expense.category || "Uncategorized"}
                                </div>

                                <small style="display:block; color:#777; margin-top:3px;">
                                    ${expense.reason || ""}
                                </small>
                            </td>

                            <td style="text-align:right;">
                                ${formatPnLAmount(
                                    expense.amount
                                )}
                            </td>

                            <td></td>
                            <td></td>
                        </tr>
                    `;

                });

            }


            /*
            |--------------------------------------------------------------------------
            | Total Expenses
            |--------------------------------------------------------------------------
            */

            html += `
                <tr>
                    <td>${rowNo++}</td>

                    <td>
                        <b style="
                            font-size:25px;
                            color:#000;
                        ">
                            Total Expenses
                        </b>
                    </td>
                    <td>
                    </td>

                    <td
                        style="text-align:right;">

                        <b style="
                            font-size:25px;
                            color:#000;
                        ">
                            ${formatPnLAmount(
                                response.totalExpenses,
                                true
                            )}
                        </b>

                    </td>
                    <td></td>
                </tr>
            `;




            /*
            |--------------------------------------------------------------------------
            | Net Profit / Loss Footer
            |--------------------------------------------------------------------------
            */

            html += `
                <tr>
                    <td>${rowNo++}</td>
                    <td>

                        <b style="
                            font-size:25px;
                            color:#000;
                            text-align:center;
                        ">
                            Net Profit/Loss
                        </b>

                    </td>
                    <td></td>
                    <td
                        style="text-align:right;">

                        <b style="
                            font-size:25px;
                            color:#000;
                        ">
                            ${formatPnLProfitLoss(
                                response.netProfit
                            )}
                        </b>

                    </td>
                    <td></td>

                </tr>
            `;


            $("#tbl_pnls tbody").html(html);

            $('#tbl_pnls').DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                pageLength: 25,
                ordering: false
            });

            exportTableButtons('tbl_pnls');
        },

        error: function (xhr, status, error) {

            console.log(
                "P&L Table AJAX Error:",
                status,
                error
            );

            console.log(
                xhr.responseText
            );

            toastr.error(
                "An error occurred while loading Profit/Loss table.",
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
function formatPnLAmount(amount, brackets = false) {

    amount = parseFloat(amount) || 0;

    let formatted = Math.abs(amount).toLocaleString(
        undefined,
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );

    if (brackets && amount != 0) {
        return "(" + formatted + ")";
    }

    return formatted;
}


function formatPnLProfitLoss(amount) {

    amount = parseFloat(amount) || 0;

    let formatted = Math.abs(amount).toLocaleString(
        undefined,
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );

    /*
     * Negative means Loss
     */
    if (amount < 0) {
        return "(" + formatted + ")";
    }

    return formatted;
}


function escapeHtml(value) {

    return $("<div>")
        .text(value ?? "")
        .html();
}
$(document).ready(function(){
    getPnLTable();
    getCommon();
    getPnLTrend();
    $("#headerCollapse2").trigger("click");
    $(document).on('click', '.open-modal', function (e) {
      e.preventDefault();
      let url = $(this).attr('href');
      let title = $(this).data('title') || 'Company';
      openModal(url,title);
    });
    $("#pnl_start_date, #pnl_end_date").on("change", function () {

        var startdate = $("#pnl_start_date").val();
        var enddate   = $("#pnl_end_date").val();

        if (!startdate) {
            $("#pnl_start_date").focus();
            return;
        }

        if (!enddate) {
            $("#pnl_end_date").focus();
            return;
        }
        if (enddate < startdate) {
            toastr.warning(
                "End date cannot be earlier than start date.",
                "Invalid Date Range"
            );
            $("#pnl_end_date").focus();
            return;
        }

        getCommon(startdate, enddate);
        getPnLTrend(startdate, enddate);
        getPnLTable(startdate, enddate);
    });
    $("#refresh").click(function () {

        let $btn = $(this);
        let $icon = $btn.find("i");

        let startDate = $("#pnl_start_date").val();
        let endDate   = $("#pnl_end_date").val();

        if (!startDate) {
            $("#pnl_start_date").focus();
            toastr.warning("Please select a start date.");
            return;
        }

        if (!endDate) {
            $("#pnl_end_date").focus();
            toastr.warning("Please select an end date.");
            return;
        }

        if (endDate < startDate) {
            toastr.warning(
                "End date cannot be earlier than start date.",
                "Invalid Date Range"
            );
            return;
        }

        // Start refresh animation
        $btn.prop("disabled", true);

        $icon.css({
            "animation": "spin 0.8s linear infinite",
            "display": "inline-block"
        });


        $.when(
            getPnLTable(startDate, endDate),
            getCommon(startDate, endDate),
            getPnLTrend(startDate, endDate)
        )
        .done(function () {

            // All 3 AJAX requests completed successfully
            $btn.prop("disabled", false);
            $icon.css("animation", "none");

            toastr.success(
                "Profit/Loss data refreshed successfully."
            );

        })
        .fail(function () {

            // At least one AJAX request failed
            $btn.prop("disabled", false);
            $icon.css("animation", "none");

            toastr.error(
                "Unable to refresh all Profit/Loss data.",
                "Error"
            );

        });

    });
})