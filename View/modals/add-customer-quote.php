<?php 

$dbObj = new DBTransactions(); 
$sql="SELECT * FROM countries";
$country_codes = $dbObj->getData($sql);
?>
<div id="customer_modal" class="modal fade show" tabindex="-1" aria-labelledby="bs-example-modal-md" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">

            <h4 class="modal-title" id="ModalLabel">Add New Customer</h4>
            <button type="button" class="btn-close" id="close_Customer_modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="add-customer-form" action="../Controller/CustomerController.php?action=create_ajax" method="post">
                    <div class="row">                        
                        <div class="col-md-6">
                            <div class="m-2">
                                <label class="form-label">Customer Name <span class="text text-danger">*</span></label>                                   
                                <input type="text" name="cust_name" id= "cust_name" style="width:100%;" class="form-control mb-2 required capitalize-input" placeholder="Eg: Jhon Fernando" required>
                                <span class="text-danger" id="alrt" style="display: none;">This Field is Required</span>
                            </div>
                        </div>                                                               
                        <div class="col-md-6">
                            <div class="m-2">
                                <label class="form-label">
                                    Contact <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <select name="country_code" id="country_code" class="form-select " style="max-width: 220px;">
                                        <?php 
                                        foreach ($country_codes as $country_code) 
                                        {
                                            ?>
                                            <option value="<?=$country_code["country_code"]?>">
                                                <?=$country_code["iso_code"]." - ".$country_code["country_code"]." - ".$country_code["country_name"]?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <input type="text" name="cust_contact" id="cust_contact"placeholder="712345678"title="Please enter a valid contact number"class="form-control required" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="m-2">
                                <label class="form-label">Address </label>                                   
                                <input type="text" name="cust_address" id="cust_address" placeholder="" class="form-control mb-2" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="m-2">
                                <label class="form-label">Date of Birth </label>                                   
                                <input type="date" name="cust_dob" id="cust_dob" placeholder="" class="form-control mb-2" >
                            </div>
                        </div>
                        <div class="col-md-6">                             
                            <div class="m-2">
                                <label class="form-label">Gender</label> 
                                <select name="gender" id="gender" class="form-select">
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                    <option value="0">Other</option>
                                </select>
                            </div>                            
                        </div>                                                           
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="btn_save_customer" id="btn_save_customer" class="btn bg-primary-subtle text-primary waves-effect">Save</button>
                        <!-- Close -->
                        <button type="button" class="btn bg-warning-subtle text-warning  waves-effect" data-bs-dismiss="modal" id="close_Customer_modal">Close</button>
                    </div>
                </form>
            </div>       
        </div>
    </div>
</div>
<script>
$(document).ready(function () {

    function formatPhoneNumber(number) {

        // Remove everything except digits
        number = number.replace(/\D/g, '');

        // If starts with 94, remove it
        if (number.startsWith('94')) {
            number = number.substring(2);
        }

        return number;
    }

    $('.capitalize-input').on('input', function () {

        let value = $(this).val().toLowerCase();

        value = value.replace(/\b\w/g, function (char) {
            return char.toUpperCase();
        });

        $(this).val(value);

    });
    $('#country_code').select2({
        dropdownParent: $('#customer_modal'),
        width: 'resolve'
    });

    $("#country_code").val("+94").trigger("change");
    
    // Contact formatting
    $('#cust_contact').on('input', function () {

        let formatted = formatPhoneNumber($(this).val());

        $(this).val(formatted);

        console.log(`formatted ${formatted}`);

    });

});
</script>