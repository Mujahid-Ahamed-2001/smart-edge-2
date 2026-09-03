<?php 

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();

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
        .company-form-wrapper{
            padding:20px;
        }

        .company-form-header{
            display:flex;
            align-items:center;
            gap:15px;
            margin-bottom:25px;
        }

        .company-header-icon{
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

        .company-form-title{
            font-size:32px;
            font-weight:700;
            margin:0;
        }

        .company-form-subtitle{
            color:#6c757d;
            margin:0;
        }

        .company-form-card{
            background:#fff;
            border:1px solid #edf0f7;
            border-radius:20px;
            padding:25px;
        }

        .company-input,
        .company-input-group .input-group-text{
            height:55px;
        }

        .company-input{
            border-radius:12px;
        }

        .company-input-group .input-group-text{
            background:#fff;
            border-right:none;
        }

        .company-input-group .form-control{
            border-left:none;
        }

        .required::after{
            content:" *";
            color:red;
        }

        .company-settings-card{
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

        .company-upload-box{
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

        .company-logo-preview{
            margin-top:20px;
            background:#fafbff;
            border:1px solid #edf0f7;
            border-radius:18px;
            text-align:center;
            padding:25px;
        }

        .company-form-footer{
            display:flex;
            justify-content:flex-end;
            gap:10px;
            margin-top:25px;
        }

        .company-btn-save{
            background:#5d5fef;
            color:#fff;
            border:none;
            padding:12px 30px;
            border-radius:12px;
        }

        .company-btn-save:hover{
            background:#4b4de0;
            color:#fff;
        }

        .company-btn-cancel{
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
$CMID ="";
$action ="../../Controller/company.php";
if($condition=="new")
{
    $title ="Add New Company";
    $subtitle ="Fill in the details below to add a new company";
    $action .="?condition=$condition";
    $btn ="<i class='ti ti-device-floppy'></i> Save Company";
}
else if($condition=="edit")
{
    $title ="Update Company";
    $subtitle ="Fill in the details below to update company details";
    $btn ="<i class='ti ti-device-floppy'></i> Update Company";
    if(isset($_GET["CMID"]) && !empty($_GET["CMID"]))
    {
        $CMID = $_GET["CMID"];
        $action .="?condition=$condition&CMID=$CMID";
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
        parent.toastr.error("Company ID Not Found", "Error");
        setTimeout(function () {
            parent.$("#modal").iziModal("close");
        }, 300);    
        </script>
        <?php
        
    }
}
?> 
<script>
    $(document).ready(function(){
        let $condition = "<?=$condition?>";
        var CMID = "<?=$CMID?>";
        console.log("Condition:", $condition);
        function companyType()
        {
            $("#company-type").html("");
            $("#company-type").append(`<option value="">Select company type</option>`);
            <?php 
                $sql="SELECT * FROM `companytype`";
                $data = $dbObj->getData($sql);
                foreach($data as $row)
                {
                    ?>
                        $("#company-type").append(`<option value="<?=$row["CTID"]?>"><?=$row["CompanyTypeName"]?></option>`);
                    <?php
                }
            ?>
        }
        companyType();
        $(".company-btn-cancel").on("click", function(e){
            e.preventDefault();
            parent.$("#modal").iziModal("close");
        });
        if($condition==="edit")
        {
            $.ajax({
                url: '../../AJAX/Company/getCompanyList.php?CMID='+CMID,
                method: 'POST',
                dataType: 'json',

                beforeSend: function () {
                },

                success: function (response) {
                    if (!response || response.length === 0 || !response[0])
                    {
                        parent.toastr.error("Company ID Not Found", "Error");
                        parent.$("#modal").iziModal("close");
                        return;
                    }
                    var company_type = response[0].CTID;             
                    var company_name = response[0].name;
                    var company_location = response[0].CompanyLocation;
                    var company_licence = response[0].LicenceNo;
                    var company_version = response[0].version;
                    var company_multi_category = response[0].is_multicategory;
                    var company_common_stock = response[0].is_commonStock;
                    var company_status = response[0].status;
                    var company_start_date = response[0].ComStartDate;
                    var company_expire_date = response[0].expiry;
                    var ComLogo = response[0].logo;
                    $("#company-type").val(company_type);
                    $("#company-name").val(company_name);
                    $("#company-location").val(company_location);
                    $("#company-licence").val(company_licence);
                    $("#company-version").val(company_version);
                    if(company_multi_category==1)
                    {
                        $("#company-multi-cate").prop("checked", true)
                    }
                    else
                    {
                        $("#company-multi-cate").prop("checked", false)
                    }
                    if(company_common_stock==1)
                    {
                        $("#company-common-stock").prop("checked", true)
                    }
                    else
                    {
                        $("#company-common-stock").prop("checked", false)
                    }
                    if(company_status==1)
                    {
                        $("#company-active").prop("checked", true)
                    }
                    else
                    {
                        $("#company-active").prop("checked", false)
                    }
                    $("#company-startDate").val(company_start_date);
                    $("#company-expireDate").val(company_expire_date);
                    $("#compLogo").attr("src","../"+ComLogo);
                    console.log("ComLogo: ","../"+ComLogo);
                    
                },
                error: function (xhr, status, error) {
                    console.log("AJAX Error:", status, error);
                    console.log("Response:", xhr.responseText);
                    parent.toastr.error("An error occurred while loading company data.","Error");
                }
            });
        }
    });
</script>

<div class="company-form-wrapper">

    <!-- Header -->
    <div class="company-form-header">
        <div class="company-header-icon">
            <i class="ti ti-building"></i>
        </div>

        <div>
            <h3 class="company-form-title"><?=$title?></h3>
            <p class="company-form-subtitle">
                <?=$subtitle?>
            </p>
        </div>
    </div>
    <form action="<?=$action?>" id="companyForm" method="post" enctype="multipart/form-data">
        <!-- Form Card -->
        <div class="company-form-card">

            <div class="row g-4">

                <!-- LEFT -->
                <div class="col-lg-6">

                    <div class="mb-3">
                        <label class="form-label required">Company Type</label>
                        <select class="form-select company-input" id="company-type" name="company_type" required>
                            <option>Select company type</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Company Name</label>
                        <div class="input-group company-input-group">
                            <span class="input-group-text">
                                <i class="ti ti-building"></i>
                            </span>
                            <input type="text" name="company_name" id="company-name"
                                class="form-control company-input"
                                placeholder="Enter company name" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Company Location</label>
                        <div class="input-group company-input-group">
                            <span class="input-group-text">
                                <i class="ti ti-map-pin"></i>
                            </span>
                            <input type="text" name="company_location" id="company-location"
                                class="form-control company-input"
                                placeholder="Enter company location" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Company Licence</label>
                        <div class="input-group company-input-group">
                            <span class="input-group-text">
                                <i class="ti ti-file-text"></i>
                            </span>
                            <input type="text" name="company_licence" id="company-licence"
                                class="form-control company-input"
                                placeholder="Enter company licence" required >
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Company Version</label>
                        <div class="input-group company-input-group">
                            <span class="input-group-text">
                                <i class="ti ti-tag"></i>
                            </span>
                            <input type="text" name="company_version" id="company-version"
                                class="form-control company-input"
                                placeholder="Enter company version" required>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="company-settings-card">

                        <h5 class="settings-title">
                            <i class="ti ti-settings"></i>
                            Company Settings
                        </h5>

                        <div class="setting-item">
                            <div>
                                <strong>Multi Category</strong>
                                <div class="setting-desc">
                                    Allow multiple categories
                                </div>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"  name="company_multi_cate" id="company-multi-cate">
                            </div>
                        </div>

                        <div class="setting-item">
                            <div>
                                <strong>Common Stock</strong>
                                <div class="setting-desc">
                                    Enable common stock across shops
                                </div>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="company_common_stock"  id="company-common-stock">
                            </div>
                        </div>

                        <div class="setting-item">
                            <div>
                                <strong>Active Company</strong>
                                <div class="setting-desc">
                                    Company will be active after saving
                                </div>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                    type="checkbox"  name="company_status"  id="company-status"
                                    checked>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="col-lg-6">

                    <div class="mb-3">
                        <label class="form-label required">
                            Start Date
                        </label>

                        <input type="date" name="company_startDate"  id="company-startDate"
                            class="form-control company-input" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            License Expire Date
                        </label>

                        <input type="date"  name="company_expireDate" id="company-expireDate"
                            class="form-control company-input">
                    </div>

                    <!-- Upload -->
                    <div class="company-upload-wrapper">

                        <h5 class="upload-title">
                            Company Logo
                        </h5>

                        <p class="upload-subtitle">
                            Upload your company logo
                        </p>

                        <div class="company-upload-box">

                            <i class="ti ti-cloud-upload upload-icon"></i>

                            <h5>
                                Drag & Drop your logo here
                            </h5>

                            <p>or</p>

                            <label class="btn btn-primary">
                                Choose File
                                <input type="file"
                                    name="company_logo"
                                    id="company-logo"
                                    style="display:none;"
                                    accept="image/*">
                            </label>

                        </div>

                        <div class="upload-info">
                            Recommended size:
                            5mb MAX, PNG/JPG
                        </div>

                    </div>

                    <!-- Preview -->
                    <div class="company-logo-preview">

                        <img src="../../Assets/Images/SystemLogo/Smart_edge_logo_3.png"
                            class="img-fluid" id="compLogo">

                    </div>

                </div>

            </div>

        </div>

        <!-- Footer -->
        <div class="company-form-footer">

            <button type="button"
                class="btn btn-danger company-btn-cancel">
                <i class="ti ti-x"></i>
                Cancel
            </button>

            <button type="submit"
                class="btn company-btn-save">
                <?=$btn?>
            </button>

        </div>    
    </form>
    <script>
        var defaultLogo = "../../Assets/Images/SystemLogo/Smart_edge_logo_3.png";
        
        $("#company-logo").on("change", function () {
            console.log("changed");
            
            const file = this.files[0];

            if (!file) {
                $("#compLogo").attr("src", defaultLogo);
                return;
            }

            const maxSize = 5 * 1024 * 1024; // 5MB

            if (file.size > maxSize) {

                parent.toastr.error("Logo size must be less than 5MB.","File Too Large");

                $(this).val("");

                $("#compLogo").attr("src", defaultLogo);

                return;
            }

            const reader = new FileReader();

            reader.onload = function (e) {
                $("#compLogo").attr("src", e.target.result);
            };

            reader.readAsDataURL(file);

        });
        $("#companyForm").on("submit", function(e){
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
                    btn.prop("disabled", true).html("Saving.....")
                },
                success: function(response){

                    if(response[0].status === 1)
                    {
                        parent.toastr.success(response[0].message, "Success");

                        // Optional
                        setTimeout(() => {
                            parent.$("#modal").iziModal("close");
                            parent.fetcompanyList(parent.$("#tbl_company"));    
                        }, 300);
                        
                    }
                    else
                    {
                        parent.toastr.error(response[0].message, "Error");
                    }

                },
                complete: function(response){

                    btn.prop("disabled", false).html(btnHTML);
                },
                error: function (xhr, status, error) {
                    btn.prop("disabled", false).html(btnHTML);
                    console.log("AJAX Error:", status, error);
                    console.log("Response:", xhr.responseText);
                    parent.toastr.error("An error occurred while loading saving company data.","Error");
                }
            })
        });
    </script>

</div>  
</body>
</html>

