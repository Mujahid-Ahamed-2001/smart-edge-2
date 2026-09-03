$(document).ready(function(){
    $("#add").click(function () 
    {
        var tr ="<tr>"+
                    "<td>"+
                        "<select name='' id='' class='form-control'></select>"+
                        <?php 
                        $details=$credit->credit_customer_details($id);
                        foreach ($details as $row)
                        {
                            $pending=$row['total_credit']-$row['total_debit'];
                            if($pending>0)
                            {
                                ?>
                                <option value='<?=$row['invoice_header_id']?>'><?=$row['invoice']?></option>
                                <?php
                            }
                            else
                            {

                            }
                        }
                        ?>
                    "</td>"+
                    "<td>"+
                        "<input type='text' name='' id='' class='form-control' readonly>"+
                    "</td>"+
                    "<td>"+
                        "<input type='text' name='' id='' class='form-control'>"+
                    "</td>"+
                    "<td> <a href='javascript:void(0)' class='btn btn-danger' id='remove'><i class='ti ti-trash'></i></a></td>"+
                "</tr>";
        $("#tbody").append(tr);
    });
    $(document).on('click', '#remove', function(){
        $(this).closest('tr').remove();
    });
});