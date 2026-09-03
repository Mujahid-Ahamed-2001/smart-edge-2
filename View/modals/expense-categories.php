<?php 
session_start();
$shop_id = $_SESSION['shop_id'];
include "../../Includes/config.php";
include "../../Model/DB_Class.php";
$dbObj = new DBTransactions(); 
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
    <script>
        function getTypes(){
            return $.ajax({
                url: "../../Controller/AddExpenseCategoryController2.php",
                method: "GET",
                data: { condition: "fetch_type"},
                dataType: "json",
                success: function(response) {
                    $("#expense_type").html("")
                    if (response.status === 1) {
                        var data = response.data;
                        var html = `<option value="">Select Type</option>`;
                        data.forEach(function(type){
                            html +=`<option value="${type.ETID}">${type.expense_type}</option>`;
                        })
                        $("#expense_type").html(html);
                    } else {
                        parent.toastr.error(response.message, "Error");
                        parent.$("#modal").iziModal("close");
                    }
                },
                error: function(xhr, status, error) {
                    console.log("AJAX Error:", status, error);
                    console.log("Response:", xhr.responseText);
                    parent.toastr.error("An error occurred while fetching Expense Category data.", "Error");
                    parent.$("#modal").iziModal("close");
                }
            });      
        }
        $(document).ready(function() {
            getTypes()
            $("#refresh-type").click(function(e){
                e.preventDefault();
                let icon = $("#refresh-type").find("i");
                icon.addClass("rotate2");
                getTypes().always(function(){
                    setTimeout(function(){
                        icon.removeClass("rotate2"); // Remove class after 1 second
                    }, 1000);
                })
            })
        });
    </script>
<?php 
$condition = $_GET["condition"];
if(!isset($condition) || empty($condition)) {
    ?>
    <script>
        parent.toastr.error("Error: Condition is missing.", "Error");
        parent.$("#modal").iziModal("close");
    </script>
    <?php
}
$title ="";
$subtitle ="";
$CTID ="";
$action ="../../Controller/AddExpenseCategoryController2.php";
$btnText ="<i class='ti ti-device-floppy'></i> Create Expense Category";
if($condition=="new")
{
    $title ="Add New Expense Category";
    $subtitle ="Fill in the details below to add a new expense category";
    $btnText ="<i class='ti ti-device-floppy'></i> Create Expense Category";
    $action .="?condition=$condition";
}
else if($condition=="edit")
{
    if(!isset($_GET["ECID"]) || empty($_GET["ECID"])) {
        ?>
        <script>
            parent.toastr.error("Error: Expense Category ID is missing.", "Error");
            parent.$("#modal").iziModal("close");
        </script>
        <?php
    }
    $ECID = $_GET["ECID"];
    $title ="Edit Expense Category";
    $subtitle ="Fill in the details below to edit the expense category";
    $btnText ="<i class='ti ti-edit'></i> Update Expense Category";
    $action .="?condition=$condition&ECID=$ECID";
    ?>
    <script>
        $(document).ready(function() {
            var ECID = "<?=$ECID?>";
            var is_default ="";
            getTypes().always(function(){
                $.ajax({
                    url: "../../Controller/AddExpenseCategoryController2.php",
                    method: "GET",
                    data: { condition: "fetch", ECID: ECID },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === 1) {
                            var data = response.data;
                            $("#expense_category").val(data.expense_ctg);
                            $("#expense_type").val(data.expense_ETID);
                            $("#status").prop("checked", data.status == 1);
                            $("#is_default").prop("checked", data.is_default == 1);
                            is_default = data.is_default;
                        } else {
                            parent.toastr.error(response.message, "Error");
                            parent.$("#modal").iziModal("close");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX Error:", status, error);
                        console.log("Response:", xhr.responseText);
                        parent.toastr.error("An error occurred while fetching Expense Category data.", "Error");
                        parent.$("#modal").iziModal("close");
                    }
                });    
            })
            
        });
    </script>
    <?php
}
else
{
    ?>
    <script>
        parent.toastr.error("Error: Invalid condition.", "Error");
        parent.$("#modal").iziModal("close");
    </script>
    <?php
}
?>

<div class="modal-form-wrapper">

    <!-- Header -->
    <div class="modal-form-header">
        <div class="modal-header-icon">
            <i class="ti ti-receipt-2"></i>
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
                <div class="col-lg-6 row mt-4">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label required">Expense Category</label>
                            <input type="text" class="form-control modal-input" id="expense_category" name="expense_category" placeholder="Expense Category" required>
                        </div>    
                    </div>                    
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label required">Expense Type</label>
                            <div class="filter-group">
                                <select name="expense_type" id="expense_type" class="form-select modal-input" required></select>  
                                <button class="btn-refresh" id="refresh-type">
                                    <i class="ti ti-refresh"></i>
                                </button>  
                            </div>
                        </div>    
                    </div>                    
                </div>
                <div class="col-lg-6 row">
                    <div class="col-lg-6 mt-4">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div class="input-group">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" id="status" checked>    
                                </div>                            
                            </div>
                        </div>
                    </div>
                    <?php 
                    if($UserType==1)
                    {
                        ?>
                        <div class="col-lg-6 mt-4">
                            <div class="mb-3">
                                <label class="form-label">Default</label>
                                <div class="input-group">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_default" id="is_default">    
                                    </div>                            
                                </div>
                            </div>
                        </div> 
                        <?php
                    }
                    ?>                    
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
            parent.$("#modal").iziModal("close");
        });
        let $condition = "<?=$condition?>";
        let ref = "<?=$_GET['ref']?>";
        let $UserType = "<?=$UserType?>";
        if($condition==="edit")
        {
            if($UserType != 1 && is_default == 1) 
            {
                parent.toastr.warning("You do not have permission to edit the default expense type.", "Warning");
                parent.$("#modal").iziModal("close");
            }
        }
        
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
                    if($condition==="new")
                    {
                        $("#submitBtn").prop("disabled", true).html("Saving.....")
                    }
                    if($condition==="edit")
                    {
                        $("#submitBtn").prop("disabled", true).html("Updating.....")
                    }
                },
                success: function(response){

                    if(response.status === 1)
                    {
                        parent.toastr.success(response.message, "Success");

                        // Optional
                        setTimeout(() => {
                            if(ref=="AddExpenseCategories")
                            {
                                parent.fetchExpenseCatData();    
                            }
                            parent.$("#modal").iziModal("close");
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
                    parent.toastr.error("An error occurred while creating Expense Type.","Error");
                }
            })
        });
        
    </script>

</div>  
</body>
</html>

