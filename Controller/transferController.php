<?php 
include "../Includes/includes.php";
$shop_id = $_SESSION['shop_id'];
$user_id = $_SESSION['user_id'];

if(isset($_POST['btn_add_grn_transfer']))
{
    $transfer_from = $_POST['hide_transfer_from'];
    $transfer_to = $_POST['cmb_transfer_shop'];

    $transObj = new Transfer();
    $transData = $transObj->getTransferMax($shop_id);
    //$max_transfer = $transData[0]['maxTransfer'];

    $commObj = new Common();

    if(!empty($transfer_to))
    {
        $transfer_no = 0;
      
        if(!empty($transData))
        {
            $max_value = floatval($transData[0]['maxTransfer']);
            $max_value += 1;
            $transfer_no = $commObj->createCount("GT", $max_value);
        }//empty
        else
        {
            $transfer_no = $commObj->createCount("GT", 1);
        }//has max
    
        //effective date
        date_default_timezone_set("Asia/Colombo");
        $effective_date = date("Y-m-d");
    
        //count
        $transfer_count = 0;
        //start amount
        $transfer_amount = 0;
        //on hold
        $transfer_stat = 0;
    
        $transObj->setTransfer($transfer_no, $effective_date, $transfer_from, $transfer_to, $transfer_count, $transfer_amount, $transfer_stat, $shop_id, $user_id);
        
        header("Location: ../Public/transfer-header.php");
        $_SESSION['transfer_update'] = 1;

    }//has transfer shop
    else
    {
        $_SESSION['transfer_update'] = 4;
        header("Location: ../Public/transfer-header.php");
        die("error: no shop to transfer");
    }//no shop to transfer
}//create new transfer header

//================================= GRN Transfer =====================================//
if(isset($_POST['btn_pending_transfer']))
{
    $transfer_header_id = $_POST['hide_transferheader_id'];
    $tranObj = new Transfer();
    $transfer_stat = 1;

    //update header stat
    $tranObj->editTransferHeaderStat($transfer_stat, $transfer_header_id);

    //update transfer detail
    $tranObj->editTransferDetailStat($transfer_stat, $transfer_header_id);

    header("Location: ../Public/transfer-header.php");
}//pending transfer

if(isset($_POST['btn_verify_transfer']))
{
    $transfer_header_id = $_POST['hide_transferheader_id'];

    $tranObj = new Transfer();
    $headerData = $tranObj->getOneTransferHeader($transfer_header_id);

    $from_shop_id = $headerData[0]['TransferFrom'];
    $to_shop_id = $headerData[0]['TransferTo'];

    //check Qty in this shop
    if(checkQty($transfer_header_id, $from_shop_id))
    {
        //get transfer shop id
        $headerData = $tranObj->getOneTransferHeader($transfer_header_id);
        $transfer_shop_id = $headerData[0]['TransferTo'];

        $dbObj = new DBTransactions();

        $sql = "SELECT * FROM transferdetails WHERE TransferHeader_THID = ".$transfer_header_id.";";
        $dbData = $dbObj->getData($sql);

        if(!empty($dbData))
        {
            $row_count = 0;
            $transfer_amount = 0;
            foreach($dbData as $row)
            {
                $row_count += 1;
                $transfer_amount += floatval($row['TransferTotalAmount']);

                $product_id = $row['products_PDID'];
                $transfer_qty = floatval($row['TransferQty']);
                $receive_qty = floatval($row['ReceivedQty']);
                $purchase_price = $row['UnitPurchasePrice'];
                $selling_price = $row['UnitSellingPrice'];
                $inventory_id = $row['InventoryID'];
                $variation_id = $row['VariationID'];
                $mnf_date = $row['MnfDate'];
                $exp_date = $row['ExpDate'];
                $batch_id = $row['Batch_ID'];

                //deduct current qty from this shop 
                $deduct_qty = -1 * $receive_qty;
                updateInvCurrentQty($deduct_qty, $inventory_id);

                //add transfer out qty
                updateInvTransferOutQty($receive_qty, $inventory_id);

                //insert into transfer shop
                //insert into inventory
                $invObj = new Inventory();
                
                //get current date time
                date_default_timezone_set("Asia/Colombo");
                $effective_date = date("Y-m-d");

                $sql = "SELECT max(INID) AS MAXSID FROM inventory;";
                $dbMax = $dbObj->getData($sql);
                $max_id = floatval($dbMax[0]['MAXSID']);
                $new_inventory_id = $max_id + 1;

                //default values
                $bill_qty = 0;
                $return_qty = 0;
                $transfer_in_qty = $receive_qty;
                $transfer_out_qty = 0;
                $current_qty = $receive_qty;
                $rack_id = 1;
                $label_price = $selling_price;
                $grn_detail_id = 0;
                
                $invObj->setInventory($current_qty, $bill_qty, $return_qty, $transfer_in_qty, $transfer_out_qty, $product_id, $transfer_shop_id, $rack_id, $batch_id);

                //add to price history table
                $priceObj = new PriceHistory();
                $priceObj->setPriceHistory($product_id, $variation_id, $effective_date, $purchase_price, $selling_price, $label_price, $mnf_date, $exp_date, $batch_id, $new_inventory_id, $grn_detail_id);

            }//foreach

            //effective date
            date_default_timezone_set("Asia/Colombo");
            $effective_date = date("Y-m-d");
            //update totals
            $tranObj->editTransferTotals($effective_date, $row_count, $transfer_amount, $transfer_header_id);

            //update stat
            $transfer_stat = 2;

            //update header stat
            $tranObj->editTransferHeaderStat($transfer_stat, $transfer_header_id);

            //add transfer transaction
            $paymethod_id = $_POST['cmb_paymethod'];
            $tranObj->setTransferTransaction($transfer_amount, 1, $transfer_header_id, $paymethod_id);

            //update transfer detail
            $tranObj->editTransferDetailStat($transfer_stat, $transfer_header_id);

            header("Location: ../Public/transfer-header.php");
        }//has rows
        else
        {
            $_SESSION['transfer_detail_update'] = 1;
            header("Location: ../Public/transfer-details.php");
            die("Error: current qty < transfer qty");
        }//not transfer rows
    }//qty pass
    else
    {
        $_SESSION['transfer_detail_update'] = 0;
        header("Location: ../Public/transfer-details.php");
        die("Error: current qty < transfer qty");
    }//qty check failed
}//transfer

if(isset($_POST['btn_cancle_transfer']))
{
    $transfer_header_id = $_POST['hide_transferheader_id'];
    $tranObj = new Transfer();
    $transfer_stat = 3;

    //update header stat
    $tranObj->editTransferHeaderStat($transfer_stat, $transfer_header_id);

    //update transfer detail
    $tranObj->editTransferDetailStat($transfer_stat, $transfer_header_id);

    header("Location: ../Public/transfer-header.php");
}//close transfer

//============================ Functions ===============================//
function checkQty($transfer_header_id, $shop_id)
{   
    $dbObj = new DBTransactions();

    $sql = "SELECT * FROM transferdetails WHERE TransferHeader_THID = ".$transfer_header_id.";";
    $dbData = $dbObj->getData($sql);

    $qty_check = false;

    foreach ($dbData as $row)
    {
        $product_id = $row['products_PDID'];
        $transfer_qty = floatval($row['TransferQty']);
        $batch_id = $row['Batch_ID'];

        $sql_1 = "SELECT * FROM inventory 
        WHERE products_PDID = ".$product_id." AND inventory.shop_SHID= ".$shop_id." AND BatchID='".$batch_id."';";

        $invData = $dbObj->getData($sql_1);
        $current_qty = floatval($invData[0]['CurrentQty']);

        //check current qty
        if($current_qty >= $transfer_qty)
        {
            $qty_check = 1;
        }//check qty
        else
        {
            $qty_check = 0;
            break;
        }//qty check failed
    }//foreach

    return $qty_check;
}//check qty

function updateInvCurrentQty($update_qty, $inventory_id)
{
    $invObj = new Inventory();
    $invData = $invObj->getOneInventory($inventory_id);

    $current_qty = floatval($invData[0]['CurrentQty']);
    $new_current_qty = $current_qty + $update_qty;

    $invObj->editInvCurrentQty($new_current_qty, $inventory_id);
}//update inventory current qty

function updateInvTransferInQty($update_qty, $inventory_id)
{
    $invObj = new Inventory();
    $invData = $invObj->getOneInventory($inventory_id);

    $transfer_in_qty = floatval($invData[0]['TransferInQty']);
    $new_transfer_in_qty = $transfer_in_qty + $update_qty;

    $invObj->editInvTransferInQty($new_transfer_in_qty, $inventory_id);
}//update transfer in qty

function updateInvTransferOutQty($update_qty, $inventory_id)
{
    $invObj = new Inventory();
    $invData = $invObj->getOneInventory($inventory_id);

    $transfer_out_qty = floatval($invData[0]['TransferInQty']);
    $new_transfer_out_qty = $transfer_out_qty + $update_qty;

    $invObj->editInvTransferOurQty($new_transfer_out_qty, $inventory_id);
}//update transfer in qty

function addPriceHistory($product_id, $purchase_price, $selling_price, $shop_id)
{
    //PHID, ProductID, VariationID, EffectiveDate, PurchasePrice, SellingPrice, MnfDate, ExpDate, BatchID, Inventory_INID
    $dbObj = new DBTransactions();
    $sql = "SELECT * FROM inventory WHERE shop_SHID = ".$shop_id." AND products_PDID = ".$product_id." LIMIT 1;";

    $dbData = $dbObj->getData($sql);
    $inventory_id = $dbData[0]['INID'];

    $sql_1 = "SELECT * FROM pricehistory WHERE Inventory_INID = ".$inventory_id." AND ProductID = ".$product_id." ORDER BY PHID DESC;";
    $phData = $dbObj->getData($sql_1);

    $variation_id = $phData[0]['VariationID'];
    $mnf_date = $phData[0]['MnfDate'];
    $exp_date = $phData[0]['ExpDate'];
    $batch_id = 1;
    $label_price = $selling_price;

    //effective date
    date_default_timezone_set("Asia/Colombo");
    $effective_date = date("Y-m-d");
    $grn_detail_id = 0;

    $priceObj = new PriceHistory();
    $priceObj->setPriceHistory($product_id, $variation_id, $effective_date, $purchase_price, $selling_price, $label_price, $mnf_date, $exp_date, $batch_id, $inventory_id, $grn_detail_id);

}//add to pricelist