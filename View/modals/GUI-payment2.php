<!-- Terms and condition Modal -->
<div class="modal fade" id="PaymentModal" tabindex="-1" aria-labelledby="PaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center w-100" id="PaymentModalLabel">Finalize Sale</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="row col-md-8">
                        <div class="col-md-12" id="paymentMethods">
                            <div class="w-100 mt-2 paying">
                                <div class="card p-3" data-payNo="1">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="Amount" class="form-label">Amount</label>
                                            <input type="number" name="Amount[]" id="Amount" class="Amount form-control" value="0.00" onkeyup="payment()" onkeydown="payment()" onkeypress="payment()" onchange="payment()">
                                        </div>
                                        <div class="col-md-5">
                                            <label for="PaymentType" class="form-label">Payment Type</label>
                                            <select name="paymentType[]" id="paymentType" class="paymentType form-select">
                                            <?php 
                                                $sql = "SELECT * FROM shoppaymethod sm
                                                INNER JOIN paymethod pm ON pm.PMID = sm.paymethod_PMID
                                                WHERE sm.shop_SHID = ".$shop_id."  AND (pm.PMID!=6 AND pm.PMID!=4 AND  pm.PMID!=5 AND pm.PMID!=13);";
                                                $dbObj = new DBTransactions();
                                                $dbPaymethods = $dbObj->getData($sql);
                                                $count = 0;
                                                $payCount=count($dbPaymethods);
                                                if($payCount > 0)
                                                {
                                                    foreach($dbPaymethods as $row)
                                                    {
                                                        $is_checked = $count==0 ? 'checked' : '';
                                                        ?>
                                                        <option value="<?php echo $row['paymethod_PMID'];?>"><?php echo $row['PaymethodName'];?></option>
                                                        <?php 
                                                        $count += 1;
                                                    }//foreach
                                                }
                                                else
                                                {
                                                    $sql="SELECT * FROM paymethod WHERE PMID=1 OR PMID=2 OR PMID=3";
                                                    $dbObj = new DBTransactions();
                                                    $dbPaymethods = $dbObj->getData($sql);
                                                    $count = 0;
                                                    $payCount=count($dbPaymethods);
                                                    if($payCount > 0)
                                                    {
                                                        foreach($dbPaymethods as $row)
                                                        {
                                                            $is_checked = $count==0 ? 'checked' : '';
                                                            ?>
                                                            <option value="<?php echo $row['PMID'];?>"><?php echo $row['PaymethodName'];?></option>
                                                            <?php 
                                                            $count += 1;
                                                        }//foreach
                                                    }
                                                }
                                                
                                            ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="Amount" class="form-label">Action</label><br>
                                            
                                        </div>
                                    </div>
                                </div>   
                            </div>                            
                        </div>
                        <div class="col-md-12 payBtn">
                            <button type="button" class="btn btn-success" id="add-payment" onclick="addPayment()">Add Payment</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card pays">
                            <table class="table">
                                <tr>
                                    <td class="text-end"><b>Total Items:</b> </td>
                                    <td><span class="custom-font-size totItemSpan"> 0.00 </span></td>
                                </tr>
                                <tr>
                                    <td class="text-end"><b>Total:</b> </td>
                                    <td><span class="custom-font-size totSpan"> 0.00 </span></td>
                                </tr>
                                <tr>
                                    <td class="text-end"><b>Total Discount:</b> </td>
                                    <td><span class="custom-font-size totDiscountSpan"> 0.00 </span></td>
                                </tr>
                                <tr>
                                    <td class="text-end"><b>Total Return:</b> </td>
                                    <td><span class="custom-font-size totReturnSpan"> 0.00 </span></td>
                                </tr>
                                <tr class="bg-danger" style="width: 101% !important;">
                                    <td class="text-end"><b>Net Total:</b> </td>
                                    <td><span class="custom-font-size netTotSpan"> 0.00 </span></td>
                                </tr>
                                <tr>
                                    <td class="text-end"><b>Total Payment:</b> </td>
                                    <td><span class="custom-font-size totPaymentSpan"> 0.00 </span></td>
                                </tr>
                                <tr>
                                    <td class="text-end"><b>Credit:</b> </td>
                                    <td><span class="custom-font-size creditSpan"> 0.00 </span></td>
                                </tr>
                                <tr class="bg-warning" style="width: 101% !important;">
                                    <td class="text-end"><b>Change Return:</b> </td>
                                    <td><span class="custom-font-size changeReturnSpan"> 0.00 </span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" name="btn_submit" class="btn btn-warning" id="btn_submit">Submit</button>

                <button type="button" name="btn_submit_invoice" class="btn btn-primary" id="btn_submit_invoice">Submit & Print Invoice</button>
            </div>
        </div>
    </div>
</div>
<script>
    function addPayment()
        {
            var payment =`<div class="w-100 mt-2 paying">`+
                                `<div class="card p-3" data-payNo="1">`+
                                    `<div class="row">`+
                                        `<div class="col-md-5">`+
                                            `<label for="Amount" class="form-label">Amount</label>`+
                                            `<input type="number" name="Amount[]" id="Amount" class="Amount form-control" onkeyup="payment()" onkeydown="payment()" onkeypress="payment()" onchange="payment()">`+
                                        `</div>`+
                                        `<div class="col-md-5">`+
                                            `<label for="PaymentType" class="form-label">Payment Type</label>`+
                                            `<select name="paymentType[]" id="paymentType" class="paymentType form-select">`+
                                            <?php 
                                                $sql = "SELECT * FROM shoppaymethod sm
                                                INNER JOIN paymethod pm ON pm.PMID = sm.paymethod_PMID
                                                WHERE sm.shop_SHID = ".$shop_id."  AND (pm.PMID!=6 AND pm.PMID!=4 AND pm.PMID!=5 AND pm.PMID!=13);";
                                                $dbObj = new DBTransactions();
                                                $dbPaymethods = $dbObj->getData($sql);
                                                $count = 0;
                                                $payCount=count($dbPaymethods);
                                                if($payCount > 0)
                                                {
                                                    foreach($dbPaymethods as $row)
                                                    {
                                                        $is_checked = $count==0 ? 'checked' : '';
                                                        ?>
                                                        `<option value="<?php echo $row['paymethod_PMID'];?>"><?php echo $row['PaymethodName'];?></option>`+
                                                        <?php 
                                                        $count += 1;
                                                    }//foreach
                                                }
                                                else
                                                {
                                                    $sql="SELECT * FROM paymethod WHERE PMID=1 OR PMID=2 OR PMID=3";
                                                    $dbObj = new DBTransactions();
                                                    $dbPaymethods = $dbObj->getData($sql);
                                                    $count = 0;
                                                    $payCount=count($dbPaymethods);
                                                    if($payCount > 0)
                                                    {
                                                        foreach($dbPaymethods as $row)
                                                        {
                                                            $is_checked = $count==0 ? 'checked' : '';
                                                            ?>
                                                            `<option value="<?php echo $row['PMID'];?>"><?php echo $row['PaymethodName'];?></option>`+
                                                            <?php 
                                                            $count += 1;
                                                        }//foreach
                                                    }
                                                }
                                                
                                            ?>
                                            `</select>`+
                                        `</div>`+
                                        `<div class="col-md-2">`+
                                            `<label for="Amount" class="form-label">Action</label><br>`+
                                            `<a href="javascript:void(0);" class='remove-payment btn btn-danger'><i class='ti ti-trash'></i></a>`+
                                        `</div>`+
                                    `</div>`+
                                `</div>`+  
                            `</div> `;
           
            $("#paymentMethods").append(payment);
        }
        
</script>