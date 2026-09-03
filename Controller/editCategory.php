<?php 
include "../Includes/includes.php";
$shop_id = $_SESSION['shop_id'];

$dbObj = new DBTransactions();

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $response = [];
    //get company count
    $category_name = $_POST['edit_cat_name'];
    $hide_category_id = $_POST['hide_category_id'];
    if(!empty($category_name) && !empty($hide_category_id))
    {
        //check duplicates
        
        $sql = "SELECT COUNT(*) AS CategoryCount FROM `categories` WHERE CategoryName = '$category_name' AND CTID != $hide_category_id";

        $cat_count = 0;
        $result = $dbObj->getData($sql);
        if (!empty($result)) {
            $cat_count = $result[0]['CategoryCount'];
        }
        if($cat_count > 0)
        {
            $response = [
                "status" => "error",
                "message" => "Category name already exists."
            ];
            
        }   //has duplicates
        else
        {
            $catObj = new Category();
            $catObj->editCategory($category_name, $hide_category_id);

            $response = [
                "status" => "success",
                "message" => "Category updated successfully."
            ];
        } //unique name
    }     //has category name
    else
    {
        $response = [
            "status" => "error",
            "message" => "No category name or ID provided."
        ];
    } //no category Name
    echo json_encode($response);
}