<?php 
include '../Includes/includes.php';
include '../Includes/authcheck.php';

$shop_id = $_SESSION['shop_id'];

?>
<!doctype html>
<html lang="en">
<head>
  <?php 
  $title = "Profit/Loss Report | Smart Edge | Powered By Next Edge";
  include '../View/head.php';
  // include '../View/loader.php';
  ?>
  <link rel="stylesheet" href="../Assets/css/profitnloss.css">
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
                <input type="hidden" name="" id="title" value="Profit/Loss Report">
                <!--  Header End -->

                <div class="container-fluid py-4">
                    <!-- PnL Dashboard Header -->
                    <section class="pnl-dashboard-head">

                        <div class="pnl-head-row">

                            <div class="pnl-heading">
                                <h1>Profit/Loss Dashboard</h1>

                                <div class="pnl-breadcrumb">
                                    <a href="javascript:void(0)">Reports</a>
                                    <i class="ti ti-chevron-right"></i>
                                    <a href="javascript:void(0)">Accounts</a>
                                    <i class="ti ti-chevron-right"></i>
                                    <a href="javascript:void(0)">Profit/Loss</a>
                                </div>
                            </div>

                            <div class="pnl-head-actions">

                                <?php
                                $start_date = date('Y-m-01');
                                $end_date   = date('Y-m-t');
                                ?>

                                <div class="pnl-date-filter">
                                    <i class="ti ti-calendar pnl-date-icon"></i>
                                    <input type="date" id="pnl_start_date" name="pnl_start_date" class="pnl-date-input" value="<?= $start_date ?>"  >

                                    <span class="pnl-date-separator">-</span>
                                    <i class="ti ti-calendar pnl-date-icon"></i>
                                    <input type="date" id="pnl_end_date" name="pnl_end_date" class="pnl-date-input" value="<?= $end_date ?>">
                                    <button type="button" id="refreshpnlDashboard" class="pnl-date-refresh" title="Refresh dashboard" >
                                        <i class="ti ti-refresh"></i>
                                    </button>

                                </div>
                            </div>

                        </div>

                        <!-- pnl Summary Cards -->
                        <div class="pnl-summary-grid">

                            <!-- Total pnls -->
                            <article class="pnl-summary-card">
                                <div class="summary-icon summary-icon-purple">
                                    <i class="ti ti-wallet"></i>
                                </div>

                                <div class="summary-content">
                                    <span class="summary-label">Total Revenue</span>
                                    <h2 class="getCommon" id="totRevenue">Rs. 0.00</h2>
                                </div>
                            </article>

                            <!-- This Month -->
                            <article class="pnl-summary-card">
                                <div class="summary-icon summary-icon-pink">
                                    <i class="ti ti-receipt"></i>
                                </div>

                                <div class="summary-content">
                                    <span class="summary-label">This Cost of Goods Sold</span>
                                    <h2 class="getCommon" id="totCost">Rs.  0.00</h2>
                                </div>
                            </article>

                            <!-- This Week -->
                            <article class="pnl-summary-card">
                                <div class="summary-icon summary-icon-green">
                                    <i class="ti ti-calendar"></i>
                                </div>

                                <div class="summary-content">
                                    <span class="summary-label">Gross Profit</span>
                                    <h2 class="getCommon" id="grossProfit">Rs.  0.00</h2>
                                </div>
                            </article>

                            <!-- Today -->
                            <article class="pnl-summary-card">
                                <div class="summary-icon summary-icon-orange">
                                    <i class="ti ti-wallet"></i>
                                </div>

                                <div class="summary-content">
                                    <span class="summary-label">Net Profit/Loss</span>
                                    <h2 class="getCommon" id="netProfit">Rs.  0.00</h2>
                                </div>
                            </article>

                        </div>
                    </section>
                    <!-- pnl Management Charts -->
                    <section class="pnl-analytics-grid">
                        <!-- pnls Trend -->
                        <div class="pnl-analytics-card">

                            <div class="analytics-card-header">
                                <h3>Profit/Loss Trend</h3>

                            </div>

                            <div class="pnl-trend-wrapper">
                                <canvas id="pnlTrendChart"></canvas>
                            </div>

                        </div>

                    </section>   
                    <div class="pnl-card">
                        <!-- Header -->
                        <div class="pnl-card-header">
                            <div class="pnl-title">
                                <div class="pnl-icon">
                                    <i class="ti ti-receipt-2"></i>
                                </div>
                                <div>
                                    <h3>Profit/Loss <a href="javascript:void(0)" id="refresh"><i class="ti ti-reload"></i></a></h3>
                                    <p>Manage Profit/Loss</p>
                                </div>

                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="card-divider"></div>

                        <!-- Table -->
                        <div class="table-responsive">

                            <table class="table pnl-table align-middle" id="tbl_pnls">
                                <thead>
                                    <tr>
                                        <th width="80">ID</th>
                                        <th width="700">Item</th>
                                        <th width="450" style=" text-align:right;">Amount</th>
                                        <th width="450" style=" text-align:right;">Amount</th>
                                        <th width="50" style=" text-align:right;"></th>
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
                    <script src="../Assets/jquery/profitnloss.js"></script>
                    <!-- footer End  -->
                </div>
            </div>
        </div>
    </div>
    <!--  Body Wrapper End -->

</body>
</html>