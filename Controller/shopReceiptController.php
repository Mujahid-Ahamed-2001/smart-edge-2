<?php 
include "../Includes/includes.php";

if(isset($_POST['btn_save_receipt']))
{
    $shop_id = $_POST['cmb_shop_receipts'];
    $receipt_name = $_POST['receipt_name'];
    $default_receipt = isset($_POST['default_receipt']) ? 1 : 0;
    $receipt_stat = isset($_POST['active_receipt']) ? 1 : 0;
    $wholesaleRecipt = isset($_POST['wholesaleRecipt']) ? 2 : 1;

    $size = floatval($_FILES['shop_receipt']['size']);

    //file upload directry
    $target_dir = "../Receipts/";
    if(!empty($_FILES['shop_receipt']['name']))
    {   
        if($size < 5000000)
        {
            //get receipt count
            $dbObj = new DBTransactions();

            $sql = "SELECT max(SRID) AS ReceiptCount FROM shopreceipts;";
            $receiptCount = $dbObj->getData($sql);

            $receipt_count = empty($receiptCount[0]['ReceiptCount']) ? 1 : (floatval($receiptCount[0]['ReceiptCount']) + 1);

            //naming
            $commonObj = new Common();
            $receipt_no = $commonObj->createCount("RCP", $receipt_count) ;

            $target_file_path = $target_dir . $receipt_no . basename($_FILES['shop_receipt']['name']);
            $file_type = pathinfo($target_file_path, PATHINFO_EXTENSION);

            $receipt_file_name = $receipt_no . "." . $file_type;
            $target_file_path = $target_dir . $receipt_file_name;

            // Allow certain file formats
            $allow_types = array('php','html', 'txt');
            if(in_array($file_type, $allow_types))
            {
                if(move_uploaded_file($_FILES["shop_receipt"]["tmp_name"], $target_file_path))
                {
                    $receiptObj = new ShopReceipt();

                    if($default_receipt == 1)
                    {
                        RemoveAllDefault($shop_id);
                    }//update default receipt
                    $RecieptType = $wholesaleRecipt;

                    $receiptObj->setShopReceipt($receipt_name, $shop_id, $default_receipt, $receipt_stat, $receipt_file_name, $RecieptType);

                    //receipt added successfully
                    $_SESSION['setting_update'] = 6;
                    header("Location: ../Public/SaleSettings.php");
                    exit();
                }//file moved
            }//support file type
            else
            {
                 //file size over 5mb
                $_SESSION['setting_update']=5;
                header("Location: ../Public/SaleSettings.php");
                die('Error: no receipt added.');
            }//not support file type

        }//file size ok
        else
        {
            //file size over 5mb
            $_SESSION['setting_update']=4;
            header("Location: ../Public/SaleSettings.php");
            die('Error: no receipt added.');
        }//receipt size high
    }//has a file
    else
    {
        //receipt not added
        $_SESSION['setting_update']=3;
        header("Location: ../Public/SaleSettings.php");
        die('Error: no receipt added.');
    }//no receit file
}//add shop receipt

if(isset($_POST['btn_update_receipt']))
{
    $receipt_id = $_POST['hide_receipt_id'];
    $shop_id = $_POST['cmb_shop_receipts'];
    $receipt_name = $_POST['receipt_name'];
    $default_receipt = isset($_POST['default_receipt']) ? 1 : 0;
    $receipt_stat = isset($_POST['active_receipt']) ? 1 : 0;

    $wholesaleRecipt = isset($_POST['wholesaleRecipt']) ? 2 : 1;
    $size = floatval($_FILES['shop_receipt']['size']);

    //file upload directry
    $target_dir = "../Receipts/";

    if(!empty($_FILES['shop_receipt']['name']))
    {   
        if($size < 5000000)
        {
            //get receipt count
            $dbObj = new DBTransactions();

            //get current file
            $sql = "SELECT * FROM shopreceipts
            INNER JOIN shop ON shop.SHID = shopreceipts.shop_id WHERE SRID = ".$receipt_id.";";

            $receiptData = $dbObj->getData($sql);
            $receipt_count = $receiptData[0]['SRID'];
            $current_receipt_file = $receiptData[0]['ReceiptPath'];

            //delete current file
            unlink("../Receipts/" . $current_receipt_file);



            // $sql = "SELECT COUNT(SRID) AS ReceiptCount FROM shopreceipts;";
            // $receiptCount = $dbObj->getData($sql);

            // $receipt_count = floatval($receiptCount[0]['ReceiptCount']) + 1;

            //naming
            $commonObj = new Common();
            $receipt_no = $commonObj->createCount("RCP", $receipt_count) ;

            $target_file_path = $target_dir . $receipt_no . basename($_FILES['shop_receipt']['name']);
            $file_type = pathinfo($target_file_path, PATHINFO_EXTENSION);

            $receipt_file_name = $receipt_no . "." . $file_type;
            $target_file_path = $target_dir . $receipt_file_name;

            // Allow certain file formats
            $allow_types = array('php','html', 'txt');
            if(in_array($file_type, $allow_types))
            {
                if(move_uploaded_file($_FILES["shop_receipt"]["tmp_name"], $target_file_path))
                {
                    if($default_receipt == 1)
                    {
                        RemoveAllDefault($shop_id);
                    }//update default receipt

                    $receiptObj = new ShopReceipt();
                    $receiptObj->editAllShopReceipt($receipt_name, $shop_id, $default_receipt, $receipt_stat, $receipt_file_name, $receipt_id,$wholesaleRecipt);

                    //receipt added successfully
                    $_SESSION['setting_update'] = 6;
                    header("Location: ../Public/SaleSettings.php");
                    exit();
                }//file moved
            }//support file type
            else
            {
                 //file size over 5mb
                $_SESSION['setting_update']=5;
                header("Location: ../Public/SaleSettings.php");
                die('Error: no receipt added.');
            }//not support file type

        }//file size ok
        else
        {
            //file size over 5mb
            $_SESSION['setting_update']=4;
            header("Location: ../Public/SaleSettings.php");
            die('Error: no receipt added.');
        }//receipt size high
    }//has a file
    else
    {
        //no new file just update data only
        $receiptObj = new ShopReceipt();

        if($default_receipt == 1)
        {
            RemoveAllDefault($shop_id);
        }//update default receipt

        $receiptObj->editShopReceipt($receipt_name, $shop_id, $default_receipt, $receipt_stat, $receipt_id,$wholesaleRecipt);
        
        //receipt not added
        $_SESSION['setting_update']=7;
        header("Location: ../Public/SaleSettings.php");
        die('Error: no receipt added.');
    }//no receit file
}//update receipt

if(isset($_POST['btn_delete_receipt']))
{
    $receipt_id = $_POST['hide_receipt_id'];

    //get receipt count
    $dbObj = new DBTransactions();

    //get current file
    $sql = "SELECT * FROM shopreceipts
    INNER JOIN shop ON shop.SHID = shopreceipts.shop_id WHERE SRID = ".$receipt_id.";";

    $receiptData = $dbObj->getData($sql);

    $receipt_file_name = $receiptData[0]['ReceiptPath'];

    unlink("../Receipts/" . $receipt_file_name);

    $receiptObj = new ShopReceipt();
    $receiptObj->deleteOneReceipt($receipt_id);

    //receipt not added
    $_SESSION['setting_update']=8;
    header("Location: ../Public/SaleSettings.php");
}//delete shop receipt

//============================ Functions ==========================//
function RemoveAllDefault($shop_id)
{
    $receiptObj = new ShopReceipt();
    $receiptObj->editAllDefaultReceipt($shop_id);
}//remove all defaults