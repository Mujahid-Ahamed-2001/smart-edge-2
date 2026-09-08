var salesChart = null;
function TableLoading($table,TableName = "", loading="Loading ", dot="...")
{
    let loaderHtml = `<div class="d-flex flex-column justify-content-center align-items-center text-center" style="min-height: 50px;">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="mt-2 fw-semibold">${loading+" "+TableName+dot}</div>
    </div>`;
    $table.html(loaderHtml);
}
function TableNoData($table,TableName = "")
{
    let loaderHtml = `<div class="d-flex flex-column justify-content-center align-items-center text-center" style="min-height: 50px;">
        <div class="mt-2 fw-semibold">No Data ${TableName}...</div>
    </div>`;
    $table.html(loaderHtml);
}
function getSales(startDate, endDate) {
    var $table = $('#chart');

    $.ajax({
        url: "../AJAX/Analytics/getSales.php",
        method: "GET",
        data: {
            start: startDate,
            end: endDate
        },
        dataType: "json",
        beforeSend: function() {
            TableLoading($table, "Sales Data");
        },
        success: function(response) {
                $table.html("");

                if (!response || !response.dates) {
                    toastr.error("No data received", "Error");
                    return;
                }

                if (response.dates.length === 0) {
                    TableNoData($table, "Sales Data");
                    return;
                }

                var options = {
                    chart: {
                        type: "area",
                        height: 300,
                        toolbar: {
                            show: false
                        },
                        fontFamily: "Plus Jakarta Sans, sans-serif",
                        foreColor: "#adb0bb"
                    },

                    series: [{
                        name: "Earnings",
                        data: response.earnings || []
                    },{
                        name: "Expenses",
                        data: response.expenses || []
                    }],

                    colors: ["#49BEFF", "#ff0100"],

                    stroke: {
                        curve: "smooth",
                        width: 3
                    },

                    fill: {
                        type: "gradient",
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.05,
                            stops: [0, 100]
                        }
                    },

                    markers: {
                        size: 4
                    },

                    xaxis: {
                        categories: response.dates || [],
                        labels: {
                            show: true
                        },
                        axisBorder: {
                            show: true
                        },
                        axisTicks: {
                            show: true
                        }
                    },

                    yaxis: {
                        show: true,
                        title: {
                            text: "Amount"
                        },
                        labels: {
                            formatter: function (value) {
                                return value.toLocaleString();
                            }
                        }
                    },

                    grid: {
                        show: true,
                        borderColor: "#e5e5e5",
                        strokeDashArray: 4
                    },

                    tooltip: {
                        theme: "dark",
                        x: {
                            show: true
                        },
                        y: {
                            formatter: function (val) {
                                return "Rs. " + val.toLocaleString();
                            }
                        }
                    }
                };

                if (salesChart) {
                    salesChart.destroy();
                }

                salesChart = new ApexCharts(document.querySelector("#chart"), options);
                salesChart.render();
            },
        error: function() {
            toastr.error("Error loading sales data", "Error");
        }
    });
    }
function getCommon(startDate, endDate) {
    var $table = $('.stat-value');

    $.ajax({
        url: "../AJAX/Analytics/getCommon.php",
        method: "GET",
        data: {
            start: startDate,
            end: endDate
        },
        dataType: "json",
        beforeSend: function() {
            TableLoading($table,"","","");
        },
        success: function(response) {
                // $table.html("");
                if (!response) {
                    TableNoData($table);
                    toastr.error("No data received", "Error");
                    return;
                }

                $("#tot-revenue").html(`Rs. ${parseFloat(response.totalRevenue).toFixed(2)}`).attr("title",`Rs. ${parseFloat(response.totalRevenue).toFixed(2)}`);
                $("#tot-cost").html(`Rs. ${parseFloat(response.totalCost).toFixed(2)}`).attr("title",`Rs. ${parseFloat(response.totalCost).toFixed(2)}`);
                $("#tot-gp").html(`Rs. ${parseFloat(response.grossProfit).toFixed(2)}`).attr("title",`Rs. ${parseFloat(response.grossProfit).toFixed(2)}`);
                $("#tot-orders").html(response.totalOrders).attr("title",` ${response.totalOrders}`);
                $("#tot-lowstock").html(response.lowStockItems).attr("title",` ${response.lowStockItems}`);
            },
        error: function() {
            toastr.error("Error loading summary data", "Error");
        }
    });
    }
function getTopSellingProducts(startDate, endDate){
    var $table = $('#pieChart');
    $.ajax({
        url: "../AJAX/Analytics/getTopSellingProducts.php",
        data: {
            start: startDate,
            end: endDate
        },
        dataType: "json",
        beforeSend: function() {
            TableLoading($table, TableName = "Top Selling Products");
        },
        success: function(data){
            $table.html("");
            if(data.length === 0 || (data.categories.length === 0 && data.amounts.length === 0)) {
              TableNoData($table, TableName = "Top Selling Products");
              return;
            }
            var options = {
                chart: {
                    type: 'donut',
                    height: 250,       // smaller height
                    width: 250         // optional width limit
                },
                series: data.amounts,
                labels: data.categories,
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',      // bigger inner circle → thinner slices
                            labels: {
                                show: true,
                                name: {
                                    show: false   // hide name inside donut for compactness
                                },
                                value: {
                                    show: true,
                                    fontSize: '12px',
                                    fontWeight: 400
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '13px',
                                    fontWeight: 600,
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce((a,b)=>a+b,0)
                                    }
                                }
                            }
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    fontSize: '11px'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val;
                        }
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: { width: 150, height: 150 },
                        legend: { position: 'bottom', fontSize: '10px' }
                    }
                }]
            };
            new ApexCharts(document.querySelector("#pieChart"), options).render();
        }
    });
}
function getSalesBySubCategory(startDate, endDate){
    var $table = $('#pieChart2');
    $.ajax({
        url: "../AJAX/Analytics/getSalesBySubCategory.php",
        data: {
            start: startDate,
            end: endDate
        },
        dataType: "json",
        beforeSend: function() {
            TableLoading($table, TableName = "Top Selling Sub-Categories");
        },
        success: function(data){
            $table.html("");
            if(data.length === 0 || (data.categories.length === 0 && data.amounts.length === 0)) {
              TableNoData($table, TableName = "Top Selling Sub-Categories");
              return;
            }
            var options = {
                chart: {
                    type: 'donut',
                    height: 250,       // smaller height
                    width: 250         // optional width limit
                },
                series: data.amounts,
                labels: data.categories,
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',      // bigger inner circle → thinner slices
                            labels: {
                                show: true,
                                name: {
                                    show: false   // hide name inside donut for compactness
                                },
                                value: {
                                    show: true,
                                    fontSize: '12px',
                                    fontWeight: 400
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '13px',
                                    fontWeight: 600,
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce((a,b)=>a+b,0)
                                    }
                                }
                            }
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    fontSize: '11px'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val;
                        }
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: { width: 150, height: 150 },
                        legend: { position: 'bottom', fontSize: '10px' }
                    }
                }]
            };
            new ApexCharts(document.querySelector("#pieChart2"), options).render();
        }
    });
}
function getCostVsRevenue(startDate, endDate){
    var $table = $('#revenueChart');
    $.ajax({
        url: "../AJAX/Analytics/getCostVsRevenue.php",
        data: {
            start: startDate,
            end: endDate
        },
        dataType: "json",
        beforeSend: function() {
            TableLoading($table, TableName = "Cost vs Revenue");
        },
        success: function(data){
            $table.html("");
            if(data.length === 0 || (data.dates.length === 0 && data.revenue.length === 0 && data.cost.length === 0)) {
              TableNoData($table, TableName = "Cost vs Revenue");
              return;
            }
            var options = {
                chart: {
                    type: 'line',
                    height: 300,
                    toolbar: { show: false }
                },
                series: [
                    {
                        name: "Revenue",
                        data: data.revenue
                    },
                    {
                        name: "Cost",
                        data: data.cost
                    }
                ],
                xaxis: {
                    categories: data.dates
                },
                dataLabels: {
                    enabled: true
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                markers: {
                    size: 4
                },
                tooltip: {
                    y: {
                        formatter: function(val){
                            return "Rs. " + val.toFixed(2);
                        }
                    }
                },
                legend: {
                    position: 'top'
                }
            };

            new ApexCharts(document.querySelector("#revenueChart"), options).render();
        }
    });
}
function lowStockAlert(){
    var $table = $('#lowStockChart');
    $.ajax({
        url: "../AJAX/Analytics/low_stock_api.php",
        dataType: "json",
        beforeSend: function() {
            TableLoading($table, TableName = "Low Stock Alert");
        },
        success: function(data){
            $table.html("");
            // Combine ProductName and CurrentQty into array of objects
            const combined = data.ProductName.map((name, i) => ({
                ProductName: name,
                CurrentQty: parseInt(data.CurrentQty[i])
            }));

            const labels = combined.map(item => item.ProductName);
            const stock = combined.map(item => item.CurrentQty);
            if(data.length === 0 || (labels.length === 0 && stock.length === 0)) {
              TableNoData($table, TableName = "Low Stock Alert");
              return;
            }

            var options = {
                chart: {
                    type: 'bar',
                    height: 350
                },
                series: [{
                    name: 'Stock Qty',
                    data: stock
                }],
                xaxis: {
                    categories: labels
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        distributed: true
                    }
                },
                colors: stock.map(q => q <= 7 ? '#FF0000' : '#FFA500'),
                dataLabels: {
                    enabled: true
                },
                title: {
                    text: "Low Stock Alert"
                }
            };

            $("#lowStockChart").html("");
            new ApexCharts(document.querySelector("#lowStockChart"), options).render();
        }
    });
}
function getSlowMovingProducts(){
    var $table = $('#slowMovingTable');
    $.ajax({
        url: "../AJAX/Analytics/slow_moving_api.php",
        dataType: "json",
        beforeSend: function() {
            TableLoading($table, TableName = "Slow Moving Products");
        },
        success: function(data){
            $table.html("");
            // Combine ProductName and CurrentQty into array of objects
            const combined = data.ProductName.map((name, i) => ({
                ProductName: name,
                CurrentQty: parseInt(data.CurrentQty[i])
            }));

            const labels = combined.map(item => item.ProductName);
            const stock = combined.map(item => item.CurrentQty);
            if(data.length === 0 || (labels.length === 0 && stock.length === 0)) {
              TableNoData($table, TableName = "Slow Moving Products");
              return;
            }

            var options = {
                chart: {
                    type: 'bar',
                    height: 350
                },
                series: [{
                    name: 'Stock Qty',
                    data: stock
                }],
                xaxis: {
                    categories: labels
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        distributed: true
                    }
                },
                colors: stock.map(q => q <= 7 ? '#FF0000' : '#FFA500'),
                dataLabels: {
                    enabled: true
                },
                title: {
                    text: "Low Stock Alert"
                }
            };

            $("#slowMovingChart").html("");
            new ApexCharts(document.querySelector("#slowMovingChart"), options).render();
        }
    });
}
function getfastMovingProducts(){
    var $table = $('#topMovingChart');
    $.ajax({
        url: "../AJAX/Analytics/top_moving_api.php",
        dataType: "json",
        beforeSend: function() {
            TableLoading($table, TableName = "Fast Moving Products");
        },
        success: function(data){
            $table.html("");
            // Combine ProductName and CurrentQty into array of objects
            const combined = data.ProductName.map((name, i) => ({
                ProductName: name,
                CurrentQty: parseInt(data.CurrentQty[i])
            }));

            const labels = combined.map(item => item.ProductName);
            const stock = combined.map(item => item.CurrentQty);
            if(data.length === 0 || (labels.length === 0 && stock.length === 0)) {
              TableNoData($table, TableName = "Fast Moving Products");
              return;
            }

            var options = {
                chart: {
                    type: 'bar',
                    height: 350
                },
                series: [{
                    name: 'Stock Qty',
                    data: stock
                }],
                xaxis: {
                    categories: labels
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        distributed: true
                    }
                },
                // colors: stock.map(q => q <= 7 ? '#FF0000' : '#FFA500'),
                colors: stock.map(q => q <= 7 ? '#FFA500' : '#007c00'),
                dataLabels: {
                    enabled: true
                },
                title: {
                    text: "Top Moving Products"
                }
            };

            $("#topMovingChart").html("");
            new ApexCharts(document.querySelector("#topMovingChart"), options).render();
        }
    });
}
function salesByPayment(startDate, endDate){
    var $table = $('#salesPaymentChart');
    $.ajax({
        url: "../AJAX/Analytics/sales_by_payment_api.php",
        data: {
            start: startDate,
            end: endDate
        },
        dataType: "json",
        beforeSend: function() {
            TableLoading($table, TableName = "Sales by Payment Method");
        },
        success: function(data){
            $table.html("");
            const labels = data.PaymethodName;
            const amounts = data.TotalAmount;
            if(data.length === 0 || (labels.length === 0 && amounts.length === 0)) {
              TableNoData($table, TableName = "Sales by Payment Method");
              return;
            }


            var options = {
                chart: {
                    type: 'donut',
                    height: 350
                },
                series: amounts,
                labels: labels,
                dataLabels: {
                    enabled: true
                },
                legend: {
                    position: 'bottom'
                },
                title: {
                    text: "Sales by Payment Method"
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "Rs. " + val.toLocaleString();
                        }
                    }
                }
            };

            $("#salesPaymentChart").html("");
            new ApexCharts(document.querySelector("#salesPaymentChart"), options).render();
        }
    });
}
$(document).ready(function() {
    $("#headerCollapse2").trigger("click");

});
$(function () {
    $("#headerCollapse2").on("click", function () {
        console.log("Clicked");
        
    });
  // =====================================
  // Load Current Month Initially
  // =====================================

  var today = new Date();

var firstDay =
    today.getFullYear() + "-" +
    String(today.getMonth() + 1).padStart(2, "0") +
    "-01";

var lastDate = new Date(
    today.getFullYear(),
    today.getMonth() + 1,
    0
);

var lastDay =
    lastDate.getFullYear() + "-" +
    String(lastDate.getMonth() + 1).padStart(2, "0") + "-" +
    String(lastDate.getDate()).padStart(2, "0");

  getSales(firstDay, lastDay);
  getTopSellingProducts(firstDay, lastDay);
  getSalesBySubCategory(firstDay, lastDay);
  getCommon(firstDay, lastDay)
  getCostVsRevenue(firstDay, lastDay);
  lowStockAlert();
  getSlowMovingProducts();
  getfastMovingProducts();
  salesByPayment(firstDay, lastDay);

  // =====================================
  // Filter Submit
  // =====================================

    $("#formCommon").on("submit", function(e){
        e.preventDefault();
        var form = $(this);
        var startDate = form.find("#startDate").val();
        var endDate = form.find("#endDate").val();

        if(startDate !== "" && endDate !== "") 
        {
            if(startDate > endDate)
            {
              toastr.error("Start date cannot be greater than End date", "Error");
              return;
            }
            getCommon(startDate, endDate);
            getSales(startDate, endDate);
            getTopSellingProducts(startDate, endDate);
            getSalesBySubCategory(startDate, endDate);
            getCostVsRevenue(startDate, endDate);
            salesByPayment(startDate, endDate);
        } 
        else 
        {
            toastr.error("Please fill both Start Date and End Date", "Error");
        }
    });
    $("#formGetSales").on("submit", function(e){
        e.preventDefault();
        var form = $(this);
        var startDate = form.find("#startDate").val();
        var endDate = form.find("#endDate").val();

        if(startDate !== "" && endDate !== "") 
        {
            if(startDate > endDate)
            {
              toastr.error("Start date cannot be greater than End date", "Error");
              return;
            }
            getSales(startDate, endDate);
        } 
        else 
        {
            toastr.error("Please fill both Start Date and End Date", "Error");
        }
    });
    $("#formsellingProducts").on("submit", function(e){
        e.preventDefault();
        var form = $(this);
        var startDate = form.find("#startDate").val();
        var endDate = form.find("#endDate").val();

        if(startDate !== "" && endDate !== "") 
        {
            if(startDate > endDate)
            {
              toastr.error("Start date cannot be greater than End date", "Error");
              return;
            }
            getTopSellingProducts(startDate, endDate);
        } 
        else 
        {
            toastr.error("Please fill both Start Date and End Date", "Error");
        }
    });
    $("#formsellingSubCate").on("submit", function(e){
        e.preventDefault();
        var form = $(this);
        var startDate = form.find("#startDate").val();
        var endDate = form.find("#endDate").val();

        if(startDate !== "" && endDate !== "") 
        {
            if(startDate > endDate)
            {
              toastr.error("Start date cannot be greater than End date", "Error");
              return;
            }
            getSalesBySubCategory(startDate, endDate);
        } 
        else 
        {
            toastr.error("Please fill both Start Date and End Date", "Error");
        }
    });
    $("#formGetRevenue").on("submit", function(e){
        e.preventDefault();

        var startDate = $("#startDate2").val();
        var endDate = $("#endDate2").val();

        if(startDate !== "" && endDate !== "") {
            if(startDate > endDate)
            {
              toastr.error("Start date cannot be greater than End date", "Error");
              return;
            }
            getCostVsRevenue(startDate, endDate);
        } 
        else 
        {
            toastr.error("Please fill both Start Date and End Date", "Error");
        }
    });
    $("#formGetPaymentMethod").on("submit", function(e){
        e.preventDefault();

        var startDate = $("#startDate3").val();
        var endDate = $("#endDate3").val();

        if(startDate !== "" && endDate !== "") {
            if(startDate > endDate)
            {
              toastr.error("Start date cannot be greater than End date", "Error");
              return;
            }
            salesByPayment(startDate, endDate);
        } 
        else 
        {
            toastr.error("Please fill both Start Date and End Date", "Error");
        }
    });


});
