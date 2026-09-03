<?php
session_start();

include "../../Includes/config.php";
include "../../Model/DB_Class.php";
$shop_id = $_SESSION['shop_id'];
$dbObj = new DBTransactions();
//shop data
$sql="SELECT * FROM shop WHERE SHID='$shop_id'";
$shopdata = $dbObj->getData($sql);

//company data
$company_id=$shopdata[0]["Company_CMID"];
$sql="SELECT * FROM company WHERE CMID='$company_id' ";
$companydata = $dbObj->getData($sql);
$is_multicategory = $companydata[0]["is_multicategory"];
$is_commonStock = $companydata[0]["is_commonStock"];
$is_minus=$shopdata[0]["is_minus"];
$add="";
$is_expire = $shopdata[0]["is_expire"] ?? false;
$date=date("Y-m-d");
if(isset($_GET["allproducts"]))
{
    if($is_multicategory==1 )
    {
        $sql = "SELECT * FROM `products` p 
        INNER JOIN subcategories sc ON sc.SCID=p.Subcategories_SCID
        INNER JOIN shop s ON s.SHID=p.shop_SHID
        WHERE s.Company_CMID='$company_id' AND p.ProductStat=1  ORDER BY p.ItemName,p.ItemType ASC;";
        $shopie=1;
    }
    else
    {
        $sql = "SELECT * FROM `products` p 
        INNER JOIN subcategories sc ON sc.SCID=p.Subcategories_SCID
        WHERE p.shop_SHID='$shop_id' AND p.ProductStat=1  ORDER BY p.ItemName,p.ItemType ASC;";
    }
    $productData = $dbObj->getData($sql); 
    

    $procount=0;
    foreach ($productData as $row) 
    {
        $product_id=$row["PDID"];
        if($row["ItemType"]=="P")
        {
            $sql="SELECT *, SUM(CurrentQty) AS CurrentQty  FROM `inventory` i WHERE i.`products_PDID` ='$product_id' AND i.shop_SHID='$shop_id' GROUP BY i.`products_PDID`";
            if($is_expire)
            {
                $sql="SELECT *, SUM(CurrentQty) AS CurrentQty  FROM `inventory` i 
                INNER JOIN pricehistory ph ON ph.Inventory_INID = i.INID
                WHERE i.`products_PDID` ='$product_id' AND i.shop_SHID='$shop_id' 
                    AND (ph.ExpDate IS NULL OR ph.ExpDate = '0000-00-00' OR ph.ExpDate > '$date')                
                GROUP BY i.`products_PDID`";
            }
            if($is_commonStock==1)
            {
                $sql="SELECT *, SUM(CurrentQty) AS CurrentQty  FROM `inventory` i 
                INNER JOIN shop s ON s.SHID=i.shop_SHID
                WHERE i.`products_PDID` ='$product_id' AND s.Company_CMID='$company_id' GROUP BY i.`products_PDID`";
                if($is_expire)
                {
                    $sql="SELECT *, SUM(CurrentQty) AS CurrentQty  FROM `inventory` i 
                    INNER JOIN pricehistory ph ON ph.Inventory_INID = i.INID
                    INNER JOIN shop s ON s.SHID=i.shop_SHID
                    WHERE i.`products_PDID` ='$product_id' AND s.Company_CMID='$company_id' 
                        AND (ph.ExpDate IS NULL OR ph.ExpDate = '0000-00-00' OR ph.ExpDate > '$date')
                    GROUP BY i.`products_PDID`";
                }
            }
            $inventoryData = $dbObj->getData($sql); 
            if(isset($inventoryData[0]["CurrentQty"]) && $inventoryData[0]["CurrentQty"] > 0)
            {
                $filepath="../Assets/Images/prod_images/".$row['ProdImage'];
                if(!empty($row[0]['ProdImage']))
                {
                    $filepath="../Assets/Images/prod_images/".$row['ProdImage'];
                }
                else
                {
                    $filepath="../Assets/no-image-2.jpg";
                }
                if(!file_exists("../".$filepath))
                {
                    $filepath="../Assets/no-image-2.jpg";
                }
                $procount+=1;
                $inventoryData['CurrentQty'] = $inventoryData[0]['CurrentQty'] * $row['UnitConversion'];
                ?>
                <div class="product-card product mb-2">
                    <div class="d-none">
                        <input type="hidden" name="" id="productid" value="<?=$row["PDID"]?>">
                    </div>
                    <div class="product-image">
                        <img src="<?=$filepath?>" class="pro-image">
                    </div>
                    <div class="product-body">
                        <h6 class="product-title" title="<?=htmlspecialchars($row["ItemName"])?>">
                            <?=$row["ItemName"]?>
                        </h6>
                        <div class="product-meta">
                            <div>
                                <span>Barcode</span>
                                <strong><?=$row["Barcode"]?></strong>
                            </div>
                            <div>
                                <span>Sub Category</span>
                                <strong><?=$row["SubCatName"]?></strong>
                            </div>
                            <div>
                                <span>Type</span>
                                <strong>
                                    <?php
                                    if($row["ItemType"]=="P")
                                    {
                                        echo "Product";
                                    }
                                    else
                                    {
                                        echo "Service";
                                    }?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            else
            {
                if($is_minus==1)
                {
                   

                    $sql="SELECT *, SUM(CurrentQty) AS CurrentQty  FROM `inventory` i WHERE i.`products_PDID` ='$product_id' AND (i.shop_SHID='$shop_id' OR i.is_default=1) GROUP BY i.`products_PDID` LIMIT 1";
                    if($is_commonStock==1)
                    {
                        $sql="SELECT *, SUM(CurrentQty) AS CurrentQty  FROM `inventory` i 
                        INNER JOIN shop s ON s.SHID=i.shop_SHID
                        WHERE i.`products_PDID` ='$product_id' AND s.Company_CMID='$company_id' GROUP BY i.`products_PDID` LIMIT 1";
                    }
                    $inventoryData = $dbObj->getData($sql); 
                    if(isset($inventoryData[0]["CurrentQty"]))
                    {
                        $filepath="../Assets/Images/prod_images/$row[ProdImage]";
                        if(isset($row["ProdImage"]) && $row["ProdImage"]!="" && file_exists("../".$filepath))
                        {
                            $filepath="../Assets/Images/prod_images/$row[ProdImage]";
                        }
                        else
                        {
                            $filepath="../Assets/no-image-2.jpg";
                        }
                        $procount+=1;
                        $inventoryData['CurrentQty'] = $inventoryData[0]['CurrentQty'] * $row['UnitConversion'];
                        ?>
                        <div class="product-card product mb-2 subcat-<?=$row["SCID"]?>">
                            <div class="d-none">
                                <input type="hidden" name="" id="productid" value="<?=$row["PDID"]?>">
                            </div>
                            <div class="product-image">
                                <img src="<?=$filepath?>" class="pro-image">
                            </div>
                            <div class="product-body">
                                <h6 class="product-title" title="<?=htmlspecialchars($row["ItemName"])?>">
                                    <?=$row["ItemName"]?>
                                </h6>
                                <div class="product-meta">
                                    <div>
                                        <span>Barcode</span>
                                        <strong><?=$row["Barcode"]?></strong>
                                    </div>
                                    <div>
                                        <span>Sub Category</span>
                                        <strong><?=$row["SubCatName"]?></strong>
                                    </div>
                                    <div>
                                        <span>Type</span>
                                        <strong>
                                            <?php
                                            if($row["ItemType"]=="P")
                                            {
                                                echo "Product";
                                            }
                                            else
                                            {
                                                echo "Service";
                                            }?>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    else
                    {
        
                    }
                }
                else
                {
        
                }    
            }
        }
        else
        {
            $sql="SELECT *, SUM(CurrentQty) AS CurrentQty  FROM `inventory` i WHERE i.`products_PDID` ='$product_id' AND (i.shop_SHID='$shop_id' OR i.is_default=1) GROUP BY i.`products_PDID` LIMIT 1";
            if($is_commonStock==1)
            {
                $sql="SELECT *, SUM(CurrentQty) AS CurrentQty  FROM `inventory` i 
                INNER JOIN shop s ON s.SHID=i.shop_SHID
                WHERE i.`products_PDID` ='$product_id' AND s.Company_CMID='$company_id' GROUP BY i.`products_PDID` LIMIT 1";
            }
            $inventoryData = $dbObj->getData($sql); 
            if(isset($inventoryData[0]["CurrentQty"]))
            {
                $filepath="../Assets/Images/prod_images/$row[ProdImage]";
                if(isset($row["ProdImage"]) && $row["ProdImage"]!="" && file_exists("../".$filepath))
                {
                    $filepath="../Assets/Images/prod_images/$row[ProdImage]";
                }
                else
                {
                    $filepath="../Assets/no-image-2.jpg";
                }
                $procount+=1;
                $inventoryData['CurrentQty'] = $inventoryData[0]['CurrentQty'] * $row['UnitConversion'];
                ?>
                <div class="product-card product mb-2 subcat-<?=$row["SCID"]?>">
                    <div class="d-none">
                        <input type="hidden" name="" id="productid" value="<?=$row["PDID"]?>">
                    </div>
                    <div class="product-image">
                        <img src="<?=$filepath?>" class="pro-image">
                    </div>
                    <div class="product-body">
                        <h6 class="product-title" title="<?=htmlspecialchars($row["ItemName"])?>">
                            <?=$row["ItemName"]?>
                        </h6>
                        <div class="product-meta">
                            <div>
                                <span>Barcode</span>
                                <strong><?=$row["Barcode"]?></strong>
                            </div>
                            <div>
                                <span>Sub Category</span>
                                <strong><?=$row["SubCatName"]?></strong>
                            </div>
                            <div>
                                <span>Type</span>
                                <strong>
                                    <?php
                                    if($row["ItemType"]=="P")
                                    {
                                        echo "Product";
                                    }
                                    else
                                    {
                                        echo "Service";
                                    }?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            else
            {
        
            }
        }
        
        
    }
    ?>
    <script>
        $("#procount").text("<?=$procount?>");
    </script>
    <?php
}
else if(!isset($_GET["product_id"]) && (isset($_GET["subcat"]) || isset($_GET["value"])))
{
    
    if(isset($_GET["subcat"]))
    {
        $subcat = $_GET["subcat"];
        $add.=" AND sc.SCID='$subcat'";
    }
    if(isset($_GET["value"]))
    {
        $txt_search=$_GET["value"];
        $add.=" AND concat(p.Barcode, p.ItemName) LIKE '%".$txt_search."%'";
    }
    if($is_multicategory==1 )
    {
        $sql = "SELECT * FROM `products` p 
        INNER JOIN subcategories sc ON sc.SCID=p.Subcategories_SCID
        INNER JOIN shop s ON s.SHID=p.shop_SHID
        WHERE s.Company_CMID='$company_id' AND p.ProductStat=1 $add ORDER BY p.ItemName,p.ItemType ASC;";
        $shopie=1;
    }
    else
    {
        $sql = "SELECT * FROM `products` p 
        INNER JOIN subcategories sc ON sc.SCID=p.Subcategories_SCID
        WHERE p.shop_SHID='$shop_id' AND p.ProductStat=1 $add  ORDER BY p.ItemName,p.ItemType ASC;";
    }
    $productData = $dbObj->getData($sql); 
    

    $procount=0;
    foreach ($productData as $row) 
    {
        $product_id=$row["PDID"];
        if($row["ItemType"]=="P")
        {
            $sql="SELECT *, SUM(CurrentQty) AS CurrentQty  FROM `inventory` i WHERE i.`products_PDID` ='$product_id' AND i.shop_SHID='$shop_id' GROUP BY i.`products_PDID`";
            if($is_expire)
            {
                $sql="SELECT *, SUM(CurrentQty) AS CurrentQty  FROM `inventory` i 
                INNER JOIN pricehistory ph ON ph.Inventory_INID = i.INID
                WHERE i.`products_PDID` ='$product_id' AND i.shop_SHID='$shop_id' 
                    AND (ph.ExpDate IS NULL OR ph.ExpDate = '0000-00-00' OR ph.ExpDate > '$date')                
                GROUP BY i.`products_PDID`";
            }
            if($is_commonStock==1)
            {
                $sql="SELECT *, SUM(CurrentQty) AS CurrentQty  FROM `inventory` i 
                INNER JOIN shop s ON s.SHID=i.shop_SHID
                WHERE i.`products_PDID` ='$product_id' AND s.Company_CMID='$company_id' GROUP BY i.`products_PDID`";
                if($is_expire)
                {
                    $sql="SELECT *, SUM(CurrentQty) AS CurrentQty  FROM `inventory` i 
                    INNER JOIN pricehistory ph ON ph.Inventory_INID = i.INID
                    INNER JOIN shop s ON s.SHID=i.shop_SHID
                    WHERE i.`products_PDID` ='$product_id' AND s.Company_CMID='$company_id' 
                        AND (ph.ExpDate IS NULL OR ph.ExpDate = '0000-00-00' OR ph.ExpDate > '$date')
                    GROUP BY i.`products_PDID`";
                }
            }
            $inventoryData = $dbObj->getData($sql); 
            if(isset($inventoryData[0]["CurrentQty"]) && $inventoryData[0]["CurrentQty"] > 0)
            {
                $filepath="../Assets/Images/prod_images/".$row['ProdImage'];
                if(!empty($row[0]['ProdImage']))
                {
                    $filepath="../Assets/Images/prod_images/".$row['ProdImage'];
                }
                else
                {
                    $filepath="../Assets/no-image-2.jpg";
                }
                if(!file_exists("../".$filepath))
                {
                    $filepath="../Assets/no-image-2.jpg";
                }
                $procount+=1;
                $inventoryData['CurrentQty'] = $inventoryData[0]['CurrentQty'] * $row['UnitConversion'];
                ?>
                <div class="product-card product mb-2 ">
                    <div class="d-none">
                        <input type="hidden" name="" id="productid" value="<?=$row["PDID"]?>">
                    </div>
                    <div class="product-image">
                        <img src="<?=$filepath?>" class="pro-image">
                    </div>
                    <div class="product-body">
                        <h6 class="product-title" title="<?=htmlspecialchars($row["ItemName"])?>">
                            <?=$row["ItemName"]?>
                        </h6>
                        <div class="product-meta">
                            <div>
                                <span>Barcode</span>
                                <strong><?=$row["Barcode"]?></strong>
                            </div>
                            <div>
                                <span>Sub Category</span>
                                <strong><?=$row["SubCatName"]?></strong>
                            </div>
                            <div>
                                <span>Type</span>
                                <strong>
                                    <?php
                                    if($row["ItemType"]=="P")
                                    {
                                        echo "Product";
                                    }
                                    else
                                    {
                                        echo "Service";
                                    }?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            else
            {
                if($is_minus==1)
                {
                   

                    $sql="SELECT *, SUM(CurrentQty) AS CurrentQty  FROM `inventory` i WHERE i.`products_PDID` ='$product_id' AND (i.shop_SHID='$shop_id' OR i.is_default=1) GROUP BY i.`products_PDID` LIMIT 1";
                    if($is_commonStock==1)
                    {
                        $sql="SELECT *, SUM(CurrentQty) AS CurrentQty  FROM `inventory` i 
                        INNER JOIN shop s ON s.SHID=i.shop_SHID
                        WHERE i.`products_PDID` ='$product_id' AND s.Company_CMID='$company_id' GROUP BY i.`products_PDID` LIMIT 1";
                    }
                    $inventoryData = $dbObj->getData($sql); 
                    if(isset($inventoryData[0]["CurrentQty"]))
                    {
                        $filepath="../Assets/Images/prod_images/$row[ProdImage]";
                        if(isset($row["ProdImage"]) && $row["ProdImage"]!="" && file_exists("../".$filepath))
                        {
                            $filepath="../Assets/Images/prod_images/$row[ProdImage]";
                        }
                        else
                        {
                            $filepath="../Assets/no-image-2.jpg";
                        }
                        $procount+=1;
                        $inventoryData['CurrentQty'] = $inventoryData[0]['CurrentQty'] * $row['UnitConversion'];
                        ?>
                        <div class="product-card product mb-2 subcat-<?=$row["SCID"]?>">
                            <div class="d-none">
                                <input type="hidden" name="" id="productid" value="<?=$row["PDID"]?>">
                            </div>
                            <div class="product-image">
                                <img src="<?=$filepath?>" class="pro-image">
                            </div>
                            <div class="product-body">
                                <h6 class="product-title" title="<?=htmlspecialchars($row["ItemName"])?>">
                                    <?=$row["ItemName"]?>
                                </h6>
                                <div class="product-meta">
                                    <div>
                                        <span>Barcode</span>
                                        <strong><?=$row["Barcode"]?></strong>
                                    </div>
                                    <div>
                                        <span>Sub Category</span>
                                        <strong><?=$row["SubCatName"]?></strong>
                                    </div>
                                    <div>
                                        <span>Type</span>
                                        <strong>
                                            <?php
                                            if($row["ItemType"]=="P")
                                            {
                                                echo "Product";
                                            }
                                            else
                                            {
                                                echo "Service";
                                            }?>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php
                    }
                    else
                    {
        
                    }
                }
                else
                {
        
                }    
            }
        }
        else
        {
            $sql="SELECT *, SUM(CurrentQty) AS CurrentQty  FROM `inventory` i WHERE i.`products_PDID` ='$product_id' AND (i.shop_SHID='$shop_id' OR i.is_default=1) GROUP BY i.`products_PDID` LIMIT 1";
            if($is_commonStock==1)
            {
                $sql="SELECT *, SUM(CurrentQty) AS CurrentQty  FROM `inventory` i 
                INNER JOIN shop s ON s.SHID=i.shop_SHID
                WHERE i.`products_PDID` ='$product_id' AND s.Company_CMID='$company_id' GROUP BY i.`products_PDID` LIMIT 1";
            }
            $inventoryData = $dbObj->getData($sql); 
            if(isset($inventoryData[0]["CurrentQty"]))
            {
                $filepath="../Assets/Images/prod_images/$row[ProdImage]";
                if(isset($row["ProdImage"]) && $row["ProdImage"]!="" && file_exists("../".$filepath))
                {
                    $filepath="../Assets/Images/prod_images/$row[ProdImage]";
                }
                else
                {
                    $filepath="../Assets/no-image-2.jpg";
                }
                $procount+=1;
                $inventoryData['CurrentQty'] = $inventoryData[0]['CurrentQty'] * $row['UnitConversion'];
                ?>
                <div class="product-card product mb-2 subcat-<?=$row["SCID"]?>">
                    <div class="d-none">
                        <input type="hidden" name="" id="productid" value="<?=$row["PDID"]?>">
                    </div>
                    <div class="product-image">
                        <img src="<?=$filepath?>" class="pro-image">
                    </div>
                    <div class="product-body">
                        <h6 class="product-title" title="<?=htmlspecialchars($row["ItemName"])?>">
                            <?=$row["ItemName"]?>
                        </h6>
                        <div class="product-meta">
                            <div>
                                <span>Barcode</span>
                                <strong><?=$row["Barcode"]?></strong>
                            </div>
                            <div>
                                <span>Sub Category</span>
                                <strong><?=$row["SubCatName"]?></strong>
                            </div>
                            <div>
                                <span>Type</span>
                                <strong>
                                    <?php
                                    if($row["ItemType"]=="P")
                                    {
                                        echo "Product";
                                    }
                                    else
                                    {
                                        echo "Service";
                                    }?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            else
            {
        
            }
        }
        
        
    }
    ?>
    <script>
        $("#procount").text("<?=$procount?>");
    </script>
    <?php
}
elseif (isset($_GET["product_id"])) {

    $product_id = $_GET["product_id"];
    $shopie = 0;
    
    // Fetch product data
    $sql = "SELECT * FROM `products` p ";
    if ($is_multicategory == 1 || $is_commonStock == 1) {
        $sql .= "INNER JOIN shop s ON s.SHID=p.shop_SHID WHERE s.Company_CMID='$company_id'";
        $shopie = 1;
    } else {
        $sql .= "WHERE p.shop_SHID='$shop_id'";
    }
    $sql .= " AND p.PDID='$product_id' AND p.ProductStat=1 ORDER BY p.ItemName, p.ItemType ASC";
    
    $productData = $dbObj->getData($sql);
    if (!$productData) returnError("00004", "Product not found");
    
    $row = $productData[0];

   
    
    if ($row["ItemType"] == "P") {
        $stockType = $shopdata[0]["StockTypes_STID"];
        $is_minus = $shopdata[0]["is_minus"] ?? false;
        $is_expire = $shopdata[0]["is_expire"] ?? false;
        
        $inventoryData = fetchInventoryData(product_id: $product_id, shop_id: $shop_id, company_id: $company_id, is_commonStock: $is_commonStock, stockType: $stockType, is_default: false,is_expire: $is_expire, is_minus : false);
        
        if (!$inventoryData && $is_minus) {
            $inventoryData = fetchInventoryData(product_id: $product_id, shop_id: $shop_id, company_id: $company_id, is_commonStock: $is_commonStock, stockType: $stockType, is_default: true,is_expire: $is_expire,is_minus : true);
        }
        
        if (!$inventoryData) returnError("00003", "No Stock Available");
        
        // Calculate discount
        list($discountType, $discount, $subtotal) = calculateDiscount($inventoryData[0]["SellingPrice"], $row);
        if($row['UnitConversion']==0.000 || $row['UnitConversion']==0 || $row['UnitConversion']=="0.000")
        {
            $row['UnitConversion']=1.000;
        }
        $inventoryData[0]["TotalCurrentQty"]=$inventoryData[0]["TotalCurrentQty"]*$row["UnitConversion"];
        // Return response
        echo json_encode([
            "product" => $row,
            "discountType" => $discountType,
            "discount" => $discount,
            "unitPrice" => $inventoryData[0]["SellingPrice"],
            "cost" => $inventoryData[0]["PurchasePrice"],
            "inventory" => $inventoryData
        ]);
    }
    else
    {
        $stockType = $shopdata[0]["StockTypes_STID"];
        $is_minus = $shopdata[0]["is_minus"] ?? false;
        
        $inventoryData = fetchInventoryData(product_id: $product_id, shop_id: $shop_id, company_id: $company_id, is_commonStock: $is_commonStock, stockType: $stockType, is_default: true,is_expire: false,is_minus:0);
        
        if (!$inventoryData) returnError("00003", "No Stock Available");
        
        // Calculate discount
        list($discountType, $discount, $subtotal) = calculateDiscount($inventoryData[0]["SellingPrice"], $row);
        if($row['UnitConversion']==0.000 || $row['UnitConversion']==0 || $row['UnitConversion']=="0.000")
        {
            $row['UnitConversion']=1.000;
        }
        $inventoryData[0]["TotalCurrentQty"]=$inventoryData[0]["TotalCurrentQty"]*1;
        // Return response
        echo json_encode([
            "product" => $row,
            "discountType" => $discountType,
            "discount" => $discount,
            "unitPrice" => $inventoryData[0]["SellingPrice"],
            "cost" => $inventoryData[0]["PurchasePrice"],
            "inventory" => $inventoryData
        ]);
    }
}

/**
 * Fetch inventory data based on stock type.
 */
function fetchInventoryData($product_id, $shop_id, $company_id, $is_commonStock, $stockType, $is_default, $is_expire, $is_minus) {
    global $dbObj;
    $condition = " ";
    $date = date("Y-m-d");

    $condition .= $is_default ? " AND i.is_default=1" : " AND i.CurrentQty > 0";

    if ($is_expire) {
        $condition .= " AND (ph.ExpDate IS NULL OR ph.ExpDate = '0000-00-00' OR ph.ExpDate > '$date')";
    }

    $order = ($stockType == 3) ? "DESC" : "ASC"; // LIFO vs FIFO
    
    $sql = "SELECT i.INID, SUM(i.CurrentQty) AS TotalCurrentQty, ph.SellingPrice AS SellingPrice, (CASE WHEN ph.PurchasePrice = 0 THEN SellingPrice ELSE ph.PurchasePrice END) AS PurchasePrice 
            FROM `inventory` i
            INNER JOIN pricehistory ph ON ph.Inventory_INID = i.INID ";

    if ($is_commonStock == 1) {
        $sql .= "INNER JOIN shop s ON s.SHID = i.shop_SHID 
                 WHERE i.products_PDID = '$product_id' 
                 AND s.Company_CMID = '$company_id' $condition ";
    } else {
        $sql .= "WHERE i.products_PDID = '$product_id' 
                 AND i.shop_SHID = '$shop_id' $condition ";
    }

    $sql .= "GROUP BY ph.SellingPrice ORDER BY i.INID $order ";
    
    return $dbObj->getData($sql); // Return all records
      
}


/**
 * Calculate discount based on percentage or flat amount.
 */
function calculateDiscount($unitPrice, $row) {
    $discount = 0;
    $discountType = 0;
    
    if (!empty($row["prodDiscount"]) && $row["prodDiscount"] != "0.00") {
        //$discount = ($unitPrice * $row["prodDiscount"]) / 100;
        $discount = $row["prodDiscount"];
        $discountType = 1;
    } elseif (!empty($row["prodFlatDiscount"]) && $row["prodFlatDiscount"] != "0.00") {
        $discount = $row["prodFlatDiscount"];
        $discountType = 2;
    }
    
    return [$discountType, $discount, $unitPrice - $discount];
}

/**
 * Return JSON error response and exit script.
 */
function returnError($code, $message) {
    echo json_encode(["error" => true, "code" => $code, "message" => $message]);
    exit;
}

?>
