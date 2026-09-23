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
        function getExpenseCat() {
            return $.ajax({
                url: "../../AJAX/Expense/fetchExpenseCatData2.php",
                method: "POST",
                dataType: "json"
            }).then(function (response) {

                if (Number(response.status) === 1) {
                    return response.data || [];
                }

                toastr.error(
                    response.message || "Unable to fetch expense categories.",
                    "Error"
                );

                return $.Deferred().reject(response).promise();
            });
        }

        function append_cat(cat_id = "") {

            const $expense_cat = $("#expense_cat");

            $expense_cat.empty().append(
                $("<option>", {
                    value: "",
                    text: "Select Expense Category"
                })
            );

            return getExpenseCat()
                .then(function (expenseCates) {

                    $.each(expenseCates, function (index, cat) {

                        $expense_cat.append(
                            $("<option>", {
                                value: cat.ECID,
                                text: cat.expense_category,
                                selected: String(cat.ECID) === String(cat_id)
                            })
                        );

                    });

                    return expenseCates;
                })
                .fail(function (error) {
                    console.error("Expense categories request failed:", error);
                });
        }
        function get_payment_methods() {
            return $.ajax({
                url: "../../AJAX/GRN/getpaymentMethods.php",
                method: "POST",
                dataType: "json"
            }).then(function (response) {
                if (Number(response.status) === 1) {
                    return response.data || [];
                }

                toastr.error(
                    response.message || "Unable to fetch payment methods.",
                    "Error"
                );

                return $.Deferred().reject(response).promise();
            });
        }

        function appendPayments(PMID = 0) {

            const $payment = $("#payment");

            $payment.empty().append(
                $("<option>", {
                    value: "",
                    text: "Select Payment"
                })
            );

            return get_payment_methods()
                .then(function (payMethods) {

                    $.each(payMethods, function (index, method) {

                        $payment.append(
                            $("<option>", {
                                value: method.PMID,
                                text: method.PaymethodName,
                                selected: String(method.PMID) === String(PMID)
                            })
                        );

                    });

                    return payMethods;
                })
                .fail(function (error) {
                    console.error("Payment methods request failed:", error);
                });
        }
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
$action ="../../Controller/AddExpensesController2.php";
$btnText ="<i class='ti ti-device-floppy'></i> Create Expense";
if($condition=="new")
{
    $title ="Add New Expense";
    $subtitle ="Fill in the details below to add a new expense";
    $btnText ="<i class='ti ti-device-floppy'></i> Create Expense";
    $action .="?condition=$condition";
    ?>
    <script>
        $(document).ready(function () {
            append_cat();
            appendPayments();
        })
    </script>
    <?php
}
else if($condition=="edit")
{
    if(!isset($_GET["EPID"]) || empty($_GET["EPID"])) {
        ?>
        <script>
            parent.toastr.error("Error: Expense ID is missing.", "Error");
            parent.$("#modal").iziModal("close");
        </script>
        <?php
    }
    $EPID = $_GET["EPID"];
    $title ="Edit Expense";
    $subtitle ="Fill in the details below to edit the expense";
    $btnText ="<i class='ti ti-edit'></i> Update Expense";
    $action .="?condition=$condition&EPID=$EPID";
    ?>
    <script>
        $(document).ready(function () {

            var EPID = "<?=$EPID?>";

            $.ajax({
                url: "../../Controller/AddExpensesController2.php",
                method: "GET",
                data: {
                    condition: "fetch",
                    EPID: EPID
                },
                dataType: "json",

                success: function (response) {

                    console.log("Expense response:", response);

                    if (Number(response.status) === 1) {

                        var data = response.data;

                        // Normal inputs
                        $("#expense").val(data.ExpenseReason);
                        $("#expense_amount").val(data.ExpenseAmount);

                        // Checkboxes
                        $("#status").prop("checked", Number(data.status) === 1);
                        $("#is_default").prop("checked", Number(data.is_default) === 1);

                        // Load category and select current category
                        append_cat(data.expensecategory_id);

                        // Load payment methods and select current payment
                        appendPayments(data.paymethod_id);

                    } else {

                        parent.toastr.error(
                            response.message || "Expense not found.",
                            "Error"
                        );

                        parent.$("#modal").iziModal("close");
                    }
                },

                error: function (xhr, status, error) {

                    console.log("AJAX Error:", status, error);
                    console.log("Response:", xhr.responseText);

                    parent.toastr.error(
                        "An error occurred while fetching Expense data.",
                        "Error"
                    );
                }
            });

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
                <div class="col-lg-3 row">
                    <div class="col-lg-6 mt-4">
                        <div class="mb-3 d-flex justify-content-center align-items-center gap-3">
                            <label class="form-label">Status</label>
                            <div class="input-group">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" id="status" value="1" checked>    
                                </div>                            
                            </div>
                        </div>
                    </div>
                    <?php 
                    if($UserType==1)
                    {
                        ?>
                        <div class="col-lg-6 mt-4">
                            <div class="mb-3 d-flex justify-content-center align-items-center gap-3">
                                <label class="form-label">Default</label>
                                <div class="input-group">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1">    
                                    </div>                            
                                </div>
                            </div>
                        </div> 
                        <?php
                    }
                    ?>                    
                </div> 
                <div class="col-lg-12 row mt-1">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label required">Expense</label>
                            <input type="text" class="form-control modal-input" id="expense" name="expense" placeholder="Expense Category" required>
                        </div>    
                    </div>                    
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label required">Expense Category</label>
                            <div class="filter-group">
                                <select name="expense_cat" id="expense_cat" class="form-select modal-input" required></select>  
                                <button type="button" class="btn-refresh" id="refresh-cat">
                                    <i class="ti ti-refresh"></i>
                                </button>  
                            </div>
                        </div>    
                    </div>                    
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label required">Expense Amount</label>
                            <div class="filter-group">
                                <input type="number" step="0.01" class="form-control modal-input" id="expense_amount" name="expense_amount" placeholder="Expense Amount" required> 
                            </div>
                        </div>    
                    </div>                    
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label required">Payment Method</label>
                            <div class="filter-group">
                                <select name="payment" id="payment" class="form-select modal-input" required></select>
                            </div>
                        </div>    
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
                            if(ref=="AddExpense")
                            {
                                parent.fetchExpenseData();    
                                parent.fetchExpenseSummary();    
                                parent.fetchExpenseCharts();    
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

