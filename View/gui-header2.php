
<?php 
  $shopObj = new Shop();
  $shopData = $shopObj->getOneShop($_SESSION['shop_id']);         
  $companyData = $shopObj->getCompanyONE($shopData[0]['Company_CMID']);         
  $company_logo = $shopData[0]['ComLogo'];
  $shop_name = $shopData[0]['ShopName'];
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
<style>
.smart-header{
    padding:15px 20px;
    background:#fff;
}

.smart-header-container{
    background:#fff;
    height:80px;
    border-radius:18px;

    display:flex;
    align-items:center;
    justify-content:space-between;

    padding:0 30px;

    box-shadow:
    0 10px 40px rgba(0,0,0,.08);
}

.smart-header-left,
.smart-header-right{
    display:flex;
    align-items:center;
    gap:18px;
    min-width:250px;
}

.smart-header-center{
    flex:1;
    text-align:center;
}

.smart-header-logo{
    height:52px;
    object-fit:contain;
}

.smart-header-icon{
    width:42px;
    height:42px;

    border-radius:12px;

    display:flex;
    align-items:center;
    justify-content:center;

    text-decoration:none;

    color:#3f4254;

    transition:.3s;
}

.smart-header-icon:hover{
    background:#eef2ff;
    color:#4f46e5;
}

.smart-header-icon i{
    font-size:22px;
}

.smart-notification{
    position:relative;
    cursor:pointer;
}

.smart-notification i{
    font-size:24px;
}

.notification-badge{
    position:absolute;
    top:-8px;
    right:-8px;

    width:18px;
    height:18px;

    border-radius:50%;

    background:red;
    color:#fff;

    font-size:10px;

    display:flex;
    align-items:center;
    justify-content:center;
}

.smart-user-dropdown{
    display:flex;
    align-items:center;
    gap:12px;

    text-decoration:none;
    color:#222;
}

.smart-user-image{
    width:45px;
    height:45px;

    border-radius:50%;
    object-fit:cover;

    border:2px solid #edf2f7;
}

.smart-user-name{
    font-size:15px;
    font-weight:600;
}

.smart-user-role{
    font-size:12px;
    color:#777;
}

.smart-dropdown{
    width:280px;
    border:none;
    border-radius:15px;
    padding:15px;
    box-shadow:0 10px 40px rgba(0,0,0,.12);
}

.smart-dropdown-header{
    text-align:center;
}

.smart-dropdown-logo {
    width: 160px;
    height: 160px;
    object-fit: cover;
    margin-bottom: 10px;
    border-radius: 50%;
}

.smart-dropdown .dropdown-item{
    padding:10px 15px;
    border-radius:10px;
}

.smart-dropdown .dropdown-item:hover{
    background:#f5f7fb;
}

.smart-dropdown .dropdown-item i{
    margin-right:10px;
} 
.smart-header-right {
    justify-content: end;
}
.container-fluid{
  padding-top: 0 !important;
}

/*new */
.smart-header{
    overflow:hidden;
}

.header-toggle-wrap{

    display:flex;
    justify-content:end;

    margin-top:-5px;
    margin-right:25px;
    margin-bottom:10px;
}

.header-toggle-btn{
    border:none;
    background-color: transparent;
    color:black;
    cursor:pointer;
    transition:.3s;
}
body {
    background: #f3f5f9;
}

#smartHeader {
    background: #f3f5f9 !important;
}
.body-wrapper>.container-fluid {
    max-width: none !important;
    padding: 0px 50px !important;
}

.header-toggle-btn:hover{

    color:#506bf5;
}

.header-toggle-btn i{

    font-size:20px;
    transition:.3s;
}
#smartHeader {
    position: relative;
    z-index: 997;
    overflow: visible !important;
}

.smart-header-container {
    position: relative;
    overflow: visible !important;
}

.smart-header-right {
    position: relative;
    z-index: 1001;
}

.smart-user-dropdown {
    position: relative;
}

.smart-dropdown {
    z-index: 99999 !important;
}
</style>

<header class="smart-header" id="smartHeader">

    <div class="smart-header-container">

        <!-- Left Side -->
        <div class="smart-header-left">

            <!-- <a href="javascript:void(0)" class="smart-header-icon sidebartoggler">
                <i class="ti ti-menu-2"></i>
            </a> -->

            <a href="javascript:void(0)"
               class="smart-header-icon"
               id="showHoldList"
               title="Hold Invoices">
                <i class="ti ti-clock-pause"></i>
            </a>

            <a href="javascript:void(0)"
               class="smart-header-icon"
               id="showShortcutList"
               title="Shortcut Keys">
                <i class="ti ti-info-circle"></i>
            </a>

        </div>

        <!-- Center Logo -->
        <div class="smart-header-center">

            <a href="../Public/home.php" >
              <img src="../Assets/Images/SystemLogo/Smart_Edge_Logo_4.png" class="smart-header-logo" alt="Smart Edge">
            </a>

        </div>

        <!-- Right Side -->
        <div class="smart-header-right">

            <div class="smart-notification">

                <!-- <i class="ti ti-bell"></i>

                <span class="notification-badge">3</span> -->

            </div>

            <div class="dropdown">

                <a href="#"
                   class="smart-user-dropdown"
                   data-bs-toggle="dropdown">

                    <img src="<?php echo $complogoa;?>"
                         class="smart-user-image"
                         alt="Company">

                    <div class="smart-user-info">

                        <div class="smart-user-name">
                            <?=$companyData[0]['ComName']?>
                        </div>

                        <div class="smart-user-role">
                            <?=$shopData[0]['ShopName']?>
                        </div>

                    </div>

                    <i class="ti ti-chevron-down"></i>

                </a>

                <div class="dropdown-menu dropdown-menu-end smart-dropdown">

                    <div class="smart-dropdown-header">

                        <img src="<?php echo $complogoa;?>"
                             class="smart-dropdown-logo">

                        <h6><?=$companyData[0]['ComName']?></h6>

                        <small><?=$shopData[0]['ShopName']?></small>

                    </div>

                    <hr>

                    <a class="dropdown-item" href="../Public/home.php">
                        <i class="ti ti-user"></i>
                        Home
                    </a>

                    <hr>

                    <a href="../Public/logout.php"
                       class="btn btn-danger w-100">
                        Logout
                    </a>

                </div>

            </div>

        </div>

    </div>

</header>
<div class="header-toggle-wrap">

    <button id="toggleHeader" class="header-toggle-btn">

        <i class="ti ti-menu-2"></i>

    </button>

</div>
<script src="../Assets/jquery/header.js"></script>
<script>
  $(document).on("click", "#toggleHeader", function(){

      $("#smartHeader").slideToggle(250);

      $(this).find("i").toggleClass("ti-menu-2 ti-x");

  });
</script>