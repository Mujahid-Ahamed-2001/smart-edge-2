<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
$shop_id = $_SESSION["shop_id"] ?? 0;

$dbObj = new DBTransactions();
$searchQl=" ";

if(isset($_POST["subcategory"]) && !empty($_POST["subcategory"]))
{
    $subcategory = $_POST["subcategory"];
    $searchQl .=" AND sc.SCID =$subcategory";
}
if(isset($_POST["PDID"]) && !empty($_POST["PDID"]))
{
    $PDID = $_POST["PDID"];
    $searchQl .=" AND p.PDID =$PDID";
}

if(isset($_POST["category"]) && !empty($_POST["category"]))
{
    $category = $_POST["category"];
    $searchQl .=" AND c.CTID =$category";
}

if(isset($_POST["search_item"]) && !empty($_POST["search_item"]))
{
    $search_item = $_POST["search_item"];
    $searchQl .=" AND (p.ProductNo LIKE '%$search_item%' OR p.ItemName LIKE '%$search_item%' OR p.Barcode LIKE '%$search_item%' ) ";
}

if(isset($_POST["product_type"]) && !empty($_POST["product_type"]))
{
    $product_type = $_POST["product_type"];
    $searchQl .=" AND p.ItemType = '$product_type' ";
}

if (isset($_POST["product_status"]) && $_POST["product_status"] !== "") {
    $product_status = (int)$_POST["product_status"];
    $searchQl .= " AND p.ProductStat = $product_status ";
}

if(isset($_POST["fixed_price"]) && !empty($_POST["fixed_price"]))
{
    $fixed_price = $_POST["fixed_price"];
    $searchQl .=" AND p.is_fixedPrice = $fixed_price ";
}

if((isset($_POST["selling_price1"]) && !empty($_POST["selling_price1"])) &&  (isset($_POST["selling_operator"]) && !empty($_POST["selling_operator"])))
{
    $selling_price1 = $_POST["selling_price1"];
    $selling_operator = $_POST["selling_operator"];
    $searchQl .=" AND p.ProdSellPrice $selling_operator $selling_price1 ";
}

if((isset($_POST["purchase_price1"]) && !empty($_POST["purchase_price1"])) &&  (isset($_POST["purchasing_operator"]) && !empty($_POST["purchasing_operator"])))
{
    $purchase_price1 = $_POST["purchase_price1"];
    $purchasing_operator = $_POST["purchasing_operator"];
    $searchQl .=" AND p.ProdPurchasePrice $purchasing_operator $purchase_price1 ";
}

$sql = "SELECT p.*, c.CTID AS cat_ID, c.CategoryName AS CatName, sc.SubCatName AS SubCatName, SUM(i.CurrentQty) AS CurrentQty FROM products p
    LEFT JOIN subcategories sc ON sc.SCID = p.Subcategories_SCID
    LEFT JOIN categories c ON c.CTID = sc.categories_CTID
    LEFT JOIN inventory i ON i.products_PDID = p.PDID AND i.shop_SHID=$shop_id AND i.is_default!=1
    WHERE 1=1 $searchQl
    GROUP BY p.PDID  
    ORDER BY p.PDID";
$response = [];
// echo $searchQl."<br>";
$productData = $dbObj->getData($sql);
foreach($productData as $row)
{
    if(empty($row["ProdImage"]))
    {
        $row["ProdImage"] = "Assets/Images/icons/product.png";
    }
    else
    {
        $imgPath ="../../Assets/Images/prod_images/".$row["ProdImage"];
        if(file_exists($imgPath))
        {
            $row["ProdImage"]="Assets/Images/prod_images/".$row["ProdImage"];
        }
        else
        {
            $row["ProdImage"] = "Assets/Images/icons/product.png";
        }
    }
    if(empty($row["CurrentQty"]))
    {
        $row["CurrentQty"]=0.00;
    }
    $response[]=$row;
}
echo json_encode($response);
?>