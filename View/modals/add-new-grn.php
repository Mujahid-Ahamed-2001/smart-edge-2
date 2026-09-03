<?php 
session_start();
$shop_id = $_SESSION['shop_id'];
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
    <link rel="stylesheet" href="../../Assets/css/modal.css">

    <link rel="stylesheet" href="../../vendor/select2-develop/dist/css/select2.min.css">
    <script src="../../vendor/select2-develop/dist/js/select2.min.js"></script>
    <script src="../../Assets/jquery/toast.js"></script>
</head>
<body>
<?php 
$condition = $_GET["condition"];
$title ="";
$subtitle ="";
$CTID ="";
$action ="../../Controller/grnController.php";
if($condition=="new")
{
    $title ="Add New GRN";
    $subtitle ="Fill in the details below to add a new GRN";
    $action .="?condition=$condition";
}
?>

<div class="modal-form-wrapper">

    <!-- Header -->
    <div class="modal-form-header">
        <div class="modal-header-icon">
            <i class="ti ti-stack-push"></i>
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
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label required">GRN No</label>
                        <input type="text" class="form-control modal-input" id="grn-no" name="grn_no" placeholder="GRN No" readonly required>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label required">Supplier</label>
                        <div class="input-group">
                            <select name="cmb_supplier" id="cmb_supplier" class="form-select " required>                                
                            </select>
                        </div>
                    </div>
                </div>             
                <div class="col-lg-12">
                    <div class="mb-3">
                        <label class="form-label ">Reference</label>
                        <textarea class="form-control modal-input" name="reference" id="reference"></textarea>
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

            <button type="submit" class="btn modal-btn-save" id="create-grn">
                <i class='ti ti-device-floppy'></i> Create GRN
            </button>
            <button type="submit" class="btn modal-btn-save" id="create-grn-enter">
                <i class='ti ti-device-floppy'></i> Create GRN & Enter Items
            </button>

        </div>    
    </form>
    <script>
        let $condition = "<?=$condition?>";
        let ref = "<?=$_GET['ref']?>";
        function initializegrnSupplierSelect2() {    
            $("#cmb_supplier").select2({
                ajax: {
                    url: '../../AJAX/GRN/getSuppliers.php',
                    dataType: 'json',
                    delay: 250,
                    cache: true,
                    data: function (params) {
                        return {
                            search: params.term,
                            type: 'item_search'
                        };
                    },
                    processResults: function (data) {
                        return { results: data };
                    }
                },
                placeholder: 'Select Suppliers',
                minimumInputLength: 1,
                width: '100%'
            }).on('select2:open', function () {
                setTimeout(function () {
                    document.querySelector('.select2-search__field')?.focus();
                }, 0);
            });
        }
        initializegrnSupplierSelect2();
        CKEDITOR.replace('reference', {
            height: 200
        });

        // Optional: Clear the editor
        CKEDITOR.instances.reference.setData('');
        $(".modal-btn-cancel").on("click", function(e){
            e.preventDefault();
            parent.$("#modal").iziModal("close");
        });
        
        $("#modalForm").on("submit", function(e){
            e.preventDefault();
            CKEDITOR.instances.reference.updateElement();
            var url = $(this).attr("action")
            var formData = new FormData(this);
            let submitter = e.originalEvent.submitter;
            let btnID = submitter ? submitter.id : (clickedBtn ? clickedBtn.attr("id") : null);
            var btnHTML1= $("#create-grn").html();
            var btnHTML2= $("#create-grn-enter").html();
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
                        $("#create-grn").prop("disabled", true).html("Saving.....")
                        $("#create-grn-enter").prop("disabled", true).html("Saving.....")
                    }
                },
                success: function(response){

                    if(response.status === 1)
                    {
                        parent.toastr.success(response.message, "Success");

                        // Optional
                        setTimeout(() => {
                            if(ref=="grn-header")
                            {
                                parent.fetchGRNData(<?=$shop_id?>);    
                            }
                            parent.$("#modal").iziModal("close");
                            var GRNID = response.GRNID;
                            var GRNHeaderNo = response.GRNHeaderNo;
                            if(btnID=="create-grn-enter")
                            {
                                parent.location="../../Public/grn-details2.php?grn_header="+GRNID+"&GRNHeaderNo="+GRNHeaderNo;
                            }
                        }, 300);
                        
                    }
                    else
                    {
                        parent.toastr.error(response.message, "Error");
                    }

                },
                complete: function(response){
                    $("#create-grn").prop("disabled", false).html(btnHTML1);
                    $("#create-grn-enter").prop("disabled", false).html(btnHTML2);
                },
                error: function (xhr, status, error) {
                    $("#create-grn").prop("disabled", false).html(btnHTML1);
                    $("#create-grn-enter").prop("disabled", false).html(btnHTML2);
                    console.log("AJAX Error:", status, error);
                    console.log("Response:", xhr.responseText);
                    parent.toastr.error("An error occurred while creating GRN.","Error");
                }
            })
        });
        
    </script>

</div>  
</body>
</html>

