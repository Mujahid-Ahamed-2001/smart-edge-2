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
function formatSummaryCurrency(amount)
{
    return "Rs. " + Number(amount || 0).toLocaleString(
        "en-LK",
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );
}
function updateSummaryComparison(
    selector,
    percentage,
    text
)
{
    percentage = Number(percentage) || 0;

    let $element = $(selector);

    let icon = "ti-minus";
    let className = "summary-neutral";

    if (percentage > 0) {

        icon = "ti-trending-up";
        className = "summary-increase";

    }
    else if (percentage < 0) {

        icon = "ti-trending-down";
        className = "summary-decrease";

    }


    $element
        .removeClass(
            "summary-increase summary-decrease summary-neutral"
        )
        .addClass(className);


    $element.html(`
        <i class="ti ${icon}"></i>

        <span>
            <strong>${Math.abs(percentage).toFixed(1)}%</strong>
            ${text}
        </span>
    `);
}
function fetchExpenseSummary()
{
    return $.ajax({

        url: "../AJAX/Expense/fetchExpenseSummary.php",
        method: "POST",
        dataType: "json",

        success: function(response) {

            if (Number(response.status) !== 1) {

                toastr.error(
                    response.message || "Unable to load expense summary.",
                    "Error"
                );

                return;
            }

            let data = response.data;

            $("#summaryTotalExpenses").text(
                formatSummaryCurrency(data.total.amount)
            );

            $("#summaryMonthExpenses").text(
                formatSummaryCurrency(data.month.amount)
            );

            $("#summaryWeekExpenses").text(
                formatSummaryCurrency(data.week.amount)
            );

            $("#summaryTodayExpenses").text(
                formatSummaryCurrency(data.today.amount)
            );

            updateSummaryComparison(
                "#summaryMonthComparison",
                data.month.percentage,
                "from last month"
            );

            updateSummaryComparison(
                "#summaryWeekComparison",
                data.week.percentage,
                "from last week"
            );

            updateSummaryComparison(
                "#summaryTodayComparison",
                data.today.percentage,
                "from yesterday"
            );
        },

        error: function(xhr, status, error) {

            console.log(xhr.responseText);

            toastr.error(
                "Unable to load expense summary.",
                "Error"
            );
        }

    });
}
function fetchExpenseData(startDate = "", endDate = "")
{
    return $.ajax({
        url: '../AJAX/Expense/fetchExpenseData.php',
        method: 'post',
        data: {
            ShopID: ShopID,
            start_date: startDate,
            end_date: endDate
        },
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
                var ExpenseAmount = row.ExpenseAmount;
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
                    btn +=`<button type="button" class="btn-action btn-delete delete-epid" data-epid="${EPID}">
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
                            Rs. ${ExpenseAmount}
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
/*
|--------------------------------------------------------------------------
| Expense Charts
|--------------------------------------------------------------------------
*/

let expenseOverviewChart = null;
let expenseTrendChart = null;


/*
|--------------------------------------------------------------------------
| Currency Formatter
|--------------------------------------------------------------------------
*/

function formatExpenseCurrency(amount, decimalPlaces = 2)
{
    return "Rs. " + Number(amount || 0).toLocaleString(
        "en-LK",
        {
            minimumFractionDigits: decimalPlaces,
            maximumFractionDigits: decimalPlaces
        }
    );
}


/*
|--------------------------------------------------------------------------
| Chart Colors
|--------------------------------------------------------------------------
*/

const expenseChartColors = [
    "#5F3DE8",
    "#446ED1",
    "#72B3A7",
    "#F4A11A",
    "#648BEA",
    "#ED6EB2",
    "#C8C7DF",
    "#8A6FF0",
    "#48A9A6",
    "#E76F51",
    "#2A9D8F",
    "#E9C46A"
];


/*
|--------------------------------------------------------------------------
| Fetch Expense Charts
|--------------------------------------------------------------------------
*/

function fetchExpenseCharts(startDate = "", endDate = "")
{
    return $.ajax({

        url: "../AJAX/Expense/fetchExpenseChartData.php",

        method: "POST",

        dataType: "json",

        data: {
            start_date: startDate,
            end_date: endDate
        },

        success: function(response) {

            if (Number(response.status) !== 1) {

                toastr.error(
                    response.message || "Unable to load expense charts.",
                    "Error"
                );

                return;
            }

            let data = response.data;

            renderExpenseOverviewChart(
                data.overview || []
            );

            renderExpenseTrendChart(
                data.trend || []
            );

        },

        error: function(xhr, status, error) {

            console.log(
                "Expense charts AJAX error:",
                status,
                error
            );

            console.log(
                xhr.responseText
            );

            toastr.error(
                "Unable to load expense charts.",
                "Error"
            );
        }

    });
}


/*
|--------------------------------------------------------------------------
| Expenses Overview Donut Chart
|--------------------------------------------------------------------------
*/

function renderExpenseOverviewChart(rows)
{
    let labels = [];
    let values = [];

    $.each(rows, function(index, row) {

        labels.push(
            row.label || "Uncategorized"
        );

        values.push(
            Number(row.value || 0)
        );

    });


    let total = values.reduce(
        function(sum, value) {
            return sum + value;
        },
        0
    );


    /*
    |--------------------------------------------------------------------------
    | Update center total
    |--------------------------------------------------------------------------
    */

    $("#expenseOverviewTotal").text(
        formatExpenseCurrency(total, 0)
    );


    /*
    |--------------------------------------------------------------------------
    | Update legend
    |--------------------------------------------------------------------------
    */

    createExpenseLegend(
        labels,
        values,
        total
    );


    /*
    |--------------------------------------------------------------------------
    | Canvas
    |--------------------------------------------------------------------------
    */

    const canvas = document.getElementById(
        "expenseOverviewChart"
    );

    if (!canvas) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Destroy previous chart
    |--------------------------------------------------------------------------
    */

    if (expenseOverviewChart) {

        expenseOverviewChart.destroy();
        expenseOverviewChart = null;

    }


    /*
    |--------------------------------------------------------------------------
    | Empty Data
    |--------------------------------------------------------------------------
    */

    if (values.length === 0) {

        labels = ["No Expenses"];
        values = [1];

    }


    /*
    |--------------------------------------------------------------------------
    | Create Chart
    |--------------------------------------------------------------------------
    */

    expenseOverviewChart = new Chart(
        canvas,
        {
            type: "doughnut",

            data: {

                labels: labels,

                datasets: [{
                    data: values,

                    backgroundColor:
                        rows.length > 0
                            ? labels.map(
                                function(label, index) {
                                    return expenseChartColors[
                                        index % expenseChartColors.length
                                    ];
                                }
                            )
                            : ["#e9ecef"],

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
                    duration: 700,
                    easing: "easeOutQuart"
                },

                plugins: {

                    legend: {
                        display: false
                    },

                    tooltip: {

                        enabled: rows.length > 0,

                        displayColors: true,

                        backgroundColor: "#202638",

                        titleColor: "#ffffff",

                        bodyColor: "#ffffff",

                        padding: 12,

                        cornerRadius: 8,

                        callbacks: {

                            label: function(context) {

                                let value =
                                    Number(context.raw || 0);

                                let percentage =
                                    total > 0
                                        ? (
                                            (value / total) * 100
                                        ).toFixed(1)
                                        : 0;

                                return (
                                    " " +
                                    context.label +
                                    ": " +
                                    formatExpenseCurrency(
                                        value,
                                        2
                                    ) +
                                    " (" +
                                    percentage +
                                    "%)"
                                );
                            }
                        }
                    }
                }
            }
        }
    );
}


/*
|--------------------------------------------------------------------------
| Expense Legend
|--------------------------------------------------------------------------
*/

function createExpenseLegend(
    labels,
    values,
    total
)
{
    const $legendContainer =
        $("#expenseChartLegend");

    if (!$legendContainer.length) {
        return;
    }

    $legendContainer.empty();


    if (!labels.length) {

        $legendContainer.html(`
            <div class="text-muted">
                No expense data available.
            </div>
        `);

        return;
    }


    $.each(
        labels,
        function(index, label) {

            let value =
                Number(values[index] || 0);

            let percentage =
                total > 0
                    ? (
                        (value / total) * 100
                    ).toFixed(1)
                    : "0.0";


            let color =
                expenseChartColors[
                    index % expenseChartColors.length
                ];


            let $legendItem =
                $("<div>", {
                    class: "expense-legend-item"
                }).html(`

                    <span
                        class="expense-legend-color"
                        style="background:${color}"
                    ></span>

                    <span
                        class="expense-legend-name"
                    >
                        ${label}
                    </span>

                    <span
                        class="expense-legend-amount"
                    >
                        ${formatExpenseCurrency(
                            value,
                            2
                        )}
                    </span>

                    <span
                        class="expense-legend-percentage"
                    >
                        ${percentage}%
                    </span>

                `);


            $legendContainer.append(
                $legendItem
            );

        }
    );
}


/*
|--------------------------------------------------------------------------
| Expense Trend Chart
|--------------------------------------------------------------------------
*/

function renderExpenseTrendChart(rows)
{
    let labels = [];
    let values = [];


    $.each(
        rows,
        function(index, row) {

            labels.push(
                formatExpenseChartDate(
                    row.date
                )
            );

            values.push(
                Number(
                    row.value || 0
                )
            );

        }
    );


    const canvas =
        document.getElementById(
            "expenseTrendChart"
        );

    if (!canvas) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Destroy Previous Chart
    |--------------------------------------------------------------------------
    */

    if (expenseTrendChart) {

        expenseTrendChart.destroy();
        expenseTrendChart = null;

    }


    /*
    |--------------------------------------------------------------------------
    | Gradient
    |--------------------------------------------------------------------------
    */

    const context =
        canvas.getContext("2d");

    const gradient =
        context.createLinearGradient(
            0,
            0,
            0,
            280
        );

    gradient.addColorStop(
        0,
        "rgba(103, 72, 246, 0.24)"
    );

    gradient.addColorStop(
        0.65,
        "rgba(103, 72, 246, 0.06)"
    );

    gradient.addColorStop(
        1,
        "rgba(103, 72, 246, 0)"
    );


    /*
    |--------------------------------------------------------------------------
    | Create Trend Chart
    |--------------------------------------------------------------------------
    */

    expenseTrendChart =
        new Chart(
            canvas,
            {

                type: "line",

                data: {

                    labels: labels,

                    datasets: [{
                        label: "Expenses",

                        data: values,

                        borderColor:
                            "#6748F6",

                        backgroundColor:
                            gradient,

                        borderWidth:
                            2.5,

                        fill:
                            true,

                        tension:
                            0.4,

                        pointRadius:
                            values.length <= 15
                                ? 3
                                : 0,

                        pointHoverRadius:
                            5,

                        pointHoverBackgroundColor:
                            "#ffffff",

                        pointHoverBorderColor:
                            "#6748F6",

                        pointHoverBorderWidth:
                            2
                    }]
                },

                options: {

                    responsive:
                        true,

                    maintainAspectRatio:
                        false,

                    interaction: {
                        mode: "index",
                        intersect: false
                    },

                    plugins: {

                        legend: {
                            display: false
                        },

                        tooltip: {

                            backgroundColor:
                                "#ffffff",

                            titleColor:
                                "#697386",

                            bodyColor:
                                "#252c3d",

                            borderColor:
                                "#e3e5ed",

                            borderWidth:
                                1,

                            padding:
                                12,

                            cornerRadius:
                                8,

                            displayColors:
                                false,

                            callbacks: {

                                label:
                                    function(context) {

                                        return formatExpenseCurrency(
                                            Number(
                                                context.raw || 0
                                            ),
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

                                color:
                                    "#7e889a",

                                font: {
                                    size: 11
                                },

                                maxRotation:
                                    0,

                                autoSkip:
                                    true,

                                maxTicksLimit:
                                    10
                            }
                        },

                        y: {

                            beginAtZero:
                                true,

                            border: {
                                display:
                                    false
                            },

                            grid: {
                                color:
                                    "#edf0f5",

                                drawTicks:
                                    false
                            },

                            ticks: {

                                padding:
                                    10,

                                color:
                                    "#7e889a",

                                font: {
                                    size: 11
                                },

                                callback:
                                    function(value) {

                                        return formatCompactNumber(
                                            value
                                        );

                                    }
                            }
                        }
                    }
                }
            }
        );
}


/*
|--------------------------------------------------------------------------
| Chart Date Formatter
|--------------------------------------------------------------------------
*/

function formatExpenseChartDate(dateString)
{
    if (!dateString) {
        return "";
    }

    let parts =
        dateString.split("-");

    if (parts.length !== 3) {
        return dateString;
    }

    let date =
        new Date(
            Number(parts[0]),
            Number(parts[1]) - 1,
            Number(parts[2])
        );


    return date.toLocaleDateString(
        "en-US",
        {
            month: "short",
            day: "2-digit"
        }
    );
}


/*
|--------------------------------------------------------------------------
| Compact Number
|--------------------------------------------------------------------------
*/

function formatCompactNumber(value)
{
    value = Number(value || 0);

    if (value >= 1000000) {

        return (
            (value / 1000000)
                .toFixed(1)
                .replace(".0", "") +
            "M"
        );
    }

    if (value >= 1000) {

        return (
            (value / 1000)
                .toFixed(1)
                .replace(".0", "") +
            "K"
        );
    }

    return value;
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
$(document).ready(function(){
    fetchExpenseData();
    fetchExpenseSummary();
    fetchExpenseCharts("", "");
    $(document).on("click", ".delete-epid", function () {

        let EPID = $(this).data("epid");

        if (!EPID) {

            toastr.error(
                "Invalid Expense ID.",
                "Error"
            );

            return;
        }


        if (!confirm("Are you sure you want to delete this expense?")) {
            return;
        }


        let $btn = $(this);
        let originalHtml = $btn.html();


        $.ajax({

            url:"../Controller/AddExpensesController2.php" + "?condition=delete&EPID=" + encodeURIComponent(EPID),

            method: "POST",

            dataType: "json",

            data: {
                // body can remain empty;
                // EPID/condition are sent in query string below
            },

            beforeSend: function () {

                $btn
                    .prop("disabled", true)
                    .html(
                        '<span class="spinner-border spinner-border-sm"></span>'
                    );

            },

            success: function (response) {

                if (Number(response.status) === 1) {

                    toastr.success(
                        response.message,
                        "Success"
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Refresh Current Table Filter
                    |--------------------------------------------------------------------------
                    */

                    let startDate =
                        $("#expense_start_date").val();

                    let endDate =
                        $("#expense_end_date").val();


                    fetchExpenseData(
                        startDate,
                        endDate
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Refresh Summary
                    |--------------------------------------------------------------------------
                    */

                    fetchExpenseSummary();


                    /*
                    |--------------------------------------------------------------------------
                    | Refresh Charts
                    |--------------------------------------------------------------------------
                    */

                    fetchExpenseCharts(
                        startDate,
                        endDate
                    );

                } else {

                    toastr.error(
                        response.message || "Unable to delete expense.",
                        "Error"
                    );
                }

            },

            error: function (xhr, status, error) {

                console.log(
                    "Delete Expense Error:",
                    status,
                    error
                );

                console.log(
                    xhr.responseText
                );

                toastr.error(
                    "An error occurred while deleting the expense.",
                    "Error"
                );

            },

            complete: function () {

                $btn
                    .prop("disabled", false)
                    .html(originalHtml);

            }

        });

    });
    $("#refreshExpenseDashboard").on(
        "click",
        function ()
        {

            let $btn =
                $(this);

            let $icon =
                $btn.find("i");

            let startDate =
                $("#expense_start_date").val();

            let endDate =
                $("#expense_end_date").val();


            /*
            |--------------------------------------------------------------------------
            | Both Empty = Show Everything
            |--------------------------------------------------------------------------
            */

            if (!startDate && !endDate) {

                startDate = "";
                endDate = "";

            }

            /*
            |--------------------------------------------------------------------------
            | Only One Date Entered
            |--------------------------------------------------------------------------
            */

            else if (!startDate || !endDate) {

                toastr.warning(
                    "Please select both start and end dates.",
                    "Warning"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Invalid Range
            |--------------------------------------------------------------------------
            */

            if (
                startDate &&
                endDate &&
                startDate > endDate
            ) {

                toastr.warning(
                    "Start date cannot be after end date.",
                    "Warning"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Loading
            |--------------------------------------------------------------------------
            */

            $btn.prop(
                "disabled",
                true
            );

            $icon.css({
                animation:
                    "spin 0.8s linear infinite",

                display:
                    "inline-block"
            });


            /*
            |--------------------------------------------------------------------------
            | Refresh Table + Charts
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            | Summary is intentionally NOT called here.
            |
            */

            $.when(

                fetchExpenseData(
                    startDate,
                    endDate
                ),

                fetchExpenseCharts(
                    startDate,
                    endDate
                )

            ).always(
                function ()
                {

                    $btn.prop(
                        "disabled",
                        false
                    );

                    $icon.css(
                        "animation",
                        "none"
                    );

                }
            );

        }
    );
    $("#expense_start_date").on("change", function () {

        $("#expense_end_date").attr(
            "min",
            $(this).val()
        );

    });


    $("#expense_end_date").on("change", function () {

        $("#expense_start_date").attr(
            "max",
            $(this).val()
        );

    });
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