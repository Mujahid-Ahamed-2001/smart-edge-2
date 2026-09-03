<?php 
include "../Includes/includes.php";
$shop_id = $_SESSION['shop_id'];

$dbObj = new DBTransactions();

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $response = [];
    //get company count
    $category_name = $_POST['cat_name'];
    if(!empty($category_name))
    {
        //check duplicates
        $catObj = new Category();
        $catDuplicate = $catObj->getCategoryByName($shop_id, $category_name);
        $catDuplicaeCount = intval($catDuplicate[0]['CategoryCount']);
        if($catDuplicaeCount > 0)
        {
            $response = [
                "status" => "error",
                "message" => "Category name already exists."
            ];
            
        }   //has duplicates
        else
        {
            $catData = $catObj->getCategoryCount($shop_id);
            $category_count = intval($catData[0]['CategoryCount']);
            $category_count += 1;
        
            $commObj = new Common();
            $category_no = $commObj->createCount("MC", $category_count);

            $catObj->setCategory($category_no, $category_name, $shop_id);

            $response = [
                "status" => "success",
                "message" => "Category added successfully."
            ];
        } //unique name
    }     //has category name
    else
    {
        $response = [
            "status" => "error",
            "message" => "No category name."
        ];
    } //no category Name
    echo json_encode($response);
}  //save category
else
{
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request."
    ]);
    exit();
}
