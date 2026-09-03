<?php 
include '../Includes/includes.php';
include '../Includes/authcheck.php';

$shop_id = $_SESSION['shop_id'];

?>
<!doctype html>
<html lang="en">
<head>
  <?php 
  include '../View/head.php';
  // include '../View/loader.php';
  ?>
  <link rel="stylesheet" href="../Assets/css/AddExpenses.css">
</head>
<body>
    <div id="modal"></div>
    <!--  Body Wrapper -->
    <div class="h-100vh">
        <div class="page-wrapper" id="main-wrapper">
            <!-- Sidebar Start -->
            <?php 
            include '../View/sidebar.php';
            $feature_id=13;
            include '../Includes/viewPermission.php';
            ?>
            <!--  Sidebar End -->
            <!--  Main wrapper -->
            <div class="body-wrapper">
                <!--  Header Start -->
                <?php 
                include '../View/header.php';
                ?>
                <?php 
                $shop = $shopObj->getOneShop($shop_id);
                ?>
                <?php $today = date('d-m-Y');?>
                <input type="hidden" name="" id="today" value="<?= $today ?>">
                <input type="hidden" name="" id="create_access" value="<?= $create ?>">
                <input type="hidden" name="" id="view_access" value="<?= $view ?>">
                <input type="hidden" name="" id="edit_access" value="<?= $edit ?>">
                <input type="hidden" name="" id="delete_access" value="<?= $delete ?>">
                <input type="hidden" name="" id="verify_access" value="<?= $verify ?>">
                <input type="hidden" name="" id="print_access" value="<?= $print ?>">
                <input type="hidden" name="" id="userType" value="<?= $userType ?>">
                <input type="hidden" name="" id="shop_name" value="<?=$shop[0]['ShopName']?>">
                <input type="hidden" name="" id="shop_id" value="<?=$shop_id?>">
                <input type="hidden" name="" id="shop_address_one" value="<?=$shop[0]['AddressLineOne']?> ">
                <input type="hidden" name="" id="shop_address_two" value="<?=$shop[0]['AddressLineTwo']?>">
                <input type="hidden" name="" id="shop_city" value="<?=$shop[0]['City']?>">
                <input type="hidden" name="" id="shop_number" value="<?=$shop[0]['PhoneNumber']?> ">
                <input type="hidden" name="" id="title" value="Expenses Report">
                <!--  Header End -->

                <div class="container-fluid py-4">
                    <!-- Expenses Dashboard Header -->
                    <section class="expenses-dashboard-head">

                        <div class="expenses-head-row">

                            <div class="expenses-heading">
                                <h1>Expenses Dashboard</h1>

                                <div class="expenses-breadcrumb">
                                    <a href="../View/dashboard.php">Home</a>
                                    <i class="ti ti-chevron-right"></i>

                                    <a href="javascript:void(0)">Expenses</a>
                                    <i class="ti ti-chevron-right"></i>

                                    <span>Dashboard</span>
                                </div>
                            </div>

                            <div class="expenses-head-actions">

                                <?php
                                $start_date = date('Y-m-01');
                                $end_date   = date('Y-m-t');
                                ?>

                                <div class="expense-date-filter">
                                    <i class="ti ti-calendar expense-date-icon"></i>
                                    <input type="date" id="expense_start_date" name="expense_start_date" class="expense-date-input" value="<?= $start_date ?>" max="<?= $end_date ?>" >

                                    <span class="expense-date-separator">-</span>
                                    <i class="ti ti-calendar expense-date-icon"></i>
                                    <input type="date" id="expense_end_date" name="expense_end_date" class="expense-date-input" value="<?= $end_date ?>" min="<?= $start_date ?>" >
                                    <button type="button" id="refreshExpenseDashboard" class="expense-date-refresh" title="Refresh dashboard" >
                                        <i class="ti ti-refresh"></i>
                                    </button>

                                </div>

                                <?php if ($create == 1) { ?>
                                    <a href="../View/modals/expenses.php?condition=new&ref=AddExpense" class="expense-add-button open-modal" data-title="Add Expense" >
                                        <i class="ti ti-plus"></i>
                                        <span>Add Expense</span>
                                    </a>
                                <?php } ?>

                            </div>

                        </div>

                        <!-- Expense Summary Cards -->
                        <div class="expense-summary-grid">

                            <!-- Total Expenses -->
                            <article class="expense-summary-card">
                                <div class="summary-icon summary-icon-purple">
                                    <i class="ti ti-wallet"></i>
                                </div>

                                <div class="summary-content">
                                    <span class="summary-label">Total Expenses</span>
                                    <h2>Rs. 485,750.00</h2>

                                    <div class="summary-comparison summary-increase">
                                        <i class="ti ti-trending-up"></i>
                                        <span><strong>12.5%</strong> from Apr 01 - Apr 30</span>
                                    </div>
                                </div>
                            </article>

                            <!-- This Month -->
                            <article class="expense-summary-card">
                                <div class="summary-icon summary-icon-pink">
                                    <i class="ti ti-receipt"></i>
                                </div>

                                <div class="summary-content">
                                    <span class="summary-label">This Month</span>
                                    <h2>Rs. 485,750.00</h2>

                                    <div class="summary-comparison summary-neutral">
                                        <i class="ti ti-minus"></i>
                                        <span><strong>0%</strong> from Apr 01 - Apr 30</span>
                                    </div>
                                </div>
                            </article>

                            <!-- This Week -->
                            <article class="expense-summary-card">
                                <div class="summary-icon summary-icon-green">
                                    <i class="ti ti-calendar"></i>
                                </div>

                                <div class="summary-content">
                                    <span class="summary-label">This Week</span>
                                    <h2>Rs. 98,250.00</h2>

                                    <div class="summary-comparison summary-increase">
                                        <i class="ti ti-trending-up"></i>
                                        <span><strong>8.3%</strong> from last week</span>
                                    </div>
                                </div>
                            </article>

                            <!-- Today -->
                            <article class="expense-summary-card">
                                <div class="summary-icon summary-icon-orange">
                                    <i class="ti ti-wallet"></i>
                                </div>

                                <div class="summary-content">
                                    <span class="summary-label">Today</span>
                                    <h2>Rs. 12,350.00</h2>

                                    <div class="summary-comparison summary-increase">
                                        <i class="ti ti-trending-up"></i>
                                        <span><strong>3.2%</strong> from yesterday</span>
                                    </div>
                                </div>
                            </article>

                        </div>
                    </section>
                    <!-- Expense Management Charts -->
                    <section class="expense-analytics-grid">

                        <!-- Expenses Overview -->
                        <div class="expense-analytics-card">

                            <div class="analytics-card-header">
                                <h3>Expenses Overview</h3>

                                <select class="analytics-filter" id="expenseOverviewFilter">
                                    <option value="1">By Category</option>
                                    <option value="2">By Type</option>
                                </select>
                            </div>

                            <div class="expense-overview-body">

                                <div class="expense-donut-wrapper">
                                    <canvas id="expenseOverviewChart"></canvas>

                                    <div class="expense-donut-center">
                                        <span>Total</span>
                                        <strong id="expenseOverviewTotal">Rs. 485,750</strong>
                                    </div>
                                </div>

                                <div class="expense-chart-legend" id="expenseChartLegend">
                                    <!-- Generated using JavaScript -->
                                </div>

                            </div>

                        </div>

                        <!-- Expenses Trend -->
                        <div class="expense-analytics-card">

                            <div class="analytics-card-header">
                                <h3>Expenses Trend</h3>

                            </div>

                            <div class="expense-trend-wrapper">
                                <canvas id="expenseTrendChart"></canvas>
                            </div>

                        </div>

                    </section>   
                    <div class="expense-card">
                        <!-- Header -->
                        <div class="expense-card-header">
                            <div class="expense-title">
                                <div class="expense-icon">
                                    <i class="ti ti-receipt-2"></i>
                                </div>
                                <div>
                                    <h3>Expenses <a href="javascript:void(0)" id="refresh"><i class="ti ti-reload"></i></a></h3>
                                    <p>Create and manage expenses</p>
                                </div>

                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="card-divider"></div>

                        <!-- Table -->
                        <div class="table-responsive">

                            <table class="table expense-table align-middle" id="tbl_expenses">
                                <thead>
                                    <tr>
                                        <th width="80">ID</th>
                                        <th width="450">Expense</th>
                                        <th width="350">Expense Category</th>
                                        <th width="150">Date</th>
                                        <th width="150">Status</th>
                                        <th width="150">Default</th>
                                        <th width="150">Created By</th>
                                        <th width="150">Created At</th>
                                        <th width="250">Modified By</th>
                                        <th width="150">Modified At</th>
                                        <th width="250">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>

                            </table>

                        </div>
                    </div>

                    <!-- footer Start  -->
                    <?php include '../View/footer.php';?> 
                    <script src="../Assets/js/chart.js"></script>
                    <script src="../Assets/jquery/AddExpenses2.js"></script>
                    <!-- footer End  -->
                </div>
            </div>
        </div>
    </div>
    <!--  Body Wrapper End -->

</body>
</html>