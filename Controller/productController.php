<?php
include "../Includes/includes.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$shop_id = $_SESSION['shop_id'];
$prodObj = new Product();
$invObj = new Inventory();
$dbObj = new DBTransactions();
$priceObj = new PriceHistory();
$commObj = new Common();
//user id
$user_id = $_SESSION['user_id'];
date_default_timezone_set("Asia/Colombo");

if(isset($_GET["query"]) && !empty($_GET["query"]))
{
    $query = $_GET["query"];
    $response =[];
    if($query=="save")
    {
        // print_r($_FILES);
        // exit();
        //PDID, ProductNo, ProdImage, Barcode, ItemName, ProdDescription, SecondName, ProdPurchasePrice, ProdSellPrice, CartonQty, ProductStat, AddedDate, UpdatedDate, ItemType, user_USID, UpdateUserID, Subcategories_SCID, shop_SHID
        $productData = $prodObj->getProductCount();
        $prod_count = intval($productData[0]['ProductCount']);
        $prod_count += 1;

        //image count
        $productImage = $prodObj->getProductImageCount();
        $prod_image_count = intval($productImage[0]['ProducImagetCount']);
        $prod_image_count += 1;

        ;
        $product_no = $commObj->createCount("PD", $prod_count);
        $prod_image_name = $commObj->createCount("PI", $prod_image_count);

        $subcat_id = $_POST['cmb_subcategory'];
        if(!empty($_POST['barcode']))
        {
            $barcode = $_POST['barcode'];
        }
        else
        {
            $barcode = $product_no;
        }
        $prod_name = $_POST['prod_name'];
        $second_name = isset($_POST['second_name']) ? $_POST['second_name'] : "NULL";
        $prod_description = $_POST['prod_description'];

        //carton qty
        $prod_carton_qty = isset($_POST['prod_carton_qty']) ? $_POST['prod_carton_qty'] : 1;

        $prod_stat = 1;
        if(isset($_POST["chk_fp"]))
        {
            $chk_fp=1;
        }
        else
        {
            $chk_fp=0;
        }
        $is_lowStock = isset($_POST["chk_ls"]) ? 1 : 0;
        $lowqty = isset($_POST["lowqty"]) ? $_POST["lowqty"] : 0;
        //units
        if(isset($_POST['cmb_purchase_unit']))
        {
        $purchase_unit = $_POST['cmb_purchase_unit']; 
        }
        else
        {
            $purchase_unit = 0; 
        }
        if(isset($_POST['conversion_rate']))
        {
        $conversion_rate = $_POST['conversion_rate']; 
        }
        else
        {
            $conversion_rate = 1; 
        }
        if(isset($_POST['cmb_selling_unit']))
        {
        $selling_unit = $_POST['cmb_selling_unit']; 
        }
        else
        {
            $selling_unit = 0; 
        }

        //purchase price
        $prod_purchase_price = isset($_POST['prod_purchase_price']) ? $_POST['prod_purchase_price'] : 0;

        //selling price
        $prod_selling_price = isset($_POST['prod_selling_price']) ? $_POST['prod_selling_price'] : 0;

        //Item Discount    
        $prod_Discount = isset($_POST['prod_Item_Dis']) ? $_POST['prod_Item_Dis'] : 0;
        $prod_flat_Discount = isset($_POST['prod_Item_Dis_flat']) ? $_POST['prod_Item_Dis_flat'] : 0;

        if ($prod_Discount > 0) {        
            $prod_Discount = floatval($prod_Discount); 
        }
        
        if ($prod_flat_Discount > 0) {        
            $prod_flat_Discount = floatval($prod_flat_Discount); 
        }


        $use_service = "P";
        $shopObj = new Shop();
        if($shopObj->hasService($shop_id))
        {
            $use_service = isset($_POST['chk_service']) ? "S" : "P";
        }//has service
        else
        {
            $use_service = "P";
        }//no service
        
        //get current date
        date_default_timezone_set("Asia/Colombo");
        $this_date = date("Y-m-d");

        


        //file upload directry
        $target_dir = "../Assets/Images/prod_images/";
        if(!empty($_FILES['prod_image']['name']))
        {
            $file_type = pathinfo($_FILES['prod_image']['name'], PATHINFO_EXTENSION);
            $prod_image_name = $prod_image_name . "." . $file_type;
            $target_file_path = $target_dir . $prod_image_name;
        }
        
        //check subcategory
        if($subcat_id > 0)
        {
            //check barcode and item name
            if(!empty($barcode))
            {
                //check prodname
                if(!empty($prod_name))
                {
                    //check image
                    $allow_types = array('jpg','JPG','png','PNG','jpeg','JPEG','webp', 'WEBP');
                    if(!empty($_FILES['prod_image']['name']))
                    {
                        if(in_array($file_type, $allow_types))
                        {

                            $size = floatval($_FILES['prod_image']['size']);
                            $maxSize = 5 * 1024 * 1024; 
                            
                            if($size <= $maxSize)
                            {
                                if(move_uploaded_file($_FILES["prod_image"]["tmp_name"], $target_file_path))
                                {
                                    $product_id = $prodObj->setProduct(ProductNo: $product_no, ProdImage: $prod_image_name, Barcode: $barcode, ItemName: $prod_name, ProdDescription: $prod_description, SecondName: $second_name, ProdPurchasePrice: $prod_purchase_price, ProdSellPrice: $prod_selling_price, CartonQty: $prod_carton_qty, ProductStat: $prod_stat, AddedDate: $this_date, UpdatedDate: $this_date, ItemType: $use_service, user_USID: $user_id, UpdateUserID: $user_id, Subcategories_SCID: $subcat_id, shop_SHID: $shop_id, PurchaseUnit: $purchase_unit, UnitConversion: $conversion_rate, SellingUnit: $selling_unit, prod_Discount: $prod_Discount, Flat_discount: $prod_flat_Discount, chk_fp:$chk_fp, is_lowStock:$is_lowStock, low_stock_qty:$lowqty);
                                    // $product_id=$prodObj->getLastInsertedProID();
                                    // print_r($product_id);
                                    // $product_id=$product_id["LastID"];
                                    $count=$invObj->getInventorywithproductID($product_id);
                                    $count=$count[0]["procount"];
                                    $count=$count+1;
                                    $batch_id=$invObj->getSequence($count);
                                    $batch_id="B".$batch_id;
                                    $CurrentQty= 0;
                                    $BillQty=0;
                                    $ReturnQty=0;
                                    $TransfeInQty=0;
                                    $TransferOutQty=0;
                                    $RackID=1;
                                    $default=1;
                                    $invObj->setInventory2($CurrentQty, $BillQty, $ReturnQty, $TransfeInQty, $TransferOutQty, $product_id, $shop_id, $RackID, $batch_id,$default);
                                    $sql = "SELECT max(INID) AS MAXSID FROM inventory WHERE shop_SHID='$shop_id' AND products_PDID='$product_id';";
                                    $dbMax = $dbObj->getData($sql);
                                    $max_id = floatval($dbMax[0]['MAXSID']);
                                    $new_inventory_id = $max_id ;
                                    $grn_detail_id = 0;
                                    $BillQty=0;
                                    $ReturnQty=0;
                                    $TransfeInQty=0;
                                    $TransferOutQty=0;
                                    $RackID=1;
                                    $default=1;
                                    $mnf_date=null;
                                    $exp_date=null;
                                    $effective_date = date("Y-m-d");
                                    $priceObj->setPriceHistory($product_id, 0, $effective_date, $prod_purchase_price, $prod_selling_price, $prod_selling_price, $mnf_date, $exp_date, $batch_id, $new_inventory_id, $grn_detail_id);
                                    if(isset($_POST["openStock"]))
                                    {
                                        $count=$invObj->getInventorywithproductID($product_id);
                                        $count=$count[0]["procount"];
                                        $count=$count+1;
                                        $batch_id=$invObj->getSequence($count);
                                        $batch_id="B".$batch_id;
                                        $CurrentQty= $_POST["openStock"];
                                        $BillQty=0;
                                        $ReturnQty=0;
                                        $TransfeInQty=0;
                                        $TransferOutQty=0;
                                        $RackID=1;
                                        $default=0;
                                        $invObj->setInventory2($CurrentQty, $BillQty, $ReturnQty, $TransfeInQty, $TransferOutQty, $product_id, $shop_id, $RackID, $batch_id,$default);
                                        $sql = "SELECT max(INID) AS MAXSID FROM inventory WHERE shop_SHID='$shop_id' AND products_PDID='$product_id';";
                                        $dbMax = $dbObj->getData($sql);
                                        $max_id = floatval($dbMax[0]['MAXSID']);
                                        $new_inventory_id = $max_id ;
                                        $grn_detail_id = 0;
                                        $BillQty=0;
                                        $ReturnQty=0;
                                        $TransfeInQty=0;
                                        $TransferOutQty=0;
                                        $RackID=1;
                                        $default=1;
                                        $mnf_date=null;
                                        $exp_date=null;
                                        $effective_date = date("Y-m-d");
                                        $priceObj->setPriceHistory($product_id, 0, $effective_date, $prod_purchase_price, $prod_selling_price, $prod_selling_price, $mnf_date, $exp_date, $batch_id, $new_inventory_id, $grn_detail_id);
                                    }

                                    //save successfully
                                    $response =[
                                            "status"=>"success",
                                            "message"=>"Product Added Successfully 1"
                                    ];
                                    
                                }//move file
                                else
                                {
                                    //unable to save
                                    
                                    $response =[
                                            "status"=>"error",
                                            "message"=>"Oops! Something Went Wrong, Please Try Again"
                                    ];
                                }//cannot save image
                            }//less than 500kb
                            else
                            {
                                
                                $response =[
                                        "status"=>"error",
                                        "message"=>"Image size is greater than the accepted size."
                                ];
                                //wrong image size
                            }//greater than 500kb
                        }
                        else
                        {
                            $prod_image_name = null;
                            $product_id = $prodObj->setProduct(ProductNo: $product_no, ProdImage: $prod_image_name, Barcode: $barcode, ItemName: $prod_name, ProdDescription: $prod_description, SecondName: $second_name, ProdPurchasePrice: $prod_purchase_price, ProdSellPrice: $prod_selling_price, CartonQty: $prod_carton_qty, ProductStat: $prod_stat, AddedDate: $this_date, UpdatedDate: $this_date, ItemType: $use_service, user_USID: $user_id, UpdateUserID: $user_id, Subcategories_SCID: $subcat_id, shop_SHID: $shop_id, PurchaseUnit: $purchase_unit, UnitConversion: $conversion_rate, SellingUnit: $selling_unit,prod_Discount: $prod_Discount, Flat_discount: $prod_flat_Discount, chk_fp:$chk_fp, is_lowStock:$is_lowStock, low_stock_qty:$lowqty);
                            // $product_id=$prodObj->getLastInsertedProID();
                            //         print_r($product_id);
                            // $product_id=$product_id["LastID"];
                            $count=$invObj->getInventorywithproductID($product_id);
                            $count=$count[0]["procount"];
                            $count=$count+1;
                            $batch_id=$invObj->getSequence($count);
                            $batch_id="B".$batch_id;
                            $CurrentQty= 0;
                            $BillQty=0;
                            $ReturnQty=0;
                            $TransfeInQty=0;
                            $TransferOutQty=0;
                            $RackID=1;
                            $default=1;
                            $mnf_date=null;
                            $exp_date=null;
                            $effective_date = date("Y-m-d");
                            $invObj->setInventory2($CurrentQty, $BillQty, $ReturnQty, $TransfeInQty, $TransferOutQty, $product_id, $shop_id, $RackID, $batch_id,$default);
                            $sql = "SELECT max(INID) AS MAXSID FROM inventory WHERE shop_SHID='$shop_id' AND products_PDID='$product_id';";
                            $dbMax = $dbObj->getData($sql);
                            $max_id = floatval($dbMax[0]['MAXSID']);
                            $new_inventory_id = $max_id ;
                            $grn_detail_id = 0;
                            $priceObj->setPriceHistory($product_id, 0, $effective_date, $prod_purchase_price, $prod_selling_price, $prod_selling_price, $mnf_date, $exp_date, $batch_id, $new_inventory_id, $grn_detail_id);
                            //save successfully
                            if(isset($_POST["openStock"]))
                            {
                                $count=$invObj->getInventorywithproductID($product_id);
                                $count=$count[0]["procount"];
                                $count=$count+1;
                                $batch_id=$invObj->getSequence($count);
                                $batch_id="B".$batch_id;
                                $CurrentQty= $_POST["openStock"];
                                $BillQty=0;
                                $ReturnQty=0;
                                $TransfeInQty=0;
                                $TransferOutQty=0;
                                $RackID=1;
                                $default=0;
                                $invObj->setInventory2($CurrentQty, $BillQty, $ReturnQty, $TransfeInQty, $TransferOutQty, $product_id, $shop_id, $RackID, $batch_id,$default);
                                $sql = "SELECT max(INID) AS MAXSID FROM inventory WHERE shop_SHID='$shop_id' AND products_PDID='$product_id';";
                                $dbMax = $dbObj->getData($sql);
                                $max_id = floatval($dbMax[0]['MAXSID']);
                                $new_inventory_id = $max_id ;
                                $grn_detail_id = 0;
                                $BillQty=0;
                                $ReturnQty=0;
                                $TransfeInQty=0;
                                $TransferOutQty=0;
                                $RackID=1;
                                $default=1;
                                $mnf_date=null;
                                $exp_date=null;
                                $effective_date = date("Y-m-d");
                                $priceObj->setPriceHistory($product_id, 0, $effective_date, $prod_purchase_price, $prod_selling_price, $prod_selling_price, $mnf_date, $exp_date, $batch_id, $new_inventory_id, $grn_detail_id);
                            }
                            
                            $response =[
                                    "status"=>"success",
                                    "message"=>"Product Added Successfully 2"
                            ];
                        }
                        
                    }//is array
                    else
                    {
                        //save product without image
                        $prod_image_name = null;
                        $product_id = $prodObj->setProduct(ProductNo: $product_no, ProdImage: $prod_image_name, Barcode: $barcode, ItemName: $prod_name, ProdDescription: $prod_description, SecondName: $second_name, ProdPurchasePrice: $prod_purchase_price, ProdSellPrice: $prod_selling_price, CartonQty: $prod_carton_qty, ProductStat: $prod_stat, AddedDate: $this_date, UpdatedDate: $this_date, ItemType: $use_service, user_USID: $user_id, UpdateUserID: $user_id, Subcategories_SCID: $subcat_id, shop_SHID: $shop_id, PurchaseUnit: $purchase_unit, UnitConversion: $conversion_rate, SellingUnit: $selling_unit,prod_Discount: $prod_Discount, Flat_discount: $prod_flat_Discount, chk_fp:$chk_fp, is_lowStock:$is_lowStock, low_stock_qty:$lowqty);
                        // $product_id=$prodObj->getLastInsertedProID();
                        //             print_r($product_id);
                        // $product_id=$product_id["LastID"];
                        $count=$invObj->getInventorywithproductID($product_id);
                        $count=$count[0]["procount"];
                        $count=$count+1;
                        $batch_id=$invObj->getSequence($count);
                        $batch_id="B".$batch_id;
                        $CurrentQty= 0;
                        $BillQty=0;
                        $ReturnQty=0;
                        $TransfeInQty=0;
                        $TransferOutQty=0;
                        $RackID=1;
                        $default=1;
                        $mnf_date="";
                        $exp_date="";
                        $effective_date = date("Y-m-d");
                        $invObj->setInventory2($CurrentQty, $BillQty, $ReturnQty, $TransfeInQty, $TransferOutQty, $product_id, $shop_id, $RackID, $batch_id,$default);
                        $sql = "SELECT max(INID) AS MAXSID FROM inventory WHERE shop_SHID='$shop_id' AND products_PDID='$product_id';";
                        $dbMax = $dbObj->getData($sql);
                        $max_id = floatval($dbMax[0]['MAXSID']);
                        $new_inventory_id = $max_id ;
                        $grn_detail_id = 0;
                        $priceObj->setPriceHistory($product_id, 0, $effective_date, $prod_purchase_price, $prod_selling_price, $prod_selling_price, $mnf_date, $exp_date, $batch_id, $new_inventory_id, $grn_detail_id);
                        //save successfully  
                        if(isset($_POST["openStock"]))
                        {
                            $count=$invObj->getInventorywithproductID($product_id);
                            $count=$count[0]["procount"];
                            $count=$count+1;
                            $batch_id=$invObj->getSequence($count);
                            $batch_id="B".$batch_id;
                            $CurrentQty= $_POST["openStock"];
                            $BillQty=0;
                            $ReturnQty=0;
                            $TransfeInQty=0;
                            $TransferOutQty=0;
                            $RackID=1;
                            $default=0;
                            $invObj->setInventory2($CurrentQty, $BillQty, $ReturnQty, $TransfeInQty, $TransferOutQty, $product_id, $shop_id, $RackID, $batch_id,$default);
                            $sql = "SELECT max(INID) AS MAXSID FROM inventory WHERE shop_SHID='$shop_id' AND products_PDID='$product_id';";
                            $dbMax = $dbObj->getData($sql);
                            $max_id = floatval($dbMax[0]['MAXSID']);
                            $new_inventory_id = $max_id ;
                            $grn_detail_id = 0;
                            $BillQty=0;
                            $ReturnQty=0;
                            $TransfeInQty=0;
                            $TransferOutQty=0;
                            $RackID=1;
                            $default=1;
                            $mnf_date=null;
                            $exp_date=null;
                            $effective_date = date("Y-m-d");
                            $priceObj->setPriceHistory($product_id, 0, $effective_date, $prod_purchase_price, $prod_selling_price, $prod_selling_price, $mnf_date, $exp_date, $batch_id, $new_inventory_id, $grn_detail_id);
                        }                      
                        $response =[
                                "status"=>"success",
                                "message"=>"Product Added Successfully 3"
                        ];
                    }//not in array
                }//has name
                else
                {
                    $response =[
                            "status"=>"error",
                            "message"=>"Missing product information"
                    ];
                }//no prod name
            }//has barcode and name
            else
            {
                $response =[
                        "status"=>"error",
                        "message"=>"Missing product information"
                ];
            }//no barcode and name
        }//has category
        else
        {
            $response =[
                    "status"=>"error",
                    "message"=>"Missing product information"
            ];
        }//no category
        echo json_encode($response);
    }
    else if($query=="update_new")
    {
        $alerts = [];

        $PDID = isset($_GET["PDID"]) ? (int)$_GET["PDID"] : 0;

        $prodStatus = !empty($_POST["prodStatus"]) ? 1 : 0;
        $cmb_subcategory = isset($_POST["cmb_subcategory"]) ? (int)$_POST["cmb_subcategory"] : 0;
        $barcode = trim($_POST["barcode"] ?? "");
        $prod_name = trim($_POST["prod_name"] ?? "");
        $prod_description = trim($_POST["prod_description"] ?? "");
        $second_name = trim($_POST["second_name"] ?? "");
        $prod_purchase_price = isset($_POST["prod_purchase_price"]) ? (float)$_POST["prod_purchase_price"] : 0;
        $prod_selling_price = isset($_POST["prod_selling_price"]) ? (float)$_POST["prod_selling_price"] : 0;
        $chk_fp = !empty($_POST["chk_fp"]) ? 1 : 0;
        $chk_ls = !empty($_POST["chk_ls"]) ? 1 : 0;
        $chk_service = !empty($_POST["chk_service"]) ? "S" : "P";
        $openStock = isset($_POST["openStock"]) ? (float)$_POST["openStock"] : 0;
        $lowqty = isset($_POST["lowqty"]) ? (float)$_POST["lowqty"] : 0;
        $cmb_purchase_unit = trim($_POST["cmb_purchase_unit"] ?? "");
        $cmb_selling_unit = trim($_POST["cmb_selling_unit"] ?? "");
        $conversion_rate = isset($_POST["conversion_rate"]) ? (float)$_POST["conversion_rate"] : 0;
        $prod_Item_Dis = isset($_POST["prod_Item_Dis"]) ? (float)$_POST["prod_Item_Dis"] : 0;

        $prod_Item_Dis_flat = isset($_POST["prod_Item_Dis_flat"]) ? (float)$_POST["prod_Item_Dis_flat"] : 0;


        /*
        * Validate the main required fields.
        */
        if ($PDID <= 0) {
            echo json_encode([
                "status" => "error",
                "alerts" => [
                    [
                        "type"    => "error",
                        "section" => "product",
                        "message" => "Missing or invalid product ID"
                    ]
                ]
            ]);
            exit;
        }

        if ($cmb_subcategory <= 0 || $prod_name === "") {
            echo json_encode([
                "status" => "error",
                "alerts" => [
                    [
                        "type"    => "error",
                        "section" => "validation",
                        "message" => "Please fill all required fields"
                    ]
                ]
            ]);
            exit;
        }


        /*
        * Find the product.
        */
        $productSelectSql = " SELECT ProductNo, ProdImage
            FROM products
            WHERE PDID = $PDID
            LIMIT 1
        ";

        $productSelectData = $dbObj->getData($productSelectSql);

        if (empty($productSelectData)) {
            echo json_encode([
                "status" => "error",
                "alerts" => [
                    [
                        "type"    => "error",
                        "section" => "product",
                        "message" => "Product not found"
                    ]
                ]
            ]);
            exit;
        }

        $ProductNo = $productSelectData[0]["ProductNo"];
        $oldProductImage = trim($productSelectData[0]["ProdImage"] ?? "");
        $barcode = $barcode === "" ? $ProductNo : $barcode;


        /*
        * ---------------------------------------------------------
        * 1. UPDATE PRODUCT
        * ---------------------------------------------------------
        */
        $productUpdateData = [
            "Barcode"            => $barcode,
            "ItemName"           => $prod_name,
            "ProdDescription"    => $prod_description,
            "SecondName"         => $second_name,
            "ProdPurchasePrice"  => $prod_purchase_price,
            "ProdSellPrice"      => $prod_selling_price,
            "ProductStat"        => $prodStatus,
            "UpdatedDate"        => date("Y-m-d H:i:s"),
            "ItemType"           => $chk_service,
            "UpdateUserID"       => $user_id,
            "Subcategories_SCID" => $cmb_subcategory,
            "PurchaseUnit"       => $cmb_purchase_unit,
            "UnitConversion"     => $conversion_rate,
            "SellingUnit"        => $cmb_selling_unit,
            "prodDiscount"       => $prod_Item_Dis,
            "is_fixedPrice"      => $chk_fp,
            "is_lowStock"        => $chk_ls,
            "low_stock_qty"      => $lowqty,
            "prodFlatDiscount"   => $prod_Item_Dis_flat
        ];

        $productWhere = [
            "PDID" => $PDID
        ];

        $productUpdate = $dbObj->updateData(
            "products",
            $productUpdateData,
            $productWhere
        );

        if ($productUpdate === false) {
            $alerts[] = [ "type"    => "error",
                "section" => "product",
                "message" => "Product information could not be updated"
            ];
        } elseif ((int)$productUpdate === 0) {
            
        } else {
            $alerts[] = [
                "type"    => "success",
                "section" => "product",
                "message" => "Product information updated successfully"
            ];
        }


        /*
        * ---------------------------------------------------------
        * 2. FIND OPENING-STOCK INVENTORY
        * ---------------------------------------------------------
        */
        $inventorySql = " SELECT INID
            FROM inventory
            WHERE products_PDID = $PDID
            AND shop_SHID = $shop_id
            AND is_openStock = 1
            AND is_default != 1
            ORDER BY INID ASC
            LIMIT 1
        ";

        $inventoryData = $dbObj->getData($inventorySql);

        $INID = 0;

        if (empty($inventoryData)) {
            $alerts[] = [
                "type"    => "error",
                "section" => "inventory",
                "message" => "Opening-stock inventory record was not found"
            ];

            /*
            * Price history cannot be updated without an inventory ID.
            */
            $alerts[] = [
                "type"    => "warning",
                "section" => "pricehistory",
                "message" => "Price history was not updated because inventory was not found"
            ];
        } else {
            $INID = (int)$inventoryData[0]["INID"];


            /*
            * -----------------------------------------------------
            * 3. UPDATE INVENTORY
            * -----------------------------------------------------
            */
            $inventoryUpdateData = [
                "CurrentQty" => $openStock
            ];

            $inventoryWhere = [
                "INID"      => $INID,
                "shop_SHID" => $shop_id
            ];

            $inventoryUpdate = $dbObj->updateData(
                "inventory",
                $inventoryUpdateData,
                $inventoryWhere
            );

            if ($inventoryUpdate === false) {
                $alerts[] = [
                    "type"    => "error",
                    "section" => "inventory",
                    "message" => "Opening stock could not be updated"
                ];
            } elseif ((int)$inventoryUpdate === 0) {

            } else {
                $alerts[] = [
                    "type"    => "success",
                    "section" => "inventory",
                    "message" => "Opening stock updated successfully"
                ];
            }


            /*
            * -----------------------------------------------------
            * 4. FIND CORRESPONDING PRICE HISTORY
            * -----------------------------------------------------
            */
            $priceHistorySql = "
                SELECT PHID
                FROM pricehistory
                WHERE Inventory_INID = $INID
                ORDER BY PHID ASC
                LIMIT 1
            ";

            $priceHistoryData = $dbObj->getData($priceHistorySql);

            if (empty($priceHistoryData)) {
                $alerts[] = [
                    "type"    => "error",
                    "section" => "pricehistory",
                    "message" => "Corresponding price-history record was not found"
                ];
            } else {
                $PHID = (int)$priceHistoryData[0]["PHID"];


                /*
                * -------------------------------------------------
                * 5. UPDATE PRICE HISTORY
                * -------------------------------------------------
                */
                $priceHistoryUpdateData = [
                    "ProductID"     => $PDID,
                    "EffectiveDate" => date("Y-m-d"),
                    "PurchasePrice" => $prod_purchase_price,
                    "SellingPrice"  => $prod_selling_price
                ];

                $priceHistoryWhere = [
                    "PHID"           => $PHID,
                    "Inventory_INID" => $INID
                ];

                $priceHistoryUpdate = $dbObj->updateData(
                    "pricehistory",
                    $priceHistoryUpdateData,
                    $priceHistoryWhere
                );

                if ($priceHistoryUpdate === false) {
                    $alerts[] = [
                        "type"    => "error",
                        "section" => "pricehistory",
                        "message" => "Purchase and selling prices could not be updated"
                    ];
                } elseif ((int)$priceHistoryUpdate === 0) {

                } else {
                    $alerts[] = [
                        "type"    => "success",
                        "section" => "pricehistory",
                        "message" => "Purchase and selling prices updated successfully"
                    ];
                }
            }
        }

        /*
        * ---------------------------------------------------------
        * UPDATE PRODUCT IMAGE SEPARATELY
        * ---------------------------------------------------------
        */
        if (isset($_FILES["prod_image"]) && $_FILES["prod_image"]["error"] !== UPLOAD_ERR_NO_FILE ) 
        {
            $uploadedImage = $_FILES["prod_image"];
            /*
            * Check for PHP upload errors.
            */
            if ($uploadedImage["error"] !== UPLOAD_ERR_OK) {
                $alerts[] = [
                    "type"    => "error",
                    "section" => "image",
                    "message" => "Product image upload failed"
                ];
            } 
            else 
            {
                $maxImageSize = 5 * 1024 * 1024; 
                /*
                * Validate the file size. 5MB
                */
                if ((int)$uploadedImage["size"] > $maxImageSize) 
                {
                    $alerts[] = [
                        "type"    => "error",
                        "section" => "image",
                        "message" => "Product image must be 5 MB or smaller"
                    ];
                } 
                else 
                {
                    /*
                    * Validate the actual MIME type instead of trusting
                    * the extension provided by the browser.
                    */
                    $fileInfo = new finfo(FILEINFO_MIME_TYPE);

                    $mimeType = $fileInfo->file($uploadedImage["tmp_name"]);

                    $allowedImageTypes = [
                        "image/jpeg" => "jpg",
                        "image/png"  => "png",
                        "image/webp" => "webp"
                    ];

                    if (!isset($allowedImageTypes[$mimeType])) 
                    {
                        $alerts[] = [
                            "type"    => "error",
                            "section" => "image",
                            "message" => "Only JPG, PNG and WEBP images are allowed"
                        ];
                    } 
                    else 
                    {
                        $fileExtension = $allowedImageTypes[$mimeType];

                        /*
                        * Rename the image using the product ID.
                        *
                        * Examples:
                        * 25.jpg
                        * 25.png
                        * 25.webp
                        */
                        $prod_image_name = $commObj->createCount("PI", $PDID);
                        $newProductImage = $prod_image_name . "." . $fileExtension;

                        /*
                        * Adjust this path if the AJAX PHP file is located
                        * at a different directory level.
                        */
                        $targetDirectory = __DIR__ . "/../Assets/Images/prod_images/";

                        $targetFilePath = $targetDirectory . $newProductImage;

                        /*
                        * Ensure the product image directory exists.
                        */
                        if (!is_dir($targetDirectory)) {
                            $alerts[] = [
                                "type"    => "error",
                                "section" => "image",
                                "message" => "Product image directory was not found"
                            ];
                        } elseif (!is_writable($targetDirectory)) {
                            $alerts[] = [
                                "type"    => "error",
                                "section" => "image",
                                "message" => "Product image directory is not writable"
                            ];
                        } else {
                            /*
                            * Upload to a temporary filename first.
                            * This protects the existing image if the upload fails.
                            */
                            $temporaryFileName = "temp_product_" . $PDID . "_" . uniqid() . "." . $fileExtension;

                            $temporaryFilePath = $targetDirectory . $temporaryFileName;

                            $imageMoved = move_uploaded_file($uploadedImage["tmp_name"], $temporaryFilePath);

                            if (!$imageMoved) {
                                $alerts[] = [
                                    "type"    => "error",
                                    "section" => "image",
                                    "message" => "Product image could not be uploaded"
                                ];
                            } else {
                                /*
                                * Store a temporary backup when the old image
                                * and new image have the same filename.
                                */
                                $oldImagePath = "";

                                if ($oldProductImage !== "") {
                                    $oldImagePath =
                                        $targetDirectory .
                                        basename($oldProductImage);
                                }

                                $backupImagePath = "";

                                if ($oldImagePath !== "" && is_file($oldImagePath)
                                ) {
                                    $backupImagePath = $targetDirectory . "backup_" . $PDID . "_" . uniqid() . "_" . basename($oldProductImage);

                                    if (!rename( $oldImagePath, $backupImagePath )) 
                                    {
                                        @unlink($temporaryFilePath);

                                        $alerts[] = [
                                            "type"    => "error",
                                            "section" => "image",
                                            "message" => "Existing product image could not be prepared for replacement"
                                        ];

                                        $temporaryFilePath = "";
                                    }
                                }

                                if ( $temporaryFilePath !== "" && is_file($temporaryFilePath) ) 
                                {
                                    /*
                                    * Move the uploaded image to its final name.
                                    */
                                    $finalImageMoved = rename( $temporaryFilePath, $targetFilePath );

                                    if (!$finalImageMoved) 
                                    {
                                        /*
                                        * Restore the old image if available.
                                        */
                                        if ( $backupImagePath !== "" && is_file($backupImagePath)) 
                                        {
                                            rename(
                                                $backupImagePath,
                                                $oldImagePath
                                            );
                                        }

                                        @unlink($temporaryFilePath);

                                        $alerts[] = [
                                            "type"    => "error",
                                            "section" => "image",
                                            "message" => "New product image could not be saved"
                                        ];
                                    } 
                                    else 
                                    {
                                        /*
                                        * Update only the product image field.
                                        */
                                        $imageDatabaseUpdate = $dbObj->updateData("products",
                                                [
                                                    "ProdImage" =>
                                                        $newProductImage
                                                ],
                                                [
                                                    "PDID" => $PDID
                                                ]
                                            );

                                        if ($imageDatabaseUpdate === false) 
                                        {
                                            /*
                                            * Database failed:
                                            * remove the new image and restore
                                            * the previous one.
                                            */
                                            if (is_file($targetFilePath)) 
                                            {
                                                @unlink($targetFilePath);
                                            }

                                            if ($backupImagePath !== "" && is_file($backupImagePath)) 
                                            {
                                                rename(
                                                    $backupImagePath,
                                                    $oldImagePath
                                                );
                                            }

                                            $alerts[] = [
                                                "type"    => "error",
                                                "section" => "image",
                                                "message" => "Image uploaded, but the product image record could not be updated"
                                            ];
                                        } 
                                        else 
                                        {
                                            /*
                                            * Everything succeeded.
                                            * Permanently delete the old image backup.
                                            */
                                            if ($backupImagePath !== "" &&is_file($backupImagePath)) 
                                            {
                                                @unlink($backupImagePath);
                                            }

                                            $alerts[] = [
                                                "type"    => "success",
                                                "section" => "image",
                                                "message" => "Product image updated successfully"
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }


        /*
        * ---------------------------------------------------------
        * CALCULATE OVERALL RESULT
        * ---------------------------------------------------------
        */
        $errorCount = 0;
        $successCount = 0;
        $infoCount = 0;
        $warningCount = 0;

        foreach ($alerts as $alertItem) {
            switch ($alertItem["type"]) {
                case "success":
                    $successCount++;
                    break;

                case "error":
                    $errorCount++;
                    break;

                case "warning":
                    $warningCount++;
                    break;

                case "info":
                    $infoCount++;
                    break;
            }
        }

        if ($errorCount === 0 && $warningCount === 0) {
            $overallStatus = "success";
        } elseif ($successCount > 0 || $infoCount > 0) {
            $overallStatus = "partial";
        } else {
            $overallStatus = "error";
        }

        echo json_encode([
            "status" => $overallStatus,
            "summary" => [
                "success" => $successCount,
                "error"   => $errorCount,
                "warning" => $warningCount,
                "info"    => $infoCount
            ],
            "alerts" => $alerts
        ]);

        exit;

    }
    else if($query=="update")
    {
        $alert=[];
        $PDID = !empty($_POST["hidden_PDID"]) ? $_POST["hidden_PDID"] : 0;
        $editcmb_category = !empty($_POST["editcmb_category"]) ? $_POST["editcmb_category"] : 0;
        $editcmb_subcategory = !empty($_POST["editcmb_subcategory"]) ? $_POST["editcmb_subcategory"] : 0;
        $editbarcode = !empty($_POST["editbarcode"]) ? $_POST["editbarcode"] : "";
        $editprod_name = !empty($_POST["editprod_name"]) ? $_POST["editprod_name"] : "";
        $editsecond_name = !empty($_POST["editsecond_name"]) ? $_POST["editsecond_name"] : "";
        $editprod_description = !empty($_POST["editprod_description"]) ? $_POST["editprod_description"] : "";
        $editprod_purchase_price = !empty($_POST["editprod_purchase_price"]) ? $_POST["editprod_purchase_price"] : "";
        $editprod_selling_price = !empty($_POST["editprod_selling_price"]) ? $_POST["editprod_selling_price"] : "";
        $editprod_Item_Dis = !empty($_POST["editprod_Item_Dis"]) ? $_POST["editprod_Item_Dis"] : "";
        $editprod_Item_Dis_flat = !empty($_POST["editprod_Item_Dis_flat"]) ? $_POST["editprod_Item_Dis_flat"] : "";
        $editcmb_purchase_unit = !empty($_POST["editcmb_purchase_unit"]) ? $_POST["editcmb_purchase_unit"] : "";
        $editconversion_rate = !empty($_POST["editconversion_rate"]) ? $_POST["editconversion_rate"] : 1;
        $editcmb_selling_unit = !empty($_POST["editcmb_selling_unit"]) ? $_POST["editcmb_selling_unit"] : "";
        $ItemType="P";
        if(isset($_POST["editchk_service"]))
        {
            $ItemType="S";
        }
        $chk_fp=0;
        if(isset($_POST["editchk_fp"]))
        {
            $chk_fp=1;
        }
        if(isset($_POST["editprodStatus"]))
        {
            $prodStatus=1;
        }
        else
        {
            $prodStatus=0;
        }
        if($PDID!=0 && !empty($PDID))
        {
            if(!empty($editprod_name) && !empty($editcmb_category) && !empty($editcmb_subcategory) && !empty($editprod_purchase_price) && !empty($editprod_selling_price) && !empty($editconversion_rate))
            {
                $productData = $prodObj->getProductByPDID($PDID);

                if(empty($editbarcode))
                {
                    $editbarcode=$productData[0]["ProductNo"];
                }
                $editP=$prodObj->editProduct2( $editbarcode, $editprod_name, $editprod_description, $editsecond_name, $editprod_purchase_price, $editprod_selling_price, $ItemType, $user_id, $editcmb_subcategory, $shop_id, $editcmb_purchase_unit, $editconversion_rate, $editcmb_selling_unit, $PDID, $editprod_Item_Dis,$prodStatus,$editprod_Item_Dis_flat,$chk_fp);
                if($editP==true)
                {
                    $alert["success"][] =["Product updated successfully"];
                    
                    $prod_image_name = $commObj->createCount("PI", $productData[0]["PDID"]);
                    
                    if(!empty($_FILES['editprod_image']['name']))
                    {
                        $target_dir = "../Assets/Images/prod_images/";
                        if(!empty($productData[0]["ProdImage"]) && file_exists($target_dir.$productData[0]["ProdImage"]))
                        {
                            if(unlink($target_dir.$productData[0]["ProdImage"]))
                            {

                            }
                            else
                            {
                                $alert["error"][] =["Unable to delete the existing image"];
                            }
                        }
                        $allow_types = array('jpg','JPG','png','PNG','jpeg','JPEG','webp', 'WEBP');
                        $file_type = pathinfo($_FILES['editprod_image']['name'], PATHINFO_EXTENSION);
                        $prod_image_name = $prod_image_name . "." . $file_type;
                        $target_file_path = $target_dir . $prod_image_name;
                        if(in_array($file_type, $allow_types))
                        {
                            $size = floatval($_FILES['editprod_image']['size']);
                            $maxSize = 5 * 1024 * 1024; 
                            if($size <= $maxSize)
                            {
                                
                                if(move_uploaded_file($_FILES["editprod_image"]["tmp_name"], $target_file_path))
                                {
                                    
                                    $sql="UPDATE products SET ProdImage='$prod_image_name' WHERE PDID='$PDID'";
                                    if($dbObj->executeTransaction($sql))
                                    {
                                        $alert["success"][] =["Product image updated successfully"];
                                    }
                                    else
                                    {
                                        $alert["error"][] =["Unable update product image"];
                                    }
                                }
                                else
                                {
                                    $alert["error"][] =["Unable upload product image"];
                                }
                            }
                            else
                            {
                                $alert["error"][] =["Image size is greater than the accepted size."];
                            }
                        }
                        else
                        {
                            $alert["error"][] =["$file_type cannot be accepted for image."];
                        }
                        
                    }
                    $sql="SELECT i.INID, ph.PHID FROM inventory i INNER JOIN pricehistory ph ON ph.Inventory_INID = i.INID WHERE i.products_PDID='$PDID' AND i.is_default=1 AND i.shop_SHID='$shop_id' ";
                    $InvData = $dbObj->getData($sql);
                    if($InvData)
                    {
                        $PHID=$InvData[0]["PHID"];
                        $sql="UPDATE pricehistory SET PurchasePrice='$editprod_purchase_price', SellingPrice='$editprod_selling_price'  WHERE PHID='$PHID'";
                        if($dbObj->executeTransaction($sql))
                        {
                            $alert["success"][] =["Pricehistory updated successfully"];
                        }
                        else
                        {
                            $alert["error"][] =["Unable to update pricehistory"];
                        }

                    }
                    else
                    {
                        $alert["error"][] =["Unable to get inventory data"];
                    }                 

                }
                else
                {
                    $alert["error"][] =["Oops! Something went wrong"];
                }
            }
            else
            {
                $alert["error"][] =["Missing product information"];
            }
        }
        else
        {
            $alert["error"][] =["Missing PDID"];
        }
        if(!empty($alert))
        echo json_encode($alert);
    }
    else if($query=="multiEdit")
    {
        $alert=[];
        $pdidArray = [];

        if (isset($_POST['bulkhidden_PDID']) && !empty($_POST['bulkhidden_PDID'])) {
            $pdidArray = array_map('intval', explode(',', $_POST['bulkhidden_PDID']));
            
            if (!empty($_FILES['multieditprod_image']['name'])) 
                {
                    ;
                    $target_dir = "../Assets/Images/prod_images/";
                    $allow_types = array('jpg','JPG','png','PNG','jpeg','JPEG','webp','WEBP');

                    $file_type = pathinfo($_FILES['multieditprod_image']['name'], PATHINFO_EXTENSION);
                    $size = floatval($_FILES['multieditprod_image']['size']);
                    $maxSize = 5 * 1024 * 1024;

                    if (in_array($file_type, $allow_types)) {
                        if ($size <= $maxSize) {

                            $tempUploadedFile = $target_dir . "temp_" . time() . "." . $file_type;

                            if (move_uploaded_file($_FILES["multieditprod_image"]["tmp_name"], $tempUploadedFile)) {

                                foreach ($pdidArray as $PDID) {

                                    $productData = $prodObj->getProductByPDID($PDID);

                                    if (!empty($productData[0]["ProdImage"]) && file_exists($target_dir . $productData[0]["ProdImage"])) {
                                        unlink($target_dir . $productData[0]["ProdImage"]);
                                    }

                                    $prod_image_name = $commObj->createCount("PI", $PDID) . "." . $file_type;
                                    $target_file_path = $target_dir . $prod_image_name;

                                    if (copy($tempUploadedFile, $target_file_path)) {
                                        $sql = "UPDATE products SET ProdImage='$prod_image_name' WHERE PDID='$PDID'";
                                        if ($dbObj->executeTransaction($sql)) {
                                            $alert["success"][] = "Product image updated successfully";
                                        } else {
                                            $alert["error"][] = "Unable to update product image";
                                        }
                                    } else {
                                        $alert["error"][] = "Unable to copy product image";
                                    }
                                }

                                unlink($tempUploadedFile);

                            } else {
                                $alert["error"][] = "Unable to upload product image";
                            }

                        } else {
                            $alert["error"][] = "Image size is greater than the accepted size.";
                        }
                    } else {
                        $alert["error"][] = "$file_type cannot be accepted for image.";
                    }
            }
            foreach ($pdidArray as $PDID) 
            {
                if(!empty($_POST["multieditcmb_subcategory"]))
                {
                    $multieditcmb_subcategory = $_POST["multieditcmb_subcategory"];
                    $sql="UPDATE products SET Subcategories_SCID='$multieditcmb_subcategory' WHERE PDID='$PDID'";
                    $update = $dbObj->executeTransaction($sql);
                    if($update)
                    {
                        $alert["success"][]="Product subcategory updated successfully";
                    }
                    else
                    {
                        $alert["error"][]="Unable to update product subcategory";
                    }
                }
                if(isset($_POST["multieditprod_Item_Dis"]) && $_POST["multieditprod_Item_Dis"]!="")
                {
                    $multieditprod_Item_Dis = $_POST["multieditprod_Item_Dis"];
                    $sql="UPDATE products SET prodDiscount='$multieditprod_Item_Dis' WHERE PDID='$PDID'";
                    $update = $dbObj->executeTransaction($sql);
                    if($update)
                    {
                        $alert["success"][]="Product % discount updated successfully";
                    }
                    else
                    {
                        $alert["error"][]="Unable to update Product % discount";
                    }
                }
                if(isset($_POST["multieditprod_Item_Dis_flat"]) && $_POST["multieditprod_Item_Dis_flat"]!="")
                {
                    $multieditprod_Item_Dis_flat = $_POST["multieditprod_Item_Dis_flat"];
                    $sql="UPDATE products SET prodFlatDiscount='$multieditprod_Item_Dis_flat' WHERE PDID='$PDID'";
                    $update = $dbObj->executeTransaction($sql);
                    if($update)
                    {
                        $alert["success"][]="Product flat discount updated successfully";
                    }
                    else
                    {
                        $alert["error"][]="Unable to update Product flat discount";
                    }
                }
                if(isset($_POST["chk_fp"]))
                {
                    $chk_fp = $_POST["chk_fp"];
                    $sql="UPDATE products SET is_fixedPrice='1' WHERE PDID='$PDID'";
                    $update = $dbObj->executeTransaction($sql);
                    if($update)
                    {
                        $alert["success"][]="Product updated to fixed price successfully";
                    }
                    else
                    {
                        $alert["error"][]="Unable to update Product to fixed price";
                    }
                }
                if(isset($_POST["chk_ls"]))
                {
                    $chk_ls = $_POST["chk_ls"];
                    $productUpdateData = [
                        "is_lowStock"        => $chk_ls
                    ];

                    $productWhere = [
                        "PDID" => $PDID
                    ];

                    $productUpdate = $dbObj->updateData(
                        "products",
                        $productUpdateData,
                        $productWhere
                    );
                    if($productUpdate === false)
                    {
                        $alert["error"][]="Unable to update Product to alert Low Stock";
                    }
                    else
                    {
                        $alert["success"][]="Product updated to alert Low Stock";
                    }
                }
                if(isset($_POST["status"]))
                {
                    $status = $_POST["status"];
                    $productUpdateData = [
                        "ProductStat"        => $status
                    ];

                    $productWhere = [
                        "PDID" => $PDID
                    ];

                    $productUpdate = $dbObj->updateData(
                        "products",
                        $productUpdateData,
                        $productWhere
                    );
                    if($productUpdate === false)
                    {
                        $alert["error"][]="Unable to update Product status";
                    }
                    else
                    {
                        $alert["success"][]="Product Status updated";
                    }
                }
                if(isset($_POST["multieditprod_purchase_price"]) && $_POST["multieditprod_purchase_price"]!="")
                {
                    $multieditprod_purchase_price = $_POST["multieditprod_purchase_price"];
                    $sql="UPDATE products SET ProdPurchasePrice='$multieditprod_purchase_price' WHERE PDID='$PDID'";
                    $update = $dbObj->executeTransaction($sql);
                    if($update)
                    {
                        $alert["success"][]="Product purchase price updated successfully";
                        $sql="SELECT i.INID, ph.PHID FROM inventory i INNER JOIN pricehistory ph ON ph.Inventory_INID = i.INID WHERE i.products_PDID='$PDID' AND i.is_default=1 AND i.shop_SHID='$shop_id' ";
                        $InvData = $dbObj->getData($sql);
                        if($InvData)
                        {
                            $PHID=$InvData[0]["PHID"];
                            $sql="UPDATE pricehistory SET PurchasePrice='$multieditprod_purchase_price'  WHERE PHID='$PHID'";
                            if($dbObj->executeTransaction($sql))
                            {
                                $alert["success"][] ="Pricehistory updated with purchase price successfully";
                            }
                            else
                            {
                                $alert["error"][] ="Unable to update with purchase price pricehistory";
                            }

                        }
                    }
                    else
                    {
                        $alert["error"][]="Unable to update Product purchase price";
                    }
                    
                }
                if(isset($_POST["multieditprod_selling_price"]) && $_POST["multieditprod_selling_price"]!="")
                {
                    $multieditprod_selling_price = $_POST["multieditprod_selling_price"];
                    $sql="UPDATE products SET ProdSellPrice='$multieditprod_selling_price' WHERE PDID='$PDID'";
                    $update = $dbObj->executeTransaction($sql);
                    if($update)
                    {
                        $alert["success"][]="Product selling price updated successfully";
                        $sql="SELECT i.INID, ph.PHID FROM inventory i INNER JOIN pricehistory ph ON ph.Inventory_INID = i.INID WHERE i.products_PDID='$PDID' AND i.is_default=1 AND i.shop_SHID='$shop_id' ";
                        $InvData = $dbObj->getData($sql);
                        if($InvData)
                        {
                            $PHID=$InvData[0]["PHID"];
                            $sql="UPDATE pricehistory SET SellingPrice='$multieditprod_selling_price'  WHERE PHID='$PHID'";
                            if($dbObj->executeTransaction($sql))
                            {
                                $alert["success"][] ="Pricehistory updated with selling price successfully";
                            }
                            else
                            {
                                $alert["error"][] ="Unable to update with selling price pricehistory";
                            }

                        }
                    }
                    else
                    {
                        $alert["error"][]="Unable to update Product selling price";
                    }
                    
                }
            }
        }
        else
        {
            $alert["error"][]="No PDID Found";
        }
        if(!empty($alert))
        echo json_encode($alert);
    }
    else if ($query === "fetch_update") 
    {
        header("Content-Type: application/json");

        $alert   = [];
        $PDID    = (int)($_GET["PDID"] ?? 0);
        $shop_id = (int)($shop_id ?? 0);

        if ($PDID <= 0 || $shop_id <= 0) {
            echo json_encode([
                "status"  => 0,
                "message" => "Missing or invalid product/shop ID"
            ]);
            exit;
        }

        $inventorySql = "SELECT INID
            FROM inventory
            WHERE products_PDID = $PDID
            AND shop_SHID = $shop_id
            AND is_openStock = 1
            AND is_default != 1
            ORDER BY INID ASC
            LIMIT 1
        ";
        $inventoryData = $dbObj->getData($inventorySql);
        $select_pro_sql = "SELECT ProdPurchasePrice, ProdSellPrice FROM products WHERE PDID='$PDID';";
        $pro_data = $dbObj->getData($select_pro_sql);
        if(empty($pro_data))
        {
            echo json_encode([
                "status"  => 0,
                "message" => "Product Not Found"
            ]);
            exit;

        }
        $PurchasePrice = $pro_data[0]["ProdPurchasePrice"];
        $SellingPrice = $pro_data[0]["ProdSellPrice"];
        if (empty($inventoryData)) 
        {
            $countData=$invObj->getInventorywithproductID($PDID);
            $count = (int) ($countData[0]["procount"] ?? 0);
            $count=$count+1;
            $batch_id = "B" . $invObj->getSequence($count + 1);
            $mnf_date="";
            $exp_date="";
            $effective_date = date("Y-m-d");
            $insert_inventory = [
                "CurrentQty" => 0,
                "BillQty" => 0,
                "ReturnQty" => 0,
                "TransferInQty" => 0,
                "TransferOutQty" => 0,
                "Sup_Rtn" => 0,
                "products_PDID" => $PDID,
                "shop_SHID" => $shop_id,
                "RackID" => 1,
                "is_default" => 0,
                "is_openStock" => 1,
                "BatchID" => $batch_id
            ];
            $INID = $dbObj->insertAndGetId("inventory", $insert_inventory);
            if($INID > 0)
            {
                $insert_pricehistory = [
                    "ProductID" => $PDID,
                    "VariationID" => 0,
                    "EffectiveDate" => $effective_date,
                    "PurchasePrice" => $PurchasePrice,
                    "SellingPrice" => $SellingPrice,
                    "labelPrice" => $SellingPrice,
                    "MnfDate" => $mnf_date,
                    "ExpDate" => $exp_date,
                    "BatchID" => $batch_id,
                    "Inventory_INID" => $INID,
                    "GrnDetailID" => 0
                ];
                $PHID = $dbObj->insertAndGetId("pricehistory", $insert_pricehistory);
                if(empty($PHID))
                {                
                    echo json_encode([
                        "status"  => 0,
                        "message" => "Pricehistory Cannot Created"
                    ]);
                    exit;
                }
            }
            else
            {                
                echo json_encode([
                    "status"  => 0,
                    "message" => "Inventory Cannot Created"
                ]);
                exit;
            }
        }

        $sql = "SELECT
                p.*,
                sc.categories_CTID AS CTID,
                COALESCE(i.INID, 0) AS INID,
                COALESCE(i.CurrentQty, 0) AS OpeningStock,
                COALESCE(ph.PurchasePrice, 0) AS PurchasePrice,
                COALESCE(ph.SellingPrice, 0) AS SellingPrice

            FROM products p

            LEFT JOIN subcategories sc
                ON sc.SCID = p.Subcategories_SCID

            LEFT JOIN inventory i
                ON i.INID = (
                    SELECT MIN(i2.INID)
                    FROM inventory i2
                    WHERE i2.products_PDID = p.PDID
                    AND i2.shop_SHID = $shop_id
                    AND i2.is_default != 1
                    AND i2.is_openStock=1
                )

            LEFT JOIN pricehistory ph
                ON ph.Inventory_INID = i.INID

            WHERE p.PDID = $PDID
            LIMIT 1
        ";

        $data = $dbObj->getData($sql);

        if (empty($data)) {
            echo json_encode([
                "status"  => 0,
                "message" => "Product not found"
            ]);
            exit;
        }

        $row = $data[0];

        /*
        * Keep the product information separate from the related values.
        */
        $CTID          = (int)($row["CTID"] ?? 0);
        $INID          = (int)($row["INID"] ?? 0);
        $OpeningStock  = (float)($row["OpeningStock"] ?? 0);
        $PurchasePrice = (float)($row["PurchasePrice"] ?? 0);
        $SellingPrice  = (float)($row["SellingPrice"] ?? 0);

        unset(
            $row["CTID"],
            $row["INID"],
            $row["OpeningStock"],
            $row["PurchasePrice"],
            $row["SellingPrice"]
        );

        $alert = [
            "status" => 1,
            "products" => [
                "products"      => $row,
                "CTID"          => $CTID,
                "INID"          => $INID,
                "OpeningStock"  => $OpeningStock,
                "PurchasePrice" => $PurchasePrice,
                "SellingPrice"  => $SellingPrice
            ]
        ];

        echo json_encode($alert);
        exit;
    }
    else 
    {
        echo json_encode(["status"=>0,"message"=>"Invalid Query"]);
    }

    
}
if(isset($_POST['btn_save_product']))
{
    //PDID, ProductNo, ProdImage, Barcode, ItemName, ProdDescription, SecondName, ProdPurchasePrice, ProdSellPrice, CartonQty, ProductStat, AddedDate, UpdatedDate, ItemType, user_USID, UpdateUserID, Subcategories_SCID, shop_SHID
    $productData = $prodObj->getLastInsertedProID();
    $prod_count = intval($productData[0]['ProductCount']);
    $prod_count += 1;

    //image count
    $productImage = $prodObj->getProductImageCount();
    $prod_image_count = intval($productImage[0]['ProducImagetCount']);
    $prod_image_count += 1;

    ;
    $product_no = $commObj->createCount("PD", $prod_count);
    $prod_image_name = $commObj->createCount("PI", $prod_image_count);

    $subcat_id = $_POST['cmb_subcategory'];
    if(!empty($_POST['barcode']))
    {
        $barcode = $_POST['barcode'];
    }
    else
    {
        $barcode = $product_no;
    }
    $prod_name = $_POST['prod_name'];
    $second_name = isset($_POST['second_name']) ? $_POST['second_name'] : "NULL";
    $prod_description = $_POST['prod_description'];

    //carton qty
    $prod_carton_qty = isset($_POST['prod_carton_qty']) ? $_POST['prod_carton_qty'] : 1;

    $prod_stat = 1;
    if(isset($_POST["chk_fp"]))
    {
        $chk_fp=1;
    }
    else
    {
        $chk_fp=0;
    }

    //units
    if(isset($_POST['cmb_purchase_unit']))
    {
       $purchase_unit = $_POST['cmb_purchase_unit']; 
    }
    else
    {
        $purchase_unit = 0; 
    }
    if(isset($_POST['conversion_rate']))
    {
       $conversion_rate = $_POST['conversion_rate']; 
    }
    else
    {
        $conversion_rate = 1; 
    }
    if(isset($_POST['cmb_selling_unit']))
    {
       $selling_unit = $_POST['cmb_selling_unit']; 
    }
    else
    {
        $selling_unit = 0; 
    }

    //purchase price
    $prod_purchase_price = isset($_POST['prod_purchase_price']) ? $_POST['prod_purchase_price'] : 0;

    //selling price
    $prod_selling_price = isset($_POST['prod_selling_price']) ? $_POST['prod_selling_price'] : 0;

    //Item Discount    
    $prod_Discount = isset($_POST['prod_Item_Dis']) ? $_POST['prod_Item_Dis'] : 0;
    $prod_flat_Discount = isset($_POST['prod_Item_Dis_flat']) ? $_POST['prod_Item_Dis_flat'] : 0;

    if ($prod_Discount > 0) {        
        $prod_Discount = floatval($prod_Discount); 
    }
    
    if ($prod_flat_Discount > 0) {        
        $prod_flat_Discount = floatval($prod_flat_Discount); 
    }


    $use_service = "P";
    $shopObj = new Shop();
    if($shopObj->hasService($shop_id))
    {
        $use_service = isset($_POST['chk_service']) ? "S" : "P";
    }//has service
    else
    {
        $use_service = "P";
    }//no service
    
    //get current date
    date_default_timezone_set("Asia/Colombo");
    $this_date = date("Y-m-d");

    //user id
    $user_id = $_SESSION['user_id'];


    //file upload directry
    $target_dir = "../Assets/Images/prod_images/";
    if(!empty($_FILES['prod_image']['name']))
    {
        $target_file_path = $target_dir . $prod_image_name . basename($_FILES['prod_image']['name']);
        $file_type = pathinfo($target_file_path, PATHINFO_EXTENSION);
    }
    if(isset($_POST["chk_fp"]))
    {
        $chk_fp=1;
    }
    else
    {
        $chk_fp=0;
    }
    
    //check subcategory
    if($subcat_id > 0)
    {
        //check barcode and item name
        if(!empty($barcode))
        {
            //check prodname
            if(!empty($prod_name))
            {
                //check image
                $allow_types = array('jpg','JPG','png','PNG','jpeg','JPEG');
                if(isset($file_type))
                {
                    if(in_array($file_type, $allow_types))
                    {
                        $prod_image_name = $prod_image_name . "." . $file_type;
                        $target_file_path = $target_dir . $prod_image_name;

                        $size = floatval($_FILES['prod_image']['size']);
                        //check size 500KB
                        if($size < 500000)
                        {
                            if(move_uploaded_file($_FILES["prod_image"]["tmp_name"], $target_file_path))
                            {
                                $prodObj->setProduct(ProductNo: $product_no, ProdImage: $prod_image_name, Barcode: $barcode, ItemName: $prod_name, ProdDescription: $prod_description, SecondName: $second_name, ProdPurchasePrice: $prod_purchase_price, ProdSellPrice: $prod_selling_price, CartonQty: $prod_carton_qty, ProductStat: $prod_stat, AddedDate: $this_date, UpdatedDate: $this_date, ItemType: $use_service, user_USID: $user_id, UpdateUserID: $user_id, Subcategories_SCID: $subcat_id, shop_SHID: $shop_id, PurchaseUnit: $purchase_unit, UnitConversion: $conversion_rate, SellingUnit: $selling_unit, prod_Discount: $prod_Discount, Flat_discount: $prod_flat_Discount, chk_fp:$chk_fp);
                                $product_id=$prodObj->getLastInsertedProID();
                                $product_id=$product_id[0]["ProductCount"];
                                $count=$invObj->getInventorywithproductID($product_id);
                                $count=$count[0]["procount"];
                                $count=$count+1;
                                $batch_id=$invObj->getSequence($count);
                                $batch_id="B".$batch_id;
                                $CurrentQty=isset($_POST["openStock"]) ? $_POST["openStock"] : 0;
                                $BillQty=0;
                                $ReturnQty=0;
                                $TransfeInQty=0;
                                $TransferOutQty=0;
                                $RackID=1;
                                $default=1;
                                $invObj->setInventory2($CurrentQty, $BillQty, $ReturnQty, $TransfeInQty, $TransferOutQty, $product_id, $shop_id, $RackID, $batch_id,$default);
                                $sql = "SELECT max(INID) AS MAXSID FROM inventory WHERE shop_SHID='$shop_id';";
                                $dbMax = $dbObj->getData($sql);
                                $max_id = floatval($dbMax[0]['MAXSID']);
                                $new_inventory_id = $max_id ;
                                $grn_detail_id = 0;
                                $CurrentQty=isset($_POST["openStock"]) ? $_POST["openStock"] : 0;
                                $BillQty=0;
                                $ReturnQty=0;
                                $TransfeInQty=0;
                                $TransferOutQty=0;
                                $RackID=1;
                                $default=1;
                                $mnf_date=null;
                                $exp_date=null;
                                $effective_date = date("Y-m-d");
                                $priceObj->setPriceHistory($product_id, 0, $effective_date, $prod_purchase_price, $prod_selling_price, $prod_selling_price, $mnf_date, $exp_date, $batch_id, $new_inventory_id, $grn_detail_id);
                                $_SESSION['product_update'] = 3; //save successfully
                                ECHO $_SESSION['product_update'];
                                header("Location: ../Public/product.php");
                            }//move file
                            else
                            {
                                $_SESSION['product_update'] = 4; //unable to save
                                ECHO $_SESSION['product_update'];
                                header("Location: ../Public/product.php");
                                die("Error: barcode or username empty.");
                            }//cannot save image
                        }//less than 500kb
                        else
                        {
                            $_SESSION['product_update'] = 5;//wrong image size
                            ECHO $_SESSION['product_update'];
                            header("Location: ../Public/product.php");
                            die("Error: barcode or username empty.");
                        }//greater than 500kb
                    }
                    else
                    {
                        $prod_image_name = null;
                        $prodObj->setProduct(ProductNo: $product_no, ProdImage: $prod_image_name, Barcode: $barcode, ItemName: $prod_name, ProdDescription: $prod_description, SecondName: $second_name, ProdPurchasePrice: $prod_purchase_price, ProdSellPrice: $prod_selling_price, CartonQty: $prod_carton_qty, ProductStat: $prod_stat, AddedDate: $this_date, UpdatedDate: $this_date, ItemType: $use_service, user_USID: $user_id, UpdateUserID: $user_id, Subcategories_SCID: $subcat_id, shop_SHID: $shop_id, PurchaseUnit: $purchase_unit, UnitConversion: $conversion_rate, SellingUnit: $selling_unit,prod_Discount: $prod_Discount, Flat_discount: $prod_flat_Discount, chk_fp:$chk_fp);
                        $product_id=$prodObj->getLastInsertedProID();
                        $product_id=$product_id[0]["ProductCount"];
                        $count=$invObj->getInventorywithproductID($product_id);
                        $count=$count[0]["procount"];
                        $count=$count+1;
                        $batch_id=$invObj->getSequence($count);
                        $batch_id="B".$batch_id;
                        $CurrentQty=isset($_POST["openStock"]) ? $_POST["openStock"] : 0;
                        $BillQty=0;
                        $ReturnQty=0;
                        $TransfeInQty=0;
                        $TransferOutQty=0;
                        $RackID=1;
                        $default=1;
                        $mnf_date=null;
                        $exp_date=null;
                        $effective_date = date("Y-m-d");
                        $invObj->setInventory2($CurrentQty, $BillQty, $ReturnQty, $TransfeInQty, $TransferOutQty, $product_id, $shop_id, $RackID, $batch_id,$default);
                        $sql = "SELECT max(INID) AS MAXSID FROM inventory WHERE shop_SHID='$shop_id';";
                        $dbMax = $dbObj->getData($sql);
                        $max_id = floatval($dbMax[0]['MAXSID']);
                        $new_inventory_id = $max_id ;
                        $grn_detail_id = 0;
                        $priceObj->setPriceHistory($product_id, 0, $effective_date, $prod_purchase_price, $prod_selling_price, $prod_selling_price, $mnf_date, $exp_date, $batch_id, $new_inventory_id, $grn_detail_id);
                        $_SESSION['product_update'] = 3; //save successfully
                                    ECHO $_SESSION['product_update'];
                        header("Location: ../Public/product.php");
                    }
                    
                }//is array
                else
                {
                    //save product without image
                    $prod_image_name = null;
                    $prodObj->setProduct(ProductNo: $product_no, ProdImage: $prod_image_name, Barcode: $barcode, ItemName: $prod_name, ProdDescription: $prod_description, SecondName: $second_name, ProdPurchasePrice: $prod_purchase_price, ProdSellPrice: $prod_selling_price, CartonQty: $prod_carton_qty, ProductStat: $prod_stat, AddedDate: $this_date, UpdatedDate: $this_date, ItemType: $use_service, user_USID: $user_id, UpdateUserID: $user_id, Subcategories_SCID: $subcat_id, shop_SHID: $shop_id, PurchaseUnit: $purchase_unit, UnitConversion: $conversion_rate, SellingUnit: $selling_unit,prod_Discount: $prod_Discount, Flat_discount: $prod_flat_Discount, chk_fp:$chk_fp);
                    $product_id=$prodObj->getLastInsertedProID();
                    $product_id=$product_id[0]["ProductCount"];
                    $count=$invObj->getInventorywithproductID($product_id);
                    $count=$count[0]["procount"];
                    $count=$count+1;
                    $batch_id=$invObj->getSequence($count);
                    $batch_id="B".$batch_id;
                    $CurrentQty=isset($_POST["openStock"]) ? $_POST["openStock"] : 0;
                    $BillQty=0;
                    $ReturnQty=0;
                    $TransfeInQty=0;
                    $TransferOutQty=0;
                    $RackID=1;
                    $default=1;
                    $mnf_date="";
                    $exp_date="";
                    $effective_date = date("Y-m-d");
                    $invObj->setInventory2($CurrentQty, $BillQty, $ReturnQty, $TransfeInQty, $TransferOutQty, $product_id, $shop_id, $RackID, $batch_id,$default);
                    $sql = "SELECT max(INID) AS MAXSID FROM inventory WHERE shop_SHID='$shop_id';";
                    $dbMax = $dbObj->getData($sql);
                    $max_id = floatval($dbMax[0]['MAXSID']);
                    $new_inventory_id = $max_id ;
                    $grn_detail_id = 0;
                    $priceObj->setPriceHistory($product_id, 0, $effective_date, $prod_purchase_price, $prod_selling_price, $prod_selling_price, $mnf_date, $exp_date, $batch_id, $new_inventory_id, $grn_detail_id);
                    $_SESSION['product_update'] = 3; //save successfully
                                ECHO $_SESSION['product_update'];
                    header("Location: ../Public/product.php");
                }//not in array
            }//has name
            else
            {
                $_SESSION['product_update'] = 1;
                                ECHO $_SESSION['product_update'];
                header("Location: ../Public/product.php");
                die("Error: barcode or username empty.");
            }//no prod name
        }//has barcode and name
        else
        {
            $_SESSION['product_update'] = 1;
                                ECHO $_SESSION['product_update'];
            header("Location: ../Public/product.php");
            die("Error: barcode or username empty.");
        }//no barcode and name
    }//has category
    else
    {
        $_SESSION['product_update'] = 0;
                                ECHO $_SESSION['product_update'];
        header("Location: ../Public/product.php");
        die("Error: No category selected.");
    }//no category
}//save product



if (isset($_POST["bulk_upload"]))
{
    $erroProduct = [];
    $successProduct = [];

    if (!isset($_FILES["file"]) || $_FILES["file"]["error"] != 0)
    {
        $_SESSION["erroProduct"][] = [
            "product" => "CSV File",
            "message" => "Please select a valid CSV file."
        ];

        header("Location: ../Public/upload_product.php");
        exit;
    }

    $filename = $_FILES["file"]["tmp_name"];

    if ($_FILES["file"]["size"] <= 0)
    {
        $_SESSION["erroProduct"][] = [
            "product" => "CSV File",
            "message" => "Uploaded file is empty."
        ];

        header("Location: ../Public/upload_product.php");
        exit;
    }

    $target_dir = "../Assets/uploads/";

    if (!file_exists($target_dir))
    {
        mkdir($target_dir, 0777, true);
    }

    $date = date("Y-m-d h-i-s a");

    $target_file = $target_dir . basename($date . " - " . $_FILES["file"]["name"]);

    if (!move_uploaded_file($filename, $target_file))
    {
        $_SESSION["erroProduct"][] = [
            "product" => "CSV File",
            "message" => "Unable to save uploaded file."
        ];

        header("Location: ../Public/upload_product.php");
        exit;
    }

    $file = fopen($target_file, "r");

    if (!$file)
    {
        $_SESSION["erroProduct"][] = [
            "product" => "CSV File",
            "message" => "Unable to read uploaded file."
        ];

        header("Location: ../Public/upload_product.php");
        exit;
    }

    // Skip Header
    fgetcsv($file, 10000, ",");

    $rowNumber = 1;

    $csvBarcodes = [];

    while (($column = fgetcsv($file, 10000, ",")) !== FALSE)
    {
        $rowNumber++;

        if (count($column) < 8)
        {
            $erroProduct[] = [
                "product" => "Row ".$rowNumber,
                "message" => "Invalid CSV format."
            ];
            continue;
        }

        $productData = $prodObj->getProductCount();
        $prod_count = intval($productData[0]['ProductCount']) + 1;

        ;

        $ProductNo = $commObj->createCount("PD", $prod_count);

        $Barcode = trim($column[0]);
        $ItemName = trim($column[1]);
        $ProdDescription = trim($column[2]);
        $SecondName = trim($column[3]);

        $ProdPurchasePrice = floatval($column[4]);
        $ProdSellPrice = floatval($column[5]);

        $SubCategoryName = trim($column[6]);

        $OpeningStock = trim($column[7]);

        if ($OpeningStock == "")
        {
            $OpeningStock = 0;
        }

        if ($Barcode == "")
        {
            $Barcode = $ProductNo;
        }

        if ($ItemName == "")
        {
            $erroProduct[] = [
                "product" => "Row ".$rowNumber,
                "message" => "Product Name cannot be empty."
            ];
            continue;
        }


        $csvBarcodes[] = $Barcode;

        $subcat = $prodObj->getSubCat($SubCategoryName);

        $errorMessage = "";

        if (count($subcat) == 0)
        {
            $errorMessage .= "Sub Category Not Found, ";
        }

        if ($errorMessage != "")
        {
            $erroProduct[] = [
                "product" => $ItemName,
                "message" => rtrim($errorMessage, ", ")
            ];
            continue;
        }

        try
        {
            $ProdImage = null;

            $date_create = date("Y-m-d");

            $CartonQty = 1;
            $ProductStat = 1;

            $AddedDate = $date_create;
            $UpdatedDate = $date_create;

            $ItemType = "P";

            $user_USID = $_SESSION["user_id"];
            $UpdateUserID = $_SESSION["user_id"];

            $shop_SHID = $_SESSION["shop_id"];

            $PurchaseUnit = 0;
            $UnitConversion = 1;
            $SellingUnit = 0;

            $Subcategories_SCID = $subcat[0]["SCID"];

            $product_id = $prodObj->setProduct(
                ProductNo: $ProductNo,
                ProdImage: $ProdImage,
                Barcode: $Barcode,
                ItemName: $ItemName,
                ProdDescription: $ProdDescription,
                SecondName: $SecondName,
                ProdPurchasePrice: $ProdPurchasePrice,
                ProdSellPrice: $ProdSellPrice,
                CartonQty: $CartonQty,
                ProductStat: $ProductStat,
                AddedDate: $AddedDate,
                UpdatedDate: $UpdatedDate,
                ItemType: $ItemType,
                user_USID: $user_USID,
                UpdateUserID: $UpdateUserID,
                Subcategories_SCID: $Subcategories_SCID,
                shop_SHID: $shop_SHID,
                PurchaseUnit: $PurchaseUnit,
                UnitConversion: $UnitConversion,
                SellingUnit: $SellingUnit
            );

            // Inventory
            $count = $invObj->getInventorywithproductID($product_id);
            $count = $count[0]["procount"] + 1;

            $batch_id = "B".$invObj->getSequence($count);

            $BillQty = 0;
            $ReturnQty = 0;
            $TransfeInQty = 0;
            $TransferOutQty = 0;

            $RackID = 1;
            $default = 1;

            $invObj->setInventory2(
                $OpeningStock,
                $BillQty,
                $ReturnQty,
                $TransfeInQty,
                $TransferOutQty,
                $product_id,
                $shop_SHID,
                $RackID,
                $batch_id,
                $default
            );

            $sql = "SELECT MAX(INID) AS MAXSID
                    FROM inventory
                    WHERE shop_SHID='$shop_SHID'";

            $dbMax = $dbObj->getData($sql);

            $new_inventory_id = $dbMax[0]['MAXSID'];

            $effective_date = date("Y-m-d");

            $priceObj->setPriceHistory(
                $product_id,
                0,
                $effective_date,
                $ProdPurchasePrice,
                $ProdSellPrice,
                $ProdSellPrice,
                "",
                "",
                $batch_id,
                $new_inventory_id,
                0
            );

            $successProduct[] = [
                "product" => $ItemName,
                "message" => "Uploaded Successfully (Opening Stock : ".$OpeningStock.")"
            ];
        }
        catch (Exception $e)
        {
            $erroProduct[] = [
                "product" => $ItemName,
                "message" => $e->getMessage()
            ];
        }
    }

    fclose($file);

    $_SESSION["erroProduct"] = $erroProduct;
    $_SESSION["successProduct"] = $successProduct;

    $_SESSION["upload_summary"] = [
        "success" => count($successProduct),
        "failed" => count($erroProduct)
    ];

    header("Location: ../Public/upload_product.php");
    exit;
}




if(isset($_POST['btn_update_product']))
{
    $product_id = $_POST['hide_product_id'];
    //get count
    $productData = $prodObj->getLastInsertedProID();
    $prod_count = intval($productData[0]['ProductCount']);
    $prod_count += 1;

    //image count
    $productImage = $prodObj->getProductImageCount();
    $prod_image_count = intval($productImage[0]['ProducImagetCount']);
    $prod_image_count += 1;

    ;
    $product_no = $commObj->createCount("PD", $prod_count);
    $prod_image_name = $commObj->createCount("PI", $product_id);//get prduct id for image

    $subcat_id = $_POST['cmb_subcategory'];
    $barcode = $_POST['barcode'];
    $prod_name = $_POST['prod_name'];
    $prod_description = $_POST['prod_description'];

    //second name
    $second_name = isset($_POST['second_name']) ? $_POST['second_name'] : "NULL";

    //carton qty
    $prod_carton_qty = isset($_POST['prod_carton_qty']) ? $_POST['prod_carton_qty'] : 1;

    //units
    $purchase_unit = isset($_POST['cmb_purchase_unit']) ? $_POST['cmb_purchase_unit'] : 1;
    $conversion_rate = isset($_POST['conversion_rate']) ? $_POST['conversion_rate'] : 1;
    $selling_unit = isset($_POST['cmb_selling_unit']) ? $_POST['cmb_selling_unit'] : 1;

    //purchase price
    $prod_purchase_price = isset($_POST['prod_purchase_price']) ? $_POST['prod_purchase_price'] : 0;
    $prodStatus = isset($_POST['prodStatus']) ? $_POST['prodStatus'] : 0;

    //selling price
    $prod_selling_price = isset($_POST['prod_selling_price']) ? $_POST['prod_selling_price'] : 0;

     //Item Discount
     $prod_Discount = isset($_POST['prod_Item_Dis']) ? $_POST['prod_Item_Dis'] : 0;
     $prod_flat_Discount = isset($_POST['prod_Item_Dis_flat']) ? $_POST['prod_Item_Dis_flat'] : 0;

     if ($prod_Discount > 0) 
     {        
         $prod_Discount = floatval($prod_Discount); 
     }
    
    if ($prod_flat_Discount > 0) 
    {        
        $prod_flat_Discount = floatval($prod_flat_Discount); 
    }

    $use_service = "P";
    $shopObj = new Shop();
    if($shopObj->hasService($shop_id))
    {
        $use_service = isset($_POST['chk_service']) ? "S" : "P";
    }//has service
    else
    {
        $use_service = "P";
    }//no service

    //get current date
    date_default_timezone_set("Asia/Colombo");
    $this_date = date("Y-m-d");

    //user id
    $user_id = $_SESSION['user_id'];

    //file upload directry
    $target_dir = "../Assets/Images/prod_images/";

    $target_file_path = $target_dir . $prod_image_name . basename($_FILES['prod_image']['name']);
    $file_type = pathinfo($target_file_path, PATHINFO_EXTENSION);

    //validation
    /*
    * check subcat_id
    * check barcode
    * check itemname
    * check image
        if(new image to add)
        {
            * check system image
                if(has image) {unlink image}
                else {update image}
        }
        else
        {
            update record
        }
    */
    //check sub category
    
    if(isset($_POST["chk_fp"]))
    {
        $chk_fp=1;
    }
    else
    {
        $chk_fp=0;
    }
    echo "chk_fp ".$chk_fp;
    if($subcat_id > 0)
    {
        //check barcode and item name
        if(!empty($barcode))
        {
            //check prodname
            if(!empty($prod_name))
            {
                //check image
                $allow_types = array('jpg','JPG','png','PNG','jpeg','JPEG');
                if(in_array($file_type, $allow_types))
                {
                    //check system image
                    $prodOne = $prodObj->getOneProduct($product_id);
                    $sys_image_name = $prodOne[0]['ProdImage'];
                    if(!empty($sys_image_name))
                    {
                        //unlink system image
                        $delete_path = "../Assets/Images/prod_images/" . $prodOne[0]['ProdImage'];
                        if(unlink($delete_path))
                        {
                            //update record
                            $prod_image_name = $prod_image_name . "." . $file_type;
                            $target_file_path = $target_dir . $prod_image_name;

                            $size = floatval($_FILES['prod_image']['size']);
                            //check size 500kb
                            if($size < 500000)
                            {
                                if(move_uploaded_file($_FILES["prod_image"]["tmp_name"], $target_file_path))
                                {
                                    //update record
                                    $editP=$prodObj->editProduct($prod_image_name, $barcode, $prod_name, $prod_description, $second_name, $prod_purchase_price, $prod_selling_price, $prod_carton_qty, $this_date, $use_service, $user_id, $subcat_id, $shop_id, $purchase_unit, $conversion_rate, $selling_unit, $product_id, $prod_Discount,$prodStatus, $prod_flat_Discount, $chk_fp);
                                    echo "editP ".$editP;


                                    $_SESSION['product_update'] = 5; //update successfully
                                    header("Location: ../Public/product.php");
                                }//move file
                                else
                                {
                                    $_SESSION['product_update'] = 4; //unable to save
                                    header("Location: ../Public/product.php");
                                    die("Error: unable to save image");
                                }//cannot save image
                            }//less than 500kb
                            else
                            {
                                $_SESSION['product_update'] = 5;//wrong image size
                                header("Location: ../Public/product.php");
                                die("Error: barcode or username empty.");
                            }//greater than 500kb
                        }//unlink system image
                        else
                        {
                            $_SESSION['product_update'] = 4;
                            header("Location: ../Public/product.php");
                            die("Error: unable to remove previous image");
                        }//cannot delete image
                    }//has sys image
                    else
                    {
                        //check image
                        $allow_types = array('jpg','JPG','png','PNG','jpeg','JPEG');
                        if(in_array($file_type, $allow_types))
                        {
                            //update record
                            $prod_image_name = $prod_image_name . "." . $file_type;
                            $target_file_path = $target_dir . $prod_image_name;

                            $size = floatval($_FILES['prod_image']['size']);
                            //check size 500kb
                            if($size < 500000)
                            {
                                if(move_uploaded_file($_FILES["prod_image"]["tmp_name"], $target_file_path))
                                {
                                    //update record
                                    $editP=$prodObj->editProduct($prod_image_name, $barcode, $prod_name, $prod_description, $second_name, $prod_purchase_price, $prod_selling_price, $prod_carton_qty, $this_date, $use_service, $user_id, $subcat_id, $shop_id, $purchase_unit, $conversion_rate, $selling_unit, $product_id, $prod_Discount,$prodStatus,$prod_flat_Discount,$chk_fp);
                                    echo "editP ".$editP;
                                    editPrice($prod_purchase_price, $prod_selling_price, $product_id);

                                    $_SESSION['product_update'] = 5; //update successfully
                                    header("Location: ../Public/product.php");
                                }//move file
                                else
                                {
                                    $_SESSION['product_update'] = 4; //unable to save
                                    header("Location: ../Public/product.php");
                                    die("Error: unable to save image");
                                }//cannot save image
                            }//less than 500kb
                            else
                            {
                                $_SESSION['product_update'] = 5;//wrong image size
                                header("Location: ../Public/product.php");
                                die("Error: barcode or username empty.");
                            }//greater than 500kb
                        }//support file type
                        else
                        {
                            $_SESSION['product_update'] = 6;
                            header("Location: ../Public/product.php");
                            die("Error: not support image type.");
                        }//not support file
                    }//no sys image
                }//has image
                else
                {
                    /*
                    * should check if the $_POST['prod_image'] isset
                    * if(isset($_POST['prod_image']))
                        {
                            update
                        }
                        else
                        {
                            not support file type
                        }
                    */

                    //check system image
                    $prodOne = $prodObj->getOneProduct($product_id);
                    $sys_image_name = $prodOne[0]['ProdImage'];

                    $editP=$prodObj->editProduct($sys_image_name, $barcode, $prod_name, $prod_description, $second_name, $prod_purchase_price, $prod_selling_price, $prod_carton_qty, $this_date, $use_service, $user_id, $subcat_id, $shop_id, $purchase_unit, $conversion_rate, $selling_unit, $product_id,$prod_Discount,$prodStatus,$prod_flat_Discount,$chk_fp);
                    echo "editP ".$editP;

                    editPrice($prod_purchase_price, $prod_selling_price, $product_id);
                    
                    $_SESSION['product_update'] = 5; //update successfully
                    header("Location: ../Public/product.php");
                }//no image
            }//has product name
            else
            {
                $_SESSION['product_update'] = 1;
                header("Location: ../Public/product.php");
                die("Error: barcode or username empty.");
            }//no product name
        }//has barcode
        else
        {
            $_SESSION['product_update'] = 1;
            header("Location: ../Public/product.php");
            die("Error: barcode or username empty.");
        }//no barcdoe
    }//has sub category
    else
    {
        $_SESSION['product_update'] = 0; //no category
        header("Location: ../Public/product.php");
        die("Error: No category selected.");
    }//no sub category
}//update product

//============================= Functions ==============================//
function editPrice($purchase_price, $selling_price, $product_id)
{   
    $dbObj = new DBTransactions();
    $sql = "SELECT PHID FROM pricehistory WHERE ProductID = ".$product_id." AND BatchID='B000000001';";

    $priceData = $dbObj->getData($sql);

    $price_history_id = $priceData[0]['PHID'];

    $priceObj = new PriceHistory();

    $priceObj->editPriceHistoryPrice($purchase_price, $selling_price, $price_history_id);

}//edit price