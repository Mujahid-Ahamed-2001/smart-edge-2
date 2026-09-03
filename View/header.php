
<?php 
  $shopObj = new Shop();
  $shopData = $shopObj->getOneShop($_SESSION['shop_id']);         
  $companyData = $shopObj->getCompanyONE($shopData[0]['Company_CMID']);         
  $company_logo = $shopData[0]['ComLogo'];
  $shop_name = $shopData[0]['ShopName'];
  $user_id = $_SESSION['user_id'];
  $userObj = new User();
  $user = $userObj->getOneUser($user_id);
?>

<header class="modern-header" id="modern-header">

    <div class="header-left">

        <!-- Sidebar -->
        <button class="header-btn" id="headerCollapse2">
            <i class="ti ti-menu-2"></i>
        </button>

        <!-- Notification -->
        <button class="header-btn position-relative">

            <i class="ti ti-bell"></i>

            <span class="header-badge"></span>

        </button>

        <!-- Quick Links -->

        <div class="dropdown">

            <button class="header-pill dropdown-toggle"
                    data-bs-toggle="dropdown">

                <i class="ti ti-link"></i>

                Quick Links

            </button>

            <div class="dropdown-menu modern-dropdown">
              <?php 
                if($shopObj->hasRetailShop($shop_id))
                {
                  ?>
                  <a href="../Public/gui-pos.php" class="d-flex align-items-center gap-2 dropdown-item" style="padding:5px 16px !important;">
                    <i class="ti ti-receipt"></i>
                    <p class="mb-0" style="font-size:12px;">Retail Invoice</p>
                  </a>
                  <?php
                }
                else
                {
                  ?>
                  <a href="../Public/wholesale-invoice.php" class="d-flex align-items-center gap-2 dropdown-item" style="padding:5px 16px !important;">
                    <i class="ti ti-receipt"></i>
                    <p class="mb-0" style="font-size:12px;">Wholesale Invoice</p>
                  </a>
                  <?php
                }
                if($shopObj->hascounter($shop_id)==1)
                  {
                    $counterObj = new Counter();
                    $counterData = $counterObj->getCounterByUserID($user_id,$shop_id);
                    if(empty($counterData))
                    {
                      ?>
                      <a href="../View/modals/startCounter.php" class="d-flex align-items-center gap-2 dropdown-item open-modal2" style="padding:5px 16px !important;">
                        <i class="ti ti-device-desktop"></i>
                        <p class="mb-0" style="font-size:12px;">New Counter</p>
                      </a>
                      <?php
                    }
                    else
                    {
                      $CCID = $counterData[0]["CCID"];
                      ?>
                      <a href="../View/modals/closeCounter.php?counterid=<?=$CCID?>" class="d-flex align-items-center gap-2 dropdown-item open-modal2" style="padding:5px 16px !important;">
                        <i class="ti ti-device-desktop"></i>
                        <p class="mb-0" style="font-size:12px;">Close Counter</p>
                      </a>
                      <?php

                    }
                  }
              ?> 
              

            </div>

        </div>

        <!-- Shop -->

        <div class="dropdown">

            <button class="header-pill dropdown-toggle"
                    data-bs-toggle="dropdown">

                <i class="ti ti-building-store"></i>

                <?=$shop_name?>

            </button>

            <div class="dropdown-menu modern-dropdown">
                <?php 
                $comObj = new Company();
                $UserType=$user[0]["UserType"];
                $comData = $comObj->getCompanyByUser($user_id,$UserType);
                foreach ($comData as $row) 
                {
                  $ComExpireDate = $row["ComExpireDate"];
                  $ComStat = $row["ComStat"];
                  $ShopName = $row["ShopName"];
                  $ComName = $row["ComName"];
                  $SHID = $row["SHID"];
                  $expired ="";
                  $disabledShop = 1;
                  $currentSHop = "";
                  $date=date("Y-m-d");
                  if($date>$row["ComExpireDate"] || $row["ComStat"]==0)
                  {
                    $expired ="<span class='badge bg-danger mt-1'>Expired/Inactive</span>";
                    $disabledShop = 0;
                  }
                  if($SHID==$_SESSION["shop_id"])
                  {
                    $currentSHop ="<span class='badge bg-success mt-1'>Current Shop</span>";
                  }
                  ?>
                  <div class="p-3 d-flex align-items-center changeShop" data-shid="<?=$SHID?>" data-disabledshop="<?=$disabledShop?>">
                    <div class="shop-icon">
                      <i class="ti ti-building-store"></i>
                    </div>
                    <div class="shop-details">
                      <h6 class="shop-name" title="<?=htmlspecialchars($ShopName)?>"><?=$ShopName?></h6>
                      <small class="company-name" title="<?=htmlspecialchars($ComName)?>"><?=$ComName?></small>
                      <small><?=$expired." ".$currentSHop?></small>
                    </div>
                  </div>
                  <?php
                }
                ?>
                <script>
                  $(".changeShop").on("click", function(e){
                    e.preventDefault();
                    var SHID = $(this).data("shid");
                    var disabledshop = $(this).data("disabledshop");
                    var UserType = <?=$UserType?>;
                    console.log("changeShop");
                    if(disabledshop==1 || UserType==1)
                    {
                      $.ajax({
                        url:"../AJAX/header/changeshop.php",
                        data: {
                          SHID:SHID,
                          UserType:UserType
                        },
                        method: 'POST',
                        dataType: 'json',
                        success: function (response) {
                          var status = response[0].status;
                          var message = response[0].message;
                          if(status==1)
                          {
                            toastr.success(message,"Success");
                            setTimeout(() => {
                              location.reload();
                            }, 1000);
                          }
                          else
                          {
                            toastr.error(message,"Error");
                          }
                        },
                        error: function (xhr, status, error) {
                          console.log("AJAX Error:", status, error);
                          console.log("Response:", xhr.responseText);
                          toastr.error("An error occurred while loading company data.","Error");
                        }
                      });
                    }
                    else
                    {
                      toastr.error("Shop or company has been disabled", "Error")
                    }
                    
                    
                  })
                </script>
            </div>

        </div>

    </div>

    <!-- Center Search -->

    <div class="header-center">

        <!-- <div class="header-search">

            <i class="ti ti-search"></i>

            <input type="text"placeholder="Search anything...">

        </div> -->

    </div>

    <!-- Right -->

    <div class="header-right">

        <div class="dropdown">

            <button class="header-user"
                    data-bs-toggle="dropdown">

                <img src="<?=$complogoa?>">

                <div>

                    <strong><?=$companyData[0]['ComName']?></strong>

                    <small><?=$shopData[0]['ShopName']?></small>

                </div>

                <i class="ti ti-chevron-down"></i>

            </button>

            <div class="dropdown-menu dropdown-menu-end modern-dropdown">

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

    </div>

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
      <script src="../Assets/jquery/header.js?v=2">
      </script>
      <?php 
      include '../View/modals/user-password-change.php';?>
      <?php 
      include '../View/modals/close-counter.php';
      include '../View/modals/open-counter.php';
      ?>