<?php 
include "../Includes/includes.php";

if(isset($_POST['btn_save_label']))
{
    $dbOBj = new DBTransactions();

    $lbl_name = $_POST['lbl_name'];

    $dpi = $_POST['dpi'];
    $num_row = $_POST['num_row'];
    $num_col = $_POST['num_col'];
    $lbl_width = $_POST['lbl_width'];
    $lbl_height = $_POST['lbl_height'];

    $stk_width = $_POST['stk_width'];
    $stk_height = $_POST['stk_height'];
    $stk_margin_left = $_POST['stk_margin_left'];
    $stk_margin_right = $_POST['stk_margin_right'];
    $stk_margin_top = $_POST['stk_margin_top'];
    $stk_margin_bottom = $_POST['stk_margin_bottom'];

    $lbl_stat = isset($_POST['chk_lbl_stat']) ? 1 : 0;

    $shop_id = $_POST['cmb_shop'];

    $size = floatval($_FILES['barcode_file']['size']);
    //file upload directry
    $target_dir = "../Barcodes/";
    if(!empty($_FILES['barcode_file']))
    {
        $sql = "SELECT MAX(LBID) AS maxLabelID FROM label;";
        $labelData = $dbOBj->getData($sql);
        $next_label_id = floatval($labelData[0]['maxLabelID']) + 1;

        $commonObj = new Common();
        $label_file_name = $commonObj->createCount("BC", $next_label_id);

        $target_file_path = $target_dir . $next_label_id . basename($_FILES['barcode_file']['name']);
        $file_type = pathinfo($target_file_path, PATHINFO_EXTENSION);

        $barcode_file_name = $label_file_name . "." . $file_type;
        $target_file_path = $target_dir . $barcode_file_name;

        //echo "data - " . $target_file_path . "<br>";

        // Allow certain file formats
        $allow_types = array('php');
        if(in_array($file_type, $allow_types))
        {
            if(move_uploaded_file($_FILES["barcode_file"]["tmp_name"], $target_file_path))
            {
                $lblObj = new Label();
                $lblObj->setLabel($lbl_name, $barcode_file_name, $dpi, $num_row, $num_col, $lbl_width, $lbl_height, $stk_width, $stk_height, $stk_margin_left, $stk_margin_right, $stk_margin_top, $stk_margin_bottom, $lbl_stat, $shop_id);

                $_SESSION['label_update'] = 2;
                header("Location: ../Public/label.php");
            }//file move
        }//valid file format
        else
        {
            $_SESSION['label_update'] = 1;
            header("Location: ../Public/label.php");
        }//not valid file type
    }//has file
    else
    {
        $_SESSION['label_update'] = 0;
        header("Location: ../Public/label.php");
    }//no file
}//save label

if(isset($_POST['btn_update_label']))
{
    $dbObj = new DBTransactions();

    $label_id = $_POST['hide_label_id'];
    $lbl_name = $_POST['lbl_name'];

    $dpi = $_POST['dpi'];
    $num_row = $_POST['num_row'];
    $num_col = $_POST['num_col'];
    $lbl_width = $_POST['lbl_width'];
    $lbl_height = $_POST['lbl_height'];

    $stk_width = $_POST['stk_width'];
    $stk_height = $_POST['stk_height'];
    $stk_margin_left = $_POST['stk_margin_left'];
    $stk_margin_right = $_POST['stk_margin_right'];
    $stk_margin_top = $_POST['stk_margin_top'];
    $stk_margin_bottom = $_POST['stk_margin_bottom'];

    $lbl_stat = isset($_POST['chk_lbl_stat']) ? 1 : 0;

    $shop_id = $_POST['cmb_shop'];

    $size = floatval($_FILES['barcode_file']['size']);
    //file upload directry
    $target_dir = "../Barcodes/";

    if($size > 0)
    {
        $sql = "SELECT * FROM label
        INNER JOIN shop ON shop.SHID = label.shop_id 
        WHERE LBID = ".$label_id.";";
        $labelData = $dbObj->getData($sql);
        $previous_file = $labelData[0]['LabelPath'];

        //delete previous path
        $delete_path = "../Barcodes/" . $previous_file;
        if(file_exists($delete_path))
        {
            unlink($delete_path);
        }//has file

        $next_label_id = floatval($labelData[0]['LBID']) + 1;

        $commonObj = new Common();
        $label_file_name = $commonObj->createCount("BC", $label_id);

        $target_file_path = $target_dir . $next_label_id . basename($_FILES['barcode_file']['name']);
        $file_type = pathinfo($target_file_path, PATHINFO_EXTENSION);

        $barcode_file_name = $label_file_name . "." . $file_type;
        $target_file_path = $target_dir . $barcode_file_name;

        if(move_uploaded_file($_FILES["barcode_file"]["tmp_name"], $target_file_path))
        {
            $lblObj = new Label();
            $lblObj->editLabel($lbl_name, $barcode_file_name, $dpi, $num_row, $num_col, $lbl_width, $lbl_height, $stk_width, $stk_height, $stk_margin_left, $stk_margin_right, $stk_margin_top, $stk_margin_bottom, $lbl_stat, $shop_id, $label_id);

            $_SESSION['label_update'] = 3;
            header("Location: ../Public/label.php");
        }//file move
    }//has file
    else
    {
        $sql = "SELECT * FROM label
        INNER JOIN shop ON shop.SHID = label.shop_id 
        WHERE LBID = ".$label_id.";";

        $labelData = $dbObj->getData($sql);
        print_r($labelData);
        $previous_file = $labelData[0]['LabelPath'];

        echo "file - " . $previous_file . "<br>";

        $lblObj = new Label();
        $lblObj->editLabel($lbl_name, $previous_file, $dpi, $num_row, $num_col, $lbl_width, $lbl_height, $stk_width, $stk_height, $stk_margin_left, $stk_margin_right, $stk_margin_top, $stk_margin_bottom, $lbl_stat, $shop_id, $label_id);

        $_SESSION['label_update'] = 3;
        header("Location: ../Public/label.php");
    }//don't have file
}//update

if(isset($_POST['btn_print_barcode']))
{
    $dbObj = new DBTransactions();
    $shop_id = $_SESSION['shop_id'];

    $sql = "SELECT * FROM `label` WHERE shop_id =".$shop_id." and lblStat = 1;";
    $lblData = $dbObj->getData($sql);

    $barcode_file_name = "";
    if(!empty($lblData))
    {
        $barcode_file_name = $lblData[0]['LabelPath'];
    }
    else
    {
        $barcode_file_name = "barcode_one.php";
    }

    $product_id = $_POST['hide_label_product_id'];
    $label_price = $_POST['label_price'];

    // echo "prod - " . $product_id . "<br>";
    // echo "shop - " . $shop_id . "<br>";

    header("Location: ../Barcodes/" . $barcode_file_name . "?label=" . $product_id . "_" . $label_price);
}

//=================================== Function ================================//
function activeLabel($label_id, $shop_id)
{
    $dbObj = new DBTransactions();
    $lblObj = new Label();

    $sql = "SELECT * FROM `label` WHERE shop_id = ".$shop_id.";";
    $lblData = $dbObj->getData($sql);

    if(!empty($lblData))
    {
        foreach($lblData as $row)
        {
            $all_label_id = $row['LBID'];
            $lbl_stat = 0;
            
            $lblObj->editLabelStatus($lbl_stat, $all_label_id);
        }//foreach

        $lbl_stat = 1;
        //update 
        $lblObj->editLabelStatus($lbl_stat, $label_id);
    }//has data
    else
    {
        $lbl_stat = 1;
        //update 
        $lblObj->editLabelStatus($lbl_stat, $label_id);
    }
    
}//active label