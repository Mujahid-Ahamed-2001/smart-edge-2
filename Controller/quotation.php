<?php

include '../Includes/includes.php';
include '../Includes/authcheck.php';

header('Content-Type: application/json');
if(isset($_GET['action']) && $_GET['action'] == 'create')
{
    if (!isset($_POST['options'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No quotation data received'
        ]);
        exit;
    }

    $shop_id = $_SESSION['shop_id'];
    $user_id = $_SESSION['user_id'];

    // ✅ Customer
    $customer_id = isset($_POST['cmb_customer']) ? $_POST['cmb_customer'] : 1;
    function formatNumber($number, $length = 3) {
        return str_pad($number, $length, '0', STR_PAD_LEFT);
    }
    function getTodayDateFormatted() {
        return date('dmY');
    }
    // Generate new quotation number
    $dbObj = new DBTransactions(); 
    $sql = "SELECT q_no FROM docno WHERE shop_id = '$shop_id' ORDER BY DNID DESC LIMIT 1";
    $result = $dbObj->getData($sql);
    if(!empty($result))
    {
        $last_quote_no = $result[0]['q_no']+1;
        $q_no = formatNumber($last_quote_no);
        $today = getTodayDateFormatted();
        $new_quote_no = 'Q' . $today . '-' . $q_no;
    }
    else
    {
        // If no previous quote, start with QT-000001
        $sql="INSERT INTO docno (shop_id, q_no) VALUES ('$shop_id', 1)";
        $dbObj->executeTransaction($sql);
        $last_quote_no = 1;
        $q_no = formatNumber($last_quote_no);
        $today = getTodayDateFormatted();
        $new_quote_no = 'Q' . $today . '-' . $q_no;
    }

    // ✅ Generate quotation number (simple version)
    $quotation_no = $new_quote_no;
    $insert_quotation_sql ="INSERT INTO `quotations`(`quotation_no`, `customer_id`, `status`, `created_by`, `created_at`, `shop_SHID`) VALUES ('$quotation_no','$customer_id','1','$user_id',NOW(),'$shop_id')";
    $create_quote = $dbObj->executeTransaction($insert_quotation_sql);
    
    if($create_quote)
    {
        $sql_get_quotation_id = "SELECT id FROM quotations WHERE quotation_no = '$quotation_no' AND shop_SHID = '$shop_id' ORDER BY id DESC LIMIT 1";
        $quotationData = $dbObj->getData($sql_get_quotation_id);
        $quotation_id = $quotationData[0]['id'];
        $update_docno_sql = "UPDATE docno SET q_no = q_no + 1 WHERE shop_id = '$shop_id'";
        $dbObj->executeTransaction($update_docno_sql);
    }
    else
    {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create quotation'
        ]);
        exit;
    }
    if (!isset($quotation_id) && empty($quotation_id)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to retrieve quotation ID'
        ]);
        exit;
    }


    // ✅ OPTIONS
    $options = $_POST['options'];
    $i=0;
    foreach ($options as $option_no => $option) {

        $subtotal = isset($option['subtotal']) ? $option['subtotal'] : 0;
        $options_name = isset($_POST['options_name'][$i]) ? $_POST['options_name'][$i] : "Option ".$option_no;
        $sale_discount_type = isset($option['sale_discount_type']) ? $option['sale_discount_type'] : 1;
        $sale_discount_value = isset($option['sale_discount_amount']) ? $option['sale_discount_amount'] : 0;
        $total_discount = isset($option['total_discount']) ? $option['total_discount'] : 0;
        $other_charges = isset($option['other_charges']) ? $option['other_charges'] : 0;
        $grand_total = isset($option['grand_total']) ? $option['grand_total'] : 0;



        // 🔥 INSERT OPTION 
        $insert_option = $dbObj->executeTransaction("INSERT INTO quotation_options (
                quotation_id,
                option_name,
                subtotal,
                other_charges,
                discount_type,
                discount_value,
                discount_amount,
                total,
                created_at
            ) VALUES (
                '$quotation_id',
                '$options_name',
                '$subtotal',
                '$other_charges',
                '$sale_discount_type',
                '$sale_discount_value',
                '$total_discount',
                '$grand_total',
                NOW()
            )");

        if (!$insert_option) continue;
        $sql_get_option_id = "SELECT id FROM quotation_options WHERE quotation_id = '$quotation_id' AND option_name = '$options_name' LIMIT 1";
        $optionData = $dbObj->getData($sql_get_option_id);
        $option_id = $optionData[0]['id'];

        // ✅ ITEMS
        if (!isset($option['items'])) continue;

        foreach ($option['items'] as $row_index => $item) {

            $item_id = isset($item['item_id']) ? $item['item_id'] : 0;
            $description = isset($item['description']) ? $item['description'] : '';
            $qty = isset($item['quantity']) && !empty($item['quantity']) ? $item['quantity'] : 0;
            $rate = isset($item['rate']) && !empty($item['rate']) ? $item['rate'] : 0;
            $cost = isset($item['cost']) && !empty($item['cost']) ? $item['cost'] : 0;
            $disc_type = isset($item['disc_type']) ? $item['disc_type'] : 1;
            $disc_value = isset($item['disc_amount']) && !empty($item['disc_amount']) ? $item['disc_amount'] : 0;
            $total = isset($item['total']) && !empty($item['total']) ? $item['total'] : 0;

            // 🔥 Calculate discount amount per unit (server safety)
            if ($disc_type == 1) {
                $disc_amount = ($rate * $disc_value) / 100;
            } else {
                $disc_amount = $disc_value;
            }

            $selling_price = $rate - $disc_amount;

            $insert_quotation_option_items = $dbObj->executeTransaction("INSERT INTO quotation_option_items (
                    quotation_id,
                    option_id,
                    item_id,
                    item_name,
                    cost_price,
                    original_price,
                    discount_type,
                    discount_value,
                    discount_amount,
                    selling_price,
                    quantity,
                    total,
                    created_at
                ) VALUES (
                    '$quotation_id',
                    '$option_id',
                    '$item_id',
                    '$description',
                    '$cost',
                    '$rate',
                    '$disc_type',
                    '$disc_value',
                    '$disc_amount',
                    '$selling_price',
                    '$qty',
                    '$total',
                    NOW()
                )
            ");
        }
        $i++;
    }

    // ✅ SUCCESS RESPONSE
    echo json_encode([
        'status' => 'success',
        'quotation_id' => $quotation_id,
        'quotation_no' => $quotation_no
    ]);
}