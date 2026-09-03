<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
$shop_id = $_SESSION["shop_id"] ?? 0;

$dbObj = new DBTransactions();
$searchQl=" ";

if(isset($_GET["PDID"]) && !empty($_GET["PDID"]))
{
    $PDID = $_GET["PDID"];
    $searchQl .=" AND p.PDID =$PDID";
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
    $response[]=$row;
}
echo json_encode($response);
?>