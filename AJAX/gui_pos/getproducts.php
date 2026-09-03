<?php 
include "../../Includes/config.php";
include "../../Model/product_class.php"; 
session_start();
$shop_id = $_SESSION['shop_id'];
$product_class=new Product();
$products=$product_class->getproductswithinventory($shop_id);
foreach ($products as $product) 
{
    $qty=$product["sum_qty"];
    if($qty>0)
    {
        $qty=substr($qty,0,-4);
        $product_id=$product['PDID'];
        $product_count=$product_class->getqtycount($shop_id,$product_id); 
        ?>
        <a href="javascript:void(0);" class="col-md-3 <?=$product['cat_ID']?>"  id="single-product">
            <div class="m-1 product2 card p-0">
                <div class="d-none" id="hidden-fields">
                    <input type="hidden" name="" value="<?=$product["PDID"]?>" id="product-id">
                    <input type="hidden" name="" value="<?=$product["Barcode"]?>" id="product-barcode">
                    <input type="hidden" name="" value="<?=$product_count[0]["count_qty"]?>" id="product-qty-count">
                    <p id="barcode"><?=$product["Barcode"]?></p>
                </div>
                <div class="quantity bg-primary">
                    <span> <?=$qty?></span>
                </div>                                                        
                <img class="rounded" style=" width: 100%; height: 100px;" src="../Assets/Images/prod_images/<?=$product["ProdImage"]?>" alt="...">
                <div class="card-body" style="padding: 0 5px 5px 5px !important;">
                    <label class="card-title m-0 prd-lable"><?=$product["ItemName"]?></label>
                    <p class="card-text mb-1 text-black font-weight-bold"><b>Rs. <?=$product["ProdSellPrice"]?></b></p>
                    
                </div>
            </div>
        </a>
        <?php
    }
}

?>
<script src="../Assets/js/pos.js"></script>