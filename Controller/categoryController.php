<?php 
include "../Includes/includes.php";
$shop_id = $_SESSION['shop_id'];

$dbObj = new DBTransactions();
if(isset($_POST['btn_update_category']))
{
    $shop_id = $_SESSION['shop_id'];
    //get company count
    $category_name = $_POST['cat_name'];
    $category_id = $_POST['hide_category_id'];
    if(!empty($category_name))
    {
        //check duplicates
        $catObj = new Category();
        $catDuplicate = $catObj->getCategoryByName($shop_id, $category_name);
        $catDuplicaeCount = intval($catDuplicate[0]['CategoryCount']);

        if($catDuplicaeCount > 0)
        {
            header("Location: ../Public/category.php");
            $_SESSION['category_update'] = 1; //duplicate entry
            die("Error: Category name already exists.");
        }//has duplicate
        else
        {
            $catObj->editCategory($category_name, $category_id);

            header("Location: ../Public/category.php");
            $_SESSION['category_update'] = 3; //uPDATE success
        }//no duplicates
    }//has category
    else
    {
        header("Location: ../Public/category.php");
        $_SESSION['category_update'] = 0; //no category name
        die("Error: No category name.");
    } //no category Name
} //update category

if(isset($_POST['btn_delete_category']))
{
    $category_id = $_POST['hide_category_id'];
    
    $sql = "SELECT * FROM subcategories WHERE categories_CTID = ".$category_id.";";
    //check constrains
    $catData = $dbObj->getData($sql);
    if(empty($catData))
    {
        $catObj = new Category();
        $catObj->deleteCategory($category_id);

        $_SESSION['category_update'] = 5;
        header("Location: ../Public/category.php");
        exit();
    } //no subcategory
    else
    {
        $_SESSION['category_update'] = 4;
        header("Location: ../Public/category.php");
        die("Error: cannot delete, Foreign key constrain.");
    } //has sub category
} //delete

//=============================== Sub category ===============================//
else if(isset($_POST['btn_save_subcat']))
{
    $category_id = $_POST['cmb_main_category'];
    $subcat_name = $_POST['subcat_name'];
    $response = [];
    if($category_id > 0 and !empty($subcat_name))
    {
        //check duplicates
        $subcatObj = new Category();
        $subcatData = $subcatObj->getSubcatDuplicate($category_id, $subcat_name, $shop_id);
        if(empty($subcatData))
        {
            //get count
            $subcatCount = $subcatObj->getSubcategoryCount($shop_id);
            $subcat_count = intval($subcatCount[0]['SubcatCount']);
            $subcat_count += 1;

            $commObj = new Common();
            $subcat_no = $commObj->createCount("SC", $subcat_count);

            $subcatObj->setSubCategory($subcat_no, $subcat_name, $category_id);

            $_SESSION['subcategory_update'] = 2; //save success
            $response = [
                "status" => "success",
                "message" => "Sub Category created successfully!"
            ];
        }//no duplicates
        else
        {
            $response = [
                "status" => "error",
                "message" => "Sub Category name already exists."
            ];
        }//has duplicates

    }//has category and name
    else
    {
        $response = [
            "status" => "error",
            "message" => "Missing category or sub category name."
        ];
    }//no category or name
    echo json_encode($response);
}//save category

else if(isset($_POST['btn_update_subcat']))
{
    $subcatObj = new Category();
    $subcat_id = $_POST['hide_subcat_id'];
    $category_id = $_POST['cmb_main_category'];
    $subcat_name = $_POST['subcat_name'];

    if(!empty($subcat_name)) 
    {
        //check duplicates
        $subcatData = $subcatObj->getSubcatDuplicate($category_id, $subcat_name, $shop_id);

        if(empty($subcatData))
        {
            $subcatObj->editSubcategory($subcat_name, $category_id, $subcat_id);

            $_SESSION['subcategory_update'] = 3; //update success
            header("Location: ../Public/subcategory.php");
        }//no duplicates
        else
        {
            header("Location: ../Public/subcategory.php");
            $_SESSION['subcategory_update'] = 1; //duplicate entry
            die("Error: Sub Category name already exists.");
        }//has duplicates

    }//has category and name
    else
    {
        header("Location: ../Public/subcategory.php");
        $_SESSION['subcategory_update'] = 0; //no category name
        die("Error: No category name.");
    }//no category or name
}    //update subcat

else if(isset($_POST['btn_delete_subcat']))
{
    $subcat_id = $_POST['hide_subcat_id'];
    $sql = "SELECT * FROM products WHERE Subcategories_SCID = ".$subcat_id.";";
    $subcatData = $dbObj->getData($sql);
    
    if(empty($subcatData))
    {
        $catObj = new Category();
        $catObj->deleteSubcategory($subcat_id);

        $_SESSION['subcategory_update'] = 5;
        header("Location: ../Public/subcategory.php");
        //delete sub category
    }   //no products
    else
    {
        $_SESSION['subcategory_update'] = 4;
        header("Location: ../Public/subcategory.php");
        die("Error: cannot delete, Foreign key constrain.");
    }//has related products
}//delete subcat
else
{
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request."
    ]);
    exit();
}
// else
// {
//     header("Location: ../Public/subcategory.php");
//     $_SESSION['subcategory_update'] = 6; //no category name
// }