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
</head>
<body>
<?php 
$title ="Start Counter";
$subtitle ="Fill in the details below to start a new counter";
$CTID ="";
$action ="../../Controller/counterController.php?condition=startCounter";
$btnText ="<i class='ti ti-device-desktop'></i> Start Counter";
?>

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
        <div class="modal-form-card">
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="mb-3">
                        <label class="form-label required">Start Amount</label>
                        <input type="number" step="0.01" class="form-control modal-input" id="startAmount" name="startAmount" placeholder="0.00" required>
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
                    $("#submitBtn").prop("disabled", true).html("Starting.....")
                },
                success: function(response){

                    if(response.status === 1)
                    {
                        parent.toastr.success(response.message, "Success");
                        // Optional
                        setTimeout(() => {
                            parent.$("#modal2").iziModal("close");
                            <?php 
                            if($shopObj->hasRetailShop($shop_id))
                            {
                                ?>
                                parent.location.href = "../../Public/gui-pos.php";
                                <?php
                            }
                            else
                            {
                                ?>
                                parent.location.href = "../../Public/wholesale-invoice.php";
                                <?php
                            }
                            ?>
                            
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
                    parent.toastr.error("An error occurred while starting the counter.","Error");
                }
            })
        });
        
    </script>

</div>  
</body>
</html>

