<?php 
include "../Includes/includes.php";
$shop_id = $_SESSION['shop_id'];
$user_id = $_SESSION['user_id'];

require '../vendor/autoload.php';
require_once '../vendor/PhpXlsxGenerator.php'; 

use PhpOffice\PhpSpreadsheet\IOFactory;

if(isset($_POST['btn_upload_file']))
{
    //file upload directry
    $target_dir = "../Assets/uploads/";

    if(!empty($_FILES['product_file']['name']))
    {
        $product_file_name = basename($_FILES['product_file']['name']);

        $target_file_path = $target_dir . $product_file_name;
        $file_type = pathinfo($target_file_path, PATHINFO_EXTENSION);

        $target_file_path = $target_dir . "grn_upload_".$shop_id."." . $file_type;
        
        // Allow certain file formats 
        $allow_types = array('csv');
        if(in_array($file_type, $allow_types))
        {
            if(move_uploaded_file($_FILES["product_file"]["tmp_name"], $target_file_path))
            {
                $_SESSION['grn_upload'] = 1;
                header("Location: ../Public/grnBulkUpload.php");
            }//file moved
        }//in array
        else
        {
            $_SESSION['grn_upload'] = 0;
            header("Location: ../Public/grnBulkUpload.php");
            die("Error: Not Support format");
        }//not support type
    }//has file
}//upload files

if(isset($_POST['btn_delete_grn_file']))
{
    $delete_path = '../Assets/uploads/grn_upload_'.$shop_id.'.csv';

    unlink($delete_path);

    $_SESSION['file_upload'] = 2;
    header("Location: ../Public/grnBulkUpload.php");
}//delete

if(isset($_POST['btn_upload_grn']))
{
    //effective date
    date_default_timezone_set("Asia/Colombo");
    $effective_date = date("Y-m-d");

    $col_barcode = $_POST['cmb_barcode'];
    $col_itemname = $_POST['cmb_itemname'];
    $col_qty = $_POST['cmb_qty'];
    $col_purchase_price = $_POST['cmb_purchaseprice'];
    $col_label_price = isset($_POST['cmb_labelprice']) ? $_POST['cmb_labelprice'] : -1 ;
    $col_selling_price = $_POST['cmb_sellingprice'];
    $col_mnf_date = isset($_POST['cmb_mnfdate']) ? $_POST['cmb_mnfdate'] : 0;
    $col_exp_date = isset($_POST['cmb_expdate']) ? $_POST['cmb_expdate'] : 0;
    $col_section = isset($_POST['cmb_section']) ? $_POST['cmb_section'] : 0;
    $col_rack = isset($_POST['cmb_rack']) ? $_POST['cmb_rack'] : 0;

    $dbObj = new DBTransactions();

    //create table and insert data
    $query = "CREATE TABLE IF NOT EXISTS temp_grnupload (
        upload_id int AUTO_INCREMENT,
        barcode varchar(45),
        itemname varchar(45),
        qty decimal(12,3),
        purchaseprice decimal(12,2),
        labelprice decimal(12,2),
        sellingprice decimal(12,2),
        mnfdate date,
        expdate date,
        section_id int,
        rack_id int,
        product_id int,
        prod_stat int,
        shop_id int,
        PRIMARY KEY(upload_id)
    );";

    $dbObj->executeTransaction($query);

    // Path to the Excel file
    $filePath = '../Assets/uploads/grn_upload_'.$shop_id.'.csv';
    // Load the Excel file
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    for($i=1; $i<count($rows); $i++)
    {
        $barcode = $rows[$i][$col_barcode];
        $item_name = $rows[$i][$col_itemname];
        $qty = $rows[$i][$col_qty];
        $purchase_price = $col_purchase_price == -1 ? 0 : $rows[$i][$col_purchase_price];
        $label_price = $col_label_price == -1 ? 0 : $rows[$i][$col_label_price];
        $selling_price = $col_selling_price == -1 ? 0 : $rows[$i][$col_selling_price];

        $mnf_date = $col_mnf_date == 0 ? $effective_date : "0000-00-00";
        $exp_date = $col_exp_date == 0 ? $effective_date : "0000-00-00";
        $section = $col_section == 0 ? 1 : 0;
        $rack = $col_rack == 0 ? 1 : 0;

        $section_id = getSectionID($section, $shop_id);
        $rack_id = getRackID($section_id, $rack, $shop_id);

        $product_id = checkItem($barcode, $shop_id);
        $prod_stat = $product_id == 0 ? 0 : 1; 

        $query = "INSERT INTO temp_grnupload(barcode, itemname, qty, purchaseprice, labelprice, sellingprice, mnfdate, expdate, section_id, rack_id, product_id, prod_stat, shop_id)
        VALUES(
        '".$barcode."', 
        '".$item_name."', 
        ".$qty.", 
        ".$purchase_price.", 
        ".$label_price.", 
        ".$selling_price.", 
        '".$mnf_date."', 
        '".$exp_date."', 
        ".$section_id.", 
        ".$rack_id.", 
        ".$product_id.", 
        ".$prod_stat.", 
        ".$shop_id.");";

        $dbObj->executeTransaction($query);

        // echo "barcode - " . $barcode . "<br>";
        // echo "name - " . $item_name . "<br>";
        // echo "qrt - " . $qty . "<br>";
        // echo "pp - " . $purchase_price . "<br>";
        // echo "lp - " . $label_price . "<br>";
        // echo "sp - " . $selling_price . "<br>";
        // echo "mnf - " . $mnf_date . "<br>";
        // echo "exp - " . $exp_date . "<br>";
        // echo "sec - " . $section_id . "<br>";
        // echo "rac - " . $rack_id . "<br>";
        // echo "insert success... <br>";
    }//for 

    $delete_path = '../Assets/uploads/grn_upload_'.$shop_id.'.csv';

    unlink($delete_path);

    $_SESSION['file_upload'] = 3;
    header("Location: ../Public/grnBulkUpload.php");

}//upload grn

if(isset($_POST['btn_clear_table']))
{
    $dbObj = new DBTransactions();

    $query = "DELETE FROM `temp_grnupload` WHERE shop_id = ".$shop_id.";";

    $dbObj->executeTransaction($query);

    $_SESSION['file_upload'] = 4;
    header("Location: ../Public/grnBulkUpload.php");
}

if(isset($_POST['btn_add_grn']))
{
    $dbObj = new DBTransactions();
    $grnObj = new GRN();
    //get header details
    $grn_header_id = $_POST['cmb_grn_header'];
    if(!empty($grn_header_id))
    {
        //get grn header data
        $sql = "SELECT * FROM `grnheader` WHERE GHID = ".$grn_header_id.";";
        $headerData = $dbObj->getData($sql);

        $sql_1 = "SELECT * FROM `temp_grnupload` WHERE prod_stat = 1 AND shop_id = ".$shop_id.";";

        $uploadData = $dbObj->getData($sql_1);

        foreach($uploadData as $row)
        {
            $product_id = $row['product_id'];
            $init_qty = $row['qty'];
            $current_qty = floatval($row['qty']);
            $purchase_price = floatval($row['purchaseprice']);
            $label_price = floatval($row['labelprice']);
            $selling_price = floatval($row['sellingprice']);
            $total_purchase_price = $current_qty * $purchase_price;
            $total_selling_price = $current_qty * $selling_price;
            $mnf_date = $row['mnfdate'];
            $exp_date = $row['expdate'];
            $rack_id = $row['rack_id'];
            $grn_stat = 0;
            $variation_id = 1;

            $grnObj->setGRNDetails($init_qty, $current_qty, $purchase_price, $label_price, $selling_price, $total_purchase_price, $total_selling_price, $mnf_date, $exp_date, $grn_stat, $variation_id, $product_id, $grn_header_id, $rack_id);

            //clear table 
            $query = "DELETE FROM `temp_grnupload` WHERE shop_id = ".$shop_id.";";
            $dbObj->executeTransaction($query);


        }//foreach

        //head to grn header
        header("Location: ../Public/grn-header.php");
        echo "items added successfully...";
    }//has grn header
    else
    {
        $_SESSION['file_upload'] = 6;
        header("Location: ../Public/grnBulkUpload.php");
    }//no grn header
}//add to grn

//===================== Functions =======================//
function checkItem($barcode, $shop_id)
{
    $shopObj = new Shop();
    $dbObj = new DBTransactions();
    
    //check multi category
    //get company stat
    $sql = "SELECT * FROM shop
    INNER JOIN company ON company.CMID = shop.Company_CMID
    WHERE SHID = ".$shop_id.";";

    $shopData = $dbObj->getData($sql);
    $multi_category = floatval($shopData[0]['is_multicategory']);
    $company_id = floatval($shopData[0]['CMID']);

    if($multi_category == 1)
    {
        $sql_1 = "SELECT * FROM products 
        INNER JOIN shop ON shop.SHID = products.shop_SHID
        WHERE Barcode = '".$barcode."' AND shop.Company_CMID = ".$company_id.";";
    }//has multi category
    else
    {
        $sql_1 = "SELECT * FROM products WHERE Barcode = '".$barcode."' AND products.shop_SHID = ".$shop_id.";";
    }//only shop

    $prodData = $dbObj->getData($sql_1);

    $product_id = !empty($prodData) ? $prodData[0]['PDID'] : 0;

    return $product_id;
}//check barcode

function getSectionID($section, $shop_id)
{
    $dbObj = new DBTransactions();

    //get company stat
    $sql = "SELECT * FROM `sections` WHERE SectionName = '".$section."' AND shop_SHID = ".$shop_id.";";
    $secData = $dbObj->getData($sql);

    //get first section
    $section_id = empty($secData) ? 1 : $secData[0]['SEID'];

    return $section_id;
}//get section

function getRackID($section_id, $rack, $shop_id)
{
    $dbObj = new DBTransactions();

    //get company stat
    $sql = "SELECT * FROM `rack` 
    INNER JOIN sections ON sections.SEID = rack.Sections_SEID
    WHERE Sections_SEID = ".$section_id." AND RackName = '".$rack."' AND sections.shop_SHID = ".$shop_id.";";
    $rackData = $dbObj->getData($sql);

    //get first section
    $rack_id = empty($rackData) ? 1 : $rackData[0]['RKID'];

    return $rack_id;
}//get rack id