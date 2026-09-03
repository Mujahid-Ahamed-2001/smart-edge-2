
<?php 
  $shopObj = new Shop();
  $shopData = $shopObj->getOneShop($_SESSION['shop_id']);         
  $companyData = $shopObj->getCompanyONE($shopData[0]['Company_CMID']);         
  $company_logo = $shopData[0]['ComLogo'];
  $shop_name = $shopData[0]['ShopName'];
?>

<style>
  </style>
<header class="app-header">
  <nav class="navbar navbar-expand-lg navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item d-block">
        <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse2" href="javascript:void(0)" style="display:block !important;">
          <i class="ti ti-menu-2"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link nav-icon-hover " href="javascript:void(0)">
          <i class="ti ti-bell-ringing"></i>
          <div class="notification bg-primary rounded-circle"></div>
        </a>
      </li>
    </ul>
    <ul class="navbar-nav quick-links d-none d-lg-flex align-items-center">
      <!-- ------------------------------- -->
      <!-- start apps Dropdown -->
      <!-- ------------------------------- -->
      <li class="nav-item nav-icon-hover-bg rounded w-auto dropdown d-none d-lg-block mx-0">
        <div class="hover-dd">
          <a class="nav-link" href="javascript:void(0)">
            Quick Links<span class="mt-1">
              <i class="ti ti-chevron-down fs-3"></i>
            </span>
          </a>
          <div class="dropdown-menu dropdown-menu-nav dropdown-menu-animate-up py-0">
            <div class="row">
              <div class="col-12">
                <div class="ps-7 pt-7">
                  <div class="border-bottom">
                    <div class="row">
                      <div class="col-6">
                        <div class="position-relative">
                          <?php 
                          if($shopObj->hasRetailShop($shop_id))
                          {
                            ?>
                            <a href="../Public/gui-pos.php" class="d-flex align-items-center pb-9 position-relative" target="_blank">
                              <div class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                <img src="../Assets/Images/icons/cashier.png" alt="modernize-img" class="img-fluid" width="24" height="24">
                              </div>
                              <div>
                                <h6 class="mb-1 fw-semibold fs-3">
                                  Add POS
                                </h6>
                                <span class="fs-2 d-block text-body-secondary">Retail POS</span>
                              </div>
                            </a>
                            <?php
                          }
                          else
                          {
                            ?>
                            <a href="../Public/wholesale-invoice.php" class="d-flex align-items-center pb-9 position-relative" target="_blank">
                              <div class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                <img src="../Assets/Images/icons/cashier.png" alt="modernize-img" class="img-fluid" width="24" height="24">
                              </div>
                              <div>
                                <h6 class="mb-1 fw-semibold fs-3">
                                  Add POS
                                </h6>
                                <span class="fs-2 d-block text-body-secondary">Wholesale POS</span>
                              </div>
                            </a>
                            <?php
                          }                          
                          ?>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="position-relative">
                          <?php 
                          if($shopObj->hascounter($shop_id)==1)
                            {
                              $counterObj = new Counter();
                              $counterData = $counterObj->getCounterByUserID($user_id,$shop_id);
                              if(empty($counterData))
                              {
                                ?>
                                <a href="javascript:void(0)" id="btn_new_counter" class="d-flex align-items-center pb-9 position-relative">
                                  <div class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                    <img src="../Assets/Images/icons/plus.png" alt="modernize-img" class="img-fluid" width="24" height="24">
                                  </div>
                                  <div>
                                    <h6 class="mb-1 fw-semibold fs-3">New Counter</h6>
                                    <span class="fs-2 d-block text-body-secondary">Start new counter</span>
                                  </div>
                                </a>
                                <?php
                              }
                              else
                              {
                                ?>
                                <a href="javascript:void(0)" id="btn_close_counter" class="d-flex align-items-center pb-9 position-relative">
                                  <div class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                    <img src="../Assets/Images/icons/close.png" alt="modernize-img" class="img-fluid" width="24" height="24">
                                  </div>
                                  <div>
                                    <h6 class="mb-1 fw-semibold fs-3">Close Counter</h6>
                                    <span class="fs-2 d-block text-body-secondary">Close current counter</span>
                                  </div>
                                </a>
                                <?php
                              }
                            }
                          ?>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- <div class="row align-items-center py-3">
                    <div class="col-8">
                      <a class="fw-semibold d-flex align-items-center lh-1" href="javascript:void(0)">
                        <i class="ti ti-help fs-6 me-2"></i>Frequently Asked Questions
                      </a>
                    </div>
                    <div class="col-4">
                      <div class="d-flex justify-content-end pe-4">
                        <button class="btn btn-primary">Check</button>
                      </div>
                    </div>
                  </div> -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </li>
      <li>
        <h4 class="" style="margin-left: 20px; font-size:15px;"><?php echo $shop_name;?></h4>
      </li>
    </ul>
    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
      <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
        
        <li class="nav-item dropdown">
          <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
            aria-expanded="false">
            <?php 
            $complogo="../Assets/Images/Company_Logos/$company_logo";
            if(file_exists($complogo) && !empty($company_logo))
            {
              $complogoa="../Assets/Images/Company_Logos/".$company_logo;
            }
            else
            {
              $complogoa="../Assets/Images/Company_Logos/smart_edge_logo.png";
            }
            ?>
            <img src="<?php echo $complogoa;?>" alt="CL" width="55" style="border-radius:50%;">
          </a>
          <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
            <p class="text-center " style="font-size:12px; margin:0;"><?=$companyData[0]['ComName']?>-<?=$shopData[0]['ShopName']?></p>
            <p class="text-center " style="font-size:12px; margin:0;"><?=$shopData[0]['ShopNo']?></p>
            <div class="message-body">
              <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item" style="padding:5px 16px !important;">
                <i class="ti ti-user " style="font-size:12px;"></i>
                <p class="mb-0" style="font-size:12px;">My Profile</p>
              </a>
              <?php 
              if($userType==1)
              {
                ?>
                <a href="../Public/switchshop.php" class="d-flex align-items-center gap-2 dropdown-item" style="padding:5px 16px !important;">
                  <i class="ti ti-arrows-exchange-2" style="font-size:12px;"></i>
                  <p class="mb-0" style="font-size:12px;">Switch Shop</p>
                </a>
                <?php
              }
              ?>
              <a href="javascript:void(0)" id="user-password-change" class="d-flex align-items-center gap-2 dropdown-item" style="padding:5px 16px !important;">
                <i class="ti ti-user " style="font-size:12px;"></i>
                <p class="mb-0" style="font-size:12px;">Password Change</p>
              </a>
              <a href="../Public/logout.php" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </nav>
      </header>
      <script>
        
      function authcheck() {
        $.ajax({
          url:"../Includes/newauthcheck.php",
          method:"post",
          success:function(response)
          {
            
            if(response==1)
            {

            }
            else if(response==0)
            {
              window.location.href="../Public/logout.php";
            }
            else if(response==-1)
            {
              window.location.href="../Public/switchshop.php";
            }
            else
            {
              window.location.href="../Public/logout.php";
            }
          }
        })
      }
      // setInterval(function(){
      //   authcheck();
      // }, 10000);
      authcheck();
      </script>
      <script src="../Assets/jquery/header.js">
      </script>
      <?php 
      include '../View/modals/user-password-change.php';?>
      <?php 
      include '../View/modals/close-counter.php';
      include '../View/modals/open-counter.php';
      ?>