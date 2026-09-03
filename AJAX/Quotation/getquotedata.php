<?php 
session_start();
include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();

$start = isset($_POST['start']) ? $_POST['start'] : '';
$end = isset($_POST['end']) ? $_POST['end'] : '';
$customerID = isset($_POST['customerID']) ? $_POST['customerID'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : '';
$sellingPrice = isset($_POST['sellingPrice']) ? $_POST['sellingPrice'] : '';
$selling_operator = isset($_POST['selling_operator']) ? $_POST['selling_operator'] : '';
$created_by = isset($_POST['created_by']) ? $_POST['created_by'] : '';
$completion = isset($_POST['completion']) ? $_POST['completion'] : '';

$sql = "SELECT 
            q.*, 
            c.CustName, c.CustContact, c.CustAddress, 
            qs.stat_name, 
            u.UserName,

            IFNULL(opt.option_count, 0) AS option_count,
            IFNULL(itm.item_count, 0) AS item_count,
            IFNULL(tot.total_amount, 0) AS total_amount

        FROM quotations q 

        LEFT JOIN customers c ON c.CTID = q.customer_id 
        LEFT JOIN quote_stat qs ON qs.QSID = q.status 
        LEFT JOIN user u ON u.USID = q.created_by

        /* OPTIONS COUNT */
        LEFT JOIN (
            SELECT quotation_id, COUNT(*) AS option_count
            FROM quotation_options
            GROUP BY quotation_id
        ) opt ON opt.quotation_id = q.id

        /* ITEMS COUNT */
        LEFT JOIN (
            SELECT quotation_id, COUNT(*) AS item_count
            FROM quotation_option_items
            GROUP BY quotation_id
        ) itm ON itm.quotation_id = q.id

        /* TOTAL AMOUNT */
        LEFT JOIN (
            SELECT quotation_id, SUM(total) AS total_amount
            FROM quotation_option_items
            GROUP BY quotation_id
        ) tot ON tot.quotation_id = q.id

        WHERE 1=1";

// Date filter
if(!empty($start) && !empty($end)){
    $sql .= " AND q.created_at BETWEEN '$start' AND '$end'";
}

// Customer filter
if(!empty($customerID)){
    $sql .= " AND q.customer_id = '$customerID'";
}

// Status filter
if(!empty($status)){
    $sql .= " AND q.status = '$status'";
}

// Created by filter
if(!empty($created_by)){
    $sql .= " AND q.created_by = '$created_by'";
}

// Completion filter (IMPORTANT PART)
if($completion !== ''){
    $sql .= " AND qs.completion = '$completion'";
}
if($sellingPrice !== '' && $selling_operator!== '')
{
    $sql .= " AND total_amount $selling_operator '$sellingPrice'";
}

// Optional ordering
$sql .= " ORDER BY q.created_at DESC";

// Execute
$result = $dbObj->getData($sql);
$tr="";
$i=1;
$total_amount = 0;
$total_pending = 0;
if(!empty($result)){
    
     foreach ($result as $row) {
        $dropdownItems = "";
        $quote_id = $row['id'];
        $quotation_no = $row['quotation_no'];
        $CustContact = $row['CustContact'];
        $status_id = $row['status'];
        $options_status = '<option value="">Select Status</option>';
        $status_sql = "SELECT * FROM quote_stat";
        $status_result = $dbObj->getData($status_sql);
        $selected_color = "#000";
        $selected_bg_color = "#fff";
        if(!empty($status_result)){
            foreach($status_result as $status_row){
                $color = $status_row["color"] != "" ? $status_row["color"] : "#000";
                $bg_color = $status_row["bg_color"] != "" ? $status_row["bg_color"] : "#fff";
                $selected = ($status_row['QSID'] == $status_id) ? 'selected' : '';
                if($status_row['QSID'] == $status_id)
                {
                    $selected_color = $color;
                    $selected_bg_color = $bg_color;
                }
                $options_status .= '<option data-color="'.$color.'" data-bg_color="'.$bg_color.'" value="'.$status_row['QSID'].'" '.$selected.' style="color: '.$color.' !important; background-color: '.$bg_color.' !important;">'.$status_row['stat_name'].'</option>';
            }
        }
        $status_select='<select name="stat" id="quote-select-stat-'.$quote_id.'" data-quote_id="'.$quote_id.'" class="form-select status_select" style="color: '.$selected_color.' !important; background-color: '.$selected_bg_color.' !important;">'.$options_status.'</select>';

        if(!empty($CustContact)){
            $dropdownItems .= '<li>
                                    <a class="dropdown-item" href="https://wa.me/'.$CustContact.'?text=Regarding%20Quotation%20No%20'.$quotation_no.'" target="_blank"><i class="ti ti-brand-whatsapp"></i> Whatsapp Quote</a>
                                </li>';
        }

        $dropdownItems .= '<li>
                                <a class="dropdown-item" href="../Print/print-quote-'.$quotation_no.'" target="_blank"><i class="ti ti-file-text"></i> Print Quote PDF</a>
                            </li>';

        $dropdownItems .= '<li>
                                <a class="dropdown-item" href="./edit-quote.php?grn_header='.$quote_id.'" target="_blank"><i class="ti ti-clipboard-list"></i> Add/Edit Items</a>
                            </li>';

        $dropdownItems .= '<li class="bg-danger">
                                <a class="dropdown-item text-white" href="./delete-quote.php?id='.$quote_id.'"><i class="ti ti-trash-x"></i> Delete Quote</a>
                            </li>';

        $action = '<div class="btn-group">
                        <button type="button" class="btn btn-primary">Action</button>
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown"></button>
                        <ul class="dropdown-menu">
                            '.$dropdownItems.'
                        </ul>
                    </div>';
        $total_amount += $row['total_amount'];
        if($row['status']==1){
            $total_pending ++;
        }
        $tr.="<tr>
                <td>".$i."</td>
                <td>".$row['quotation_no']."</td>
                <td>".$row['CustName']."</td>
                <td>".$row['CustContact']."</td>
                <td>".$row['CustAddress']."</td>
                <td>".$status_select."</td>
                <td>".$row['option_count']."</td><!--No of options-->
                <td>".$row['item_count']."</td><!--No of items-->
                <td>".$row['total_amount']."</td><!--No of total amount-->
                <td>".$row['UserName']."</td>
                <td>".$row['created_at']."</td>
                <td>$action</td>
            </tr>";
            $i++;
    }
    $i--;
    $tr .= "<script>
        $('#quoteTotalOrder').html(".$i.");
        $('#quoteTotalValue').html('Rs. ".number_format($total_amount,2)."');
        $('#quotePending').html(".$total_pending.");
    </script>";
}
else
{
    $tr="<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td class='text-center'>No data available</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
</tr>";
$i--;
$tr .= "<script>
        $('#quoteTotalOrder').html(".$i.");
        $('#quoteTotalValue').html('Rs. ".number_format($total_amount,2)."');
        $('#quotePending').html(".$total_pending.");
    </script>";
}

echo $tr;
// echo json_encode($result);
?>