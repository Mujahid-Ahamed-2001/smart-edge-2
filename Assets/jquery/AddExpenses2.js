let $table = $('#tbl_expenses');
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
function fetchExpenseData()
{
    return $.ajax({
        url: '../AJAX/Expense/fetchExpenseData.php',
        method: 'post',
        data: { ShopID: ShopID },
        dataType: 'json',
        beforeSend: function() {
            TableLoading($table, "Expenses");
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
                var EPID = row.EPID; 
                var EffectiveDate = row.EffectiveDate;
                var ExpenseReason = row.ExpenseReason;
                var expense_cat = row.expense_cat;
                var is_default = row.is_default;
                var status = row.status;
                var created_date = row.created_date;
                var Created_by = row.Created_by;
                var ModifiedBy = row.ModifiedBy;
                var modified_date = row.modified_date;
                var badge =``;
                var status_badge =``;
                if(is_default ==1)
                {
                    badge=`<span class="badge badge-success default_badge" data-epid="${EPID}" data-is_default="${is_default}">
                                <i class="ti ti-check"></i>
                                Default
                            </span>`;
                }
                else
                {
                    badge=`<span class="badge badge-warning default_badge" data-epid="${EPID}" data-is_default="${is_default}">
                                <i class="ti ti-alert-circle"></i>
                                Not Default
                            </span>`;
                }
                if(status ==1)
                {
                    status_badge=`<span class="badge badge-success status_badge" data-epid="${EPID}">
                                <i class="ti ti-check"></i>
                                Active
                            </span>`;
                }
                else
                {
                    status_badge=`<span class="badge badge-warning status_badge" data-epid="${EPID}">
                                <i class="ti ti-alert-circle"></i>
                                Inactive
                            </span>`;
                }
                var btn =``;
                if(edit_access==1)
                {
                    btn=`<a class="btn-action btn-edit open-modal" data-epid="${EPID}" href="../View/modals/expenses.php?condition=edit&ref=AddExpense&EPID=${EPID}" data-title="Edit Expense Category">
                                <i class="ti ti-edit"></i>
                            </a>`;
                }
                if(delete_access==1)
                {
                    btn +=`<button class="btn-action btn-delete delete-epid" data-epid="${EPID}">
                                <i class="ti ti-trash"></i>
                            </button>`;
                }
                $table.find('tbody').append(`
                    <tr data-epid="${EPID}">
                        <td>${sl}</td>
                        <td>
                            <div class="expense-name">
                                <div class="expense-color"></div>
                                ${ExpenseReason}
                            </div>
                        </td>
                        <td>
                            ${expense_cat}
                        </td>
                        <td>
                            ${EffectiveDate}
                        </td>
                        <td>
                            ${status_badge}
                        </td>
                        <td>
                            ${badge}
                        </td>
                        <td>
                            ${Created_by}
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
$(function () {
    /*==================================================
    Sample expense overview data
    Later, replace this object with your AJAX response.
    ==================================================*/
    const expenseOverviewData = {
        labels: [
            "Rent",
            "Salaries",
            "Utilities",
            "Office Supplies",
            "Marketing",
            "Transportation",
            "Others"
        ],
        values: [
            150000,
            120000,
            65000,
            45500,
            38750,
            28250,
            37250
        ],
        colors: [
            "#5F3DE8",
            "#446ED1",
            "#72B3A7",
            "#F4A11A",
            "#648BEA",
            "#ED6EB2",
            "#C8C7DF"
        ]
    };
    const overviewTotal = expenseOverviewData.values.reduce(
        function (total, value) {
            return total + value;
        },
        0
    );
    $("#expenseOverviewTotal").text(
        formatExpenseCurrency(overviewTotal, 0)
    );
    createExpenseLegend(expenseOverviewData, overviewTotal);
    /*==================================================
    Expenses Donut Chart
    ==================================================*/
    const $overviewCanvas = $("#expenseOverviewChart");
    if ($overviewCanvas.length) {
        const overviewCanvas = $overviewCanvas[0];
        new Chart(overviewCanvas, {
            type: "doughnut",
            data: {
                labels: expenseOverviewData.labels,
                datasets: [{
                    data: expenseOverviewData.values,
                    backgroundColor: expenseOverviewData.colors,
                    borderColor: "#ffffff",
                    borderWidth: 2,
                    hoverBorderWidth: 2,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "62%",
                animation: {
                    duration: 900,
                    easing: "easeOutQuart"
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        displayColors: true,
                        backgroundColor: "#202638",
                        titleColor: "#ffffff",
                        bodyColor: "#ffffff",
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                const value = Number(context.raw || 0);
                                const percentage = overviewTotal > 0
                                    ? ((value / overviewTotal) * 100).toFixed(1)
                                    : 0;
                                return " " +
                                    context.label +
                                    ": " +
                                    formatExpenseCurrency(value, 2) +
                                    " (" + percentage + "%)";
                            }
                        }
                    }
                }
            }
        });
    }
    /*==================================================
    Sample expenses trend data
    ==================================================*/
    const expenseTrendData = {
        labels: [
            "May 01",
            "May 03",
            "May 05",
            "May 07",
            "May 09",
            "May 11",
            "May 13",
            "May 15",
            "May 17",
            "May 19",
            "May 21",
            "May 23",
            "May 25",
            "May 27",
            "May 29",
            "May 31"
        ],
        values: [
            25000,
            45500,
            28000,
            30000,
            24500,
            40500,
            35000,
            54000,
            52000,
            72450,
            62500,
            58000,
            61000,
            56000,
            64500,
            79000
        ]
    };
    /*==================================================
    Expenses Trend Chart
    ==================================================*/
    const $trendCanvas = $("#expenseTrendChart");
    if ($trendCanvas.length) {
        const trendCanvas = $trendCanvas[0];
        const trendContext = trendCanvas.getContext("2d");
        const trendGradient = trendContext.createLinearGradient(0, 0, 0, 280);
        trendGradient.addColorStop(0, "rgba(103, 72, 246, 0.24)");
        trendGradient.addColorStop(0.65, "rgba(103, 72, 246, 0.06)");
        trendGradient.addColorStop(1, "rgba(103, 72, 246, 0)");
        new Chart(trendCanvas, {
            type: "line",
            data: {
                labels: expenseTrendData.labels,
                datasets: [{
                    label: "Expenses",
                    data: expenseTrendData.values,
                    borderColor: "#6748F6",
                    backgroundColor: trendGradient,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "#ffffff",
                    pointHoverBorderColor: "#6748F6",
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: "index",
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "#ffffff",
                        titleColor: "#697386",
                        bodyColor: "#252c3d",
                        borderColor: "#e3e5ed",
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function (context) {
                                return formatExpenseCurrency(
                                    Number(context.raw || 0),
                                    2
                                );
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        border: {
                            display: false
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: "#7e889a",
                            font: {
                                size: 11
                            },
                            maxRotation: 0,
                            callback: function (value, index) {
                                return index % 3 === 0
                                    ? this.getLabelForValue(value)
                                    : "";
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMax: 100000,
                        border: {
                            display: false
                        },
                        grid: {
                            color: "#edf0f5",
                            drawTicks: false
                        },
                        ticks: {
                            stepSize: 25000,
                            padding: 10,
                            color: "#7e889a",
                            font: {
                                size: 11
                            },
                            callback: function (value) {
                                if (value === 0) {
                                    return "0";
                                }
                                return (value / 1000) + "K";
                            }
                        }
                    }
                }
            }
        });
    }
    /*==================================================
    Generate custom overview legend
    ==================================================*/
    function createExpenseLegend(data, total) {
        const $legendContainer = $("#expenseChartLegend");
        if (!$legendContainer.length) {
            return;
        }
        $legendContainer.empty();
        $.each(data.labels, function (index, label) {
            const value = Number(data.values[index] || 0);
            const percentage = total > 0
                ? ((value / total) * 100).toFixed(1)
                : "0.0";
            const $legendItem = $("<div>", {
                class: "expense-legend-item"
            }).html(`
                <span
                    class="expense-legend-color"
                    style="background:${data.colors[index]}"
                ></span>
                <span class="expense-legend-name">
                    ${label}
                </span>
                <span class="expense-legend-amount">
                    ${formatExpenseCurrency(value, 2)}
                </span>
                <span class="expense-legend-percentage">
                    ${percentage}%
                </span>
            `);
            $legendContainer.append($legendItem);
        });
    }
    /*==================================================
    Currency formatter
    ==================================================*/
    function formatExpenseCurrency(amount, decimalPlaces = 2) {
        return "Rs. " + Number(amount).toLocaleString("en-LK", {
            minimumFractionDigits: decimalPlaces,
            maximumFractionDigits: decimalPlaces
        });
    }
});
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
$(document).ready(function(){
    fetchExpenseData();
    $("#headerCollapse2").trigger("click");
    $(document).on('click', '.open-modal', function (e) {
      e.preventDefault();
      let url = $(this).attr('href');
      let title = $(this).data('title') || 'Company';
      openModal(url,title);
    });
    $("#refresh").click(function(){
        let $btn = $(this);
        let $icon = $btn.find("i");
        $btn.prop("disabled", true);
        $icon.css({
            "animation": "spin 0.8s linear infinite",
            "display": "inline-block"
        });
        setTimeout(() => {
            fetchExpenseData().always(function () {
                // Stop spinning when AJAX completes
                $btn.prop("disabled", false);
                $icon.css("animation", "none");
            });     
        }, 1500);
                   
    });
})