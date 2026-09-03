<?php 
include "../Includes/includes.php";
if($_GET["query"] =="save")
{
    $category_id = $_POST['cmb_main_category'];
    $subcat_name = $_POST['subcat_name'];
    $response = [];
    if($category_id > 0 && !empty($subcat_name) && !empty($category_id))
    {
        //check duplicates
        $subcatObj = new Category();
        $shop_id=0;
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
                "message" => "Subcategory created successfully!"
            ];
        }//no duplicates
        else
        {
            $response = [
                "status" => "error",
                "message" => "Subcategory name already exists."
            ];
        }//has duplicates

    }//has category and name
    else
    {
        $response = [
            "status" => "error",
            "message" => "Missing category or subcategory name."
        ];
    }//no category or name
    echo json_encode($response);
}
else if($_GET["query"] =="update")
{
    $SCID = $_POST["hide_subcat_id"];
    $CMID = $_POST["edit_cmb_main_category"];
    $SubCatName = $_POST["edit_subcat_name"];

    if(!empty($SCID) && !empty($CMID) && !empty($SubCatName))
    {        
        $subcatObj = new Category();
        $subcatData = $subcatObj->getSubcatDuplicateid($CMID, $SubCatName, $SCID);
        $subcatCount = $subcatData[0]["subcatCount"];

        if($subcatCount == 0)
        {
            $dbObj = new DBTransactions();
            $sql = "UPDATE `subcategories` 
                    SET `SubCatName`='$SubCatName' , `categories_CTID`='$CMID' 
                    WHERE `SCID`='$SCID' ";

            if($dbObj->executeTransaction($sql))
            {
                $response = [
                    "status" => "success",
                    "message" => "Subcategory updated successfully!"
                ];
            }
            else
            {
                $response = [
                    "status" => "error",
                    "message" => "Oops! something went wrong, Please Try Again."
                ];
            }
        }
        else
        {
            $response = [
                "status" => "error",
                "message" => "Subcategory name already exists."
            ];
        }
    }
    else
    {
        $response = [
            "status" => "error",
            "message" => "Missing category or subcategory name."
        ];
    }

    echo json_encode($response);
}
?>