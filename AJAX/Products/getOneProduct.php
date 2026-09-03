<?php
session_start();

include "../../Includes/config.php";
include "../../Model/DB_Class.php";
include "../../Model/product_class.php";

$product_id = $_GET['product_id'];

$sql = "SELECT * FROM products
LEFT JOIN subcategories ON subcategories.SCID = products.Subcategories_SCID
LEFT JOIN categories ON categories.CTID = subcategories.categories_CTID
WHERE PDID = ".$product_id.";";

$dbObj = new DBTransactions();
$prodData = $dbObj->getData($sql);
$prodData = $prodData[0];
if(empty($prodData["ProdImage"]))
    {
        $prodData["ProdImage"] = "Assets/Images/icons/product.png";
    }
    else
    {
        $imgPath ="../../Assets/Images/prod_images/".$prodData["ProdImage"];
        if(file_exists($imgPath))
        {
            $prodData["ProdImage"]="Assets/Images/prod_images/".$prodData["ProdImage"];
        }
        else
        {
            $prodData["ProdImage"] = "Assets/Images/icons/product.png";
        }
    }
if(!empty($prodData))
{
    echo json_encode($prodData);
}//has data
