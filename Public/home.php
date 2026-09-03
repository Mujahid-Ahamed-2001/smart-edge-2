<?php 
error_reporting(E_ALL);
error_reporting(-1);
ini_set('error_reporting', E_ALL);
include '../Includes/includes.php';
include '../Includes/authcheck.php';
?>
<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="dark" data-color-theme="Blue_Theme" data-layout="vertical" data-boxed-layout="boxed" data-card="shadow">

<head>
  <?php
  include '../View/head.php';
  // if(isset($_SESSION["loading"]))
  // {
    include '../View/loader.php';
    // unset($_SESSION["loading"]);
  // }
  $shopObj = new Shop();
  $shop_id = $_SESSION['shop_id'];
  $shopData = $shopObj->getOneShop($shop_id);         
  // echo $shopData;
  $company_logo = $shopData[0]['ComLogo'];
  $shop_id = $_SESSION['shop_id'];
  ?>
  <link rel="stylesheet" href="../Assets/css/dashboard.css">
</head>

<body>
  <!--  Body Wrapper -->
    <div class="h-100vh">
        <div class="page-wrapper mini-sidebar show-sidebar" id="main-wrapper" >
            <!-- Sidebar Start -->
            <?php 
            include '../View/sidebar.php';
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
                  <?php 
                  // $_SESSION['status']=1;
                  if (isset($_SESSION['status'])) 
                  {
                    if ($_SESSION['status']==1) 
                    {
                      ?>
                      <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show" role="alert">
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        <strong>Counter Started</strong> Successfully
                      </div>
                      <?php
                    }
                    else if ($_SESSION['status']==2) 
                    {
                      ?>
                      <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show" role="alert">
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        <strong>Counter Closed</strong> Successfully
                      </div>
                      <?php
                    }
                    else if ($_SESSION['status']==3) 
                    {
                      ?>
                      <div class="alert alert-warning alert-dismissible bg-warning text-white border-0 fade show" role="alert">
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        Please start a new <strong>Cash Counter</strong>.
                      </div>
                      <?php
                    }
                    else if ($_SESSION['status']==4) 
                    {
                      ?>
                      <div class="alert alert-warning alert-dismissible bg-warning text-white border-0 fade show" role="alert">
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        Please add <strong>payment methods</strong> to the shop.
                      </div>
                      <?php
                    }//no paymethods
                    else if ($_SESSION['status']==5) 
                    {
                      ?>
                      <div class="alert alert-warning alert-dismissible bg-warning text-white border-0 fade show" role="alert">
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        Please add <strong>Sale Settings</strong> to the shop.
                      </div>
                      <?php
                    }//no sale settings
                    else if ($_SESSION['status']==6) 
                    {
                      ?>
                      <div class="alert alert-warning alert-dismissible bg-warning text-white border-0 fade show" role="alert">
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        Please add <strong>Receipt Settings</strong> to the shop.
                      </div>
                      <?php
                    }//no sale settings
                    else
                    {
                      ?>
                      <div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show" role="alert">
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        <strong>Error - </strong> Something went wrong, Please try again!
                      </div>
                      <?php
                    }
                    unset($_SESSION['status']);
                  }//counter status

                  if(isset($_SESSION['user_error']))
                  {
                    if ($_SESSION['user_error']==4) 
                    {
                      ?>
                      <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show" role="alert">
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        <strong>Password Updated!</strong>Successfully
                      </div>
                      <?php
                    }
                    else if ($_SESSION['user_error']==5) 
                    {
                      ?>
                      <div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show" role="alert">
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        <strong>Oops!Something Went Wrong,</strong>Please Try Again
                      </div>
                      <?php
                    }
                    unset($_SESSION['user_error']);
                  }
                  ?>   
                  <div class="dashboard-home">
                    <div class="welcome-section">
                        <div class="welcome-content">
                            <span class="welcome-badge">
                                Welcome to Smart Edge
                            </span>
                            <h1>
                                Welcome back,
                                <span><?=$user_name?></span>
                                👋
                            </h1>
                            <p>
                                Use the sidebar to access the modules available for your account.
                                Your dashboard is customized based on your permissions.
                            </p>
                        </div>
                    </div>
                    <div class="home-info-card">
                        <div class="home-info-left">
                            <div class="home-info-icon">
                                <i class="ti ti-lock"></i>
                            </div>
                            <div>
                                <h4>Welcome to Smart Edge</h4>
                                <p>
                                    We're glad to have you here.
                                    Start by using the sidebar to access the tools
                                    and modules available to your account.
                                </p>
                            </div>
                        </div>
                        <div class="home-info-right">
                            <img src="../Assets/Images/SystemImages/Backgrounds/home-icon.png">
                        </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
    
  <script>
    $(document).ready(function(){
      $("#headerCollapse2").trigger("click");
    })
    $("#headerCollapse2").trigger("click");
  </script>
  <?php 
  include "../View/footer.php";
  ?>
</body>

</html>