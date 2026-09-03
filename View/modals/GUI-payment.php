<!-- Terms and condition Modal -->
<div class="modal fade" id="PaymentModal" tabindex="-1" aria-labelledby="PaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center w-100" id="PaymentModalLabel">Finalize Sale</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body payment-body">

                <div class="payment-left">

                    <div id="paymentMethods">
                        <div class="payment-card paying">

                            <div class="payment-box card p-4" data-payNo="1">

                                <div class="row align-items-end">

                                    <!-- Amount -->

                                    <div class="col-lg-5">

                                        <label class="payment-label">
                                            Amount
                                        </label>

                                        <div class="payment-input-group">

                                            <div class="payment-icon">

                                                <i class="ti ti-currency-dollar"></i>

                                            </div>

                                            <input type="number" name="Amount[]" id="Amount" class="Amount form-control payment-input" value="0.00" onkeyup="payment()" onkeydown="payment()" onkeypress="payment()" onchange="payment()">

                                        </div>

                                    </div>


                                    <!-- Payment Type -->

                                    <div class="col-lg-5">

                                        <label class="payment-label">Payment Type</label>

                                        <div class="payment-select-group">

                                            <div class="payment-icon">

                                                <i class="ti ti-wallet"></i>

                                            </div>

                                            <select name="paymentType[]"  id="paymentType" class="paymentType form-select payment-select">

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

                                    </div>


                                    <!-- Action -->

                                    <div class="col-lg-2">

                                        <label class="payment-label">Action</label>

                                        <button type="button" class="payment-delete payment-delete-disabled" disabled>
                                            <i class="ti ti-trash"></i>
                                        </button>
                                        <!-- <button type="button" class="payment-delete remove-payment">
                                            <i class="ti ti-trash"></i>
                                        </button> -->

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                    <button
                        type="button"
                        id="add-payment"
                        onclick="addPayment()"
                        class="btn-add-payment">

                        <i class="ti ti-plus"></i>

                        Add Payment

                    </button>

                </div>


                <div class="payment-right">

                    <div class="payment-summary">

                        <div class="summary-row">

                            <div class="summary-icon blue">
                                <i class="ti ti-shopping-cart"></i>
                            </div>

                            <div>

                                <small>Total Items</small>

                                <h6 class="totItemSpan">0.00</h6>

                            </div>

                        </div>


                        <div class="summary-row">

                            <div class="summary-icon green">
                                <i class="ti ti-wallet"></i>
                            </div>

                            <div>

                                <small>Total</small>

                                <h6 class="totSpan">0.00</h6>

                            </div>

                        </div>


                        <div class="summary-row">

                            <div class="summary-icon orange">
                                <i class="ti ti-discount"></i>
                            </div>

                            <div>

                                <small>Discount</small>

                                <h6 class="totDiscountSpan">0.00</h6>

                            </div>

                        </div>


                        <div class="summary-row">

                            <div class="summary-icon purple">
                                <i class="ti ti-arrow-back-up"></i>
                            </div>

                            <div>

                                <small>Return</small>

                                <h6 class="totReturnSpan">0.00</h6>

                            </div>

                        </div>


                        <div class="summary-highlight danger">

                            <span>Net Total</span>

                            <strong class="netTotSpan">
                                0.00
                            </strong>

                        </div>


                        <div class="summary-row">

                            <div class="summary-icon blue">
                                <i class="ti ti-credit-card"></i>
                            </div>

                            <div>

                                <small>Total Payment</small>

                                <h6 class="totPaymentSpan">0.00</h6>

                            </div>

                        </div>


                        <div class="summary-row">

                            <div class="summary-icon purple">
                                <i class="ti ti-building-bank"></i>
                            </div>

                            <div>

                                <small>Credit</small>

                                <h6 class="creditSpan">0.00</h6>

                            </div>

                        </div>


                        <div class="summary-highlight warning">

                            <span>Change Return</span>

                            <strong class="changeReturnSpan">
                                0.00
                            </strong>

                        </div>

                    </div>

                </div>

            </div>
            <div class="modal-footer payment-footer">

                <button
                    type="button"
                    name="btn_submit"
                    id="btn_submit"
                    class="btn-pos btn-submit">

                    <i class="ti ti-check"></i>

                    Submit

                </button>

                <button
                    type="button"
                    name="btn_submit_invoice"
                    id="btn_submit_invoice"
                    class="btn-pos btn-submit-print">

                    <i class="ti ti-printer"></i>

                    Submit & Print Invoice

                </button>

            </div>
        </div>
    </div>
</div>
<script>
    function addPayment()
    {
        var totalpayment = 0;
        $(document).find(".Amount").each(function(){
            totalpayment +=  parseFloat($(this).val()) || 0;;
        });
        var netamount =  parseFloat($("#netamount").val()) || 0;
        var balance = netamount - totalpayment;
        if(balance <=0)
        {
            balance =0;
        }
        var payNo = $(".payment-box").length + 1;
        var payment =   `<div class="payment-card paying">`+
                            `<div class="payment-box card p-4" data-payNo="${payNo}">`+
                                `<div class="row align-items-end">`+
                                    `<div class="col-lg-5">`+
                                        `<label class="payment-label">Amount</label>`+
                                        `<div class="payment-input-group">`+
                                            `<div class="payment-icon">`+
                                                `<i class="ti ti-currency-dollar"></i>`+
                                            `</div>`+
                                            `<input type="number" name="Amount[]" id="Amount-${payNo}" class="Amount form-control payment-input" value="${balance.toFixed(2)}" step="0.01" onkeyup="payment()" onkeydown="payment()" onkeypress="payment()" onchange="payment()">`+
                                        `</div>`+
                                    `</div>`+
                                    `<div class="col-lg-5">`+
                                        `<label class="payment-label">Payment Type</label>`+
                                        `<div class="payment-select-group">`+
                                            `<div class="payment-icon">`+
                                                `<i class="ti ti-wallet"></i>`+
                                            `</div>`+
                                            `<select name="paymentType[]"  id="paymentType" class="paymentType form-select payment-select">`+
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
                                    `</div>`+
                                    `<div class="col-lg-2">`+
                                        `<label class="payment-label">Action</label>`+
                                        `<button type="button" class="payment-delete remove-payment">`+
                                            `<i class="ti ti-trash"></i>`+
                                        `</button>`+
                                    `</div>`+
                                `</div>`+
                            `</div>`+
                        `</div>`;
       
        $("#paymentMethods").append(payment);
        $(`#Amount-${payNo}`).trigger("keyup").focus();
    }
        
</script>

<style>

.modal-content{

    border:none;

    border-radius:28px;

    overflow:hidden;

    box-shadow:0 25px 80px rgba(0,0,0,.18);
}

.modal-header{

    border:none;

    padding:30px 35px 10px;
}

.modal-title{

    font-size:34px;

    font-weight:700;

    color:#162042;
}

.payment-body{

    display:grid;

    grid-template-columns:2fr 1fr;

    gap:30px;

    padding:30px;
}

.payment-left{

    display:flex;

    flex-direction:column;

    gap:20px;
}

.payment-right{

    position:sticky;

    top:0;
}

.payment-summary{

    background:#fff;

    border-radius:22px;

    border:1px solid #edf2ff;

    overflow:hidden;

    box-shadow:0 15px 35px rgba(0,0,0,.06);
}

.summary-row{

    display:flex;

    align-items:center;

    justify-content:space-between;

    padding:18px 22px;

    border-bottom:1px solid #eef2f7;
}

.summary-row>div:last-child{

    flex:1;

    margin-left:18px;
}

.summary-row small{

    color:#8892b0;

    display:block;
}

.summary-row h6{

    margin:0;

    font-size:20px;

    font-weight:700;
}

.summary-icon{

    width:54px;

    height:54px;

    border-radius:16px;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:22px;
}

.blue{

    background:#eef3ff;

    color:#3968ff;
}

.green{

    background:#eafaf0;

    color:#1ca552;
}

.orange{

    background:#fff5e6;

    color:#f79b14;
}

.purple{

    background:#f3edff;

    color:#7b52f8;
}

.summary-highlight{

    display:flex;

    justify-content:space-between;

    align-items:center;

    padding:20px 22px;

    font-size:22px;

    font-weight:700;
}

.summary-highlight.danger{

    background:#fff0f0;

    color:#ff2b2b;
}

.summary-highlight.warning{

    background:#fff8e8;

    color:#ff9800;
}

.btn-add-payment{

    height:75px;

    border:2px dashed #d7e3ff;

    background:#fff;

    border-radius:20px;

    font-size:20px;

    font-weight:600;

    color:#2f6df6;

    transition:.3s;
}

.btn-add-payment:hover{

    background:#f7faff;

    border-color:#2f6df6;
}

.payment-footer{

    border:none;

    padding:25px 30px 35px;

    justify-content:flex-end;

    gap:15px;
}

.btn-submit{

    background:#ffb126;

    color:#fff;

    border:none;

    border-radius:14px;

    padding:14px 28px;

    font-size:17px;

    font-weight:600;
}

.btn-submit-print{

    background:#2f6df6;

    color:#fff;

    border:none;

    border-radius:14px;

    padding:14px 28px;

    font-size:17px;

    font-weight:600;
}

@media(max-width:992px){

    .payment-body{

    grid-template-columns:1fr;
    }

    .payment-right{

    order:-1;
    }

}
.payment-card{

    margin-bottom:22px;

}

.payment-box{

    border:none;

    border-radius:22px;

    box-shadow:0 10px 35px rgba(20,40,80,.08);

    background:#fff;

}

.payment-label{

    font-size:18px;

    font-weight:600;

    color:#2d3a5b;

    margin-bottom:12px;

}

.payment-input-group,

.payment-select-group{

    position:relative;

}

.payment-icon{

    position:absolute;

    left:18px;

    top:50%;

    transform:translateY(-50%);

    width:40px;

    height:40px;

    border-radius:50%;

    background:#eef3ff;

    display:flex;

    align-items:center;

    justify-content:center;

    color:#4b6bff;

    font-size:18px;

    z-index:5;

}

.payment-input{

    height:72px;

    border-radius:18px;

    padding-left:68px;

    font-size:32px;

    font-weight:600;

    border:2px solid #dfe8ff;

    box-shadow:none;

}

.payment-input:focus{

    border-color:#4b6bff;

    box-shadow:none;

}

.payment-select{

    height:72px;

    border-radius:18px;

    padding-left:68px;

    font-size:22px;

    font-weight:500;

    border:2px solid #dfe8ff;

    box-shadow:none;

}

.payment-select:focus{

    border-color:#4b6bff;

    box-shadow:none;

}

.payment-delete{

    width:72px;

    height:72px;

    border:none;

    border-radius:18px;

    background:#ffeaea;

    color:#ff3434;

    font-size:24px;

    transition:.25s;

}

.payment-delete:hover{

    background:#ff3434;

    color:#fff;

}

.payment-delete-disabled{

    background:#f4f6fb;

    color:#aab2c8;

    cursor:default;

}

.payment-delete-disabled:hover{

    background:#f4f6fb;

    color:#aab2c8;

}

.payment-input::-webkit-inner-spin-button,

.payment-input::-webkit-outer-spin-button{

    -webkit-appearance:none;

    margin:0;

}

.payment-input{

    appearance:textfield;

}

@media(max-width:992px){

.payment-delete{

width:100%;

margin-top:15px;

}

.payment-label{

margin-top:15px;

}

}

</style>