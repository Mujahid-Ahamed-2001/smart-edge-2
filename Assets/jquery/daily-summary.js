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
function setDailySummaryStatsLoading(
    isLoading
)
{

    let $section =
        $(".daily-summary-stats");


    if (!$section.length)
    {
        return;
    }


    if (isLoading)
    {

        $section.addClass(
            "is-loading"
        );


        $("#daily_summary_refresh")
            .prop(
                "disabled",
                true
            );


        // Prevent duplicate loaders
        if (
            !$section.find(
                ".daily-stats-loading-overlay"
            ).length
        )
        {

            $section.append(`

                <div class="daily-stats-loading-overlay">

                    <div class="daily-stats-loading-content">

                        <div
                            class="spinner-border text-primary"
                            role="status"
                        ></div>

                        <span>
                            Loading summary...
                        </span>

                    </div>

                </div>

            `);

        }

    }
    else
    {

        $section.removeClass(
            "is-loading"
        );


        $section.find(
            ".daily-stats-loading-overlay"
        ).remove();


        $("#daily_summary_refresh")
            .prop(
                "disabled",
                false
            );

    }

}

function setSalesPerformanceLoading(isLoading)
{
    let $section =
        $(".sales-performance-card");


    if (!$section.length)
    {
        return;
    }


    if (isLoading)
    {
        $section.addClass(
            "is-loading"
        );


        // Prevent duplicate loader
        if (
            !$section.find(
                ".sales-performance-loading-overlay"
            ).length
        )
        {
            $section.append(`

                <div class="sales-performance-loading-overlay">

                    <div class="sales-performance-loading-content">

                        <div
                            class="spinner-border text-primary"
                            role="status"
                        ></div>

                        <span>
                            Loading sales performance...
                        </span>

                    </div>

                </div>

            `);
        }
    }
    else
    {
        $section.removeClass(
            "is-loading"
        );


        $section.find(
            ".sales-performance-loading-overlay"
        ).remove();
    }
}

// =========================================================
// Sales Performance AJAX
// =========================================================

let salesPerformanceRequest = null;


function getSalesPerformance()
{
    let selectedDate =
        $("#daily_summary_date").val();


    // ==========================================
    // Validate Date
    // ==========================================

    if (!selectedDate)
    {
        toastr.warning(
            "Please select a date."
        );


        return $.Deferred()
            .reject()
            .promise();
    }


    // ==========================================
    // Abort Previous Request
    // ==========================================

    if (
        salesPerformanceRequest &&
        salesPerformanceRequest.readyState !== 4
    )
    {
        salesPerformanceRequest.abort();
    }


    // ==========================================
    // Show Loader
    // ==========================================

    setSalesPerformanceLoading(
        true
    );


    // ==========================================
    // AJAX
    // ==========================================

    salesPerformanceRequest =
        $.ajax({

            url:
                "../AJAX/DailySummary/getSalesPerformance.php",

            type:
                "POST",

            dataType:
                "json",

            data: {

                date:
                    selectedDate

            },


            success:
                function (response)
                {

                    console.log(
                        "Sales Performance:",
                        response
                    );


                    if (
                        response.status == 1 &&
                        response.data
                    )
                    {
                        renderSalesPerformance(
                            response.data
                        );
                    }
                    else
                    {
                        resetSalesPerformance();


                        toastr.error(
                            response.message ||
                            "Unable to load sales performance."
                        );
                    }

                },


            error:
                function (
                    xhr,
                    status,
                    error
                )
                {

                    // Ignore request aborted
                    // because another date was selected

                    if (
                        status === "abort"
                    )
                    {
                        return;
                    }


                    console.error(
                        "Sales Performance Error:",
                        error
                    );


                    console.error(
                        xhr.responseText
                    );


                    resetSalesPerformance();


                    toastr.error(
                        "Something went wrong while loading sales performance."
                    );

                },


            complete:
                function ()
                {

                    setSalesPerformanceLoading(
                        false
                    );

                }

        });


    return salesPerformanceRequest;
}

function renderSalesPerformance(data)
{
    // ==========================================
    // Average Bill
    // ==========================================

    $("#average_bill").text(
        formatCurrency(
            data.averageBill
        )
    );


    // ==========================================
    // Hourly Sales Chart
    // ==========================================

    renderSalesPerformanceChart(
        Array.isArray(
            data.hourlySales
        )
            ? data.hourlySales
            : []
    );
}

function resetSalesPerformance()
{
    // Average Bill
    $("#average_bill").text(
        formatCurrency(0)
    );


    // Empty chart
    renderSalesPerformanceChart(
        []
    );
}
// =========================================================
// Chart Variable
// =========================================================

let salesPerformanceChart = null;
// =========================================================
// Daily Summary Stats AJAX Request
// =========================================================

let dailySummaryStatsRequest = null;
function getDailySummaryStats()
{
    let selectedDate = $("#daily_summary_date").val();

    if (!selectedDate)
    {
        toastr.warning("Please select a date.");

        return $.Deferred().reject().promise();
    }


    // Cancel previous request
    if (
        dailySummaryStatsRequest &&
        dailySummaryStatsRequest.readyState !== 4
    )
    {
        dailySummaryStatsRequest.abort();
    }


    setDailySummaryStatsLoading(true);


    dailySummaryStatsRequest = $.ajax({

        url: "../AJAX/DailySummary/getDailySummaryStats.php",

        type: "POST",

        dataType: "json",

        data: {
            date: selectedDate
        },


        success: function (response)
        {
            console.log(
                "Daily Summary Stats:",
                response
            );


            if (
                response.status == 1 &&
                response.data
            )
            {
                renderDailySummaryStats(
                    response.data
                );
            }
            else
            {
                resetDailySummaryStats();

                toastr.error(
                    response.message ||
                    "Unable to load daily summary."
                );
            }
        },


        error: function (xhr, status, error)
        {
            if (status === "abort")
            {
                return;
            }


            console.error(
                "Daily Summary Error:",
                error
            );


            console.error(
                xhr.responseText
            );


            resetDailySummaryStats();


            toastr.error(
                "Something went wrong while loading the daily summary."
            );
        },


        complete: function ()
        {
            setDailySummaryStatsLoading(false);
        }

    });


    // IMPORTANT
    return dailySummaryStatsRequest;
}
function renderDailySummaryStats(data)
{

    // ==========================================
    // Net Revenue
    // ==========================================

    $("#net_revenue").text(
        formatCurrency(
            data.netRevenue
        )
    );


    $("#completed_invoice_count").text(
        Number(
            data.completedInvoices
        ) || 0
    );


    // ==========================================
    // Gross Profit
    // ==========================================

    $("#gross_profit").text(
        formatCurrency(
            data.grossProfit
        )
    );


    $("#gross_margin").text(
        formatPercentage(
            data.grossMargin
        )
    );


    // ==========================================
    // Expenses
    // ==========================================

    $("#total_expenses").text(
        formatCurrency(
            data.totalExpenses
        )
    );


    $("#expense_entry_count").text(
        Number(
            data.expenseCount
        ) || 0
    );


    // ==========================================
    // Net Profit
    // ==========================================

    $("#net_profit").text(
        formatCurrency(
            data.netProfit
        )
    );


    // ==========================================
    // Percentage Changes
    // ==========================================

    updateSummaryPercentage(
        "#revenue_percentage",
        data.revenuePercentageChange
    );


    updateSummaryPercentage(
        "#profit_percentage_change",
        data.profitPercentageChange
    );


    // Expense is special:
    // Increasing expense = bad/red
    // Decreasing expense = good/green

    updateSummaryPercentage(
        "#expense_percentage_change",
        data.expensePercentageChange,
        true
    );


    updateSummaryPercentage(
        "#net_profit_percentage_change",
        data.netProfitPercentageChange
    );

}
function resetDailySummaryStats()
{

    $("#net_revenue").text(
        formatCurrency(0)
    );


    $("#completed_invoice_count").text(
        0
    );


    $("#gross_profit").text(
        formatCurrency(0)
    );


    $("#gross_margin").text(
        "0%"
    );


    $("#total_expenses").text(
        formatCurrency(0)
    );


    $("#expense_entry_count").text(
        0
    );


    $("#net_profit").text(
        formatCurrency(0)
    );


    updateSummaryPercentage(
        "#revenue_percentage",
        0
    );


    updateSummaryPercentage(
        "#profit_percentage_change",
        0
    );


    updateSummaryPercentage(
        "#expense_percentage_change",
        0,
        true
    );


    updateSummaryPercentage(
        "#net_profit_percentage_change",
        0
    );

}
function formatPercentage(value)
{

    value =
        Number(value) || 0;


    return (
        value.toFixed(1)
        .replace(
            ".0",
            ""
        ) + "%"
    );

}
function updateSummaryPercentage(
    selector,
    percentage,
    reverseColor = false
)
{

    let $percentage =
        $(selector);


    if (!$percentage.length)
    {
        return;
    }


    percentage =
        Number(percentage) || 0;


    let $container =
        $percentage.closest(
            ".summary-stat-change"
        );


    let $icon =
        $container.find("i");


    $container.removeClass(
        "positive negative"
    );


    // ==========================================
    // Positive Change
    // ==========================================

    if (percentage > 0)
    {

        $icon
            .removeClass(
                "ti-arrow-down"
            )
            .addClass(
                "ti-arrow-up"
            );


        if (reverseColor)
        {

            $container.addClass(
                "negative"
            );

        }
        else
        {

            $container.addClass(
                "positive"
            );

        }

    }


    // ==========================================
    // Negative Change
    // ==========================================

    else if (percentage < 0)
    {

        $icon
            .removeClass(
                "ti-arrow-up"
            )
            .addClass(
                "ti-arrow-down"
            );


        if (reverseColor)
        {

            $container.addClass(
                "positive"
            );

        }
        else
        {

            $container.addClass(
                "negative"
            );

        }

    }


    // ==========================================
    // No Change
    // ==========================================

    else
    {

        $icon
            .removeClass(
                "ti-arrow-down"
            )
            .addClass(
                "ti-minus"
            );


        $container.addClass(
            "positive"
        );

    }


    $percentage.text(
        Math.abs(percentage)
            .toFixed(1)
            .replace(".0", "")
        +
        "%"
    );

}

const operationSummaryData = {

    paymentCollection: {

        total: 286450,

        payments: [

            {
                name: "Cash",
                amount: 148700,
                color: "green"
            },

            {
                name: "Card",
                amount: 83250,
                color: "blue"
            },

            {
                name: "Bank / QR",
                amount: 39500,
                color: "purple"
            },

            {
                name: "Customer Credit",
                amount: 15000,
                color: "orange"
            }

        ]

    },


    cashCounter: {

        openingCash: 15000,

        cashSales: 148700,

        cashExpenses: 9250,

        systemCash: 154450,

        physicalClosing: 154200,

        difference: -250

    },


    inventory: {

        itemsSold: 376,

        grnReceived: 124,

        lowStock: 18,

        expiringSoon: 7,

        stockValue: 3842900

    }

};

// =========================================================
// Render Sales + Invoice Summary
// =========================================================

function renderDailySalesSummary(data)
{

    // =====================================================
    // Average Bill
    // =====================================================

    $("#average_bill").text(

        formatCurrency(data.averageBill)

    );


    // =====================================================
    // Invoice Summary
    // =====================================================

    $("#invoice_completed").text(
        data.invoiceSummary.completed
    );


    $("#invoice_cash_sales").text(
        data.invoiceSummary.cashSales
    );


    $("#invoice_credit_sales").text(
        data.invoiceSummary.creditSales
    );


    $("#invoice_mixed_payments").text(
        data.invoiceSummary.mixedPayments
    );


    $("#invoice_items_sold").text(
        data.invoiceSummary.itemsSold
    );


    // =====================================================
    // Chart
    // =====================================================

    renderSalesPerformanceChart(
        data.hourlySales
    );

}
function renderSalesPerformanceChart(hourlySales)
{

    const canvas = document.getElementById(
        "salesPerformanceChart"
    );


    if (!canvas)
    {
        return;
    }


    const labels = hourlySales.map(function (item)
    {

        return item.time;

    });


    const values = hourlySales.map(function (item)
    {

        return Number(item.amount) || 0;

    });



    // =====================================================
    // Destroy previous chart
    // Important when refresh AJAX runs
    // =====================================================

    if (salesPerformanceChart)
    {

        salesPerformanceChart.destroy();

    }



    const context = canvas.getContext("2d");


    // =====================================================
    // Gradient
    // =====================================================

    const gradient = context.createLinearGradient(
        0,
        0,
        0,
        250
    );


    gradient.addColorStop(
        0,
        "#2E90FA"
    );


    gradient.addColorStop(
        1,
        "#53B1FD"
    );



    // =====================================================
    // Create Chart
    // =====================================================

    salesPerformanceChart = new Chart(
        context,
        {

            type: "bar",


            data: {

                labels: labels,


                datasets: [

                    {

                        data: values,

                        backgroundColor: gradient,

                        borderColor: "#2E90FA",

                        borderWidth: 0,

                        borderRadius: 5,

                        borderSkipped: false,

                        maxBarThickness: 48

                    }

                ]

            },


            options: {

                responsive: true,

                maintainAspectRatio: false,


                animation: {

                    duration: 500

                },


                plugins: {

                    legend: {

                        display: false

                    },


                    tooltip: {

                        displayColors: false,


                        callbacks: {

                            label: function (context)
                            {

                                return formatCurrency(
                                    context.raw
                                );

                            }

                        }

                    }

                },


                scales: {

                    x: {

                        grid: {

                            display: true,

                            color: "#F2F4F7",

                            drawBorder: false

                        },


                        border: {

                            display: false

                        },


                        ticks: {

                            color: "#667085",

                            font: {

                                size: 11,

                                weight: "500"

                            }

                        }

                    },


                    y: {

                        beginAtZero: true,


                        grid: {

                            color: "#EAECF0",

                            drawBorder: false

                        },


                        border: {

                            display: false

                        },


                        ticks: {

                            color: "#667085",

                            stepSize: 10000,


                            callback: function (value)
                            {

                                if (value >= 1000)
                                {

                                    return (
                                        value / 1000
                                    ) + "K";

                                }


                                return value;

                            },


                            font: {

                                size: 11

                            }

                        }

                    }

                }

            }

        }
    );

}
function formatCurrency(amount)
{

    amount = Number(amount) || 0;


    return "Rs. " + amount.toLocaleString(
        "en-LK",
        {

            maximumFractionDigits: 2

        }
    );

}
function renderOperationSummary(data)
{

    if (!data)
    {
        return;
    }


    // ==========================================
    // Payment Collection
    // ==========================================

    if (data.paymentCollection)
    {

        renderPaymentCollection(
            data.paymentCollection
        );

    }


    // ==========================================
    // Cash Counter
    // Only execute when card exists
    // ==========================================

    if (
        $("#cash_counter_card").length &&
        data.cashCounter
    )
    {

        renderCashCounter(
            data.cashCounter
        );

    }


    // ==========================================
    // Inventory
    // ==========================================

    if (data.inventory)
    {

        renderInventoryActivity(
            data.inventory
        );

    }

}
function renderPaymentCollection(data)
{

    const total =
        Number(data.total) || 0;


    $("#payment_collection_total").text(
        formatCurrency(total)
    );


    const container =
        $("#payment_collection_list");


    container.empty();


    if (
        !Array.isArray(data.payments) ||
        data.payments.length === 0
    )
    {

        container.html(`
            <div class="text-muted py-3">
                No payments recorded.
            </div>
        `);

        return;
    }


    data.payments.forEach(function (payment)
    {

        const amount =
            Number(payment.amount) || 0;


        let percentage = 0;


        if (total > 0)
        {

            percentage =
                (amount / total) * 100;

        }


        percentage =
            Math.min(
                Math.max(
                    percentage,
                    0
                ),
                100
            );


        const colorClass =
            getPaymentColorClass(
                payment.color
            );


        const html = `

            <div class="payment-collection-item">

                <div class="payment-row-header">

                    <span class="payment-name">
                        ${escapeHtml(payment.name)}
                    </span>

                    <span class="payment-amount">
                        ${formatCurrency(amount)}
                    </span>

                </div>


                <div class="payment-progress-wrapper">

                    <div class="payment-progress">

                        <div
                            class="payment-progress-bar ${colorClass}"
                            style="width: ${percentage.toFixed(1)}%;"
                        ></div>

                    </div>


                    <span class="payment-percentage">

                        ${Math.round(percentage)}%

                    </span>

                </div>

            </div>

        `;


        container.append(html);

    });

}
function getPaymentColorClass(color)
{

    switch (color)
    {

        case "green":

            return "payment-bar-green";


        case "blue":

            return "payment-bar-blue";


        case "purple":

            return "payment-bar-purple";


        case "orange":

            return "payment-bar-orange";


        case "red":

            return "payment-bar-red";


        default:

            return "payment-bar-blue";

    }

}
function renderCashCounter(data)
{

    $("#counter_opening_cash").text(
        formatCurrency(
            data.openingCash
        )
    );


    $("#counter_cash_sales").text(
        formatCurrency(
            data.cashSales
        )
    );


    $("#counter_cash_expenses").text(
        formatCurrency(
            data.cashExpenses
        )
    );


    $("#counter_system_cash").text(
        formatCurrency(
            data.systemCash
        )
    );


    // Physical Closing may not exist
    // until counter is actually closed.

    if (
        data.physicalClosing !== null &&
        data.physicalClosing !== undefined
    )
    {

        $("#counter_physical_closing").text(
            formatCurrency(
                data.physicalClosing
            )
        );

    }
    else
    {

        $("#counter_physical_closing").text(
            "Not closed"
        );

    }


    renderCashDifference(
        data.difference
    );

}
function renderCashDifference(difference)
{

    const element =
        $("#counter_difference");


    difference =
        Number(difference) || 0;


    element.removeClass(
        "negative positive equal"
    );


    if (difference < 0)
    {

        element.addClass(
            "negative"
        );


        element.text(
            "- " +
            formatCurrency(
                Math.abs(difference)
            )
        );

    }
    else if (difference > 0)
    {

        element.addClass(
            "positive"
        );


        element.text(
            "+ " +
            formatCurrency(
                difference
            )
        );

    }
    else
    {

        element.addClass(
            "equal"
        );


        element.text(
            formatCurrency(0)
        );

    }

}
function renderInventoryActivity(data)
{

    $("#inventory_items_sold").text(
        Number(
            data.itemsSold
        ) || 0
    );


    $("#inventory_grn_received").text(
        Number(
            data.grnReceived
        ) || 0
    );


    $("#inventory_low_stock").text(
        Number(
            data.lowStock
        ) || 0
    );


    $("#inventory_expiring_soon").text(
        Number(
            data.expiringSoon
        ) || 0
    );


    $("#inventory_stock_value").text(
        formatCurrency(
            data.stockValue
        )
    );

}
function escapeHtml(value)
{

    return $("<div>")
        .text(value ?? "")
        .html();

}
$(document).ready(function(){
    $("#headerCollapse2").trigger("click");
    getDailySummaryStats();
    getSalesPerformance();
    $(document).on("click", "#daily_summary_refresh", function ()
        {

            getDailySummaryStats();
            getSalesPerformance();

        }
    );
    $("#refresh").on("click",
        function ()
        {
            let $btn =
                $(this);

            let $icon =
                $btn.find("i");


            if (
                $btn.hasClass(
                    "refresh-loading"
                )
            )
            {
                return;
            }


            $btn.addClass(
                "refresh-loading"
            );


            $icon.css({

                "animation":
                    "spin 0.8s linear infinite",

                "display":
                    "inline-block"

            });


            $.when(
                getDailySummaryStats(),
                getSalesPerformance()
            )
            .always(
                function ()
                {
                    $btn.removeClass(
                        "refresh-loading"
                    );


                    $icon.css(
                        "animation",
                        "none"
                    );
                }
            );
        }
    );
    const dailySummaryData = {

        averageBill: 2017,

        invoiceSummary: {

            completed: 142,

            cashSales: 81,

            creditSales: 19,

            mixedPayments: 42,

            itemsSold: 376
        },

        hourlySales: [

            {
                time: "8AM",
                amount: 10000
            },

            {
                time: "10AM",
                amount: 17000
            },

            {
                time: "12PM",
                amount: 28000
            },

            {
                time: "2PM",
                amount: 24000
            },

            {
                time: "4PM",
                amount: 36000
            },

            {
                time: "6PM",
                amount: 46000
            },

            {
                time: "8PM",
                amount: 34000
            },

            {
                time: "10PM",
                amount: 17000
            }

        ]

    };


    // =====================================================
    // Render Daily Summary
    // =====================================================

    renderDailySalesSummary(dailySummaryData);
    renderOperationSummary(
        operationSummaryData
    );

});