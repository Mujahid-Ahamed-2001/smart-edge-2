<?php 
include '../Includes/includes.php';
include '../Includes/authcheck.php';
?>
<!doctype html>
<html lang="en">

<head>
  <?php
  include '../View/head.php';
  ?>
  <link rel="stylesheet" href="../Assets/css/analytics.css">
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper mini-sidebar show-sidebar" id="main-wrapper" >
    <!-- Sidebar Start -->
    <?php 
    include '../View/sidebar.php';
    include '../View/loader.php';
    ?>
    
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
      <?php 
      include '../View/header.php';
      ?>
      <!--  Header End -->
      <div class="container-fluid">
        <!-- Analytics Header -->
        <div class="analytics-header">

            <div>
                <h2 class="analytics-title">
                    Analytics Overview
                </h2>

                <p class="analytics-subtitle">
                    Monitor sales, inventory performance and business insights.
                </p>
            </div>

            <div class="analytics-filter">

                <i class="ti ti-calendar"></i>
                <form action="" id="formCommon" class="row align-items-end">
                  <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" id="startDate" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" id="endDate" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                  </div>                      
                </form>               

            </div>

        </div>
        <div class="analytics-stats">
          <!-- Revenue -->
          <div class="stat-card">

              <div class="stat-top">

                  <div class="stat-icon blue">
                    <i class="ti ti-cash-banknote"></i>
                  </div>

                  <div style="width: calc(100% - 58px);">

                      <div class="stat-title">
                          Total Revenue
                      </div>

                      <p class="stat-value" id="tot-revenue">
                          Rs. 0.00
                      </p>

                  </div>

              </div>
          </div>

          <!-- Cost -->
          <div class="stat-card">

              <div class="stat-top">

                  <div class="stat-icon green">
                      <i class="ti ti-shopping-cart"></i>
                  </div>

                  <div style="width: calc(100% - 58px);">

                      <div class="stat-title">
                          Total Cost
                      </div>

                      <p class="stat-value" id="tot-cost">
                          Rs. 0.00
                      </p>

                  </div>

              </div>

          </div>

          <!-- Profit -->
          <div class="stat-card">

              <div class="stat-top">

                  <div class="stat-icon purple">
                      <i class="ti ti-chart-bar"></i>
                  </div>

                  <div style="width: calc(100% - 58px);">

                      <div class="stat-title">
                          Gross Profit
                      </div>

                      <p class="stat-value" id="tot-gp">
                          Rs. 0.00
                      </p>

                  </div>

              </div>

          </div>

          <!-- Orders -->
          <div class="stat-card">

              <div class="stat-top">

                  <div class="stat-icon orange">
                    <i class="ti ti-receipt"></i>
                  </div>

                  <div style="width: calc(100% - 58px);">

                      <div class="stat-title">
                          Total Orders
                      </div>

                      <p class="stat-value" id="tot-orders">
                          0
                      </p>

                  </div>

              </div>

          </div>

          <!-- Low Stock -->
          <div class="stat-card">

              <div class="stat-top">

                  <div class="stat-icon red">
                      <i class="ti ti-alert-circle"></i>
                  </div>

                  <div style="width: calc(100% - 58px);">

                      <div class="stat-title">
                          Low Stock Items
                      </div>

                      <p class="stat-value" id="tot-lowstock">
                          0
                      </p>

                  </div>

              </div>

          </div>

      </div>
        <!--  Sales Overview -->
        <div class="row">
          <div class="col-lg-8 d-flex align-items-strech">
            <div class="card w-100">
              <div class="card-body">
                <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                  <div class="mb-3 mb-sm-0 w-30">
                    <h5 class="card-title fw-semibold">Sales Overview</h5>
                  </div>
                  <div class="mb-3 mb-sm-0 w-50">
                    <form action="" id="formGetSales" class="row align-items-end">
                      <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" id="startDate" class="form-control" required>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" id="endDate" class="form-control" required>
                      </div>
                      <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                      </div>                      
                    </form>
                  </div>
                </div>
                <div id="chart"></div>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="row">
              <div class="col-lg-12">
                <!-- Top Selling Products -->
                <div class="card overflow-hidden">
                  <div class="card-body p-4">
                    <h5 class="card-title mb-9 fw-semibold">Top Selling Products</h5>
                    <div class="row align-items-center">
                      <div class="mb-9 mb-sm-0 col-md-12">
                        <form action="" id="formsellingProducts" class="row align-items-end">
                          <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" id="startDate" class="form-control" required>
                          </div>
                          <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" id="endDate" class="form-control" required>
                          </div>
                          <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                          </div>                      
                        </form>
                      </div>
                      <div class="col-12">
                        <div class="d-flex justify-content-center">
                          <div id="pieChart" style="width: 180px; height: 180px;"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <!-- Monthly Earnings -->
                <div class="card">
                  <div class="card-body">
                    <div class="row align-items-start">
                      <h5 class="card-title mb-9 fw-semibold"> Top Selling Sub-Categories</h5>
                      <div class="mb-9 mb-sm-0 col-md-12">
                        <form action="" id="formsellingSubCate" class="row align-items-end">
                          <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" id="startDate" class="form-control" required>
                          </div>
                          <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" id="endDate" class="form-control" required>
                          </div>
                          <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                          </div>                      
                        </form>
                      </div>
                      <div class="col-12">
                        <div class="d-flex justify-content-center">
                          <div id="pieChart2" style="width: 180px; height: 180px;"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12 d-flex align-items-stretch">
            <div class="card w-100">
              <div class="card-body p-4">
                <div class="row align-items-start">
                  <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                    <div class="mb-3 mb-sm-0 w-70">
                      <h5 class="card-title fw-semibold">Cost vs Revenue</h5>
                    </div>
                    <div class="mb-3 mb-sm-0 w-30">
                      <form action="" id="formGetRevenue" class="row align-items-end">
                        <div class="col-md-4">
                          <label class="form-label">Start Date</label>
                          <input type="date" id="startDate2" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                          <label class="form-label">End Date</label>
                          <input type="date" id="endDate2" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                          <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>                      
                      </form>
                    </div>
                  </div>
                </div>
                <div id="revenueChart"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12 d-flex align-items-stretch">
            <div class="card w-100">
              <div class="card-body p-4">
                <div class="row align-items-start">
                  <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                    <div class="mb-3 mb-sm-0 w-70">
                      <h5 class="card-title fw-semibold">Sales by Payment Method</h5>
                    </div>
                    <div class="mb-3 mb-sm-0 w-30">
                      <form action="" id="formGetPaymentMethod" class="row align-items-end">
                        <div class="col-md-4">
                          <label class="form-label">Start Date</label>
                          <input type="date" id="startDate3" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                          <label class="form-label">End Date</label>
                          <input type="date" id="endDate3" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                          <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>                      
                      </form>
                    </div>
                  </div>
                </div>
                <div id="salesPaymentChart"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12 d-flex align-items-stretch">
            <div class="card w-100">
              <div class="card-body p-4">
                <div class="row align-items-start">
                  <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                    <div class="mb-3 mb-sm-0 w-70">
                      <h5 class="card-title fw-semibold">Low-Stock Alerts</h5>
                    </div>
                  </div>
                </div>
                <div id="lowStockChart"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-6 d-flex align-items-stretch">
            <div class="card w-100">
              <div class="card-body p-4">
                <div class="row align-items-start">
                  <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                    <div class="mb-3 mb-sm-0 w-70">
                      <h5 class="card-title fw-semibold">Top Moving Items</h5>
                    </div>
                  </div>
                </div>
                <div id="topMovingChart"></div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 d-flex align-items-stretch">
            <div class="card w-100">
              <div class="card-body p-4">
                <div class="row align-items-start">
                  <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                    <div class="mb-3 mb-sm-0 w-70">
                      <h5 class="card-title fw-semibold">Slow Moving Items</h5>
                    </div>
                  </div>
                </div>
                <div id="slowMovingChart"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="../Assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../Assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../Assets/js/sidebarmenu.js"></script>
  <script src="../Assets/js/app.min.js"></script>
  <script src="../Assets/libs/apexcharts/dist/apexcharts.min.js"></script>
   <!-- ApexCharts CSS and JS -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> -->
  <script src="../Assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="../Assets/js/dashboard.js"></script>
</body>

</html> 