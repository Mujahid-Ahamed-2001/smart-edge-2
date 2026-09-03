<?php 
  session_start();
  if(isset($_SESSION["user_id"]) && isset($_SESSION["user"]))
  {
    header("Location:../Public/dashboard.php");
  }
?>
<!doctype html>
<html lang="en" data-skin="dark">

<head>
  <?php 
  include '../View/loginhead.php';
  // print_r($_SESSION);
  ?>
  <style>
    .card {
    /* margin-bottom: 30px !important; */
        background: #ffffffe8 !important;
    }
    .radial-gradient:before
    {
      background: none !important;
    }
  </style>
</head>

<body data-sidebartype="full">
  <!-- Preloader -->
  <div class="preloader" style="display: none;">
    <img src="../Assets/images/logos/favicon.png" alt="loader" class="lds-ripple img-fluid">
  </div>
  <div id="main-wrapper" class="auth-customizer-none">
    <div class="position-relative overflow-hidden radial-gradient min-vh-100 w-100">
      <div class="position-relative z-index-5">
        <div class="row">
          <div class="col-xl-8 col-xxl-8" style="background-image: url('../Assets/Images/SystemImages/Backgrounds/background-login.png'); background-size:cover;">
          </div>
          <div class="col-xl-4 col-xxl-4 dark_background">
            <div class="authentication-login min-vh-100 bg-body row justify-content-center align-items-center p-4 dark_background">
              <div class="auth-max-width col-sm-8 col-md-6 col-xl-7 px-4 w-100">
                <h2 class="mb-1 fs-7 fw-bolder ">Welcome to Smart Edge</h2>
                <p class="mb-7 ">Your Admin Dashboard</p>
                <?php  
                if(isset($_SESSION['user_id']))
                {
                  header("Location:../Public/dashboard.php");
                }
                else if(isset($_COOKIE['remember_me_Smart_edge']))
                {
                  $user_id = $_COOKIE['remember_me'];
                  $_SESSION['user_id'] = $user_id;
                  header("Location:../Public/dashboard.php");

                }
                if (isset($_SESSION['expired'])) 
                {
                  ?>
                  <p class="text-center" style="color:#ff0000; font-weight:bold;">Company Expired. Please Contact Next Edge Solutions.</p>
                  <?php
                  unset($_SESSION["expired"]);
                }
                if (isset($_SESSION['user_error'])) 
                {
                  if ($_SESSION['user_error']==1) 
                  {
                    ?>
                    <p class="text-center" style="color:#ff0000; font-weight:bold;">Access Denied, You have no Permssion to that page</p>
                    <?php
                  }
                  elseif ($_SESSION['user_error']==2) 
                  {     
                    ?>
                    <p class="text-center" style="color:#ff0000; font-weight:bold;">No username/email found</p>
                    <?php
                  }
                  elseif ($_SESSION['user_error']==3) 
                  {
                    ?>
                    <p class="text-center" style="color:#ff0000; font-weight:bold;">Incorrect password</p>
                    <?php
                  }
                  elseif ($_SESSION['user_error']==7) 
                  {
                    ?>
                    <p class="text-center" style="color:#ff0000; font-weight:bold;">Inactive userrole, Please contact your system Admin</p>
                    <?php
                  }
                  elseif ($_SESSION['user_error']==8) 
                  {
                    ?>
                    <p class="text-center" style="color:#ff0000; font-weight:bold;">Inactive user, Please contact your system Admin</p>
                    <?php
                  }
                  else
                  {
                    ?>
                    <p class="text-center" style="color:#ff0000; font-weight:bold;">Oops! Something went wrong</p>
                    <?php
                  }
                  unset($_SESSION['user_error']);
                }
                ?>
                <form action="../Controller/userController.php" method="POST">
                  <div class="mb-3">
                    <label for="user_name" class="form-label">Username/email</label>
                    <input type="text" name="user_name" class="form-control" id="user_name" maxlength="50" required>
                  </div>
                  <div class="mb-4">
                    <label for="user_pwd" class="form-label">Password</label>
                    <input type="password" class="form-control" id="user_pwd" name="user_pwd" required>
                  </div>
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <!-- <div class="form-check">
                      <input class="form-check-input primary" name="remember_me" type="checkbox" id="remember_me">
                      <label class="form-check-label text-dark" for="remember_me">
                        Remeber this device
                      </label>
                    </div> -->
                    <div class="form-check">
                      <input class="form-check-input primary" type="checkbox" id="show_password">
                      <label class="form-check-label" for="show_password">
                        Show Password
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input primary" type="checkbox" id="remember_me" name="remember_me">
                      <label class="form-check-label" for="remember_me">
                        Remember Me
                      </label>
                    </div>
                  </div>
                  <input type="submit" class="btn btn-primary w-100 py-8 mb-4 rounded-2" value="Sign In" name="btn_log_in">
                  <div class="d-flex align-items-center justify-content-center">
                    <p class="fs-4 mb-0 fw-medium">Forgot Password?&nbsp;</p>
                    <a class="text-primary fw-medium fs-3" href="../Public/forget-password.php">Click Here</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
      function handleColorTheme(e) {
        document.documentElement.setAttribute("data-color-theme", e);
      }
    </script>
  </div>
  <div class="dark-transparent sidebartoggler"></div>
  <script src="../Assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../Assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    $(document).ready(function () 
    {
      $('#show_password').click(function () 
      {
          if ($('#user_pwd').prop("type")=="text") 
          {
            $('#user_pwd').prop("type","password")
          }
          else
          {
            $('#user_pwd').prop("type","text")
          }
      });
    });
  </script>
  <!-- Import Js Files -->
  <!-- <script src="../Assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../Assets/libs/simplebar/dist/simplebar.min.js"></script>
  <script src="../Assets/js/theme/app.init.js"></script>
  <script src="../Assets/js/theme/theme.js"></script>
  <script src="../Assets/js/theme/app.min.js"></script> -->

  <!-- solar icons -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script> -->


</body>
</html>