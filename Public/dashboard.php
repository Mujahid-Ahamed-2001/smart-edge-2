<?php 
include '../Includes/includes.php';
include '../Includes/authcheck-dashboard.php';

//======================= User Log in check ====================//
    /*
    * when the session is destroyed force to log in
    */
    $designation = 0;
    $user = "";
    $user_company = "";
    if(isset($_SESSION['shop_id']))
    {
      header("Location:../Public/home.php");
    }
    else if(isset($_COOKIE["remember_me_Smart_edge_shop"]))
    {
      $_SESSION['shop_id']=$_COOKIE["remember_me_Smart_edge_shop"];
      header("Location:../Public/home.php");

    }
    if(isset($_SESSION['user_id']))
    {
        $user_id = $_SESSION['user_id'];
        $userObj = new User();
        $user = $userObj->getOneUser($user_id);
    }//user logged in
    else
    {
        header("Location: login.php");
    }//force to log in

//================== Admin Log in ===================//
if($designation == 1)
{
    header("Location: company.php");
}

?>
<!doctype html>
<html lang="en">

<head>
  <?php 
  include '../View/head.php';
  ?>
  <style>
    body{

    background:#eef3fb;

}

/*============================*/

.dashboard-container{
    padding:25px;
    max-width:1800px;
    margin:auto;
}

/*============================*/

.shop-page{

    min-height:calc(100vh - 60px);

    background:#fff;

    border-radius:30px;

    overflow:hidden;

    display:grid;

    grid-template-columns:58% 42%;

    box-shadow:
    0 20px 60px rgba(0,0,0,.08);

}

/*============================*/

.shop-left{

    padding:60px;

    display:flex;

    flex-direction:column;

    overflow:hidden;

}

/*============================*/

.shop-right{

    position:relative;

    overflow:hidden;

    background:url("../Assets/Images/SystemImages/Backgrounds/background-dashboard.png");

    background-size:cover;

    background-position:center;

}

/*============================*/

.shop-right::after{

    content:"";

    position:absolute;

    inset:0;

    background:

    linear-gradient(
        rgba(255,255,255,.55),
        rgba(255,255,255,.55)
    );

}

/*============================*/

.right-overlay{

    position:absolute;

    left:-180px;

    top:-60px;

    width:350px;

    height:120%;

    background:white;

    transform:rotate(12deg);

    z-index:2;

}

/*============================*/

.welcome-section{

    display:flex;

    align-items:flex-start;

    gap:25px;

    margin-top:40px;

    margin-bottom:60px;

    position:relative;

    z-index:5;

}

/*============================*/

.welcome-icon{

    width:82px;

    height:82px;

    border-radius:50%;

    background:#eef3ff;

    display:flex;

    align-items:center;

    justify-content:center;

    color:#4f6df5;

    font-size:34px;

    flex-shrink:0;

}

/*============================*/

.welcome-small{
    
    font-size:clamp(20px,2vw,30px);

    color:#59657b;

    margin-bottom:10px;

}

.welcome-small span{

    color:#3867ff;

    font-weight:700;

}

/*============================*/

.welcome-section h1{

    font-size:clamp(40px,4vw,62px);

    font-weight:800;

    color:#14213d;

    margin-bottom:18px;

}

/*============================*/

.welcome-section p{

    font-size:clamp(16px,1.4vw,24px);

    color:#74809b;

    line-height:1.7;

}

/*============================*/

.shop-list {

    display:grid;

    grid-template-columns:repeat(auto-fit,minmax(320px,1fr));

    gap:30px;

    padding:15px;

    overflow-y:auto;

    flex:1;

    align-content:start;
}

.container-fluid{
    padding-top: 30px !important;
}

/*============================*/

.security-box{

    position:absolute;

    top:30px;

    right:30px;

    z-index:10;

    background:white;

    border-radius:22px;

    padding:18px 24px;

    display:flex;

    align-items:center;

    gap:18px;

    box-shadow:
    0 12px 30px rgba(0,0,0,.08);

}
.security-box i
{
    transition: all 0.5s;
}
.security-box:hover .security-icon i
{
    transform: translateX(10px);
}
/*============================*/

.security-icon{

    width:54px;

    height:54px;

    border-radius:50%;

    background:#edf3ff;

    display:flex;

    align-items:center;

    justify-content:center;

    color:#3867ff;

    font-size:24px;

}

/*============================*/

.security-box h5{

    margin:0;

    font-size:18px;

    font-weight:700;

}

.security-box small{

    color:#7c879f;

}

.online-dot{

    width:10px;

    height:10px;

    border-radius:50%;

    background:#31d05a;

}

/* CARD */

.shop-card{

position:relative;

width:100%;

background:#fff;

border:none;

border-radius:28px;

padding:35px;

text-align:center;

transition:.35s;

box-shadow:

0 15px 40px rgba(0,0,0,.08);

cursor:pointer;

}

.shop-card:hover{

transform:translateY(-10px);

box-shadow:

0 25px 60px rgba(79,109,245,.20);

}

.shop-card:disabled{

opacity:.55;

cursor:not-allowed;

transform:none;

}

/* RADIO */

.shop-radio{

position:absolute;

left:20px;

top:20px;

transform:scale(1.3);

}

/* STAR */

.favorite{

position:absolute;

right:22px;

top:22px;

font-size:26px;

color:#7f8aa6;

}

/* ICON */

.shop-icon{

width:140px;

height:140px;

margin:auto;

margin-bottom:25px;

border-radius:50%;

background:#eef3ff;

display:flex;

align-items:center;

justify-content:center;

}

.shop-icon img{

width:90px;

height:90px;

object-fit:contain;

}

/* TITLE */

.shop-card h3{

font-size:30px;

font-weight:700;

margin-bottom:10px;

color:#1f2c52;

}

.shop-card p{

font-size:20px;

color:#7a859d;

margin-bottom:20px;

}

/* STATUS */

.shop-status{

display:inline-block;

padding:8px 18px;

border-radius:50px;

font-size:15px;

font-weight:600;

margin-bottom:25px;

}

.shop-status.active{

background:#e7fff1;

color:#12a54d;

}

.shop-status.danger{

background:#ffe9e9;

color:#ff3d3d;

}

/* DIVIDER */

.shop-divider{

height:1px;

background:#edf2f7;

margin:15px 0 22px;

}

/* BUTTON */

.shop-btn{

height:58px;

border-radius:16px;

background:linear-gradient(90deg,#3867ff,#7b4dff);

color:#fff;

display:flex;

justify-content:center;

align-items:center;

gap:10px;

font-size:18px;

font-weight:600;

}

.shop-btn i{

font-size:22px;

transition:.3s;

}

.shop-card:hover .shop-btn i{

transform:translateX(6px);

}

/* NO COMPANY */

.no-company{

padding:80px;

}

.no-company h2{

font-size:34px;

margin-bottom:15px;

}

.no-company p{

font-size:20px;

color:#777;

}
.shopName
{
    width: 100%;
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
@media (max-width:1200px){

    .shop-page{

        grid-template-columns:55% 45%;

    }

    .shop-left{

        padding:40px;

    }

    .security-box{

        right:20px;

        top:20px;

    }

    .shop-card{

        min-height:auto;

    }

}
@media (max-width:992px){

    .shop-page{

        grid-template-columns:1fr;

    }

    .shop-right{

        display:none;

    }

    .shop-left{

        padding:35px;

    }

    .shop-list{

        grid-template-columns:repeat(auto-fit,minmax(280px,1fr));

    }

    .welcome-section{

        margin-bottom:40px;

    }

}
@media (max-width:768px){

    .dashboard-container{

        padding:15px;

    }

    .shop-page{

        border-radius:20px;

    }

    .welcome-section{

        gap:18px;

    }

    .welcome-icon{

        width:65px;

        height:65px;

        font-size:28px;

    }

    .shop-list{

        grid-template-columns:1fr;

    }

    .security-box{

        display:none;

    }

}

@media (max-width:576px){

    .shop-left{

        padding:20px;

    }

    .welcome-section{

        flex-direction:column;

        align-items:flex-start;

    }

    .welcome-small{

        font-size:18px;

    }

    .welcome-section h1{

        font-size:34px;

    }

    .welcome-section p{

        font-size:15px;

    }

    .shop-card{

        border-radius:20px;

        padding:25px;

    }

    .shop-icon{

        width:100px;

        height:100px;

    }

    .shop-icon img{

        width:65px;

        height:65px;

    }

    .shop-card h3{

        font-size:24px;

    }

    .shop-btn{

        height:50px;

        font-size:16px;

    }

}
  </style>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!--  Main wrapper -->
    <div class="body-wrapper dashboard-wrapper" style="margin-left: 0 !important;">
      <!--  Header Start -->
      <?php 
    //   include '../View/header-dashbord.php';
      ?>
      <!--  Header End -->
      <div class="container-fluid dashboard-container">

        <div class="shop-page">

            <!-- LEFT SIDE -->
            <div class="shop-left">

                <!-- Welcome -->
                <div class="welcome-section">

                    <div class="welcome-icon">
                        <i class="ti ti-building-store"></i>
                    </div>

                    <div>

                        <div class="welcome-small">
                            Welcome back,
                            <span><?=$user[0]['UserName']?> 👋</span>
                        </div>

                        <h1>Select Your Shop</h1>

                        <p>
                            Choose a location to continue managing your business.
                        </p>

                    </div>

                </div>

                <!-- Shop Cards -->
                

                    <?php

                    $comObj = new Company();
                    $UserType = $user[0]["UserType"];
                    $comData = $comObj->getCompanyByUser($user_id,$UserType);

                    if(!empty($comData))
                    {

                        ?>

                        <form action="../Controller/shopController.php" method="post" class="shop-list">

                            <?php

                            if(count($comData)==1)
                            {

                                $date=date("Y-m-d");

                                if($date>$comData[0]["ComExpireDate"])
                                {

                                    if($user[0]["UserType"]==0)
                                    {

                                        $_SESSION["expired"]=1;
                                        unset($_SESSION['user_id']);

                                        ?>

                                        <script>

                                            window.location="../Public/login.php";

                                        </script>

                                        <?php

                                    }
                                    else
                                    {

                                        $_SESSION['shop_id']=$comData[0]['SHID'];

                                        ?>

                                        <script>

                                            window.location="../Public/home.php";

                                        </script>

                                        <?php

                                    }

                                }
                                else
                                {

                                    $_SESSION['shop_id']=$comData[0]['SHID'];

                                    ?>

                                    <script>

                                        window.location="../Public/home.php";

                                    </script>

                                    <?php

                                }

                            }
                            else
                            {
                                foreach($comData as $row)
                                {

                                    $date=date("Y-m-d");

                                    $isExpired = ($date > $row["ComExpireDate"]);
                                    $isInactive = ($row["ComStat"]==0);

                                    $disabled = ($user[0]["UserType"]==0 && ($isExpired || $isInactive));

                                    ?>

                                    <div class="shop-card-wrapper">

                                        <button  name="btn_continue" class="shop-card" id="Smart_edge_shop" <?=$disabled ? "disabled" : ""?> >

                                            <input type="hidden" name="hide_shop_id" value="<?=$row['SHID']?>">

                                            <?php

                                            if($user[0]["UserType"]==0)
                                            {

                                            ?>
                                                <input type="radio" name="cmb_shops" class="shop-radio" id="radio" value="<?=$row['SHID']?>" >

                                            <?php

                                            }
                                            else
                                            {

                                            ?>

                                                <input type="radio" name="cmb_shops" class="shop-radio" id="radio" value="<?=$row['shopID']?>">

                                            <?php

                                            }

                                            ?>

                                            <div class="favorite">

                                                <i class="ti ti-star"></i>

                                            </div>

                                            <div class="shop-icon">

                                                <img src="../Assets/Images/icons/clothing-shop.gif" alt="Shop">

                                            </div>

                                            <h3 title="<?=$row["ShopName"]?>" class="shopName"><?=$row["ShopName"]?></h3>

                                            <p>

                                                <i class="ti ti-map-pin"></i>

                                                <?=$row["ComName"]?>

                                            </p>

                                            <?php

                                            if($isInactive && $user[0]["UserType"]==0)
                                            {

                                                ?>
                                                <div class="shop-status danger">

                                                    Company Inactive

                                                </div>

                                                <?php

                                            }
                                            elseif($isExpired && $user[0]["UserType"]==0)
                                            {

                                            ?>

                                                <div class="shop-status danger">

                                                Company Expired

                                                </div>

                                            <?php

                                            }
                                            else
                                            {

                                            ?>

                                                <div class="shop-status active">

                                                Available

                                                </div>

                                            <?php

                                            }

                                            ?>

                                            <div class="shop-divider"></div>

                                            <div class="shop-btn">

                                            Select Shop

                                            <i class="ti ti-arrow-right"></i>

                                            </div>

                                        </button>

                                    </div>

                                    <?php

                                }

                            }

                            ?>

                        </form>

                        <?php

                    }
                    else
                    {

                    ?>

                        <div class="no-company">

                            <h2>

                            You don't have any shops assigned.

                            </h2>

                            <p>

                            Please contact Smart Edge Solutions or activate your trial account. <a href="tel:0094770206960" target="_blank" rel="noopener noreferrer"> Call: +94-770-206-960</a>

                            </p>

                        </div>

                    <?php

                    }

                    ?>

                    

            </div>

            <!-- RIGHT SIDE -->
            <div class="shop-right">

                <div class="right-overlay"></div>

                <a class="security-box" href="../Public/logout.php">

                    <div class="security-icon">
                        <i class="ti ti-logout"></i>
                    </div>

                    <div>

                        <h5>Logout</h5>

                        <small>
                            Your data is safe with Smart Edge
                        </small>

                    </div>

                    <span class="online-dot"></span>

                </a>

            </div>

        </div>

    </div>
    </div>
  </div>

  <script src="../Assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../Assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../Assets/js/sidebarmenu.js"></script>
  <script src="../Assets/js/app.min.js"></script>
  <script src="../Assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="../Assets/jquery/toast.js"></script>
  <script>
    $(document).ready(function(){
      $('body').on('click','#Smart_edge_shop', function(){
        var radio = $(this).find("#radio");
        var value = $(this).find("#radio").val();
        console.log(value);
        if (radio.is(':checked')) 
        {
          radio.removeAttr('checked');
        }
        else
        {
          radio.attr('checked','checked'); 
        }
      });

    });
  </script>
</body>

</html>