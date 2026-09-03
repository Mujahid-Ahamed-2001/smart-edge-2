<?php 
include '../Includes/includes.php';
include '../Includes/authcheck.php';

$shop_id = $_SESSION['shop_id'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  include '../View/head.php';
  // include '../View/loader.php';

  ?>
</head>
<body>
    <div class="h-100vh">
        <div class="page-wrapper" id="main-wrapper">
            <?php 
                include '../View/sidebar.php';            
                $feature_id = 72;
                include '../Includes/viewPermission.php';
            ?>
            <div class="body-wrapper">
                <?php 
                    include '../View/header.php';
                    $shop = $shopObj->getOneShop($shop_id);
                    $today = date('d-m-Y');
                ?>
                <div class="container-fluid h-100">
                    <h5 class="card-title fw-semibold mb-2 color_white" style="margin-top: 0px;">Create Quotation </h5>  
                    <input type="hidden" name="" id="today" value="<?= $today ?>">
                    <input type="hidden" name="" id="print_access" value="<?= $print ?>">
                    <input type="hidden" name="" id="userType" value="<?= $userType ?>">
                    <input type="hidden" name="" id="shop_name" value="<?=$shop[0]['ShopName']?>">
                    <input type="hidden" name="" id="shop_id" value="<?=$shop_id?>">
                    <input type="hidden" name="" id="shop_address_one" value="<?=$shop[0]['AddressLineOne']?> ">
                    <input type="hidden" name="" id="shop_address_two" value="<?=$shop[0]['AddressLineTwo']?>">
                    <input type="hidden" name="" id="shop_city" value="<?=$shop[0]['City']?>">
                    <input type="hidden" name="" id="shop_number" value="<?=$shop[0]['PhoneNumber']?> ">
                    <input type="hidden" name="" id="title" value="Quotation Report">
                    
                </div>
                <!-- footer Start  -->
                <?php include '../View/footer.php';?> 
                <!-- footer End  -->
            </div>
        </div>
    </div>
    
    <?php
    if(isset($_GET["app_status"]))
    {
        ?>
        <?php
    }
    else
    {
        ?>
        <?php
    }
    ?>
</body>
</html>