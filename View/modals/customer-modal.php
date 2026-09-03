<?php 

include "../../Includes/config.php";
include "../../Model/DB_Class.php";
$dbObj = new DBTransactions(); 
$sql="SELECT * FROM countries";
$country_codes = $dbObj->getData($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Smart Edge | Powered By Next Edge</title>
    <link rel="shortcut icon" type="image/png" href="../../Assets/Images/SystemLogo/favicon.png" />
    <link rel="stylesheet" href="../../Assets/css/styles.min.css" />

    <script src="../../JQuery_361.js"></script>

    <script src="../../Assets/jquery/jquery.min.js"></script>
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="../../Assets/css/toastr.min.css">
    <!-- jQuery (Required for Toastr) -->
    <script src="../../Assets/js/jquery-3.6.0.min.js"></script>
    <!-- Toastr JS -->
    <script src="../../Assets/jquery/toastr.min.js"></script>


    <script src="../../Assets/ckeditor/ckeditor.js"></script>

    <link rel="stylesheet" href="../../Assets/css/styles.css">
    <link rel="stylesheet" href="../../Assets/css/head.css">

    <link rel="stylesheet" href="../../vendor/select2-develop/dist/css/select2.min.css">
    <script src="../../vendor/select2-develop/dist/js/select2.min.js"></script>
    <script src="../../Assets/jquery/toast.js"></script>
    <style>
        .customer-form-wrapper{
            padding:20px;
        }

        .customer-form-header{
            display:flex;
            align-items:center;
            gap:15px;
            margin-bottom:25px;
        }

        .customer-header-icon{
            width:65px;
            height:65px;
            background:#f3f5ff;
            border-radius:18px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:30px;
            color:#5d5fef;
        }

        .customer-form-title{
            font-size:32px;
            font-weight:700;
            margin:0;
        }

        .customer-form-subtitle{
            color:#6c757d;
            margin:0;
        }

        .customer-form-card{
            background:#fff;
            border:1px solid #edf0f7;
            border-radius:20px;
            padding:25px;
        }

        .customer-input,
        .customer-input-group .input-group-text{
            height:55px;
        }

        .customer-input{
            border-radius:12px;
        }

        .customer-input-group .input-group-text{
            background:#fff;
            border-right:none;
        }

        .customer-input-group .form-control{
            border-left:none;
        }

        .required::after{
            content:" *";
            color:red;
        }

        .customer-settings-card{
            background:#fafbff;
            border:1px solid #edf0f7;
            border-radius:18px;
            padding:20px;
            margin-top:20px;
        }

        .settings-title{
            color:#5d5fef;
            margin-bottom:20px;
        }

        .setting-item{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:18px;
        }

        .setting-desc{
            font-size:13px;
            color:#6c757d;
        }

        .customer-upload-box{
            border:2px dashed #d6d9ea;
            border-radius:18px;
            text-align:center;
            padding:40px;
        }

        .upload-icon{
            font-size:60px;
            color:#5d5fef;
            margin-bottom:15px;
        }

        .upload-info{
            margin-top:12px;
            color:#6c757d;
            font-size:13px;
        }

        .customer-logo-preview{
            margin-top:20px;
            background:#fafbff;
            border:1px solid #edf0f7;
            border-radius:18px;
            text-align:center;
            padding:25px;
        }

        .customer-form-footer{
            display:flex;
            justify-content:flex-end;
            gap:10px;
            margin-top:25px;
        }

        .customer-btn-save{
            background:#5d5fef;
            color:#fff;
            border:none;
            padding:12px 30px;
            border-radius:12px;
        }

        .customer-btn-save:hover{
            background:#4b4de0;
            color:#fff;
        }

        .customer-btn-cancel{
            padding:12px 25px;
            border-radius:12px;
        }
        #compLogo{
            max-width:250px;
            max-height:250px;
            object-fit:contain;
        }
    </style>
</head>
<body>
<?php 
$condition = $_GET["condition"];
$title ="";
$subtitle ="";
$CTID ="";
$action ="../../Controller/CustomerController.php";
if($condition=="new")
{
    $title ="Add New customer";
    $subtitle ="Fill in the details below to add a new customer";
    $action .="?condition=$condition";
    $btn ="<i class='ti ti-device-floppy'></i> Save customer";
}
else if($condition=="edit")
{
    $title ="Update Customer";
    $subtitle ="Fill in the details below to update customer details";
    $btn ="<i class='ti ti-device-floppy'></i> Update customer";
    if(isset($_GET["CTID"]) && !empty($_GET["CTID"]))
    {
        $CTID = $_GET["CTID"];
        $action .="?condition=$condition&CTID=$CTID";
        ?>
        <script>
            
        </script>
        <?php

    }
    else
    {
        // close modal
        ?>
        <script>
        parent.toastr.error("Customer ID Not Found", "Error");
        setTimeout(function () {
            parent.$("#modal").iziModal("close");
        }, 300);    
        </script>
        <?php
        
    }
}
?>

<div class="customer-form-wrapper">

    <!-- Header -->
    <div class="customer-form-header">
        <div class="customer-header-icon">
            <i class="ti ti-user-circle"></i>
        </div>

        <div>
            <h3 class="customer-form-title"><?=$title?></h3>
            <p class="customer-form-subtitle">
                <?=$subtitle?>
            </p>
        </div>
    </div>
    <form action="<?=$action?>" id="customerForm" method="post" enctype="multipart/form-data">
        <!-- Form Card -->
        <div class="customer-form-card">

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label required">Customer Name</label>
                        <input type="text" class="form-control customer-input" id="customer-name" name="customer_name" placeholder="Enter customer name" required>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label required">Customer Contact</label>
                        <div class="input-group">
                            <select name="country_code" id="country_code" class="form-select " style="max-width: 220px;" required>
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
                            <input type="text" name="cust_contact" id="cust_contact" placeholder="712345678" title="Please enter a valid contact number"class="form-control required" required>
                        </div>
                    </div>
                </div>             
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label ">Customer Email</label>
                        <input type="email" class="form-control customer-input" id="customer-email" name="customer_email" placeholder="Enter Customer Email" >
                    </div>
                </div>   
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label ">Customer Address</label>
                        <input type="text" class="form-control customer-input" id="customer-address" name="customer_address" placeholder="Enter Customer Address" >
                    </div>
                </div>       
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label ">Customer DOB</label>
                        <input type="date" class="form-control customer-input" id="customer-dob" name="customer_dob" placeholder="Enter Customer DOB" >
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label ">Max Credit Limit</label>
                        <input type="number" class="form-control customer-input" id="customer-credit-limit" step="any" name="customer_credit_limit" placeholder="Enter Max Credit Limit" >
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label ">Gender</label>
                        <select class="form-select customer-input" id="customer-gender" name="customer_gender" >
                            <option value="1">Male</option>
                            <option value="2">Female</option>
                            <option value="0">Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label ">Customer Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="customer_status"  id="customer_status" value="1">
                        </div>
                        
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="customer-form-footer">

            <button type="button"
                class="btn btn-danger customer-btn-cancel">
                <i class="ti ti-x"></i>
                Cancel
            </button>

            <button type="submit"
                class="btn customer-btn-save">
                <?=$btn?>
            </button>

        </div>    
    </form>
    <script>
        let $condition = "<?=$condition?>";
        let ref = "<?=$_GET['ref']?>";
        // Contact formatting
        function formatPhoneNumber(number) {
            // Remove everything except digits
            number = number.replace(/\D/g, '');
            // If starts with 94, remove it
            if (number.startsWith('94')) {
                number = number.substring(2);
            }
            if (number.startsWith('0')) {
                number = number.substring(1);
            }
            return number;
        }
        $('#cust_contact').on('input', function () {

            let formatted = formatPhoneNumber($(this).val());

            $(this).val(formatted);

            // console.log(`formatted ${formatted}`);

        });
        $("#customerForm").on("submit", function(e){
            e.preventDefault();
            console.log("submitted ");
            var url = $(this).attr("action")
            var formData = new FormData(this);
            var btn = $(this).find("button[type=submit]");
            var btnHTML = $(this).find("button[type=submit]").html();
            $.ajax({
                url: url,
                method: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    if($condition==="new")
                    {
                        btn.prop("disabled", true).html("Saving.....")
                    }
                    else if($condition==="edit")
                    {
                        btn.prop("disabled", true).html("Updating.....")
                    }
                },
                success: function(response){

                    if(response.status === 1)
                    {
                        parent.toastr.success(response.message, "Success");

                        // Optional
                        setTimeout(() => {
                            parent.$("#modal").iziModal("close");
                            if(ref=="customerlist")
                            {
                                parent.fetchcustomrData();    
                            }
                            else if(ref=="create-quote")
                            {
                                // parent.fetchCustomerList(parent.$("#cmb_customer"));
                                var CTID = response.customer[0].CTID;
                                var CustName = response.customer[0].CustName;
                                var CustContact = response.customer[0].CustContact;
                                var country_code = response.customer[0].country_code;
                                var string = CustName + " - " + country_code + CustContact;
                                parent.setPredefinedCustomer(CTID, string);
                                if(CTID!=1)
                                {
                                    string+="<a href='../Public/customerProfile.php?cus_id="+CTID+"&ref=guipos' class='open-modal'> View Customer Profile</a>";
                                    string +=`<a href="../View/modals/customer-modal.php?CTID=${CTID}&condition=edit&ref=${ref}" data-title="Edit Customer" class="open-modal">
                                                            <i class="ti ti-edit"></i>
                                                        </a>`;
                                }

                                parent.customerChange(string);
                            }   
                            else if(ref=="create-quote2")
                            {
                                var CTID = response.customer[0].CTID;
                                var CustName = response.customer[0].CustName;
                                var initials = '';
                                if (CustName) {
                                    var words = CustName.trim().split(/\s+/);
                                    initials = words
                                        .slice(0, 2)
                                        .map(function(word) {
                                            return word.charAt(0).toUpperCase();
                                        })
                                        .join('');
                                }
                                var CustContact = response.customer[0].CustContact;
                                var country_code = response.customer[0].country_code;
                                var data = {
                                    id: CTID,
                                    text: CustName + " - " + country_code + CustContact,
                                    phone: CustContact,
                                    full_text: response.customer[0].CustomerNo+" - "+CustName+" - "+country_code+" "+CustContact,
                                    initials: initials,
                                    customerProfile: "../Public/customerProfile.php?cus_id="+CTID+"&ref=guipos",
                                    editCustomer: '../View/modals/customer-modal.php?CTID='+CTID+'&condition=edit&ref=create-quote2'
                                };
                                var string = CustName + " - " + country_code + CustContact;
                                parent.setPredefinedCustomer(CTID, string);
                                parent.customerChange(data);
                            }   
                            else if(ref=="guipos")
                            {
                                var CTID = response.customer[0].CTID;
                                var CustName = response.customer[0].CustName;
                                var CustContact = response.customer[0].CustContact;
                                var country_code = response.customer[0].country_code;
                                var string = CustName + " - " + country_code + CustContact;
                                parent.setPredefinedCustomer(CTID, string);;
                            }   
                        }, 300);
                        
                    }
                    else
                    {
                        parent.toastr.error(response.message, "Error");
                    }

                },
                complete: function(response){

                    btn.prop("disabled", false).html(btnHTML);
                },
                error: function (xhr, status, error) {
                    btn.prop("disabled", false).html(btnHTML);
                    console.log("AJAX Error:", status, error);
                    console.log("Response:", xhr.responseText);
                    parent.toastr.error("An error occurred while saving customer data.","Error");
                }
            })
        });
        
        $(document).ready(function(){
            var CTID = "<?=$CTID?>";
            console.log("Condition:", $condition);
            $(".customer-btn-cancel").on("click", function(e){
                e.preventDefault();
                parent.$("#modal").iziModal("close");
            });
            $('#country_code').select2({
                dropdownParent: $('body'),
                width: 'resolve'
            });
            if($condition==="new")
            {
                $("#customer_status").prop("checked", true);
                $("#country_code").val("+94").trigger("change");
            }
            if($condition==="edit")
            {
                $.ajax({
                    url: '../../AJAX/Customer/getCustomers.php?CTID='+CTID,
                    method: 'GET',
                    dataType: 'json',

                    beforeSend: function () {
                    },

                    success: function (response) {
                        if (!response || response.length === 0 || !response[0])
                        {
                            parent.toastr.error("customer ID Not Found", "Error");
                            parent.$("#modal").iziModal("close");
                            return;
                        }
                        var CTID = response[0].CTID;             
                        var CustAddress = response[0].CustAddress;
                        var CustContact = response[0].CustContact;
                        var CustDOB = response[0].CustDOB;
                        var CustEmail = response[0].CustEmail;
                        var CustGender = response[0].CustGender;
                        var CustName = response[0].CustName;
                        var CustStat = response[0].CustStat;
                        var CustomerNo = response[0].CustomerNo;
                        var MaxCreditAmount = response[0].MaxCreditAmount;
                        var country_code = response[0].country_code || "+94"; // Default to +94 if not set
                        $("#customer-name").val(CustName);
                        $("#cust_contact").val(CustContact).trigger("input");
                        $("#customer-email").val(CustEmail);
                        $("#customer-address").val(CustAddress);
                        $("#customer-dob").val(CustDOB);
                        $("#customer-credit-limit").val(MaxCreditAmount);
                        $("#customer-gender").val(CustGender);
                        $("#country_code").val(country_code).trigger("change");
                        var customer_form_title = "Update Customer - " + CustomerNo;
                        $(".customer-form-title").text(customer_form_title);
                        if(CustStat == 1)
                        {
                            $("#customer_status").prop("checked", true);
                        }
                        else
                        {
                            $("#customer_status").prop("checked", false);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log("AJAX Error:", status, error);
                        console.log("Response:", xhr.responseText);
                        parent.toastr.error("An error occurred while loading customer data.","Error");
                    }
                });
            }
        });
    </script>

</div>  
</body>
</html>

