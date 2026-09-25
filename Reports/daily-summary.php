<?php 
include '../Includes/includes.php';
include '../Includes/authcheck.php';
$cashCounterEnabled = false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
    include '../View/head.php';
    include '../View/loader.php';
    ?>
    <style>
        .dis-none
        {
            display: none;
        }
        .tbl_row
        {
            cursor: pointer;
        }
    </style>
    <link rel="stylesheet" href="../Assets/css/page.css">
    <link rel="stylesheet" href="../Assets/css/daily-summary.css">
</head>
<body>
    <div id="modal"></div>
    <div class="h-100">
        <div class="page-wrapper" id="main-wrapper">
            <!-- Sidebar Start -->
            <?php 
            include '../View/sidebar.php';
            $feature_id=16;
            include '../Includes/viewPermission.php';
            ?>
            <div class="body-wrapper">
                <!--  Header Start -->
                <?php 
                include '../View/header.php';
                ?>
                <?php
                // include "../View/modals/addproducts.php";
                include "../View/modals/editproducts.php";
                include "../View/modals/multieditproducts.php";
                include "../View/modals/product_barcode.php";
                
                ?>
                <!--  Header End -->
                <div class="container-fluid">
                    <section class="head-dashboard-head">

                        <div class="head-head-row">

                            <div class="head-heading">
                                <h1>Daily Business Summary <button type="button" id="refresh"> <i class="ti ti-reload"></i> </button></h1> 

                                <div class="head-breadcrumb">
                                    <a href="javascript:void(0)">Home</a>
                                    <i class="ti ti-chevron-right"></i>

                                    <a href="javascript:void(0)">Daily Summary</a>
                                </div>
                            </div>
                            <div class="head-head-actions">
                                <div class="daily-summary-head-actions">
                                    <!-- Date Selector -->
                                    <div class="summary-date-selector">
                                        <i class="ti ti-calendar"></i>
                                        <input type="date" id="daily_summary_date" name="daily_summary_date" value="<?= date('Y-m-d') ?>" >
                                        <i class="ti ti-chevron-down date-arrow"></i>
                                    </div>
                                    <!-- Refresh Button -->
                                    <button type="button" class="summary-refresh-btn" id="daily_summary_refresh" >
                                        <i class="ti ti-refresh"></i>
                                        <span>Refresh</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>
                    <?php $today = date('d-m-Y');?>
                    <?php $shop = $shopObj->getOneShop($shop_id);?>
                    <?php $query = isset($_GET['query']) && !empty($_GET['query']) ? $_GET['query'] : '';?>
                    <input type="hidden" name="app_status" value="<?= $app ?>" id="app_status">
                    <input type="hidden" name="query" value="<?= $query ?>" id="query">
                    <input type="hidden" name="" id="today" value="<?= $today ?>">
                    <input type="hidden" name="" id="print_access" value="<?= $print ?>">
                    <input type="hidden" name="" id="verify_access" value="<?= $verify ?>">
                    <input type="hidden" name="" id="edit_access" value="<?= $edit ?>">
                    <input type="hidden" name="" id="delete_access" value="<?= $delete ?>">
                    <input type="hidden" name="" id="userType" value="<?= $userType ?>">
                    <input type="hidden" name="" id="shop_name" value="<?=$shop[0]['ShopName']?>">
                    <input type="hidden" name="" id="shop_id" value="<?=$shop_id?>">
                    <input type="hidden" name="" id="shop_address_one" value="<?=$shop[0]['AddressLineOne']?> ">
                    <input type="hidden" name="" id="shop_address_two" value="<?=$shop[0]['AddressLineTwo']?>">
                    <input type="hidden" name="" id="shop_city" value="<?=$shop[0]['City']?>">
                    <input type="hidden" name="" id="shop_number" value="<?=$shop[0]['PhoneNumber']?> ">
                    <input type="hidden" name="" id="title" value="Subcategories">
                    <!-- ==========================================
                        Daily Summary - Main Statistic Cards
                    =========================================== -->
                    <div class="daily-summary-stats">

                        <!-- Net Revenue -->
                        <div class="summary-stat-card revenue-card">

                            <div class="summary-stat-icon revenue-icon">
                                <i class="ti ti-report-money"></i>
                            </div>

                            <div class="summary-stat-content">

                                <div class="summary-stat-header">
                                    <span class="summary-stat-title">
                                        Net Revenue
                                    </span>

                                    <span class="summary-stat-change positive">
                                        <i class="ti ti-arrow-up"></i>
                                        <span id="revenue_percentage">12%</span>
                                    </span>
                                </div>

                                <h2 class="summary-stat-value" id="net_revenue">
                                    Rs. 286,450
                                </h2>

                                <p class="summary-stat-description">
                                    <span id="completed_invoice_count">142</span>
                                    completed invoices
                                </p>

                            </div>

                        </div>


                        <!-- Gross Profit -->
                        <div class="summary-stat-card profit-card">

                            <div class="summary-stat-icon profit-icon">
                                <i class="ti ti-chart-bar"></i>
                            </div>

                            <div class="summary-stat-content">

                                <div class="summary-stat-header">
                                    <span class="summary-stat-title">
                                        Gross Profit
                                    </span>

                                    <span class="summary-stat-change positive">
                                        <i class="ti ti-arrow-up"></i>
                                        <span id="profit_percentage_change">8%</span>
                                    </span>
                                </div>

                                <h2 class="summary-stat-value" id="gross_profit">
                                    Rs. 86,320
                                </h2>

                                <p class="summary-stat-description">
                                    <span id="gross_margin">30.1%</span>
                                    gross margin
                                </p>

                            </div>

                        </div>


                        <!-- Total Expenses -->
                        <div class="summary-stat-card expense-card">

                            <div class="summary-stat-icon expense-icon">
                                <i class="ti ti-receipt-2"></i>
                            </div>

                            <div class="summary-stat-content">

                                <div class="summary-stat-header">
                                    <span class="summary-stat-title">
                                        Total Expenses
                                    </span>

                                    <span class="summary-stat-change negative">
                                        <i class="ti ti-arrow-up"></i>
                                        <span id="expense_percentage_change">5%</span>
                                    </span>
                                </div>

                                <h2 class="summary-stat-value" id="total_expenses">
                                    Rs. 18,750
                                </h2>

                                <p class="summary-stat-description">
                                    <span id="expense_entry_count">8</span>
                                    expense entries
                                </p>

                            </div>

                        </div>


                        <!-- Net Profit -->
                        <div class="summary-stat-card net-profit-card">

                            <div class="summary-stat-icon net-profit-icon">
                                <i class="ti ti-chart-bar"></i>
                            </div>

                            <div class="summary-stat-content">

                                <div class="summary-stat-header">
                                    <span class="summary-stat-title">
                                        Net Profit
                                    </span>

                                    <span class="summary-stat-change positive">
                                        <i class="ti ti-arrow-up"></i>
                                        <span id="net_profit_percentage_change">10%</span>
                                    </span>
                                </div>

                                <h2 class="summary-stat-value" id="net_profit">
                                    Rs. 67,570
                                </h2>

                                <p class="summary-stat-description">
                                    After recorded expenses
                                </p>

                            </div>

                        </div>

                    </div> <!-- .daily-summary-stats -->
                    <!-- ==========================================
                        Sales Performance + Invoice Summary
                    =========================================== -->

                    <div class="daily-sales-section">

                        <!-- Sales Performance -->
                        <div class="daily-report-card sales-performance-card">

                            <div class="daily-report-card-header">

                                <div class="report-title-area">

                                    <div class="report-title-icon blue">
                                        <i class="ti ti-chart-bar"></i>
                                    </div>

                                    <div>
                                        <h3>Sales Performance</h3>

                                        <p>
                                            Hourly sales distribution
                                        </p>
                                    </div>

                                </div>


                                <!-- Average Bill -->
                                <div class="average-bill-box">

                                    <span class="average-bill-label">
                                        Avg. bill
                                    </span>

                                    <strong id="average_bill">
                                        Rs. 2,017
                                    </strong>

                                </div>

                            </div>


                            <!-- Chart -->
                            <div class="sales-chart-container">

                                <canvas id="salesPerformanceChart"></canvas>

                            </div>

                        </div>



                        <!-- Invoice Summary -->
                        <div class="daily-report-card invoice-summary-card">

                            <div class="daily-report-card-header">

                                <div class="report-title-area">

                                    <div class="report-title-icon blue">
                                        <i class="ti ti-file-invoice"></i>
                                    </div>

                                    <div>
                                        <h3>Invoice Summary</h3>
                                    </div>

                                </div>

                            </div>


                            <div class="invoice-summary-list">

                                <!-- Completed -->
                                <div class="invoice-summary-row">

                                    <span>
                                        Completed
                                    </span>

                                    <strong
                                        class="summary-blue"
                                        id="invoice_completed"
                                    >
                                        142
                                    </strong>

                                </div>


                                <!-- Cash Sales -->
                                <div class="invoice-summary-row">

                                    <span>
                                        Cash Sales
                                    </span>

                                    <strong
                                        class="summary-green"
                                        id="invoice_cash_sales"
                                    >
                                        81
                                    </strong>

                                </div>


                                <!-- Credit Sales -->
                                <div class="invoice-summary-row">

                                    <span>
                                        Credit Sales
                                    </span>

                                    <strong
                                        class="summary-orange"
                                        id="invoice_credit_sales"
                                    >
                                        19
                                    </strong>

                                </div>


                                <!-- Mixed Payments -->
                                <div class="invoice-summary-row">

                                    <span>
                                        Mixed Payments
                                    </span>

                                    <strong
                                        class="summary-purple"
                                        id="invoice_mixed_payments"
                                    >
                                        42
                                    </strong>

                                </div>


                                <!-- Items Sold -->
                                <div class="invoice-summary-row no-border">

                                    <span>
                                        Items Sold
                                    </span>

                                    <strong
                                        class="summary-dark"
                                        id="invoice_items_sold"
                                    >
                                        376
                                    </strong>

                                </div>

                            </div>

                        </div>

                    </div> <!-- .daily-sales-section -->
                    <!-- =========================================================
                        Payment Collection + Cash Counter + Inventory Activity
                    ========================================================= -->

                    <div class="daily-operations-section <?= $cashCounterEnabled ? 'cash-counter-enabled' : 'cash-counter-disabled' ?>" >

                        <!-- =====================================================
                            Payment Collection
                        ====================================================== -->
                        <div class="daily-report-card payment-collection-card">

                            <div class="operation-card-header">

                                <div class="operation-title">

                                    <div class="operation-title-icon payment-icon">
                                        <i class="ti ti-credit-card"></i>
                                    </div>

                                    <h3>Payment Collection</h3>

                                </div>

                                <div class="payment-total">

                                    <span>Total:</span>

                                    <strong id="payment_collection_total">
                                        Rs. 286,450
                                    </strong>

                                </div>

                            </div>


                            <!-- Payment rows will be generated using JS -->
                            <div
                                class="payment-collection-list"
                                id="payment_collection_list"
                            ></div>

                        </div>



                        <?php if ($cashCounterEnabled): ?>

                            <!-- =================================================
                                Cash Counter
                            ================================================== -->
                            <div
                                class="daily-report-card cash-counter-card"
                                id="cash_counter_card"
                            >

                                <div class="operation-card-header">

                                    <div class="operation-title">

                                        <div class="operation-title-icon cash-icon">
                                            <i class="ti ti-cash"></i>
                                        </div>

                                        <h3>Cash Counter</h3>

                                    </div>

                                </div>


                                <div class="cash-counter-list">

                                    <div class="cash-counter-row">

                                        <span>Opening Cash</span>

                                        <strong id="counter_opening_cash">
                                            Rs. 15,000
                                        </strong>

                                    </div>


                                    <div class="cash-counter-row">

                                        <span>
                                            + Cash Sales
                                        </span>

                                        <strong id="counter_cash_sales">
                                            Rs. 148,700
                                        </strong>

                                    </div>


                                    <div class="cash-counter-row">

                                        <span>
                                            - Cash Expenses
                                        </span>

                                        <strong id="counter_cash_expenses">
                                            Rs. 9,250
                                        </strong>

                                    </div>


                                    <!-- System Cash -->
                                    <div class="cash-highlight system-cash">

                                        <span>System Cash</span>

                                        <strong id="counter_system_cash">
                                            Rs. 154,450
                                        </strong>

                                    </div>


                                    <!-- Physical Closing -->
                                    <div class="cash-highlight physical-closing">

                                        <span>Physical Closing</span>

                                        <strong id="counter_physical_closing">
                                            Rs. 154,200
                                        </strong>

                                    </div>


                                    <!-- Difference -->
                                    <div class="cash-counter-row cash-difference-row">

                                        <span>Difference</span>

                                        <strong
                                            id="counter_difference"
                                            class="cash-difference negative"
                                        >
                                            - Rs. 250
                                        </strong>

                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>



                        <!-- =====================================================
                            Inventory Activity
                        ====================================================== -->
                        <div class="daily-report-card inventory-activity-card">

                            <div class="operation-card-header">

                                <div class="operation-title">

                                    <div class="operation-title-icon inventory-icon">
                                        <i class="ti ti-package"></i>
                                    </div>

                                    <h3>Inventory Activity</h3>

                                </div>

                            </div>


                            <div class="inventory-stat-grid">

                                <!-- Items Sold -->
                                <div class="inventory-stat-box inventory-green">

                                    <span>Items Sold</span>

                                    <strong id="inventory_items_sold">
                                        376
                                    </strong>

                                </div>


                                <!-- GRN Received -->
                                <div class="inventory-stat-box inventory-blue">

                                    <span>GRN Received</span>

                                    <strong id="inventory_grn_received">
                                        124
                                    </strong>

                                </div>


                                <!-- Low Stock -->
                                <div class="inventory-stat-box inventory-orange">

                                    <span>Low Stock</span>

                                    <strong id="inventory_low_stock">
                                        18
                                    </strong>

                                </div>


                                <!-- Expiring Soon -->
                                <div class="inventory-stat-box inventory-red">

                                    <span>Expiring Soon</span>

                                    <strong id="inventory_expiring_soon">
                                        7
                                    </strong>

                                </div>

                            </div>


                            <!-- Stock Value -->
                            <div class="inventory-stock-value">

                                <div class="stock-value-icon">
                                    <i class="ti ti-database"></i>
                                </div>

                                <div>

                                    <span>
                                        Stock value at close:
                                    </span>

                                    <strong id="inventory_stock_value">
                                        Rs. 3,842,900
                                    </strong>

                                </div>

                            </div>

                        </div>

                    </div> <!-- .daily-operations-section -->
                    <!-- =========================================================
                        Top Products + Customers & Credit + Other Summary
                    ========================================================= -->

                    <div class="daily-bottom-summary-section">

                        <!-- =====================================================
                            Top Selling Products
                        ====================================================== -->
                        <div class="daily-report-card top-products-card">

                            <div class="bottom-summary-header">

                                <div class="bottom-summary-title">

                                    <div class="bottom-summary-icon products-icon">
                                        <i class="ti ti-star"></i>
                                    </div>

                                    <div>

                                        <h3>Top Selling Products</h3>

                                        <p>
                                            By sales value
                                        </p>

                                    </div>

                                </div>


                                <a
                                    href="javascript:void(0)"
                                    class="view-all-link"
                                    id="view_all_products"
                                >
                                    View all
                                </a>

                            </div>


                            <div class="top-products-table-wrapper">

                                <table class="top-products-table">

                                    <thead>

                                        <tr>

                                            <th class="product-number-col">
                                                #
                                            </th>

                                            <th>
                                                Product
                                            </th>

                                            <th class="text-center">
                                                Qty
                                            </th>

                                            <th class="text-right">
                                                Revenue
                                            </th>

                                            <th class="text-right">
                                                Profit
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody id="top_products_body">

                                        <!-- Sample Data -->

                                        <tr>

                                            <td>
                                                1
                                            </td>

                                            <td class="product-name">
                                                Milk Powder 400g
                                            </td>

                                            <td class="text-center">
                                                32
                                            </td>

                                            <td class="text-right">
                                                Rs. 39,840
                                            </td>

                                            <td class="text-right">
                                                Rs. 7,620
                                            </td>

                                        </tr>


                                        <tr>

                                            <td>
                                                2
                                            </td>

                                            <td class="product-name">
                                                Premium Rice 5kg
                                            </td>

                                            <td class="text-center">
                                                18
                                            </td>

                                            <td class="text-right">
                                                Rs. 31,500
                                            </td>

                                            <td class="text-right">
                                                Rs. 5,040
                                            </td>

                                        </tr>


                                        <tr>

                                            <td>
                                                3
                                            </td>

                                            <td class="product-name">
                                                Soft Drink 1.5L
                                            </td>

                                            <td class="text-center">
                                                47
                                            </td>

                                            <td class="text-right">
                                                Rs. 22,560
                                            </td>

                                            <td class="text-right">
                                                Rs. 6,110
                                            </td>

                                        </tr>


                                        <tr>

                                            <td>
                                                4
                                            </td>

                                            <td class="product-name">
                                                Laundry Powder
                                            </td>

                                            <td class="text-center">
                                                21
                                            </td>

                                            <td class="text-right">
                                                Rs. 18,690
                                            </td>

                                            <td class="text-right">
                                                Rs. 4,230
                                            </td>

                                        </tr>


                                        <tr>

                                            <td>
                                                5
                                            </td>

                                            <td class="product-name">
                                                Cooking Oil 1L
                                            </td>

                                            <td class="text-center">
                                                26
                                            </td>

                                            <td class="text-right">
                                                Rs. 16,900
                                            </td>

                                            <td class="text-right">
                                                Rs. 3,780
                                            </td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        </div>



                        <!-- =====================================================
                            Customers & Credit
                        ====================================================== -->
                        <div class="daily-report-card customer-credit-card">

                            <div class="bottom-summary-header">

                                <div class="bottom-summary-title">

                                    <div class="bottom-summary-icon customer-icon">
                                        <i class="ti ti-users"></i>
                                    </div>

                                    <h3>
                                        Customers & Credit
                                    </h3>

                                </div>

                            </div>


                            <div class="customer-credit-grid">

                                <!-- Walk-in Sales -->
                                <div class="customer-credit-box credit-green">

                                    <span>
                                        Walk-in Sales
                                    </span>

                                    <strong id="walk_in_sales">
                                        109
                                    </strong>

                                </div>


                                <!-- Registered Customers -->
                                <div class="customer-credit-box credit-blue">

                                    <span>
                                        Registered Customers
                                    </span>

                                    <strong id="registered_customers">
                                        33
                                    </strong>

                                </div>


                                <!-- Credit Given -->
                                <div class="customer-credit-box credit-orange">

                                    <span>
                                        Credit Given Today
                                    </span>

                                    <strong id="credit_given_today">
                                        Rs. 15,000
                                    </strong>

                                </div>


                                <!-- Credit Collected -->
                                <div class="customer-credit-box credit-purple">

                                    <span>
                                        Credit Collected
                                    </span>

                                    <strong id="credit_collected">
                                        Rs. 24,500
                                    </strong>

                                </div>

                            </div>


                            <div class="outstanding-credit-row">

                                <span>
                                    Outstanding customer credit
                                </span>

                                <strong id="outstanding_customer_credit">
                                    Rs. 348,600
                                </strong>

                            </div>

                        </div>



                        <!-- =====================================================
                            Right Side Summary Cards
                        ====================================================== -->
                        <div class="additional-summary-column">


                            <!-- Sales Returns -->
                            <div class="daily-report-card small-summary-card">

                                <div class="small-summary-icon returns-icon">
                                    <i class="ti ti-arrow-back-up"></i>
                                </div>

                                <div class="small-summary-content">

                                    <span class="small-summary-title">
                                        Sales Returns
                                    </span>

                                    <strong id="sales_returns_amount">
                                        Rs. 3,840
                                    </strong>

                                    <p>
                                        <span id="sales_returns_count">
                                            6
                                        </span>
                                        returned invoices
                                    </p>

                                </div>

                            </div>



                            <!-- Supplier Payments -->
                            <div class="daily-report-card small-summary-card">

                                <div class="small-summary-icon supplier-icon">
                                    <i class="ti ti-truck-delivery"></i>
                                </div>

                                <div class="small-summary-content">

                                    <span class="small-summary-title">
                                        Supplier Payments
                                    </span>

                                    <strong id="supplier_payment_amount">
                                        Rs. 42,000
                                    </strong>

                                    <p>
                                        <span id="supplier_payment_count">
                                            3
                                        </span>
                                        settlements today
                                    </p>

                                </div>

                            </div>



                            <!-- Discounts Given -->
                            <div class="daily-report-card small-summary-card">

                                <div class="small-summary-icon discount-icon">
                                    <i class="ti ti-tag"></i>
                                </div>

                                <div class="small-summary-content">

                                    <span class="small-summary-title">
                                        Discounts Given
                                    </span>

                                    <strong id="discount_given_amount">
                                        Rs. 5,620
                                    </strong>

                                    <p>
                                        <span id="discount_percentage">
                                            1.9%
                                        </span>
                                        of gross sales
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div> <!-- .daily-bottom-summary-section -->
                    <!-- =========================================================
                        Day Closing Snapshot
                    ========================================================= -->

                    <div class="day-closing-snapshot">

                        <div class="day-closing-left">

                            <div class="day-closing-icon">
                                <i class="ti ti-check"></i>
                            </div>

                            <div class="day-closing-content">

                                <h3>
                                    Day Closing Snapshot
                                </h3>

                                <p>
                                    Summary generated for
                                    <span id="summary_generated_date">
                                        21 Sep 2026
                                    </span>.
                                </p>

                            </div>

                        </div>


                        <div class="day-closing-actions">

                            <button
                                type="button"
                                class="day-summary-btn btn-print-summary"
                                id="btn_print_daily_summary"
                            >

                                <i class="ti ti-printer"></i>

                                <span>
                                    Print Summary
                                </span>

                            </button>


                            <button
                                type="button"
                                class="day-summary-btn btn-detailed-report"
                                id="btn_detailed_daily_report"
                            >

                                <i class="ti ti-file-description"></i>

                                <span>
                                    Detailed Report
                                </span>

                            </button>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <!-- footer Start  -->
    <?php include '../View/footer.php';?> 
    <script src="../Assets/js/chart2.js"></script>
    <script src="../Assets/jquery/daily-summary.js?v=1"></script>
    <!-- footer End  -->
</body>
</html>