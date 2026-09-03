<?php 
session_start();
$shop_id = $_SESSION['shop_id'];
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
include "../../Model/shop_class.php";
$dbObj = new DBTransactions(); 
$shopObj = new Shop();
$sql="SELECT * FROM countries";
$country_codes = $dbObj->getData($sql);
$UserType = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : 0;

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
    <link rel="stylesheet" href="../../Assets/css/modal.css">

    <link rel="stylesheet" href="../../vendor/select2-develop/dist/css/select2.min.css">
    <script src="../../vendor/select2-develop/dist/js/select2.min.js"></script>
    <script src="../../Assets/jquery/toast.js"></script>
    <style>
        .cash-summary-card {
            position: relative;
            display: flex;
            align-items: center;
            gap: 14px;
            height: 100%;
            min-height: 112px;
            padding: 20px;
            background: #ffffff;
            border: 1px solid #e8e5f0;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(31, 24, 55, 0.06);
            overflow: hidden;
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                border-color 0.2s ease;
        }

        .cash-summary-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #7c3aed;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .cash-summary-card:hover {
            transform: translateY(-3px);
            border-color: #d8ccff;
            box-shadow: 0 12px 28px rgba(109, 40, 217, 0.12);
        }

        .cash-summary-card:hover::before {
            opacity: 1;
        }

        .cash-summary-icon {
            flex: 0 0 48px;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6d28d9;
            background: #f3e8ff;
            border-radius: 13px;
            font-size: 21px;
        }

        .cash-summary-content {
            min-width: 0;
        }

        .cash-summary-label {
            display: block;
            margin-bottom: 7px;
            color: #747089;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.3;
        }

        .cash-summary-value {
            margin: 0;
            color: #211b35;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
        }

        .cash-summary-card-primary {
            background: linear-gradient(135deg, #6d28d9, #7c3aed);
            border-color: transparent;
            box-shadow: 0 10px 25px rgba(109, 40, 217, 0.22);
        }

        .cash-summary-card-primary::before {
            display: none;
        }

        .cash-summary-card-primary:hover {
            border-color: transparent;
            box-shadow: 0 14px 32px rgba(109, 40, 217, 0.3);
        }

        .cash-summary-card-primary .cash-summary-icon {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.16);
        }

        .cash-summary-card-primary .cash-summary-label {
            color: rgba(255, 255, 255, 0.75);
        }

        .cash-summary-card-primary .cash-summary-value {
            color: #ffffff;
        }

        @media (max-width: 575.98px) {
            .cash-summary-card {
                min-height: 96px;
                padding: 16px;
            }

            .cash-summary-value {
                font-size: 17px;
            }
        }
    </style>
</head>
<body>
<?php 
$title ="Close Counter";
$subtitle ="Fill in the details below to close the current counter";
$CTID ="";
if(isset($_GET['counterid']) && !empty($_GET['counterid']))
{
    $counterid = $_GET['counterid'];
}
else
{
    $counterid = "";
}
if(empty($counterid))
{
    echo "<script>parent.toastr.error('Invalid counter ID. Please try again.', 'Error');</script>";
    echo "<script>parent.$('#modal2').iziModal('close');</script>";
    exit;
}
$action ="../../Controller/counterController.php?condition=closeCounter&counterid=".$counterid;
$btnText ="<i class='ti ti-device-desktop'></i> Close Counter";
?>
<script>
    function TableLoading($table,TableName = "", loading="Loading ", dot="...")
    {
        let loaderHtml = `<div class="d-flex flex-column justify-content-center align-items-center text-center" style="min-height: 50px;">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="mt-2 fw-semibold">${loading+" "+TableName+dot}</div>
        </div>`;
        $table.html(loaderHtml);
    }
    $.ajax({
        url: "../../Controller/counterController.php",
        method: "GET",
        data: { condition: "fetch", CCID: <?=$counterid?> },
        dataType: "json",
        beforeSend: function() {
            // Optional: Show a loading indicator or disable the form while fetching data
            $("#submitBtn").prop("disabled", true).html("Loading...");
            var $table = $('.cash-summary-value');
            TableLoading($table,"","","");
        },
        success: function(response) {
            if (response.status === 1) 
            {
                var counterData = response.data;
                // Populate the form fields with the fetched data
                $("#startAmount").text("Rs. " + parseFloat(counterData.StartBalance).toFixed(2));
                $("#totalCashSales").text("Rs. " + parseFloat(counterData.TotalCashAmount || 0).toFixed(2));
                $("#totalCashExpenses").text("Rs. " + parseFloat(counterData.TotalCashExpenses || 0).toFixed(2));
                $("#systemCashBalance").text("Rs. " + parseFloat(counterData.ActualBalance || 0).toFixed(2));
            } 
            else 
            {
                parent.toastr.error(response.message, "Error");
                parent.$("#modal2").iziModal("close");

            }
            $("#submitBtn").prop("disabled", false).html("<?=$btnText?>");
        },
        error: function(xhr, status, error) {
            console.log("AJAX Error:", status, error);
            console.log("Response:", xhr.responseText);
            parent.toastr.error("An error occurred while fetching Cashcounter data.", "Error");
            parent.$("#modal").iziModal("close");
        }
    });  
</script>
<div class="modal-form-wrapper">

    <!-- Header -->
    <div class="modal-form-header">
        <div class="modal-header-icon">
            <i class="ti ti-device-desktop"></i>
        </div>

        <div>
            <h3 class="modal-form-title"><?=$title?></h3>
            <p class="modal-form-subtitle">
                <?=$subtitle?>
            </p>
        </div>
    </div>
    <form action="<?=$action?>" id="modalForm" method="post" enctype="multipart/form-data">
        <!-- Form Card -->
        <div class="row g-3 mb-4">
            <!-- Start Amount -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="cash-summary-card">
                    <div class="cash-summary-icon">
                        <i class="ti ti-cash"></i>
                    </div>
                    <div class="cash-summary-content">
                        <span class="cash-summary-label">Start Amount</span>
                        <h5 class="cash-summary-value" id="startAmount">Rs. 0.00</h5>
                    </div>
                </div>
            </div>
            <!-- Total Cash Sales -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="cash-summary-card">
                    <div class="cash-summary-icon">
                        <i class="ti ti-cash"></i>
                    </div>
                    <div class="cash-summary-content">
                        <span class="cash-summary-label">Total Cash Sales</span>
                        <h5 class="cash-summary-value" id="totalCashSales">Rs. 0.00</h5>
                    </div>
                </div>
            </div>
            <!-- Total Cash Expenses -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="cash-summary-card">
                    <div class="cash-summary-icon">
                        <i class="ti ti-receipt"></i>
                    </div>
                    <div class="cash-summary-content">
                        <span class="cash-summary-label">Total Cash Expenses</span>
                        <h5 class="cash-summary-value" id="totalCashExpenses">Rs. 0.00</h5>
                    </div>
                </div>
            </div>
            <!-- System Cash Amount -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="cash-summary-card cash-summary-card-primary">
                    <div class="cash-summary-icon">
                        <i class="ti ti-cash"></i>
                    </div>
                    <div class="cash-summary-content">
                        <span class="cash-summary-label">System Cash Balance</span>
                        <h5 class="cash-summary-value" id="systemCashBalance">Rs. 0.00</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-form-card">
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="mb-3">
                        <label class="form-label required">Actual Cash Balance</label>
                        <input type="number" step="0.01" class="form-control modal-input" id="endAmount" name="endAmount" placeholder="0.00" required>
                    </div>                   
                </div>     
            </div>
        </div>

        <!-- Footer -->
        <div class="modal-form-footer">

            <button type="button" class="btn btn-danger modal-btn-cancel">
                <i class="ti ti-x"></i>
                Cancel
            </button>

            <button type="submit" class="btn modal-btn-save" id="submitBtn">
                <?=$btnText?>
            </button>

        </div>    
    </form>
    <script>
        $(".modal-btn-cancel").on("click", function() {
            parent.$("#modal2").iziModal("close");
        });
        
        $("#modalForm").on("submit", function(e){
            e.preventDefault();
            var url = $(this).attr("action")
            var formData = new FormData(this);
            let submitter = e.originalEvent.submitter;
            let btnID = submitter ? submitter.id : (clickedBtn ? clickedBtn.attr("id") : null);
            var btnHTML1= $("#submitBtn").html();
            $.ajax({
                url: url,
                method: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $("#submitBtn").prop("disabled", true).html("Ending.....")
                },
                success: function(response){

                    if(response.status === 1)
                    {
                        parent.toastr.success(response.message, "Success");
                        // Optional
                        setTimeout(() => {
                            parent.$("#modal2").iziModal("close");
                            parent.location.href = "../../Receipts/counter_close.php?counter_id=<?=$counterid?>";
                            
                        }, 300);                        
                    }
                    else
                    {
                        parent.toastr.error(response.message, "Error");
                    }

                },
                complete: function(response){
                    $("#submitBtn").prop("disabled", false).html(btnHTML1);
                },
                error: function (xhr, status, error) {
                    $("#submitBtn").prop("disabled", false).html(btnHTML1);
                    console.log("AJAX Error:", status, error);
                    console.log("Response:", xhr.responseText);
                    parent.toastr.error("An error occurred while ending the counter.","Error");
                }
            })
        });
        
    </script>

</div>  
</body>
</html>

