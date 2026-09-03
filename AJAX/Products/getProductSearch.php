<?php 
session_start();

include "../../Includes/config.php";
include "../../Model/DB_Class.php";
include "../../Model/shop_class.php";
include "../../Model/user_class.php";

$user_id = $_SESSION['user_id'];
$shop_id = $_SESSION['shop_id'];
$txt_input = $_GET['txt_input'];

$dbObj = new DBTransactions();
$shopObj = new Shop();
$userObj = new User();

$sql = "SELECT * FROM user WHERE USID = ".$user_id.";";

//get user type
$userData = $dbObj->getData($sql);
$userType = $userData[0]['UserType'];

$feature_id = 16; //product feature
//get user role access
$userData = $userObj->getUserFeatureAccess($user_id,$feature_id);
if(empty($userData))
{
    $create = 0;
}
else
{
    $create = floatval($userData[0]['is_create']);
}


//get company stat
$sql = "SELECT * FROM shop
INNER JOIN company ON company.CMID = shop.Company_CMID
WHERE SHID = ".$shop_id.";";

$shopData = $dbObj->getData($sql);
$multi_category = floatval($shopData[0]['is_multicategory']);
$company_id = floatval($shopData[0]['CMID']);

if($multi_category == 1)
{
    $sql_1 = "SELECT *,categories.CTID AS cat_ID FROM products 
    INNER JOIN subcategories ON subcategories.SCID = products.Subcategories_SCID
    INNER JOIN categories ON categories.CTID = subcategories.categories_CTID
    INNER JOIN shop ON shop.SHID = products.shop_SHID
    WHERE concat(Barcode, ItemName) LIKE '%".$txt_input."%' AND shop.Company_CMID = ".$company_id." LIMIT 50;";
}//has multi category
else
{
    $sql_1 = "SELECT *,categories.CTID AS cat_ID FROM products 
    INNER JOIN subcategories ON subcategories.SCID = products.Subcategories_SCID
    INNER JOIN categories ON categories.CTID = subcategories.categories_CTID
    WHERE concat(Barcode, ItemName) LIKE '%".$txt_input."%' AND products.shop_SHID = ".$shop_id." LIMIT 50;";
}//mo multi category

$prodData = $dbObj->getData($sql_1);

?>
<thead>
    <tr>
        <th>No</th>
        <th>Category</th>
        <th>Sub Category</th>
        <th>Barcode</th>
        <th>Item</th>
        <th>Image</th>
        <th>Purchase Price</th>
        <th>Selling Price</th>
        <th>Dis(%) </th>
        <th>Dis. </th>
        <th>Type</th>
        <th>Status</th>
        <th>FP</th>
        <th>Action</th>
        <th>Barcode</th>
    </tr>
</thead>
<tbody>
   <?php 
   $row_count=1;
foreach($prodData as $row)
{
    ?>                                        
    <tr data-id=<?php echo $row['PDID'];?>>
    <td><?php echo $row_count; ?></td>
    <td><?php echo $row['CategoryName']; ?></td>
    <td><?php echo $row['SubCatName']; ?></td>
    <td><?php echo $row['Barcode']; ?></td>
    <td>
        <b>
        <?php 
            echo $row['ItemName'];
            if($shopObj->hasSecondLanguage($shop_id))
            {
                echo "<br>" . $row['SecondName'];
            }//has second name
            ?>
        </b>
    </td><!-- 4 -->
    <td>
        <?php 
        if(isset($row['ProdImage']))
        {
            ?>
            <img src="../Assets/Images/prod_images/<?php echo $row['ProdImage'];?>" alt="" srcset="" style="width: 30px; height:auto;">
            <?php 
        }//has image
        else
        {
            ?>
            <img src="../Assets/Images/icons/product.png" alt="Product Image" style="width: 30px; height:auto;">
            <?php 
        }//no image
        ?> 
    </td><!-- 6 -->
    <td><?php echo $row['ProdPurchasePrice'];?></td>
    <td><?php echo $row['ProdSellPrice'];?></td>

    <td><?php echo $row['prodDiscount']; ?></td>
    <td><?php echo $row['prodFlatDiscount']; ?></td>
    
    <td>
        <?php 
            if($row['ItemType'] == "P")
            {
                ?>
                <p class="text-success" style="font-weight: 700;"><i class="ti ti-box"></i> Product</p>
                <?php 
            }//is a product
            else
            {
                ?>
                <p class="text-warning" style="font-weight: 700;"><i class="ti ti-man"></i> Service</p>
                <?php 
            }//is a service
        ?>
    </td><!-- 7 -->
    <td>
        <?php 
        if($row["ProductStat"]==1)
        {
            ?>
            <span class="badge bg-primary">Active</span>
            <?php
        }
        else
        {
            ?>
            <span class="badge bg-danger">Inactive</span>
            <?php
        }
        ?>
    </td>
    <td>
        <?php 
        if($row["is_fixedPrice"]==1)
        {
            ?>
            <span class="badge bg-primary">Fixed Price</span>
            <?php
        }
        else
        {
            ?>
            <span class="badge bg-danger">No Fixed Price</span>
            <?php
        }
        ?>
    </td>

    <td>
        <?php 
            if($userType==1 || $create==1)
            {
                if($shopObj->hasVariation($shop_id))
                {
                    ?>
                    <button type="button" id="btn_product_variant_<?php echo $row['PDID']?>" class="btn border border-primary m-1"><i class="ti ti-list"></i></button>
                    <?php 
                }//has variations
                ?>
                <button type="button" id="btn_product_<?php echo $row['PDID']?>" class="btn_edit_product btn border border-primary m-1"><i class="ti ti-edit"></i></button>
                <?php
            }

        ?>
    </td><!-- 8 -->

    <td>
        <?php 
        if($userType==1 || $print==1)
        {
            ?>
            <!-- <a href="../Public/print_product_barcode.php?print_barcode=<?=$row['Barcode']?>" class="btn btn-primary"><i class="ti ti-printer"></i> Print Barcode</a> -->
            <button class="btn border-primary btn_open_barcode"><small>Print Barcode</small></button>
            <?php
        }
        ?>
    </td>         
    
</tr>
    <?php
    $row_count++;
}//foreach
   
   ?> 
</tbody>
<?php
